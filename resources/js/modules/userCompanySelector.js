/**
 * userCompanySelector.js
 *
 * Handles client-side interactivity for the admin user create/edit forms.
 * - Filters companies checkbox list in real-time as the admin searches.
 * - Shows/hides the company selector section based on selected user role.
 */

export function initUserCompanySelector() {
    document.addEventListener('DOMContentLoaded', function () {
        const initEl = document.getElementById('user-company-selector-init');
        if (!initEl) return;

        const roleSelect = document.getElementById('user-role-select');
        const companySection = document.getElementById('company-selector-section');
        const searchInput = document.getElementById('company-search-input');
        const checkboxItems = document.querySelectorAll('.company-checkbox-item');

        // Toggle visibility based on role
        function toggleCompanySection() {
            if (!roleSelect || !companySection) return;
            if (roleSelect.value === 'admin') {
                companySection.style.setProperty('display', 'none', 'important');
            } else {
                companySection.style.setProperty('display', 'block', 'important');
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', toggleCompanySection);
            toggleCompanySection(); // Run initially
        }

        // Live filtering of companies
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.toLowerCase().trim();

                checkboxItems.forEach(item => {
                    const companyName = item.getAttribute('data-company-name') || '';
                    if (companyName.includes(query)) {
                        item.style.setProperty('display', 'block', 'important');
                    } else {
                        item.style.setProperty('display', 'none', 'important');
                    }
                });
            });
        }
    });
}
