<div class="version-stats">
    <div class="row g-3">
        <div class="col-md-3">
            <div class="stat-card bg-primary text-white">
                <div class="stat-icon">📊</div>
                <div class="stat-number">{{ $stats['total_versions'] ?? 0 }}</div>
                <div class="stat-label">Total Versions</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-success text-white">
                <div class="stat-icon">✅</div>
                <div class="stat-number">{{ $stats['published_versions'] ?? 0 }}</div>
                <div class="stat-label">Published</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-secondary text-white">
                <div class="stat-icon">📝</div>
                <div class="stat-number">{{ $stats['unpublished_versions'] ?? 0 }}</div>
                <div class="stat-label">Drafts</div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card bg-info text-white">
                <div class="stat-icon">🔄</div>
                <div class="stat-number">{{ $stats['total_versions'] > 0 ? round(($stats['published_versions'] / $stats['total_versions']) * 100, 1) : 0 }}%</div>
                <div class="stat-label">Publish Rate</div>
            </div>
        </div>
    </div>
</div>

<style>
.version-stats .stat-card {
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.version-stats .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.version-stats .stat-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.version-stats .stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.version-stats .stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Dark mode support */
html[data-theme="dark"] .version-stats .stat-card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
}

html[data-theme="dark"] .version-stats .stat-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
}
</style>
