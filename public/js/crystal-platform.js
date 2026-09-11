/**
 * Crystal Platform JavaScript
 * Enhanced animations and interactions for Orchid admin platform
 */

(function() {
    'use strict';

    // Prevent multiple initializations
    if (window.__crystalPlatformBound) return;
    window.__crystalPlatformBound = true;

    // Platform configuration
    const config = {
        animationDuration: 300,
        animationEasing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        hoverDelay: 150,
        scrollThreshold: 0.1
    };

    /**
     * Initialize platform animations
     */
    function initPlatformAnimations() {
        // Add crystal classes to platform elements
        addCrystalClasses();
        
        // Initialize intersection observer for animations
        initIntersectionObserver();
        
        // Initialize hover effects
        initHoverEffects();
        
        // Initialize form enhancements
        initFormEnhancements();
        
        // Initialize table enhancements
        initTableEnhancements();
        
        // Initialize modal enhancements
        initModalEnhancements();
        
        // Initialize sidebar animations
        initSidebarAnimations();
        
        // Initialize loading states
        initLoadingStates();
        
        // Initialize theme transitions
        initThemeTransitions();
    }

    /**
     * Add crystal classes to platform elements
     */
    function addCrystalClasses() {
        // Add classes to main platform elements
        const platformElements = {
            '.orchid-main': 'orchid-main',
            '.orchid-sidebar': 'orchid-sidebar',
            '.orchid-header': 'orchid-header',
            '.orchid-content': 'orchid-content',
            '.card': 'orchid-card',
            '.btn': 'btn-crystal',
            '.form-control': 'crystal-form-control',
            '.table': 'crystal-table',
            '.modal-content': 'crystal-modal'
        };

        Object.entries(platformElements).forEach(([selector, className]) => {
            document.querySelectorAll(selector).forEach(element => {
                if (!element.classList.contains(className)) {
                    element.classList.add(className);
                }
            });
        });

        // Add hover classes
        document.querySelectorAll('.card, .btn, .nav-link').forEach(element => {
            element.classList.add('hover-lift');
        });
    }

    /**
     * Initialize intersection observer for animations
     */
    function initIntersectionObserver() {
        const observerOptions = {
            threshold: config.scrollThreshold,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    
                    // Add staggered animation to cards
                    if (entry.target.classList.contains('orchid-card')) {
                        const cards = entry.target.parentElement.querySelectorAll('.orchid-card');
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

        // Observe all platform elements
        document.querySelectorAll('.orchid-card, .orchid-page-header, .crystal-table').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Initialize hover effects
     */
    function initHoverEffects() {
        // Card hover effects
        document.querySelectorAll('.orchid-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
                this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = '0 8px 32px rgba(0,0,0,0.1)';
            });
        });

        // Button hover effects
        document.querySelectorAll('.btn-crystal').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px)';
                this.style.boxShadow = '0 12px 35px rgba(240,194,75,0.4)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 8px 25px rgba(240,194,75,0.3)';
            });
        });

        // Navigation link hover effects
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1.1) rotate(5deg)';
                }
            });
            
            link.addEventListener('mouseleave', function() {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1) rotate(0deg)';
                }
            });
        });
    }

    /**
     * Initialize form enhancements
     */
    function initFormEnhancements() {
        // Form control focus effects
        document.querySelectorAll('.form-control, .form-select').forEach(control => {
            control.addEventListener('focus', function() {
                this.style.borderColor = '#F0C24B';
                this.style.boxShadow = '0 0 0 0.2rem rgba(240,194,75,0.25)';
            });
            
            control.addEventListener('blur', function() {
                this.style.borderColor = '';
                this.style.boxShadow = '';
            });
        });

        // Form validation animations
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.style.animation = 'shake 0.5s ease-in-out';
        });
    }

    /**
     * Initialize table enhancements
     */
    function initTableEnhancements() {
        // Table row hover effects
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.background = 'rgba(255,255,255,0.15)';
                this.style.transform = 'scale(1.01)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.background = '';
                this.style.transform = 'scale(1)';
            });
        });

        // Table cell animations
        document.querySelectorAll('.table td').forEach(cell => {
            cell.addEventListener('click', function() {
                this.style.animation = 'pulse 0.3s ease-in-out';
                setTimeout(() => {
                    this.style.animation = '';
                }, 300);
            });
        });
    }

    /**
     * Initialize modal enhancements
     */
    function initModalEnhancements() {
        // Modal show animation
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('show.bs.modal', function() {
                const content = this.querySelector('.modal-content');
                content.style.transform = 'scale(0.8)';
                content.style.opacity = '0';
                
                setTimeout(() => {
                    content.style.transform = 'scale(1)';
                    content.style.opacity = '1';
                }, 50);
            });
        });

        // Modal hide animation
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('hide.bs.modal', function() {
                const content = this.querySelector('.modal-content');
                content.style.transform = 'scale(0.8)';
                content.style.opacity = '0';
            });
        });
    }

    /**
     * Initialize sidebar animations
     */
    function initSidebarAnimations() {
        // Sidebar menu item animations
        document.querySelectorAll('.orchid-sidebar .nav-link').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(5px)';
                this.style.background = 'rgba(255,255,255,0.15)';
            });
            
            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
                this.style.background = '';
            });
        });

        // Active menu item highlight
        document.querySelectorAll('.nav-link.active').forEach(link => {
            link.style.background = 'linear-gradient(135deg, #F0C24B, #D9A92F)';
            link.style.color = '#111216';
            link.style.boxShadow = '0 8px 25px rgba(240,194,75,0.3)';
        });
    }

    /**
     * Initialize loading states
     */
    function initLoadingStates() {
        // Show loading spinner for form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<div class="crystal-spinner"></div> جاري الإرسال...';
                    submitBtn.disabled = true;
                    
                    // Reset button after form submission (you may need to adjust this based on your form handling)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                }
            });
        });

        // Show loading for AJAX requests
        document.addEventListener('ajax:before', function() {
            showLoadingOverlay();
        });

        document.addEventListener('ajax:complete', function() {
            hideLoadingOverlay();
        });
    }

    /**
     * Initialize theme transitions
     */
    function initThemeTransitions() {
        // Theme toggle animation
        const themeToggle = document.querySelector('[data-theme-toggle]');
        if (themeToggle) {
            themeToggle.addEventListener('click', function() {
                document.body.style.transition = 'background 0.3s ease, color 0.3s ease';
                
                setTimeout(() => {
                    document.body.style.transition = '';
                }, 300);
            });
        }
    }

    /**
     * Show loading overlay
     */
    function showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'crystal-loading-overlay';
        overlay.innerHTML = `
            <div class="crystal-loading-content">
                <div class="crystal-spinner"></div>
                <div class="crystal-loading-text">جاري التحميل...</div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    /**
     * Hide loading overlay
     */
    function hideLoadingOverlay() {
        const overlay = document.querySelector('.crystal-loading-overlay');
        if (overlay) {
            overlay.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(overlay);
            }, 300);
        }
    }

    /**
     * Add CSS animations
     */
    function addCrystalAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            /* Shake animation for form validation */
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-5px); }
                75% { transform: translateX(5px); }
            }

            /* Pulse animation */
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }

            /* Crystal loading overlay */
            .crystal-loading-overlay {
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

            .crystal-loading-content {
                text-align: center;
                color: white;
            }

            .crystal-loading-text {
                margin-top: 1rem;
                font-size: 1.1rem;
                font-weight: 500;
            }

            /* Enhanced transitions */
            .orchid-card,
            .btn-crystal,
            .nav-link,
            .form-control,
            .table tbody tr {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Hover glow effect */
            .hover-glow:hover {
                box-shadow: 0 0 30px rgba(240,194,75,0.3);
            }

            /* Active state animations */
            .nav-link.active {
                animation: activePulse 2s infinite;
            }

            @keyframes activePulse {
                0%, 100% { box-shadow: 0 8px 25px rgba(240,194,75,0.3); }
                50% { box-shadow: 0 8px 25px rgba(240,194,75,0.5); }
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Initialize platform when DOM is ready
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPlatformAnimations);
        } else {
            initPlatformAnimations();
        }
        
        addCrystalAnimations();
    }

    // Initialize the platform
    init();

    // Export functions for external use
    window.CrystalPlatform = {
        showLoading: showLoadingOverlay,
        hideLoading: hideLoadingOverlay,
        addCrystalClass: function(element, className) {
            element.classList.add(className);
        },
        removeCrystalClass: function(element, className) {
            element.classList.remove(className);
        }
    };

})();
