@php($stats = $stats ?? ['products'=>0,'categories'=>0,'brands'=>0,'offers'=>0])

<div class="dashboard-cards reveal">
    <div class="row g-4">
        <div class="col-12 col-md-3">
            <div class="dashboard-card p-4 h-100">
                <div class="card-icon">
                    <x-orchid-icon path="bs.box-seam" />
                </div>
                <div class="card-content">
                    <h3 class="card-title">المنتجات</h3>
                    <div class="card-number">{{ number_format($stats['products']) }}</div>
                    <p class="card-description">إجمالي المنتجات في المتجر</p>
                    <a href="{{ route('platform.products.list') }}" class="card-link">
                        إدارة المنتجات
                        <x-orchid-icon path="bs.arrow-left" class="ms-2" />
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="dashboard-card p-4 h-100">
                <div class="card-icon">
                    <x-orchid-icon path="bs.collection" />
                </div>
                <div class="card-content">
                    <h3 class="card-title">الأقسام</h3>
                    <div class="card-number">{{ number_format($stats['categories']) }}</div>
                    <p class="card-description">أقسام المنتجات المتاحة</p>
                    <a href="{{ route('platform.categories.list') }}" class="card-link">
                        إدارة الأقسام
                        <x-orchid-icon path="bs.arrow-left" class="ms-2" />
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="dashboard-card p-4 h-100">
                <div class="card-icon">
                    <x-orchid-icon path="bs.tags" />
                </div>
                <div class="card-content">
                    <h3 class="card-title">العلامات التجارية</h3>
                    <div class="card-number">{{ number_format($stats['brands']) }}</div>
                    <p class="card-description">العلامات التجارية المسجلة</p>
                    <a href="{{ route('platform.brands.list') }}" class="card-link">
                        إدارة العلامات
                        <x-orchid-icon path="bs.arrow-left" class="ms-2" />
                    </a>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="dashboard-card p-4 h-100">
                <div class="card-icon">
                    <x-orchid-icon path="bs.ticket-perforated" />
                </div>
                <div class="card-content">
                    <h3 class="card-title">العروض</h3>
                    <div class="card-number">{{ number_format($stats['offers']) }}</div>
                    <p class="card-description">العروض والخصومات النشطة</p>
                    <a href="{{ route('platform.offers.list') }}" class="card-link">
                        إدارة العروض
                        <x-orchid-icon path="bs.arrow-left" class="ms-2" />
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Cards Styles */
.dashboard-cards {
    margin-bottom: 2rem;
}

.dashboard-card {
    background: var(--bs-tertiary-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: var(--bs-border-radius-lg, 12px);
    transition: border-color 0.15s ease, transform 0.15s ease;
}

.dashboard-card:hover {
    border-color: #B88A2A;
    transform: translateY(-2px);
}

[data-bs-theme="dark"] .dashboard-card:hover {
    border-color: #D4AD55;
}

.card-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    color: #B88A2A;
    margin-bottom: 1rem;
}

.card-icon svg {
    width: 22px;
    height: 22px;
}

[data-bs-theme="dark"] .card-icon {
    color: #D4AD55;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--bs-body-color);
    margin-bottom: 0.5rem;
}

.card-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--bs-body-color);
    line-height: 1;
    margin-bottom: 0.5rem;
}

.card-description {
    font-size: 0.875rem;
    color: var(--bs-secondary-color);
    margin-bottom: 1rem;
    line-height: 1.4;
}

.card-link {
    display: inline-flex;
    align-items: center;
    color: #B88A2A;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
}

.card-link svg {
    width: 14px;
    height: 14px;
}

.card-link:hover {
    text-decoration: underline;
    color: #a67d26;
}

[data-bs-theme="dark"] .card-link {
    color: #D4AD55;
}

[data-bs-theme="dark"] .card-link:hover {
    color: #c49c47;
}

@media (max-width: 768px) {
    .dashboard-card {
        margin-bottom: 1rem;
    }

    .card-number {
        font-size: 1.75rem;
    }
}
</style>
