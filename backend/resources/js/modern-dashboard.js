/**
 * Modern Dashboard JavaScript Helpers
 * Enhanced JavaScript functionality for modern dashboard styling
 */

class ModernDashboard {
    constructor() {
        this.init();
    }

    init() {
        this.setupSidebar();
        this.setupAnimations();
        this.setupThemeSwitching();
        this.setupSmoothScrolling();
        this.setupTooltips();
        this.setupLoadingStates();
        this.setupFormEnhancements();
        this.setupTableEnhancements();
        this.setupCardInteractions();
    }

    /**
     * Setup sidebar functionality
     */
    setupSidebar() {
        const sidebar = document.querySelector('.main-sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const contentWrapper = document.querySelector('.content-wrapper');
        const mainHeader = document.querySelector('.main-header');

        if (!sidebar) return;

        // Toggle sidebar
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Close sidebar when clicking overlay
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', () => {
                this.closeSidebar();
            });
        }

        // Handle submenu toggles
        const navItems = sidebar.querySelectorAll('.nav-item.has-treeview');
        navItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            if (link) {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleSubmenu(item);
                });
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    }

    /**
     * Toggle sidebar visibility
     */
    toggleSidebar() {
        const sidebar = document.querySelector('.main-sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const body = document.body;

        if (window.innerWidth <= 768) {
            // Mobile behavior
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            body.classList.toggle('sidebar-open');
        } else {
            // Desktop behavior
            body.classList.toggle('sidebar-collapse');
        }
    }

    /**
     * Close sidebar
     */
    closeSidebar() {
        const sidebar = document.querySelector('.main-sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const body = document.body;

        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        body.classList.remove('sidebar-open');
    }

    /**
     * Toggle submenu
     */
    toggleSubmenu(item) {
        const submenu = item.querySelector('.nav-treeview');
        const link = item.querySelector('.nav-link');
        
        if (submenu) {
            submenu.classList.toggle('menu-open');
            link.classList.toggle('has-submenu');
        }
    }

    /**
     * Handle window resize
     */
    handleResize() {
        const body = document.body;
        
        if (window.innerWidth > 768) {
            body.classList.remove('sidebar-open');
            document.querySelector('.sidebar-overlay')?.classList.remove('show');
        }
    }

    /**
     * Setup smooth animations
     */
    setupAnimations() {
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe elements with animation classes
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Add hover effects to cards
        document.querySelectorAll('.modern-card, .stat-widget').forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.classList.add('hover-lift');
            });

            card.addEventListener('mouseleave', () => {
                card.classList.remove('hover-lift');
            });
        });
    }

    /**
     * Setup theme switching
     */
    setupThemeSwitching() {
        const themeToggle = document.querySelector('.theme-toggle');
        
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                this.toggleTheme();
            });
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('dashboard-theme');
        if (savedTheme) {
            document.body.classList.add(savedTheme);
        }
    }

    /**
     * Toggle theme
     */
    toggleTheme() {
        const body = document.body;
        const currentTheme = body.classList.contains('dark-theme') ? 'dark-theme' : 'light-theme';
        const newTheme = currentTheme === 'dark-theme' ? 'light-theme' : 'dark-theme';
        
        body.classList.remove(currentTheme);
        body.classList.add(newTheme);
        
        localStorage.setItem('dashboard-theme', newTheme);
    }

    /**
     * Setup smooth scrolling
     */
    setupSmoothScrolling() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Setup tooltips
     */
    setupTooltips() {
        const tooltipElements = document.querySelectorAll('[data-tooltip]');
        
        tooltipElements.forEach(element => {
            element.addEventListener('mouseenter', (e) => {
                this.showTooltip(e.target, e.target.dataset.tooltip);
            });

            element.addEventListener('mouseleave', () => {
                this.hideTooltip();
            });
        });
    }

    /**
     * Show tooltip
     */
    showTooltip(element, text) {
        const tooltip = document.createElement('div');
        tooltip.className = 'modern-tooltip';
        tooltip.textContent = text;
        document.body.appendChild(tooltip);

        const rect = element.getBoundingClientRect();
        tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
        tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';

        setTimeout(() => tooltip.classList.add('show'), 10);
    }

    /**
     * Hide tooltip
     */
    hideTooltip() {
        const tooltip = document.querySelector('.modern-tooltip');
        if (tooltip) {
            tooltip.classList.remove('show');
            setTimeout(() => tooltip.remove(), 200);
        }
    }

    /**
     * Setup loading states
     */
    setupLoadingStates() {
        // Add loading state to buttons
        document.querySelectorAll('.modern-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                if (button.type === 'submit' || button.classList.contains('btn-loading')) {
                    this.setButtonLoading(button, true);
                    
                    // Simulate loading (remove in production)
                    setTimeout(() => {
                        this.setButtonLoading(button, false);
                    }, 2000);
                }
            });
        });
    }

    /**
     * Set button loading state
     */
    setButtonLoading(button, loading) {
        if (loading) {
            button.classList.add('modern-btn--loading');
            button.disabled = true;
        } else {
            button.classList.remove('modern-btn--loading');
            button.disabled = false;
        }
    }

    /**
     * Setup form enhancements
     */
    setupFormEnhancements() {
        // Add floating labels
        document.querySelectorAll('.form-control').forEach(input => {
            this.setupFloatingLabel(input);
        });

        // Add form validation styling
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.validateForm(form);
            });
        });
    }

    /**
     * Setup floating label
     */
    setupFloatingLabel(input) {
        const label = input.previousElementSibling;
        if (!label || !label.classList.contains('form-label')) return;

        const updateLabel = () => {
            if (input.value || input === document.activeElement) {
                label.classList.add('floating');
            } else {
                label.classList.remove('floating');
            }
        };

        input.addEventListener('focus', updateLabel);
        input.addEventListener('blur', updateLabel);
        input.addEventListener('input', updateLabel);
        
        // Initial state
        updateLabel();
    }

    /**
     * Validate form
     */
    validateForm(form) {
        const inputs = form.querySelectorAll('.form-control');
        let isValid = true;

        inputs.forEach(input => {
            const isValidInput = this.validateInput(input);
            if (!isValidInput) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Validate input
     */
    validateInput(input) {
        const value = input.value.trim();
        const required = input.hasAttribute('required');
        const type = input.type;
        let isValid = true;

        // Remove existing validation classes
        input.classList.remove('is-valid', 'is-invalid');

        if (required && !value) {
            isValid = false;
        } else if (type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
        } else if (type === 'password' && value && value.length < 8) {
            isValid = false;
        }

        // Add validation class
        input.classList.add(isValid ? 'is-valid' : 'is-invalid');

        return isValid;
    }

    /**
     * Validate email
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Setup table enhancements
     */
    setupTableEnhancements() {
        // Add sorting functionality
        document.querySelectorAll('.modern-table th.sortable').forEach(th => {
            th.addEventListener('click', () => {
                this.sortTable(th);
            });
        });

        // Add row selection
        document.querySelectorAll('.modern-table tbody tr').forEach(row => {
            row.addEventListener('click', (e) => {
                if (!e.target.closest('button, a')) {
                    this.toggleRowSelection(row);
                }
            });
        });
    }

    /**
     * Sort table
     */
    sortTable(th) {
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        const columnIndex = Array.from(th.parentNode.children).indexOf(th);
        const isAscending = !th.classList.contains('sort-asc');

        // Remove existing sort classes
        table.querySelectorAll('th').forEach(header => {
            header.classList.remove('sort-asc', 'sort-desc');
        });

        // Add sort class
        th.classList.add(isAscending ? 'sort-asc' : 'sort-desc');

        // Sort rows
        rows.sort((a, b) => {
            const aValue = a.children[columnIndex].textContent.trim();
            const bValue = b.children[columnIndex].textContent.trim();
            
            if (isAscending) {
                return aValue.localeCompare(bValue);
            } else {
                return bValue.localeCompare(aValue);
            }
        });

        // Reorder rows
        rows.forEach(row => tbody.appendChild(row));
    }

    /**
     * Toggle row selection
     */
    toggleRowSelection(row) {
        row.classList.toggle('selected');
    }

    /**
     * Setup card interactions
     */
    setupCardInteractions() {
        // Add click handlers for clickable cards
        document.querySelectorAll('.modern-card.clickable').forEach(card => {
            card.addEventListener('click', () => {
                const link = card.querySelector('a');
                if (link) {
                    link.click();
                }
            });
        });

        // Add hover effects for stat widgets
        document.querySelectorAll('.stat-widget').forEach(widget => {
            widget.addEventListener('mouseenter', () => {
                widget.classList.add('hover-glow');
            });

            widget.addEventListener('mouseleave', () => {
                widget.classList.remove('hover-glow');
            });
        });
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `modern-notification modern-notification--${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => notification.classList.add('show'), 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 200);
        }, duration);
    }

    /**
     * Show modal
     */
    showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
    }

    /**
     * Hide modal
     */
    hideModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
    }

    /**
     * Update stats with animation
     */
    animateStats() {
        document.querySelectorAll('.stat-number').forEach(element => {
            const finalValue = parseInt(element.textContent);
            const duration = 1000;
            const startTime = performance.now();

            const animate = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const currentValue = Math.floor(progress * finalValue);
                
                element.textContent = currentValue;
                
                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            };

            requestAnimationFrame(animate);
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.modernDashboard = new ModernDashboard();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ModernDashboard;
}
