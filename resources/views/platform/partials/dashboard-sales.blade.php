@php($sales = $sales ?? [])

<div class="dashboard-sales reveal">
    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="crystal-card sales-chart-card p-4 h-100">
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
                                <div class="bar-shine"></div>
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
            <div class="crystal-card quick-actions-card p-4 h-100">
                <div class="actions-header mb-4">
                    <h3 class="actions-title">إجراءات سريعة</h3>
                    <p class="actions-subtitle">الوصول السريع للمهام المهمة</p>
                </div>
                
                <div class="actions-list">
                    <a href="{{ route('platform.products.create') }}" class="action-item">
                        <div class="action-icon">
                            <i class="bi bi-plus-circle"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">إضافة منتج جديد</div>
                            <div class="action-desc">إنشاء منتج جديد في المتجر</div>
                        </div>
                        <div class="action-arrow">
                            <i class="bi bi-arrow-left"></i>
                        </div>
                    </a>
                    
                    <a href="{{ route('platform.categories.create') }}" class="action-item">
                        <div class="action-icon">
                            <i class="bi bi-folder-plus"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">إضافة قسم جديد</div>
                            <div class="action-desc">تنظيم المنتجات في أقسام</div>
                        </div>
                        <div class="action-arrow">
                            <i class="bi bi-arrow-left"></i>
                        </div>
                    </a>
                    
                    <a href="{{ route('platform.offers.create') }}" class="action-item">
                        <div class="action-icon">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">إنشاء عرض جديد</div>
                            <div class="action-desc">عروض وخصومات للمنتجات</div>
                        </div>
                        <div class="action-arrow">
                            <i class="bi bi-arrow-left"></i>
                        </div>
                    </a>
                    
                    <a href="{{ route('platform.slides.create') }}" class="action-item">
                        <div class="action-icon">
                            <i class="bi bi-images"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">إضافة شريحة</div>
                            <div class="action-desc">شرائح العرض في الصفحة الرئيسية</div>
                        </div>
                        <div class="action-arrow">
                            <i class="bi bi-arrow-left"></i>
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
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg,
        rgba(255,255,255,0.1) 0%,
        rgba(255,255,255,0.05) 50%,
        rgba(255,255,255,0.02) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 20px;
    box-shadow:
        0 8px 32px rgba(0,0,0,0.1),
        inset 0 1px 0 rgba(255,255,255,0.2);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.sales-chart-card::before, .quick-actions-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
        transparent,
        rgba(255,255,255,0.1),
        transparent);
    transition: left 0.6s ease;
}

.sales-chart-card:hover::before, .quick-actions-card:hover::before {
    left: 100%;
}

.sales-chart-card:hover, .quick-actions-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow:
        0 20px 40px rgba(0,0,0,0.15),
        0 0 0 1px rgba(255,255,255,0.3),
        inset 0 1px 0 rgba(255,255,255,0.3);
}

/* Chart Styles */
.chart-header {
    text-align: center;
}

.chart-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.5rem;
}

.chart-subtitle {
    color: var(--muted);
    margin-bottom: 0;
}

.chart-container {
    position: relative;
    height: 300px;
    margin: 2rem 0;
}

.chart-bars {
    display: flex;
    align-items: end;
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
    transition: all 0.3s ease;
}

.chart-bar-wrapper:hover {
    transform: translateY(-5px);
}

.chart-bar {
    width: 40px;
    height: 100%;
    position: relative;
    display: flex;
    align-items: end;
    margin-bottom: 1rem;
}

.bar-fill {
    width: 100%;
    background: linear-gradient(180deg, #F0C24B, #D9A92F);
    border-radius: 8px 8px 0 0;
    position: relative;
    transition: all 0.6s ease;
    animation: barGrow 1.5s ease-out forwards;
    transform-origin: bottom;
}

.bar-shine {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
        transparent,
        rgba(255,255,255,0.3),
        transparent);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.chart-bar-wrapper:hover .bar-shine {
    transform: translateX(100%);
}

@keyframes barGrow {
    from {
        transform: scaleY(0);
    }
    to {
        transform: scaleY(1);
    }
}

.bar-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text);
    text-align: center;
    margin-bottom: 0.5rem;
}

.bar-value {
    font-size: 0.75rem;
    color: var(--muted);
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
    background: rgba(255,255,255,0.1);
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
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.3s ease;
    flex: 1;
    min-width: 120px;
}

.stat-item:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.3);
}

.stat-label {
    font-size: 0.875rem;
    color: var(--muted);
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #F0C24B;
}

/* Quick Actions Styles */
.actions-header {
    text-align: center;
}

.actions-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 0.5rem;
}

.actions-subtitle {
    color: var(--muted);
    margin-bottom: 0;
}

.actions-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    text-decoration: none;
    color: var(--text);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.action-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
        transparent,
        rgba(255,255,255,0.1),
        transparent);
    transition: left 0.6s ease;
}

.action-item:hover::before {
    left: 100%;
}

.action-item:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.3);
    color: var(--text);
    text-decoration: none;
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, #F0C24B, #D9A92F);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.action-content {
    flex: 1;
}

.action-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.action-desc {
    font-size: 0.875rem;
    color: var(--muted);
}

.action-arrow {
    color: var(--muted);
    transition: transform 0.3s ease;
}

.action-item:hover .action-arrow {
    transform: translateX(-5px);
}

/* Dark mode enhancements */
html[data-theme="dark"] .sales-chart-card,
html[data-theme="dark"] .quick-actions-card {
    background: linear-gradient(135deg,
        rgba(255,255,255,0.05) 0%,
        rgba(255,255,255,0.02) 50%,
        rgba(255,255,255,0.01) 100%);
    border: 1px solid rgba(255,255,255,0.1);
}

html[data-theme="dark"] .stat-item,
html[data-theme="dark"] .action-item {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
}

html[data-theme="dark"] .stat-item:hover,
html[data-theme="dark"] .action-item:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .chart-container {
        height: 250px;
    }
    
    .chart-bars {
        padding: 0 0.5rem;
    }
    
    .chart-bar {
        width: 30px;
    }
    
    .chart-stats {
        flex-direction: column;
    }
    
    .stat-item {
        min-width: auto;
    }
    
    .actions-list {
        gap: 0.75rem;
    }
    
    .action-item {
        padding: 0.75rem;
    }
}
</style>
