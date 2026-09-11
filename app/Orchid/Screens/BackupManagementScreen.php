<?php

namespace App\Orchid\Screens;

use App\Services\BackupService;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Toast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class BackupManagementScreen extends Screen
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(): iterable
    {
        return [
            'stats' => $this->backupService->getBackupStats(),
            'database_backups' => $this->getBackupFiles('database'),
            'file_backups' => $this->getBackupFiles('files'),
            'backup_config' => config('backup'),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Backup Management';
    }

    /**
     * The screen's action buttons.
     */
    public function commandBar(): iterable
    {
        return [
            Button::make('Create Full Backup')
                ->icon('cloud-upload')
                ->method('createBackup')
                ->class('btn btn-primary')
                ->confirm('Are you sure you want to create a full backup? This may take several minutes.'),

            Button::make('Create Database Backup')
                ->icon('database')
                ->method('createDatabaseBackup')
                ->class('btn btn-info'),

            Button::make('Create Files Backup')
                ->icon('folder')
                ->method('createFilesBackup')
                ->class('btn btn-warning'),

            Button::make('Cleanup Old Backups')
                ->icon('trash')
                ->method('cleanupOldBackups')
                ->class('btn btn-danger')
                ->confirm('Are you sure you want to cleanup old backups? This action cannot be undone.'),

            Button::make('Refresh Stats')
                ->icon('refresh')
                ->method('refreshStats')
                ->class('btn btn-secondary'),
        ];
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Layout::view('partials.backup-stats'),
            ])->title('📊 Backup Statistics'),

            Layout::tabs([
                'Database Backups' => Layout::table('database_backups', [
                    TD::make('name', 'Filename')->sort(),
                    TD::make('size_mb', 'Size (MB)')->sort(),
                    TD::make('created', 'Created')->sort(),
                    TD::make('actions', 'Actions')->render(function ($backup) {
                        return Button::make('Download')
                            ->icon('download')
                            ->method('downloadBackup', ['type' => 'database', 'file' => $backup['name']])
                            ->class('btn btn-sm btn-primary');
                    }),
                ])->title('🗄️ Database Backups'),

                'File Backups' => Layout::table('file_backups', [
                    TD::make('name', 'Filename')->sort(),
                    TD::make('size_mb', 'Size (MB)')->sort(),
                    TD::make('created', 'Created')->sort(),
                    TD::make('actions', 'Actions')->render(function ($backup) {
                        return Button::make('Download')
                            ->icon('download')
                            ->method('downloadBackup', ['type' => 'files', 'file' => $backup['name']])
                            ->class('btn btn-sm btn-primary');
                    }),
                ])->title('📁 File Backups'),

                'Configuration' => Layout::rows([
                    Layout::view('partials.backup-config'),
                ])->title('⚙️ Backup Configuration'),
            ])->title('🔧 Backup Management'),
        ];
    }

    /**
     * Create full backup
     */
    public function createBackup(): void
    {
        try {
            $result = $this->backupService->createFullBackup();

            if ($result['success']) {
                Toast::success('Full backup created successfully!');
            } else {
                Toast::error('Backup failed: ' . ($result['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Toast::error('Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Create database backup only
     */
    public function createDatabaseBackup(): void
    {
        try {
            $result = $this->backupService->backupDatabase();
            Toast::success('Database backup created successfully!');
        } catch (\Exception $e) {
            Toast::error('Database backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Create files backup only
     */
    public function createFilesBackup(): void
    {
        try {
            $result = $this->backupService->backupFiles();
            Toast::success('Files backup created successfully!');
        } catch (\Exception $e) {
            Toast::error('Files backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Cleanup old backups
     */
    public function cleanupOldBackups(): void
    {
        try {
            $this->backupService->cleanupOldBackups();
            Toast::success('Old backups cleaned up successfully!');
        } catch (\Exception $e) {
            Toast::error('Cleanup failed: ' . $e->getMessage());
        }
    }

    /**
     * Refresh backup statistics
     */
    public function refreshStats(): void
    {
        Toast::info('Statistics refreshed!');
    }

    /**
     * Download backup file
     */
    public function downloadBackup(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $type = $request->get('type');
        $file = $request->get('file');

        $filepath = storage_path("backups/{$type}/{$file}");

        if (!File::exists($filepath)) {
            abort(404, 'Backup file not found');
        }

        return response()->streamDownload(function () use ($filepath) {
            $stream = fopen($filepath, 'rb');
            fpassthru($stream);
            fclose($stream);
        }, $file);
    }

    /**
     * Get backup files for a specific type
     */
    protected function getBackupFiles(string $type): array
    {
        $backupDir = storage_path("backups/{$type}");

        if (!File::exists($backupDir)) {
            return [];
        }

        $files = [];
        $fileList = File::files($backupDir);

        foreach ($fileList as $file) {
            $files[] = [
                'name' => $file->getFilename(),
                'size' => $file->getSize(),
                'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                'created' => \Carbon\Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
            ];
        }

        // Sort by creation time (newest first)
        usort($files, function($a, $b) {
            return strtotime($b['created']) - strtotime($a['created']);
        });

        return $files;
    }
}
