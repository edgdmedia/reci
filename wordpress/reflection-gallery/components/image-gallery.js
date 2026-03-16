/**
 * Image Gallery Component
 * Lightbox image viewer with zoom
 */

window.ImageGallery = (function() {
  
  /**
   * Initialize image gallery
   */
  function init(galleryElement) {
    if (!galleryElement) return;
    
    const images = galleryElement.querySelectorAll('.gallery-image');
    
    images.forEach((img, index) => {
      img.style.cursor = 'pointer';
      img.setAttribute('tabindex', '0');
      img.setAttribute('role', 'button');
      img.setAttribute('aria-label', `View image ${index + 1}`);
      
      // Click to open lightbox
      img.addEventListener('click', () => {
        openLightbox(galleryElement, index);
      });
      
      // Keyboard support
      img.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openLightbox(galleryElement, index);
        }
      });
    });
  }
  
  /**
   * Open lightbox viewer
   */
  function openLightbox(gallery, startIndex) {
    const images = Array.from(gallery.querySelectorAll('.gallery-image'));
    let currentIndex = startIndex;
    
    // Create lightbox overlay
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox-overlay';
    lightbox.innerHTML = `
      <button class="lightbox-close" aria-label="Close lightbox">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <button class="lightbox-prev" aria-label="Previous image">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
      </button>
      <button class="lightbox-next" aria-label="Next image">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
      </button>
      <div class="lightbox-content">
        <img src="" alt="" class="lightbox-image">
        <p class="lightbox-caption"></p>
      </div>
      <div class="lightbox-counter"></div>
    `;
    
    document.body.appendChild(lightbox);
    document.body.style.overflow = 'hidden';
    
    // Get elements
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');
    const img = lightbox.querySelector('.lightbox-image');
    const caption = lightbox.querySelector('.lightbox-caption');
    const counter = lightbox.querySelector('.lightbox-counter');
    
    // Show current image
    function showImage(index) {
      const currentImg = images[index];
      img.src = currentImg.src;
      img.alt = currentImg.alt;
      caption.textContent = currentImg.alt;
      counter.textContent = `${index + 1} / ${images.length}`;
      
      // Update button states
      prevBtn.style.display = index === 0 ? 'none' : 'flex';
      nextBtn.style.display = index === images.length - 1 ? 'none' : 'flex';
    }
    
    showImage(currentIndex);
    
    // Event listeners
    closeBtn.addEventListener('click', closeLightbox);
    
    prevBtn.addEventListener('click', () => {
      if (currentIndex > 0) {
        currentIndex--;
        showImage(currentIndex);
      }
    });
    
    nextBtn.addEventListener('click', () => {
      if (currentIndex < images.length - 1) {
        currentIndex++;
        showImage(currentIndex);
      }
    });
    
    // Click outside to close
    lightbox.addEventListener('click', (e) => {
      if (e.target === lightbox) {
        closeLightbox();
      }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', handleKeydown);
    
    function handleKeydown(e) {
      if (e.key === 'Escape') {
        closeLightbox();
      } else if (e.key === 'ArrowLeft' && currentIndex > 0) {
        currentIndex--;
        showImage(currentIndex);
      } else if (e.key === 'ArrowRight' && currentIndex < images.length - 1) {
        currentIndex++;
        showImage(currentIndex);
      }
    }
    
    function closeLightbox() {
      document.removeEventListener('keydown', handleKeydown);
      document.body.style.overflow = '';
      lightbox.remove();
    }
  }
  
  // Public API
  return {
    init
  };
})();
