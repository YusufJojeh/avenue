<div class="dashboard-hero crystal-card p-5 mb-4 reveal">
    {{-- Floating Elements --}}
    <div class="floating-element" style="top: 20%; left: 10%; font-size: 2rem;">✨</div>
    <div class="floating-element" style="top: 60%; right: 15%; font-size: 1.5rem;">💎</div>
    <div class="floating-element" style="top: 30%; right: 25%; font-size: 1.8rem;">🌟</div>
    
    <div class="hero-bg"></div>
    
    <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap position-relative">
        <div class="hero-content">
            <h2 class="fw-bold mb-2 text-gradient">أهلاً بك في لوحة التحكم 👋</h2>
            <p class="mb-0 opacity-90 fs-5">واجهة Orchid حديثة مع تأثيرات الكريستال والزجاج — أناقة بلا حدود</p>
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
            <a href="{{ route('platform.products.create') }}" class="btn btn-crystal btn-lg">
                <i class="bi bi-plus-circle me-2"></i>
                أضف منتج جديد
            </a>
        </div>
    </div>
</div>

<style>
/* Dashboard Hero Styles */
.dashboard-hero {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg,
        rgba(255,255,255,0.1) 0%,
        rgba(255,255,255,0.05) 50%,
        rgba(255,255,255,0.02) 100%);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 24px;
    box-shadow:
        0 8px 32px rgba(0,0,0,0.1),
        inset 0 1px 0 rgba(255,255,255,0.2);
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-hero::before {
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

.dashboard-hero:hover::before {
    left: 100%;
}

.dashboard-hero:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow:
        0 20px 40px rgba(0,0,0,0.15),
        0 0 0 1px rgba(255,255,255,0.3),
        inset 0 1px 0 rgba(255,255,255,0.3);
}

.hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        radial-gradient(circle at 20% 80%, rgba(240,194,75,0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(120,119,198,0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(255,255,255,0.05) 0%, transparent 50%);
    animation: heroFloat 20s ease-in-out infinite;
    pointer-events: none;
}

@keyframes heroFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-10px) rotate(1deg); }
    50% { transform: translateY(-5px) rotate(-1deg); }
    75% { transform: translateY(-15px) rotate(0.5deg); }
}

.floating-element {
    position: absolute;
    animation: float 6s ease-in-out infinite;
    pointer-events: none;
    z-index: 1;
}

.floating-element:nth-child(2) { animation-delay: -2s; }
.floating-element:nth-child(3) { animation-delay: -4s; }

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-8px) rotate(5deg); }
    50% { transform: translateY(-12px) rotate(-3deg); }
    75% { transform: translateY(-6px) rotate(2deg); }
}

.hero-content {
    position: relative;
    z-index: 2;
}

.text-gradient {
    background: linear-gradient(135deg, #F0C24B, #D9A92F);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-stats {
    display: flex;
    gap: 1.5rem;
    flex-wrap: wrap;
}

.stat-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.75rem 1rem;
    background: rgba(255,255,255,0.1);
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.stat-badge:hover {
    transform: translateY(-2px);
    background: rgba(255,255,255,0.15);
    border-color: rgba(255,255,255,0.3);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #F0C24B;
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: rgba(255,255,255,0.8);
    margin-top: 0.25rem;
}

.hero-actions {
    position: relative;
    z-index: 2;
}

.btn-crystal {
    background: linear-gradient(135deg, rgba(240,194,75,0.9), rgba(217,169,47,0.9));
    color: #111216 !important;
    border: none;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(240,194,75,0.3);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.btn-crystal::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg,
        transparent,
        rgba(255,255,255,0.2),
        transparent);
    transition: left 0.6s ease;
}

.btn-crystal:hover::before {
    left: 100%;
}

.btn-crystal:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 35px rgba(240,194,75,0.4);
    background: linear-gradient(135deg, rgba(240,194,75,1), rgba(217,169,47,1));
}

/* Dark mode enhancements */
html[data-theme="dark"] .dashboard-hero {
    background: linear-gradient(135deg,
        rgba(255,255,255,0.05) 0%,
        rgba(255,255,255,0.02) 50%,
        rgba(255,255,255,0.01) 100%);
    border: 1px solid rgba(255,255,255,0.1);
}

html[data-theme="dark"] .stat-badge {
    background: rgba(255,255,255,0.05);
    border-color: rgba(255,255,255,0.1);
}

html[data-theme="dark"] .stat-badge:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.15);
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-hero {
        padding: 2rem !important;
    }
    
    .hero-stats {
        gap: 1rem;
    }
    
    .stat-badge {
        padding: 0.5rem 0.75rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .hero-actions {
        width: 100%;
        margin-top: 1rem;
    }
    
    .btn-crystal {
        width: 100%;
        text-align: center;
    }
}
</style>
