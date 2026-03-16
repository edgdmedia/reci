/**
 * RECI Reflection Gallery - Main Application
 * Center of Race and Social Problems, University of Pittsburgh
 */

// Global state
const AppState = {
  content: null,
  currentItem: null,
  templates: {}
};

/**
 * Initialize the application
 */
async function initApp() {
  try {
    // Load content data
    AppState.content = await loadContent();
    
    // Render gallery components
    renderCarousel(AppState.content.items);
    renderGallery(AppState.content.items);
    
    // Initialize carousel
    if (window.Carousel) {
      window.Carousel.init('carousel');
    }
    
    // Initialize modal
    if (window.Modal) {
      window.Modal.init();
    }
    
    // Setup event listeners
    setupEventListeners();
    
    console.log('RECI Reflection Gallery initialized successfully');
  } catch (error) {
    console.error('Error initializing app:', error);
    showError('Failed to load gallery content. Please refresh the page.');
  }
}

/**
 * Load content from JSON file
 */
async function loadContent() {
  try {
    const response = await fetch('data/content.json');
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    return await response.json();
  } catch (error) {
    console.error('Error loading content:', error);
    // Return sample data as fallback
    return getSampleContent();
  }
}

/**
 * Render carousel items
 */
function renderCarousel(items) {
  const track = document.getElementById('carouselTrack');
  const nav = document.getElementById('carouselNav');
  
  if (!track || !nav) return;
  
  // Clear existing content
  track.innerHTML = '';
  nav.innerHTML = '';
  
  // Render carousel items
  items.forEach((item, index) => {
    const carouselItem = createCarouselItem(item, index);
    track.appendChild(carouselItem);
    
    // Create navigation dot
    const dot = document.createElement('button');
    dot.className = `carousel-dot ${index === 0 ? 'active' : ''}`;
    dot.setAttribute('aria-label', `Go to slide ${index + 1}`);
    dot.addEventListener('click', () => {
      if (window.Carousel) {
        window.Carousel.goToSlide(index);
      }
    });
    nav.appendChild(dot);
  });
}

/**
 * Create a carousel item element
 */
function createCarouselItem(item, index) {
  const div = document.createElement('div');
  div.className = 'carousel-item';
  div.setAttribute('role', 'button');
  div.setAttribute('tabindex', '0');
  div.setAttribute('aria-label', `View ${item.title}`);
  
  div.innerHTML = `
    <img src="${item.thumbnail}" alt="${item.title}" loading="${index === 0 ? 'eager' : 'lazy'}">
    <div class="carousel-item-overlay">
      <h3 class="carousel-item-title">${item.title}</h3>
      <p class="carousel-item-excerpt">${item.excerpt}</p>
    </div>
  `;

  // Attach click handler for navigation
  div.addEventListener('click', () => {
    if (item.link) {
      window.location.href = item.link;
    } else {
      if (window.Modal) {
        window.Modal.open(item);
      }
    }
  });

  // Keyboard support
  div.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      if (item.link) {
        window.location.href = item.link;
      } else if (window.Modal) {
        window.Modal.open(item);
      }
    }
  });
  
  return div;
}

/**
 * Render gallery grid
 */
function renderGallery(items) {
  const grid = document.getElementById('galleryGrid');
  if (!grid) return;
  
  // Clear existing content
  grid.innerHTML = '';
  
  // Render gallery cards
  items.forEach((item) => {
    const card = createGalleryCard(item);
    grid.appendChild(card);
  });
}

/**
 * Create a gallery card element
 */
