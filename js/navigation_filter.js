document.addEventListener('DOMContentLoaded', function() {
    // Get all dropdown links (both from main nav and filter nav)
    const filterLinks = document.querySelectorAll('.dropdown-menu a, .dropdown-content a');
    
    // Add click event listeners to all filter links
    filterLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            // Skip if it's just a # link (placeholder)
            if (url === '#') return;
            
            // Extract the query parameters
            const params = new URLSearchParams(url.split('?')[1] || '');
            
            // Update active states
            updateActiveStates(this);
            
            // Fetch services with AJAX
            fetchFilteredServices(params);
        });
    });
    
    // Function to update active states in the navigation
    function updateActiveStates(activeLink) {
        // Remove active class from all links
        document.querySelectorAll('.dropdown-menu a, .dropdown-content a').forEach(link => {
            link.classList.remove('active');
        });
        
        // Remove active class from all top-level links
        document.querySelectorAll('.nav-categories > li > a, .filter-tab').forEach(link => {
            link.classList.remove('active');
        });
        
        // Add active class to the clicked link
        activeLink.classList.add('active');
        
        // Handle main navigation active states
        const parentCategory = activeLink.closest('li.nav-category');
        if (parentCategory) {
            const categoryLink = parentCategory.querySelector('a');
            if (categoryLink) {
                categoryLink.classList.add('active');
            }
        }
        
        // Handle filter navigation active states
        const filterDropdown = activeLink.closest('.filter-dropdown');
        if (filterDropdown) {
            const filterTab = filterDropdown.querySelector('.filter-tab');
            if (filterTab) {
                filterTab.classList.add('active');
            }
        }
    }
    
    // Function to fetch filtered services with AJAX
    function fetchFilteredServices(params) {
        // Show loading indicator
        const resultsContainer = document.getElementById('services');
        if (resultsContainer) {
            resultsContainer.style.opacity = 0.5;
            
            // Add a loading indicator if it doesn't exist
            let loadingIndicator = document.getElementById('loading-indicator');
            if (!loadingIndicator) {
                loadingIndicator = document.createElement('div');
                loadingIndicator.id = 'loading-indicator';
                loadingIndicator.className = 'ajax-loading';
                resultsContainer.parentNode.insertBefore(loadingIndicator, resultsContainer);
            }
            
            // Fetch filtered results
            fetch('ajax_filter_services.php?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    // Update the services container with new content
                    if (data.html) {
                        resultsContainer.innerHTML = data.html;
                    } else {
                        resultsContainer.innerHTML = '<div class="no-services">No services found matching your filters.</div>';
                    }
                    
                    // Update URL without reloading the page (for bookmarking)
                    const newUrl = 'main.php?' + params.toString();
                    window.history.pushState({}, '', newUrl);
                    
                    // Remove loading indicator and restore opacity
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                    resultsContainer.style.opacity = 1;
                })
                .catch(error => {
                    console.error('Error fetching filtered services:', error);
                    resultsContainer.innerHTML = '<div class="error">An error occurred while loading services. Please try again.</div>';
                    
                    // Remove loading indicator and restore opacity
                    if (loadingIndicator) {
                        loadingIndicator.remove();
                    }
                    resultsContainer.style.opacity = 1;
                });
        }
    }
});
