<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Exception;

class BackupService
{
    protected $config;
    protected $backupPath;

    public function __construct()
    {
        $this->config = config('backup');
        $this->backupPath = storage_path('backups');
    }

    /**
     * Create a complete backup
     */
    public function createFullBackup(): array
    {
        $startTime = microtime(true);
        $results = [];

        try {
            Log::info('Starting full backup process');
            $this->ensureBackupDirectory();

            // Database backup
            if ($this->config['database']['enabled']) {
                $results['database'] = $this->backupDatabase();
            }

            // Files backup
            if ($this->config['files']['enabled']) {
                $results['files'] = $this->backupFiles();
            }

            // Cleanup old backups
            $this->cleanupOldBackups();

            $duration = microtime(true) - $startTime;
            Log::info("Backup completed in {$duration} seconds");

            return [
                'success' => true,
                'results' => $results,
                'duration' => $duration,
                'timestamp' => now(),
            ];

        } catch (Exception $e) {
            Log::error('Backup failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'timestamp' => now(),
            ];
        }
    }

    /**
     * Backup database
     */
    protected function backupDatabase(): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "database_backup_{$timestamp}.sql";
        $filepath = $this->backupPath . '/database/' . $filename;

        File::makeDirectory(dirname($filepath), 0755, true, true);

        try {
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $host = config("database.connections.{$connection}.host");
            $port = config("database.connections.{$connection}.port") ?? 3306;

            $command = "mysqldump --host={$host} --port={$port} --user={$username}";
            
            if ($password) {
                $command .= " --password={$password}";
            }

            $command .= " --single-transaction --routines --triggers";
            $command .= " --result-file={$filepath} {$database}";

            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new Exception("Database backup failed with return code: {$returnCode}");
            }

            $fileSize = File::size($filepath);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            Log::info("Database backup created: {$filename} ({$fileSizeMB} MB)");

            return [
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => $fileSize,
                'size_mb' => $fileSizeMB,
                'timestamp' => now(),
            ];

        } catch (Exception $e) {
            Log::error("Database backup failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Backup application files
     */
    protected function backupFiles(): array
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "files_backup_{$timestamp}.zip";
        $filepath = $this->backupPath . '/files/' . $filename;

        File::makeDirectory(dirname($filepath), 0755, true, true);

        try {
            $zip = new \ZipArchive();
            
            if ($zip->open($filepath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                throw new Exception('Could not create ZIP archive');
            }

            $basePath = base_path();
            $includedPaths = $this->config['files']['include'];

            foreach ($includedPaths as $path) {
                $fullPath = $basePath . '/' . $path;
                
                if (File::exists($fullPath)) {
                    if (File::isFile($fullPath)) {
                        $zip->addFile($fullPath, $path);
                    } else {
                        $this->addDirectoryToZip($zip, $fullPath, $path);
                    }
                }
            }

            $zip->close();

            $fileSize = File::size($filepath);
            $fileSizeMB = round($fileSize / 1024 / 1024, 2);

            Log::info("Files backup created: {$filename} ({$fileSizeMB} MB)");

            return [
                'filename' => $filename,
                'filepath' => $filepath,
                'size' => $fileSize,
                'size_mb' => $fileSizeMB,
                'timestamp' => now(),
            ];

        } catch (Exception $e) {
            Log::error("Files backup failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Add directory to ZIP archive
     */
    protected function addDirectoryToZip($zip, $dirPath, $relativePath): void
    {
        $files = File::allFiles($dirPath);

        foreach ($files as $file) {
            $fileRelativePath = $relativePath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getPathname(), $fileRelativePath);
        }
    }

    /**
     * Cleanup old backups
     */
    protected function cleanupOldBackups(): void
    {
        if (!$this->config['cleanup']['enabled']) {
            return;
        }

        try {
            $this->cleanupDatabaseBackups();
            $this->cleanupFileBackups();
            Log::info('Old backups cleanup completed');

        } catch (Exception $e) {
            Log::error("Backup cleanup failed: {$e->getMessage()}");
        }
    }

    /**
     * Cleanup old database backups
     */
    protected function cleanupDatabaseBackups(): void
    {
        $retention = $this->config['schedule']['database']['retention'];
        $backupDir = $this->backupPath . '/database';

        if (!File::exists($backupDir)) {
            return;
        }

        $files = File::glob($backupDir . '/*.sql');
        $this->cleanupFilesByRetention($files, $retention);
    }

    /**
     * Cleanup old file backups
     */
    protected function cleanupFileBackups(): void
    {
        $retention = $this->config['schedule']['files']['retention'];
        $backupDir = $this->backupPath . '/files';

        if (!File::exists($backupDir)) {
            return;
        }

        $files = File::glob($backupDir . '/*.zip');
        $this->cleanupFilesByRetention($files, $retention);
    }

    /**
     * Cleanup files based on retention policy
     */
    protected function cleanupFilesByRetention(array $files, array $retention): void
    {
        usort($files, function($a, $b) {
            return File::lastModified($b) - File::lastModified($a);
        });

        foreach ($retention as $period => $count) {
            $filesToDelete = array_slice($files, $count);

            foreach ($filesToDelete as $file) {
                if (File::delete($file)) {
                    Log::info("Deleted old backup file: " . basename($file));
                }
            }
        }
    }

    /**
     * Get backup statistics
     */
    public function getBackupStats(): array
    {
        $stats = [
            'total_backups' => 0,
            'total_size_mb' => 0,
            'last_backup' => null,
            'backup_types' => [
                'database' => ['count' => 0, 'size' => 0],
                'files' => ['count' => 0, 'size' => 0],
            ],
        ];

        try {
            $dbBackups = $this->getBackupFiles('database');
            $stats['backup_types']['database']['count'] = count($dbBackups);
            $stats['backup_types']['database']['size'] = $this->calculateTotalSize($dbBackups);

            $fileBackups = $this->getBackupFiles('files');
            $stats['backup_types']['files']['count'] = count($fileBackups);
            $stats['backup_types']['files']['size'] = $this->calculateTotalSize($fileBackups);

            $stats['total_backups'] = $stats['backup_types']['database']['count'] + 
                                    $stats['backup_types']['files']['count'];

            $stats['total_size_mb'] = round(($stats['backup_types']['database']['size'] + 
                                           $stats['backup_types']['files']['size']) / 1024 / 1024, 2);

            $allBackups = array_merge($dbBackups, $fileBackups);
            if (!empty($allBackups)) {
                $lastBackup = max(array_column($allBackups, 'modified'));
                $stats['last_backup'] = Carbon::createFromTimestamp($lastBackup);
            }

        } catch (Exception $e) {
            Log::error("Failed to get backup stats: {$e->getMessage()}");
        }

        return $stats;
    }

    /**
     * Get list of backup files
     */
    protected function getBackupFiles(string $type): array
    {
        $backupDir = $this->backupPath . '/' . $type;
        
        if (!File::exists($backupDir)) {
            return [];
        }

        $files = [];
        $fileList = File::files($backupDir);

        foreach ($fileList as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
            ];
        }

        return $files;
    }

    /**
     * Calculate total size of files
     */
    protected function calculateTotalSize(array $files): int
    {
        return array_sum(array_column($files, 'size'));
    }

    /**
     * Ensure backup directory exists
     */
    protected function ensureBackupDirectory(): void
    {
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }
}