function createGalleryCard(item) {
  const article = document.createElement('article');
  article.className = 'gallery-card';
  article.setAttribute('role', 'button');
  article.setAttribute('tabindex', '0');
  article.setAttribute('aria-label', `View ${item.title}`);
  
  const templateLabel = getTemplateLabel(item.template);
  
  article.innerHTML = `
    <div class="gallery-card-image">
      <img src="${item.thumbnail}" alt="${item.title}" loading="lazy">
      <span class="gallery-card-badge">${templateLabel}</span>
    </div>
    <div class="gallery-card-content">
      <h3 class="gallery-card-title">${item.title}</h3>
      <p class="gallery-card-excerpt">${item.excerpt}</p>
      <div class="gallery-card-meta">
        <span>📖 ${estimateReadTime(item.content.text)} min read</span>
        ${item.content.media.audio.length > 0 ? '<span>🎧 Audio available</span>' : ''}
      </div>
      <span class="gallery-card-cta">
        Explore →
      </span>
    </div>
  `;
  
  // Click handler
  article.addEventListener('click', () => openImmersiveView(item));
  article.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      openImmersiveView(item);
    }
  });
  
  return article;
}

/**
 * Get human-readable template label
 */
function getTemplateLabel(template) {
  const labels = {
    narrative: 'Narrative',
    documentary: 'Documentary',
    testimonial: 'Testimonial',
    analytical: 'Analytical'
  };
  return labels[template] || 'Story';
}

/**
 * Estimate reading time in minutes
 */
function estimateReadTime(text) {
  const wordsPerMinute = 200;
  const wordCount = text.split(/\s+/).length;
  return Math.max(1, Math.ceil(wordCount / wordsPerMinute));
}

/**
 * Open immersive view with appropriate template
 */
function openImmersiveView(item) {
  AppState.currentItem = item;
  
  // Get the appropriate template renderer
  const templateRenderer = getTemplateRenderer(item.template);
  
  if (!templateRenderer) {
    console.error(`Template renderer not found for: ${item.template}`);
    showError('Template not available');
    return;
  }
  
  // Render template content
  const content = templateRenderer(item);
  
  // Open modal with content
  if (window.Modal) {
    window.Modal.open(content);
  }
  
  // Track analytics (if implemented)
  trackEvent('immersive_view_opened', {
    title: item.title,
    template: item.template
  });
}

/**
 * Get template renderer function
 */
function getTemplateRenderer(templateName) {
  const renderers = {
    narrative: window.NarrativeTemplate?.render,
    documentary: window.DocumentaryTemplate?.render,
    testimonial: window.TestimonialTemplate?.render,
    analytical: window.AnalyticalTemplate?.render
  };
  
  return renderers[templateName];
}

/**
 * Setup global event listeners
 */
function setupEventListeners() {
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href === '#') return;
      
      e.preventDefault();
      const target = document.querySelector(href);
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });
  
  // Keyboard shortcuts
  document.addEventListener('keydown', (e) => {
    // Escape key closes modal
    if (e.key === 'Escape' && window.Modal) {
      window.Modal.close();
    }
  });
}

/**
 * Show error message to user
 */
function showError(message) {
  // Create error notification
  const notification = document.createElement('div');
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: #dc2626;
    color: white;
    padding: 16px 24px;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 10000;
    font-family: var(--font-primary);
  `;
  notification.textContent = message;
  
  document.body.appendChild(notification);
  
  // Remove after 5 seconds
  setTimeout(() => {
    notification.remove();
  }, 5000);
}

/**
 * Track analytics events (placeholder)
 */
function trackEvent(eventName, data) {
  // Implement analytics tracking here
  console.log('Event:', eventName, data);
}

/**
 * Get sample content (fallback)
 */
function getSampleContent() {
  return {
    items: [
      {
        id: 'sample-1',
        title: 'Sample Reflection',
        template: 'narrative',
        thumbnail: 'assets/images/placeholder.jpg',
        excerpt: 'This is a sample reflection item.',
        content: {
          text: 'Sample content text...',
          highlights: [],
          media: {
            images: [],
            audio: [],
            video: []
          },
          timeline: [],
          reflectionPrompts: []
        }
      }
    ]
  };
}

// Initialize app when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initApp);
} else {
  initApp();
}

// Export for use in other modules
window.App = {
  state: AppState,
  openImmersiveView
};
