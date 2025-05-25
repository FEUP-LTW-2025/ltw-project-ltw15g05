document.addEventListener('DOMContentLoaded', function() {
    const filterLinks = document.querySelectorAll('.dropdown-menu a, .dropdown-content a');
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            if (url === '#') return;
            
            const params = new URLSearchParams(url.split('?')[1] || '');
            updateActiveStates(this);
            fetchFilteredServices(params);
        });
    });
    function updateActiveStates(activeLink) {
        document.querySelectorAll('.dropdown-menu a, .dropdown-content a').forEach(link => {
            link.classList.remove('active');
        });
        
        document.querySelectorAll('.nav-categories > li > a, .filter-tab').forEach(link => {
            link.classList.remove('active');
        });
        
        activeLink.classList.add('active');
        
        const parentCategory = activeLink.closest('li.nav-category');
        if (parentCategory) {
            const categoryLink = parentCategory.querySelector('a');
            if (categoryLink) {
                categoryLink.classList.add('active');
            }
        }
        
        const filterDropdown = activeLink.closest('.filter-dropdown');
        if (filterDropdown) {
            const filterTab = filterDropdown.querySelector('.filter-tab');
            if (filterTab) {
                filterTab.classList.add('active');
            }
        }
    }
    
    function fetchFilteredServices(params) {
        const resultsContainer = document.getElementById('services');
        if (resultsContainer) {
            resultsContainer.style.opacity = 0.5;
            
            let loadingIndicator = document.getElementById('loading-indicator');
            if (!loadingIndicator) {
                loadingIndicator = document.createElement('div');
                loadingIndicator.id = 'loading-indicator';
                loadingIndicator.className = 'ajax-loading';
                resultsContainer.parentNode.insertBefore(loadingIndicator, resultsContainer);
            }
            
            fetch('ajax_filter_services.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        resultsContainer.innerHTML = data.html;
                    } else {
                        resultsContainer.innerHTML = '<div class="no-services">No services found matching your filters.</div>';
                    }
                    
                    const newUrl = 'main.php?' + params.toString();
                    window.history.pushState({}, '', newUrl);
                    
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                    resultsContainer.style.opacity = 1;
                })
                .catch(error => {
                    console.error('Error fetching filtered services:', error);
                    resultsContainer.innerHTML = '<div class="error">An error occurred while loading services. Please try again.</div>';
                    
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                    resultsContainer.style.opacity = 1;
                });
        }
    }
});
