<div class="backup-stats">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="stat-card bg-primary text-white">
                <div class="stat-icon">📊</div>
                <div class="stat-number">{{ $stats['total_backups'] ?? 0 }}</div>
                <div class="stat-label">Total Backups</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-success text-white">
                <div class="stat-icon">💾</div>
                <div class="stat-number">{{ $stats['total_size_mb'] ?? 0 }}</div>
                <div class="stat-label">Total Size (MB)</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-info text-white">
                <div class="stat-icon">🗄️</div>
                <div class="stat-number">{{ $stats['backup_types']['database']['count'] ?? 0 }}</div>
                <div class="stat-label">Database Backups</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-warning text-white">
                <div class="stat-icon">📁</div>
                <div class="stat-number">{{ $stats['backup_types']['files']['count'] ?? 0 }}</div>
                <div class="stat-label">File Backups</div>
            </div>
        </div>
    </div>
    
    @if($stats['last_backup'])
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-info">
                <strong>Last Backup:</strong> {{ $stats['last_backup']->format('F j, Y \a\t g:i A') }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.backup-stats .stat-card {
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.backup-stats .stat-card:hover {
    transform: translateY(-2px);
}

.backup-stats .stat-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.backup-stats .stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.backup-stats .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.backup-stats .alert {
    border-radius: 8px;
    border: none;
}
</style>
