var sideBarIsOpen = true;



    const toggleBtn = document.getElementById('toggleBtn');
    const dashboard_sidebar = document.getElementById('dashboard_sidebar');
    const dashboard_content_container = document.getElementById('dashboard_content_container');
    const dashboard_logo = document.getElementById('dashboard_logo');
    const adminImg = document.getElementById('AdminImg');

    toggleBtn.addEventListener('click', (event) => {
        event.preventDefault();

        if(sideBarIsOpen){
            dashboard_sidebar.style.width = '10%';
            dashboard_sidebar.style.transition = '0.5s all';
            dashboard_content_container.style.width = '90%';
            dashboard_logo.style.fontSize = '25px';
            adminImg.style.width = '60px';
            adminImg.style.display = 'block';
            adminImg.style.margin ='0 auto 0 0';

            menuIcons = document.getElementsByClassName('menuIcons');
            for(var i=0; i< menuIcons.length;i++) {
                menuIcons[i].style.display = 'none';
        }
            document.getElementsByClassName('dashboard_menu_list')[0].style.textAlign = 'center';
            sideBarIsOpen = false;
        } else {
            dashboard_sidebar.style.width = '20%';
            dashboard_content_container.style.width = '80%';
            dashboard_logo.style.fontSize = '40px';
            adminImg.style.width = '80px';
            adminImg.style.margin = '0 auto';



            menuIcons = document.getElementsByClassName('menuIcons');
            for(var i=0; i< menuIcons.length;i++) {
                menuIcons[i].style.display = 'inline-block';
        }
            document.getElementsByClassName('dashboard_menu_list')[0].style.textAlign = 'left';
            sideBarIsOpen = true;
        }

});


//Submenu/ hide function.
document.addEventListener('click', function(e){
    let clickedE1 = e.target;

    if(clickedE1.classList.contains('liMainMenu_link')) {
        alert('main menu');
    }

});

console.log(document.querySelectorAll('.liMainMenu_link'));

try {
  localStorage.setItem('user', 'admin');
} catch (error) {
  console.warn('Storage not allowed in this context:', error);
}
    $(document).ready(function () {
    // $('#myTable').DataTable({ ... }); // Removed to prevent double initialization. Each page should handle its own DataTable setup.
  });

const sidebar = document.querySelector('.dashboard_sidebar');
if (sidebar && sidebar.classList.contains('is-collapsed')) {
    sidebar.classList.remove('is-collapsed');
}

document.addEventListener('DOMContentLoaded', function() {
    const formsToValidate = document.querySelectorAll('form.appForm');

    formsToValidate.forEach(form => {
        const submitBtn = form.querySelector('button.SubmitBtn');
        const originalBtnText = submitBtn ? submitBtn.innerHTML : '';

        const showError = (input, message) => {
            input.classList.add('input-error');
            const errorContainer = input.parentElement.querySelector('.error-message-inline');
            if (errorContainer) {
                errorContainer.textContent = message;
                errorContainer.style.display = 'block';
            }
        };

        const clearError = (input) => {
            input.classList.remove('input-error');
            const errorContainer = input.parentElement.querySelector('.error-message-inline');
            if (errorContainer) {
                errorContainer.textContent = '';
                errorContainer.style.display = 'none';
            }
        };

        form.querySelectorAll('input[required], textarea[required], select[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (input.value.trim() === '') {
                    showError(input, `${input.labels[0].textContent} is required.`);
                } else {
                    clearError(input);
                }
            });
             input.addEventListener('input', function() {
                clearError(input);
            });
        });

        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            form.querySelectorAll('.input-error').forEach(input => clearError(input));

            form.querySelectorAll('input[required], textarea[required], select[required]').forEach(input => {
                if (input.value.trim() === '') {
                    isValid = false;
                    showError(input, `${input.labels[0].textContent} is required.`);
                }
            });

            if (!isValid) {
                e.preventDefault();
            } else {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
                }
            }
        });
    });
});

// Live AJAX update for dashboard KPIs and charts
setInterval(function() {
    fetch('dashboard_data.php')
        .then(response => response.json())
        .then(data => {
            // Update KPI cards
            document.getElementById('kpi_out_of_stock').innerText = data.kpi_out_of_stock;
            document.getElementById('kpi_pending_approvals').innerText = data.kpi_pending_approvals;
            document.getElementById('kpi_total_products').innerText = data.kpi_total_products;
            document.getElementById('kpi_total_users').innerText = data.kpi_total_users;
            document.getElementById('kpi_total_suppliers').innerText = data.kpi_total_suppliers;
            // Update charts (example for Highcharts)
            if(window.lowStockChart && data.lowStockLabels && data.lowStockData) {
                lowStockChart.xAxis[0].setCategories(data.lowStockLabels);
                lowStockChart.series[0].setData(data.lowStockData);
            }
            // Repeat for other charts as needed
        });
}, 60000);

// Expandable Text Functionality
function initializeExpandableText() {
    // Find all elements with expandable-text class
    const expandableElements = document.querySelectorAll('.expandable-text');
    
    expandableElements.forEach(element => {
        // Only make expandable if text is actually truncated
        if (element.scrollWidth > element.clientWidth) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle expanded state
                this.classList.toggle('expanded');
                
                // Update tooltip
                if (this.classList.contains('expanded')) {
                    this.title = 'Click to collapse';
                } else {
                    this.title = 'Click to expand';
                }
            });
            
            // Add initial tooltip
            element.title = 'Click to expand';
        }
    });
}

// Initialize expandable text when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeExpandableText();
});

// Re-initialize after DataTables are loaded (for dynamic content)
function reinitializeExpandableText() {
    setTimeout(initializeExpandableText, 100);
}

// Make function globally available for DataTables callbacks
window.reinitializeExpandableText = reinitializeExpandableText;