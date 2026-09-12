<div class="dashboard-hero p-4 p-md-5 mb-4 reveal">
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div class="hero-content">
            <h2 class="hero-title mb-2">أهلاً بك في لوحة التحكم</h2>
            <p class="hero-subtitle mb-0">نظرة سريعة على متجرك</p>
            <div class="hero-stats mt-3 d-flex gap-4">
                <div class="stat-badge">
                    <span class="stat-number">{{ $stats['products'] ?? 0 }}</span>
                    <span class="stat-label">منتج</span>
                </div>
                <div class="stat-badge">
                    <span class="stat-number">{{ $stats['categories'] ?? 0 }}</span>
                    <span class="stat-label">قسم</span>
                </div>
                <div class="stat-badge">
                    <span class="stat-number">{{ $stats['brands'] ?? 0 }}</span>
                    <span class="stat-label">علامة</span>
                </div>
            </div>
        </div>
        <div class="hero-actions">
            <a href="{{ route('platform.products.create') }}" class="btn btn-hero-accent btn-lg">
                <x-orchid-icon path="bs.plus-circle" class="me-2" />
                أضف منتج جديد
            </a>
        </div>
    </div>
</div>

<style>
/* Dashboard Hero Styles */
.dashboard-hero {
    background: var(--bs-tertiary-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: var(--bs-border-radius-lg, 12px);
}

.hero-title {
    font-weight: 700;
    font-size: 1.5rem;
    color: var(--bs-body-color);
}

.hero-subtitle {
    color: var(--bs-secondary-color);
    font-size: 1rem;
}

.hero-stats {
    flex-wrap: wrap;
}

.stat-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.6rem 1rem;
    background: var(--bs-body-bg);
    border-radius: 10px;
    border: 1px solid var(--bs-border-color);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #B88A2A;
    line-height: 1;
}

[data-bs-theme="dark"] .stat-number {
    color: #D4AD55;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--bs-secondary-color);
    margin-top: 0.25rem;
}

.btn-hero-accent {
    background: #B88A2A;
    color: #111216;
    border: none;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    transition: background-color 0.15s ease;
}

.btn-hero-accent:hover {
    background: #a67d26;
    color: #111216;
}

[data-bs-theme="dark"] .btn-hero-accent {
    background: #D4AD55;
}

[data-bs-theme="dark"] .btn-hero-accent:hover {
    background: #c49c47;
}

@media (max-width: 768px) {
    .hero-actions {
        width: 100%;
    }

    .btn-hero-accent {
        width: 100%;
        justify-content: center;
    }
}
</style>
