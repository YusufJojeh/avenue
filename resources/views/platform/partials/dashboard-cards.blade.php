@php($stats = $stats ?? ['products'=>0,'categories'=>0,'brands'=>0,'offers'=>0])

<div class="dashboard-cards reveal">
    <div class="row g-4">
        <div class="col-12 col-md-3">
            <div class="crystal-card dashboard-card p-4 h-100" data-delay="0">
                <div class="card-icon-wrapper mb-3">
                    <div class="card-icon products-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="card-title">المنتجات</h3>
                    <div class="card-number">{{ number_format($stats['products']) }}</div>
                    <p class="card-description">إجمالي المنتجات في المتجر</p>
                    <a href="{{ route('platform.products.list') }}" class="card-link">
                        إدارة المنتجات
                        <i class="bi bi-arrow-left ms-2"></i>
                    </a>
                </div>
                <div class="card-shine"></div>
            </div>
        </div>
        
        <div class="col-12 col-md-3">
            <div class="crystal-card dashboard-card p-4 h-100" data-delay="0.1">
                <div class="card-icon-wrapper mb-3">
                    <div class="card-icon categories-icon">
                        <i class="bi bi-collection"></i>
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="card-title">الأقسام</h3>
                    <div class="card-number">{{ number_format($stats['categories']) }}</div>
                    <p class="card-description">أقسام المنتجات المتاحة</p>
                    <a href="{{ route('platform.categories.list') }}" class="card-link">
                        إدارة الأقسام
                        <i class="bi bi-arrow-left ms-2"></i>
                    </a>
                </div>
                <div class="card-shine"></div>
            </div>
        </div>
        
        <div class="col-12 col-md-3">
            <div class="crystal-card dashboard-card p-4 h-100" data-delay="0.2">
                <div class="card-icon-wrapper mb-3">
                    <div class="card-icon brands-icon">
                        <i class="bi bi-tags"></i>
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="card-title">العلامات التجارية</h3>
                    <div class="card-number">{{ number_format($stats['brands']) }}</div>
                    <p class="card-description">العلامات التجارية المسجلة</p>
                    <a href="{{ route('platform.brands.list') }}" class="card-link">
                        إدارة العلامات
                        <i class="bi bi-arrow-left ms-2"></i>
                    </a>
                </div>
                <div class="card-shine"></div>
            </div>
        </div>
        
        <div class="col-12 col-md-3">
            <div class="crystal-card dashboard-card p-4 h-100" data-delay="0.3">
                <div class="card-icon-wrapper mb-3">
                    <div class="card-icon offers-icon">
                        <i class="bi bi-ticket-perforated"></i>
                    </div>
                </div>
                <div class="card-content">
                    <h3 class="card-title">العروض</h3>
                    <div class="card-number">{{ number_format($stats['offers']) }}</div>
                    <p class="card-description">العروض والخصومات النشطة</p>
                    <a href="{{ route('platform.offers.list') }}" class="card-link">
                        إدارة العروض
                        <i class="bi bi-arrow-left ms-2"></i>
                    </a>
                </div>
                <div class="card-shine"></div>
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
    cursor: pointer;
}

.dashboard-card::before {
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

.dashboard-card:hover::before {
    left: 100%;
}

.dashboard-card:hover {
    transform: translateY(-12px) scale(1.03);
    box-shadow:
        0 25px 50px rgba(0,0,0,0.15),
        0 0 0 1px rgba(255,255,255,0.3),
        inset 0 1px 0 rgba(255,255,255,0.3);
}

.card-icon-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
}

.card-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    position: relative;
    overflow: hidden;
    transition: all 0.4s ease;
}

.card-icon::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.4s ease;
}

.dashboard-card:hover .card-icon::before {
    transform: translateX(100%);
}

.products-icon {
    background: linear-gradient(135deg, #F0C24B, #D9A92F);
    box-shadow: 0 8px 25px rgba(240,194,75,0.3);
}

.categories-icon {
    background: linear-gradient(135deg, #6366F1, #8B5CF6);
    box-shadow: 0 8px 25px rgba(99,102,241,0.3);
}

.brands-icon {
    background: linear-gradient(135deg, #10B981, #059669);
    box-shadow: 0 8px 25px rgba(16,185,129,0.3);
}

.offers-icon {
    background: linear-gradient(135deg, #F59E0B, #D97706);
    box-shadow: 0 8px 25px rgba(245,158,11,0.3);
}

.dashboard-card:hover .card-icon {
    transform: scale(1.1) rotate(5deg);
}

.card-content {
    text-align: center;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text);
    margin-bottom: 0.5rem;
    transition: color 0.3s ease;
}

.card-number {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #F0C24B, #D9A92F);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.card-description {
    font-size: 0.875rem;
    color: var(--muted);
    margin-bottom: 1rem;
    line-height: 1.4;
}

.card-link {
    display: inline-flex;
    align-items: center;
    color: var(--text);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.card-link:hover {
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.3);
    transform: translateY(-2px);
    color: var(--text);
    text-decoration: none;
}

.card-shine {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg,
        transparent 30%,
        rgba(255,255,255,0.1) 50%,
        transparent 70%);
    transform: translateX(-100%);
    transition: transform 0.6s ease;
    pointer-events: none;
}

.dashboard-card:hover .card-shine {
    transform: translateX(100%);
}

/* Dark mode enhancements */
html[data-theme="dark"] .dashboard-card {
    background: linear-gradient(135deg,
        rgba(255,255,255,0.05) 0%,
        rgba(255,255,255,0.02) 50%,
        rgba(255,255,255,0.01) 100%);
    border: 1px solid rgba(255,255,255,0.1);
}

html[data-theme="dark"] .dashboard-card:hover {
    box-shadow:
        0 25px 50px rgba(0,0,0,0.3),
        0 0 0 1px rgba(255,255,255,0.2),
        inset 0 1px 0 rgba(255,255,255,0.2);
}

html[data-theme="dark"] .card-link {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
}

html[data-theme="dark"] .card-link:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-card {
        margin-bottom: 1rem;
    }
    
    .card-number {
        font-size: 2rem;
    }
    
    .card-icon {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
}

/* Animation delays for staggered entrance */
.dashboard-card[data-delay="0"] { animation-delay: 0s; }
.dashboard-card[data-delay="0.1"] { animation-delay: 0.1s; }
.dashboard-card[data-delay="0.2"] { animation-delay: 0.2s; }
.dashboard-card[data-delay="0.3"] { animation-delay: 0.3s; }
</style>
