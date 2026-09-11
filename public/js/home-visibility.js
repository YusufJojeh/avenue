/**
 * Home Visibility Controller - JavaScript API
 * Handles dynamic section visibility management
 */

class HomeVisibilityController {
    constructor() {
        this.baseUrl = '/api/home';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * Get all settings
     */
    async getAllSettings() {
        try {
            const response = await fetch(`${this.baseUrl}/settings`);
            return await response.json();
        } catch (error) {
            console.error('Error fetching settings:', error);
            return null;
        }
    }

    /**
     * Update section visibility
     */
    async updateVisibility(section, visible) {
        try {
            const response = await fetch(`${this.baseUrl}/visibility/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({ section, visible })
            });
            return await response.json();
        } catch (error) {
            console.error('Error updating visibility:', error);
            return { success: false, message: 'Network error' };
        }
    }

    /**
     * Update section limits
     */
    async updateLimits(section, limit) {
        try {
            const response = await fetch(`${this.baseUrl}/limits/update`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({ section, limit })
            });
            return await response.json();
        } catch (error) {
            console.error('Error updating limits:', error);
            return { success: false, message: 'Network error' };
        }
    }

    /**
     * Toggle section visibility
     */
    async toggleVisibility(section) {
        try {
            const response = await fetch(`${this.baseUrl}/visibility/toggle/${section}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                }
            });
            return await response.json();
        } catch (error) {
            console.error('Error toggling visibility:', error);
            return { success: false, message: 'Network error' };
        }
    }

    /**
     * Bulk update visibility
     */
    async bulkUpdateVisibility(sections, visible) {
        try {
            const response = await fetch(`${this.baseUrl}/visibility/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({ sections, visible })
            });
            return await response.json();
        } catch (error) {
            console.error('Error bulk updating visibility:', error);
            return { success: false, message: 'Network error' };
        }
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // You can integrate with your preferred notification library
        if (typeof Toast !== 'undefined') {
            Toast[type](message);
        } else {
            alert(message);
        }
    }

    /**
     * Refresh page after settings change
     */
    refreshPage() {
        window.location.reload();
    }

    /**
     * Initialize visibility controls
     */
    init() {
        // Add event listeners for visibility toggles if they exist
        document.querySelectorAll('[data-visibility-toggle]').forEach(toggle => {
            toggle.addEventListener('change', async (e) => {
                const section = e.target.dataset.section;
                const visible = e.target.checked;
                
                const result = await this.updateVisibility(section, visible);
                if (result.success) {
                    this.showNotification(result.message, 'success');
                    // Optionally refresh the page to show/hide sections
                    setTimeout(() => this.refreshPage(), 1000);
                } else {
                    this.showNotification(result.message, 'error');
                    // Revert the toggle
                    e.target.checked = !visible;
                }
            });
        });

        // Add event listeners for limit inputs if they exist
        document.querySelectorAll('[data-limit-input]').forEach(input => {
            input.addEventListener('change', async (e) => {
                const section = e.target.dataset.section;
                const limit = parseInt(e.target.value);
                
                if (limit < 1 || limit > 50) {
                    this.showNotification('Limit must be between 1 and 50', 'error');
                    return;
                }
                
                const result = await this.updateLimits(section, limit);
                if (result.success) {
                    this.showNotification(result.message, 'success');
                } else {
                    this.showNotification(result.message, 'error');
                }
            });
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.homeVisibilityController = new HomeVisibilityController();
    window.homeVisibilityController.init();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HomeVisibilityController;
}
