<script>
// Dashboard Animations and Interactions
document.addEventListener('DOMContentLoaded', function() {
    // Prevent multiple initializations
    if (window.__dashboardBound) return;
    window.__dashboardBound = true;

    // Intersection Observer for reveal animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');

                // Add staggered animation to cards
                if (entry.target.classList.contains('dashboard-cards')) {
                    const cards = entry.target.querySelectorAll('.dashboard-card');
                    cards.forEach((card, index) => {
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, index * 100);
                    });
                }
            }
        });
    }, observerOptions);

    // Observe all reveal elements
    document.querySelectorAll('.reveal').forEach(el => {
        observer.observe(el);
    });

    // Initialize cards with opacity 0
    document.querySelectorAll('.dashboard-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });

    // Chart bar animations
    const chartBars = document.querySelectorAll('.chart-bar-wrapper');
    chartBars.forEach((bar, index) => {
        setTimeout(() => {
            bar.style.opacity = '1';
            bar.style.transform = 'translateY(0)';
        }, 500 + (index * 100));
    });

    // Initialize chart bars
    chartBars.forEach(bar => {
        bar.style.opacity = '0';
        bar.style.transform = 'translateY(20px)';
        bar.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    });

    // Parallax effect for hero section
    const hero = document.querySelector('.dashboard-hero');
    if (hero) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            hero.style.transform = `translateY(${rate}px)`;
        });
    }

    // Floating elements animation
    const floatingElements = document.querySelectorAll('.floating-element');
    floatingElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 2}s`;
    });

    // Card hover effects with sound simulation
    document.querySelectorAll('.dashboard-card, .action-item').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = this.style.transform.replace('scale(1.02)', 'scale(1.03)');
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = this.style.transform.replace('scale(1.03)', 'scale(1.02)');
        });
    });

    // Chart bar hover effects
    document.querySelectorAll('.chart-bar-wrapper').forEach(bar => {
        bar.addEventListener('mouseenter', function() {
            const barFill = this.querySelector('.bar-fill');
            if (barFill) {
                barFill.style.filter = 'brightness(1.2)';
            }
        });

        bar.addEventListener('mouseleave', function() {
            const barFill = this.querySelector('.bar-fill');
            if (barFill) {
                barFill.style.filter = 'brightness(1)';
            }
        });
    });

    // Quick action hover effects
    document.querySelectorAll('.action-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });

        item.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Add pulse animation to important elements
    const pulseElements = document.querySelectorAll('.card-number, .stat-value');
    pulseElements.forEach(element => {
        element.style.animation = 'pulse 2s infinite';
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading animation
    const loadingAnimation = () => {
        const loader = document.createElement('div');
        loader.className = 'dashboard-loader';
        loader.innerHTML = `
            <div class="loader-content">
                <div class="loader-spinner"></div>
                <div class="loader-text">جاري تحميل البيانات...</div>
            </div>
        `;
        document.body.appendChild(loader);

        // Remove loader after content loads
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(loader);
            }, 300);
        }, 1000);
    };

    // Run loading animation on page load
    loadingAnimation();
});

// Add CSS for loader
const loaderStyles = `
<style>
.dashboard-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: opacity 0.3s ease;
}

.loader-content {
    text-align: center;
    color: white;
}

.loader-spinner {
    width: 50px;
    height: 50px;
    border: 3px solid rgba(255,255,255,0.3);
    border-top: 3px solid #F0C24B;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

.loader-text {
    font-size: 1.1rem;
    font-weight: 500;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Enhanced hover effects */
.dashboard-card:hover .card-icon {
    animation: bounce 0.6s ease;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
    40%, 43% { transform: translate3d(0,-8px,0); }
    70% { transform: translate3d(0,-4px,0); }
    90% { transform: translate3d(0,-2px,0); }
}

/* Chart bar growth animation */
.bar-fill {
    animation: barGrow 1.5s ease-out forwards;
    transform-origin: bottom;
}

@keyframes barGrow {
    from { transform: scaleY(0); }
    to { transform: scaleY(1); }
}

/* Floating animation for hero elements */
.floating-element {
    animation: float 6s ease-in-out infinite;
}

.floating-element:nth-child(2) { animation-delay: -2s; }
.floating-element:nth-child(3) { animation-delay: -4s; }

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-8px) rotate(5deg); }
    50% { transform: translateY(-12px) rotate(-3deg); }
    75% { transform: translateY(-6px) rotate(2deg); }
}
</style>
`;

// Inject loader styles
document.head.insertAdjacentHTML('beforeend', loaderStyles);
</script>
