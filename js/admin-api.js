// Admin API Integration for Dynamic Content

const API_BASE = 'api/';

// Fetch gallery images
async function fetchGallery(type = 'home') {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=gallery`);
        const data = await response.json();
        return data[type] || [];
    } catch (error) {
        console.error('Error fetching gallery:', error);
        return [];
    }
}

// Fetch services
async function fetchServices() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=services`);
        const data = await response.json();
        return data || [];
    } catch (error) {
        console.error('Error fetching services:', error);
        return [];
    }
}

// Fetch reviews
async function fetchReviews() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=reviews`);
        const data = await response.json();
        return data || [];
    } catch (error) {
        console.error('Error fetching reviews:', error);
        return [];
    }
}

// Submit review
async function submitReview(name, message, rating) {
    try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('message', message);
        formData.append('rating', rating);
        
        const response = await fetch(`${API_BASE}submit-review.php`, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error submitting review:', error);
        return { success: false, message: 'Gabim në dërgim të review!' };
    }
}

// Render gallery images
function renderGallery(images, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    if (images.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Nuk ka foto për momentin.</p>';
        return;
    }
    
    container.innerHTML = images.map(img => `
        <div class="portfolio-item">
            <img src="${img.path}" alt="${img.title || ''}" class="w-full h-full object-cover">
            ${img.title ? `<div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2">${img.title}</div>` : ''}
        </div>
    `).join('');
}

// Render services
function renderServices(services, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    if (services.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Nuk ka shërbime për momentin.</p>';
        return;
    }
    
    container.innerHTML = services.map(service => `
        <div class="group bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-500">
            <div class="w-full h-36 rounded-lg overflow-hidden mb-4">
                <img src="${service.image}" alt="${service.title}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">${service.title}</h3>
            <p class="text-gray-600">${service.description}</p>
        </div>
    `).join('');
}

// Render reviews
function renderReviews(reviews, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    if (reviews.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Nuk ka reviews për momentin.</p>';
        return;
    }
    
    container.innerHTML = reviews.map(review => `
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-bold text-lg">${review.name || 'Anonim'}</h4>
                <div class="flex items-center">
                    ${Array.from({ length: 5 }, (_, i) => 
                        `<i class="fas fa-star ${i < review.rating ? 'text-yellow-400' : 'text-gray-300'}"></i>`
                    ).join('')}
                </div>
            </div>
            <p class="text-gray-700">${review.message}</p>
            <p class="text-sm text-gray-500 mt-2">${review.date || ''}</p>
        </div>
    `).join('');
}

// Fetch catalogs
async function fetchCatalogs() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=catalogs`);
        const data = await response.json();
        return data || [];
    } catch (error) {
        console.error('Error fetching catalogs:', error);
        return [];
    }
}

// Fetch activities
async function fetchActivities() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=activities`);
        const data = await response.json();
        return data || {};
    } catch (error) {
        console.error('Error fetching activities:', error);
        return {};
    }
}

// Render catalogs
function renderCatalogs(catalogs, containerId) {
    const container = document.getElementById(containerId);
    const emptyContainer = document.getElementById('catalogs-empty');
    if (!container) return;
    
    if (catalogs.length === 0) {
        container.classList.add('hidden');
        if (emptyContainer) emptyContainer.classList.remove('hidden');
        return;
    }
    
    container.classList.remove('hidden');
    if (emptyContainer) emptyContainer.classList.add('hidden');
    
    container.innerHTML = catalogs.map((catalog, index) => `
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group" data-aos="fade-up" data-aos-delay="${index * 100}">
            <div class="relative h-64 overflow-hidden">
                <img src="${catalog.cover_image}" 
                     alt="${catalog.title}" 
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute top-4 right-4">
                    <span class="bg-primary text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                        ${catalog.category}
                    </span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">${catalog.title}</h3>
                <p class="text-gray-600 mb-4">${catalog.description}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-box mr-1"></i>
                        ${catalog.products ? catalog.products.length : 0} Produkte
                    </span>
                    <button onclick="viewCatalog('${catalog.id}')" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors font-semibold">
                        <i class="fas fa-eye mr-2"></i>Anzeigen
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Fetch and render catalogs
async function fetchAndRenderCatalogs() {
    const catalogs = await fetchCatalogs();
    renderCatalogs(catalogs, 'catalogs-container');
}

// View catalog details (modal or expand)
function viewCatalog(catalogId) {
    // This can be expanded to show a modal with catalog details
    console.log('View catalog:', catalogId);
}

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        fetchGallery,
        fetchServices,
        fetchReviews,
        fetchCatalogs,
        fetchActivities,
        submitReview,
        renderGallery,
        renderServices,
        renderReviews,
        renderCatalogs,
        fetchAndRenderCatalogs
    };
}

