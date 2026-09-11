/**
 * Navbar Search with Autocomplete
 * Handles search input, suggestions, and navigation
 */
(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        minQueryLength: 2,
        debounceDelay: 300,
        suggestionsUrl: '/api/search/suggestions',
        searchUrl: '/products',
        maxResultsPerSection: 5
    };

    // State
    let searchTimeout = null;
    let currentSuggestions = [];
    let selectedIndex = -1;

    // DOM Elements
    const searchInput = document.getElementById('navbarSearchInput');
    const searchForm = document.getElementById('navbarSearchForm');
    const suggestionsContainer = document.getElementById('navbarSearchSuggestions');
    const suggestionsResults = document.getElementById('suggestionsResults');
    let suggestionsLoading = null;
    const i18n = {
        products: suggestionsContainer?.dataset?.labelProducts || 'Products',
        categories: suggestionsContainer?.dataset?.labelCategories || 'Categories',
        brands: suggestionsContainer?.dataset?.labelBrands || 'Brands',
        results: suggestionsContainer?.dataset?.labelResults || 'results',
        popularSearches: suggestionsContainer?.dataset?.labelPopularSearches || 'Popular Searches',
    };

    if (!searchInput || !suggestionsContainer || !suggestionsResults) {
        return; // Exit if required elements don't exist
    }

    suggestionsLoading = suggestionsContainer.querySelector('.suggestions-loading');

    /**
     * Debounce function to limit API calls
     */
    function debounce(func, wait) {
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(searchTimeout);
                func(...args);
            };
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(later, wait);
        };
    }

    /**
     * Highlight matching text in suggestion
     */
    function highlightText(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    /**
     * Get icon for suggestion type
     */
    function getTypeIcon(type) {
        const icons = {
            product: '<i class="fas fa-box"></i>',
            category: '<i class="fas fa-folder"></i>',
            brand: '<i class="fas fa-tag"></i>'
        };
        return icons[type] || '<i class="fas fa-search"></i>';
    }

    /**
     * Render suggestion item - Amazon/AliExpress Style
     */
    function renderSuggestion(suggestion, index) {
        const highlightedText = highlightText(suggestion.text, searchInput.value);
        const isSelected = index === selectedIndex ? 'selected' : '';
        
        let html = `
            <a href="${suggestion.url}" class="suggestion-item search-suggestion-item ${isSelected}" data-index="${index}" data-type="${suggestion.type}">
                <img src="${suggestion.image || '/images/placeholder-product.png'}" 
                     alt="${suggestion.text}" 
                     class="suggestion-image search-suggestion-thumbnail"
                     data-fallback="/images/placeholder-product.png">
                <div class="suggestion-content">
                    <div class="suggestion-title">
                        <span class="suggestion-type-badge ${suggestion.type}">${suggestion.type}</span>
                        <span>${highlightedText}</span>
                    </div>
        `;

        if (suggestion.description) {
            html += `<div class="suggestion-description">${suggestion.description}</div>`;
        }

        if (suggestion.price) {
            html += `<div class="suggestion-price"><span class="currency">$</span>${parseFloat(suggestion.price).toFixed(2)}</div>`;
        }

        html += `
                </div>
            </a>
        `;

        return html;
    }

    /**
     * Render all suggestions - Amazon/AliExpress Style with Sections
     */
    function renderSuggestions(suggestions) {
        if (!suggestions || suggestions.length === 0) {
            renderTrendingSearches();
            return;
        }

        currentSuggestions = suggestions;
        
        // Group suggestions by type
        const grouped = {
            product: [],
            category: [],
            brand: []
        };

        suggestions.forEach(s => {
            if (grouped[s.type]) {
                grouped[s.type].push(s);
            }
        });

        let html = '';
        let globalIndex = 0;

        // Render Products Section
        if (grouped.product.length > 0) {
            const products = grouped.product.slice(0, CONFIG.maxResultsPerSection);
            html += `
                <div class="mega-dropdown-section">
                    <div class="mega-dropdown-section-header">
                        <span>${i18n.products}</span>
                        <span class="mega-dropdown-section-count">${grouped.product.length} ${i18n.results}</span>
                    </div>
                    <div class="mega-dropdown-section-content">
            `;
            products.forEach((suggestion, idx) => {
                html += renderSuggestion(suggestion, globalIndex++);
            });
            html += `
                    </div>
                </div>
            `;
        }

        // Render Categories Section
        if (grouped.category.length > 0) {
            const categories = grouped.category.slice(0, CONFIG.maxResultsPerSection);
            html += `
                <div class="mega-dropdown-section">
                    <div class="mega-dropdown-section-header">
                        <span>${i18n.categories}</span>
                        <span class="mega-dropdown-section-count">${grouped.category.length} ${i18n.results}</span>
                    </div>
                    <div class="mega-dropdown-section-content">
            `;
            categories.forEach((suggestion, idx) => {
                html += renderSuggestion(suggestion, globalIndex++);
            });
            html += `
                    </div>
                </div>
            `;
        }

        // Render Brands Section
        if (grouped.brand.length > 0) {
            const brands = grouped.brand.slice(0, CONFIG.maxResultsPerSection);
            html += `
                <div class="mega-dropdown-section">
                    <div class="mega-dropdown-section-header">
                        <span>${i18n.brands}</span>
                        <span class="mega-dropdown-section-count">${grouped.brand.length} ${i18n.results}</span>
                    </div>
                    <div class="mega-dropdown-section-content">
            `;
            brands.forEach((suggestion, idx) => {
                html += renderSuggestion(suggestion, globalIndex++);
            });
            html += `
                    </div>
                </div>
            `;
        }

        suggestionsResults.innerHTML = html;

        // Show footer when there are results
        const footer = document.querySelector('.mega-dropdown-footer');
        if (footer) {
            footer.style.display = 'block';
        }

        // Update View All Results button
        const viewAllBtn = document.getElementById('viewAllResultsBtn');
        if (viewAllBtn) {
            const query = searchInput.value.trim();
            const category = document.getElementById('searchCategorySelect')?.value || '';
            let url = `${CONFIG.searchUrl}?q=${encodeURIComponent(query)}`;
            if (category) {
                url += `&category=${encodeURIComponent(category)}`;
            }
            viewAllBtn.href = url;
        }

        // Add click handlers
        suggestionsResults.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url) {
                    window.location.href = url;
                }
            });
        });
    }

    /**
     * Render Trending Searches when no input - Matches Image Design
     */
    function renderTrendingSearches() {
        const trendingSearches = [
            suggestionsContainer?.dataset?.trending1 || 'Featured Products',
            suggestionsContainer?.dataset?.trending2 || 'New Arrivals',
            suggestionsContainer?.dataset?.trending3 || 'On Sale',
            suggestionsContainer?.dataset?.trending4 || 'Best Sellers',
            suggestionsContainer?.dataset?.trending5 || 'Top Rated'
        ];

        let html = `
            <div class="trending-searches">
                <div class="trending-searches-title">${i18n.popularSearches}</div>
                <div class="trending-searches-list">
        `;

        trendingSearches.forEach((term, index) => {
            // Add selected class to first and last items to match image (Featured Products and Top Rated)
            const selectedClass = (index === 0 || index === trendingSearches.length - 1) ? 'selected' : '';
            html += `
                <a href="${CONFIG.searchUrl}?q=${encodeURIComponent(term)}" class="trending-search-item ${selectedClass}" data-index="${index}">
                    ${term}
                </a>
            `;
        });

        html += `
                </div>
            </div>
        `;

        suggestionsResults.innerHTML = html;
        
        // Hide footer when showing trending searches
        const footer = document.querySelector('.mega-dropdown-footer');
        if (footer) {
            footer.style.display = 'none';
        }
    }

    /**
     * Fetch search suggestions from API
     */
    async function fetchSuggestions(query) {
        if (query.length < CONFIG.minQueryLength) {
            hideSuggestions();
            return;
        }

        showLoading();

        try {
            const response = await fetch(`${CONFIG.suggestionsUrl}?q=${encodeURIComponent(query)}`);
            if (!response.ok) {
                throw new Error('Failed to fetch suggestions');
            }
            
            const suggestions = await response.json();
            hideLoading();
            renderSuggestions(suggestions);
            showSuggestions();
        } catch (error) {
            console.error('Error fetching suggestions:', error);
            hideLoading();
            hideSuggestions();
        }
    }

    /**
     * Show/hide suggestions dropdown
     */
    function showSuggestions() {
        if (suggestionsContainer) {
            suggestionsContainer.style.display = 'block';
            
            // On mobile, position dropdown below search input
            if (window.innerWidth <= 991.98) {
                const searchInput = document.getElementById('navbarSearchInput');
                const searchContainer = document.querySelector('.navbar-search-container');
                if (searchInput && searchContainer) {
                    const inputRect = searchInput.getBoundingClientRect();
                    const dropdown = suggestionsContainer;
                    
                    // Position dropdown below input
                    dropdown.style.top = (inputRect.bottom + window.scrollY + 8) + 'px';
                    dropdown.style.left = '0.75rem';
                    dropdown.style.right = '0.75rem';
                    dropdown.style.width = 'calc(100vw - 1.5rem)';
                    dropdown.style.maxWidth = 'calc(100vw - 1.5rem)';
                }
            }
        }
    }

    function hideSuggestions() {
        if (suggestionsContainer) {
            suggestionsContainer.style.display = 'none';
        }
        selectedIndex = -1;
    }

    function showLoading() {
        if (suggestionsLoading) {
            suggestionsLoading.style.display = 'block';
        }
        if (suggestionsResults) {
            suggestionsResults.style.display = 'none';
        }
    }

    function hideLoading() {
        if (suggestionsLoading) {
            suggestionsLoading.style.display = 'none';
        }
        if (suggestionsResults) {
            suggestionsResults.style.display = 'block';
        }
    }

    /**
     * Handle input events
     */
    const handleInput = debounce(function() {
        const query = searchInput.value.trim();
        if (query.length >= CONFIG.minQueryLength) {
            fetchSuggestions(query);
        } else if (query.length === 0) {
            renderTrendingSearches();
            showSuggestions();
        } else {
            hideSuggestions();
        }
    }, CONFIG.debounceDelay);

    /**
     * Handle keyboard navigation
     */
    function handleKeyDown(e) {
        if (!suggestionsContainer || suggestionsContainer.style.display === 'none') {
            return;
        }

        const items = suggestionsResults.querySelectorAll('.suggestion-item');
        if (items.length === 0) {
            // Allow Enter to submit form even if no suggestions
            if (e.key === 'Enter') {
                searchForm.submit();
            }
            return;
        }

        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelection(items);
                break;
            case 'ArrowUp':
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelection(items);
                break;
            case 'Enter':
                e.preventDefault();
                if (selectedIndex >= 0 && currentSuggestions[selectedIndex]) {
                    window.location.href = currentSuggestions[selectedIndex].url;
                } else {
                    searchForm.submit();
                }
                break;
            case 'Escape':
                hideSuggestions();
                searchInput.blur();
                break;
        }
    }

    /**
     * Update visual selection
     */
    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('selected');
                item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            } else {
                item.classList.remove('selected');
            }
        });
    }

    /**
     * Initialize event listeners
     */
    function init() {
        // Input event
        searchInput.addEventListener('input', handleInput);

        // Keyboard events
        searchInput.addEventListener('keydown', handleKeyDown);

        // Focus events
        searchInput.addEventListener('focus', function() {
            const query = this.value.trim();
            if (query.length >= CONFIG.minQueryLength) {
                fetchSuggestions(query);
            } else {
                renderTrendingSearches();
                showSuggestions();
            }
        });

        // Click outside to close
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && 
                !suggestionsContainer.contains(e.target)) {
                hideSuggestions();
            }
        });

        // Form submit
        searchForm.addEventListener('submit', function(e) {
            const query = searchInput.value.trim();
            if (query.length < CONFIG.minQueryLength) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

/**
 * Premium navbar interactions (mega menu + floating state)
 */
(function () {
    'use strict';

    const nav = document.querySelector('.premium-nav-shell');
    const categoriesItem = document.querySelector('.premium-categories-item');
    const trigger = document.querySelector('[data-mega-toggle="categories"]');

    function setOpen(isOpen) {
        if (!categoriesItem || !trigger) return;
        categoriesItem.classList.toggle('is-open', isOpen);
        trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    if (categoriesItem && trigger) {
        let closeTimer = null;

        const scheduleClose = () => {
            clearTimeout(closeTimer);
            closeTimer = setTimeout(() => setOpen(false), 100);
        };

        const cancelClose = () => clearTimeout(closeTimer);

        trigger.addEventListener('mouseenter', () => {
            if (window.innerWidth >= 992) {
                cancelClose();
                setOpen(true);
            }
        });

        categoriesItem.addEventListener('mouseenter', cancelClose);
        categoriesItem.addEventListener('mouseleave', () => {
            if (window.innerWidth >= 992) scheduleClose();
        });

        trigger.addEventListener('click', (event) => {
            if (window.innerWidth < 992) {
                event.preventDefault();
                setOpen(!categoriesItem.classList.contains('is-open'));
                return;
            }

            // Desktop click keeps navigation if user wants the categories page.
            const targetHref = trigger.getAttribute('href');
            if (!categoriesItem.classList.contains('is-open') && targetHref) {
                event.preventDefault();
                setOpen(true);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setOpen(false);
            }
        });

        document.addEventListener('click', (event) => {
            if (!categoriesItem.contains(event.target)) {
                setOpen(false);
            }
        });
    }

    if (nav) {
        let ticking = false;
        const onScroll = () => {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(() => {
                nav.classList.toggle('is-floating', (window.scrollY || window.pageYOffset || 0) > 24);
                ticking = false;
            });
        };

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }
})();
