@php($sales = $sales ?? [])

<div class="dashboard-sales reveal">
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="sales-chart-card p-4 h-100">
                <div class="chart-header mb-4">
                    <h3 class="chart-title">تحليل المبيعات</h3>
                    <p class="chart-subtitle">أداء المبيعات خلال آخر 6 أشهر</p>
                </div>

                <div class="chart-container">
                    <div class="chart-bars">
                        @foreach($sales as $index => $sale)
                        <div class="chart-bar-wrapper" data-value="{{ $sale['value'] }}" data-month="{{ $sale['month'] }}">
                            <div class="chart-bar">
                                <div class="bar-fill" style="height: {{ ($sale['value'] / 3000) * 100 }}%"></div>
                            </div>
                            <div class="bar-label">{{ $sale['month'] }}</div>
                            <div class="bar-value">{{ number_format($sale['value']) }}</div>
                        </div>
                        @endforeach
                    </div>

                    <div class="chart-grid">
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                    </div>
                </div>

                <div class="chart-stats mt-4">
                    <div class="stat-item">
                        <div class="stat-label">إجمالي المبيعات</div>
                        <div class="stat-value">{{ number_format(array_sum(array_column($sales, 'value'))) }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">متوسط شهري</div>
                        <div class="stat-value">{{ number_format(array_sum(array_column($sales, 'value')) / count($sales)) }}</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">أعلى شهر</div>
                        <div class="stat-value">{{ $sales[array_search(max(array_column($sales, 'value')), array_column($sales, 'value'))]['month'] ?? 'غير محدد' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="quick-actions-card p-4 h-100">
                <div class="actions-header mb-4">
                    <h3 class="actions-title">إجراءات سريعة</h3>
                    <p class="actions-subtitle">الوصول السريع للمهام المهمة</p>
                </div>

                <div class="actions-list">
                    <a href="{{ route('platform.products.create') }}" class="action-item">
                        <div class="action-icon">
                            <x-orchid-icon path="bs.plus-circle" />
                        </div>
                        <div class="action-content">
                            <div class="action-title">إضافة منتج جديد</div>
                            <div class="action-desc">إنشاء منتج جديد في المتجر</div>
                        </div>
                        <div class="action-arrow">
                            <x-orchid-icon path="bs.arrow-left" />
                        </div>
                    </a>

                    <a href="{{ route('platform.categories.create') }}" class="action-item">
                        <div class="action-icon">
                            <x-orchid-icon path="bs.folder-plus" />
                        </div>
                        <div class="action-content">
                            <div class="action-title">إضافة قسم جديد</div>
                            <div class="action-desc">تنظيم المنتجات في أقسام</div>
                        </div>
                        <div class="action-arrow">
                            <x-orchid-icon path="bs.arrow-left" />
                        </div>
                    </a>

                    <a href="{{ route('platform.offers.create') }}" class="action-item">
                        <div class="action-icon">
                            <x-orchid-icon path="bs.percent" />
                        </div>
                        <div class="action-content">
                            <div class="action-title">إنشاء عرض جديد</div>
                            <div class="action-desc">عروض وخصومات للمنتجات</div>
                        </div>
                        <div class="action-arrow">
                            <x-orchid-icon path="bs.arrow-left" />
                        </div>
                    </a>

                    <a href="{{ route('platform.slides.create') }}" class="action-item">
                        <div class="action-icon">
                            <x-orchid-icon path="bs.images" />
                        </div>
                        <div class="action-content">
                            <div class="action-title">إضافة شريحة</div>
                            <div class="action-desc">شرائح العرض في الصفحة الرئيسية</div>
                        </div>
                        <div class="action-arrow">
                            <x-orchid-icon path="bs.arrow-left" />
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Dashboard Sales Styles */
.dashboard-sales {
    margin-bottom: 2rem;
}

.sales-chart-card, .quick-actions-card {
    background: var(--bs-tertiary-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: var(--bs-border-radius-lg, 12px);
}

/* Chart Styles */
.chart-header {
    text-align: center;
}

.chart-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--bs-body-color);
    margin-bottom: 0.5rem;
}

.chart-subtitle {
    color: var(--bs-secondary-color);
    margin-bottom: 0;
    font-size: 0.9rem;
}

.chart-container {
    position: relative;
    height: 260px;
    margin: 2rem 0;
}

.chart-bars {
    display: flex;
    align-items: stretch;
    justify-content: space-around;
    height: 100%;
    padding: 0 1rem;
    position: relative;
    z-index: 2;
}

.chart-bar-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    max-width: 80px;
}

.chart-bar {
    width: 32px;
    flex: 1 1 auto;
    min-height: 0;
    position: relative;
    display: flex;
    align-items: end;
    margin: 0 auto 1rem;
}

.bar-fill {
    width: 100%;
    background: #B88A2A;
    border-radius: 6px 6px 0 0;
    transform-origin: bottom;
    animation: barGrow 0.5s ease-out;
}

[data-bs-theme="dark"] .bar-fill {
    background: #D4AD55;
}

@keyframes barGrow {
    from { transform: scaleY(0); }
    to { transform: scaleY(1); }
}

.bar-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--bs-body-color);
    text-align: center;
    margin-bottom: 0.25rem;
}

.bar-value {
    font-size: 0.75rem;
    color: var(--bs-secondary-color);
    text-align: center;
}

.chart-grid {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.grid-line {
    position: absolute;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--bs-border-color);
}

.grid-line:nth-child(1) { top: 0%; }
.grid-line:nth-child(2) { top: 25%; }
.grid-line:nth-child(3) { top: 50%; }
.grid-line:nth-child(4) { top: 75%; }
.grid-line:nth-child(5) { top: 100%; }

.chart-stats {
    display: flex;
    justify-content: space-around;
    gap: 1rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    padding: 0.85rem;
    background: var(--bs-body-bg);
    border-radius: 10px;
    border: 1px solid var(--bs-border-color);
    flex: 1;
    min-width: 120px;
}

.stat-label {
    font-size: 0.8rem;
    color: var(--bs-secondary-color);
    margin-bottom: 0.35rem;
}

.stat-value {
    font-size: 1.15rem;
    font-weight: 700;
    color: #B88A2A;
}

[data-bs-theme="dark"] .stat-value {
    color: #D4AD55;
}

/* Quick Actions Styles */
.actions-header {
    text-align: center;
}

.actions-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--bs-body-color);
    margin-bottom: 0.5rem;
}

.actions-subtitle {
    color: var(--bs-secondary-color);
    margin-bottom: 0;
    font-size: 0.9rem;
}

.actions-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.85rem 1rem;
    background: var(--bs-body-bg);
    border-radius: 10px;
    border: 1px solid var(--bs-border-color);
    text-decoration: none;
    color: var(--bs-body-color);
    transition: border-color 0.15s ease;
}

.action-item:hover {
    border-color: #B88A2A;
    color: var(--bs-body-color);
    text-decoration: none;
}

[data-bs-theme="dark"] .action-item:hover {
    border-color: #D4AD55;
}

.action-icon {
    width: 38px;
    height: 38px;
    border-radius: 9px;
    background: var(--bs-tertiary-bg);
    border: 1px solid var(--bs-border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #B88A2A;
    flex-shrink: 0;
}

.action-icon svg {
    width: 18px;
    height: 18px;
}

[data-bs-theme="dark"] .action-icon {
    color: #D4AD55;
}

.action-content {
    flex: 1;
}

.action-title {
    font-weight: 600;
    margin-bottom: 0.15rem;
    font-size: 0.9rem;
}

.action-desc {
    font-size: 0.8rem;
    color: var(--bs-secondary-color);
}

.action-arrow {
    color: var(--bs-secondary-color);
}

.action-arrow svg {
    width: 14px;
    height: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .chart-container {
        height: 220px;
    }

    .chart-bars {
        padding: 0 0.5rem;
    }

    .chart-bar {
        width: 26px;
    }

    .chart-stats {
        flex-direction: column;
    }

    .stat-item {
        min-width: auto;
    }
}
</style>
