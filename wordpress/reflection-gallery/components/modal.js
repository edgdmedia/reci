/**
 * Modal Component
 * Fullscreen modal for immersive experiences
 */

window.Modal = (function() {
  let overlay = null;
  let container = null;
  let closeBtn = null;
  let isOpen = false;
  
  /**
   * Initialize modal
   */
  function init() {
    overlay = document.getElementById('modalOverlay');
    container = document.getElementById('modalContainer');
    closeBtn = document.getElementById('modalClose');
    
    if (!overlay || !container || !closeBtn) {
      console.error('Modal elements not found');
      return;
    }
    
    setupEventListeners();
  }
  
  /**
   * Setup event listeners
   */
  function setupEventListeners() {
    // Close button
    closeBtn.addEventListener('click', close);
    
    // Click outside content to close
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        close();
      }
    });
    
    // Escape key to close
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && isOpen) {
        close();
      }
    });
  }
  
  /**
   * Open modal with content
   */
  function open(content) {
    if (!overlay || !container) return;
    
    // Set content
    container.innerHTML = content;
    
    // Show modal
    overlay.classList.add('active');
    isOpen = true;
    
    // Lock body scroll
    document.body.style.overflow = 'hidden';
    
    // Focus close button for accessibility
    setTimeout(() => {
      closeBtn.focus();
    }, 100);
    
    // Initialize any components in the template
    initializeTemplateComponents();
    
    // Initialize scroll engine for immersive experience
    if (window.ScrollEngine) {
      setTimeout(() => {
        window.ScrollEngine.init();
      }, 200);
    }
    
    // Initialize progress indicator
    if (window.ProgressIndicator) {
      setTimeout(() => {
        window.ProgressIndicator.init();
      }, 200);
    }
  }
  
  /**
   * Close modal
   */
  function close() {
    if (!overlay) return;
    
    // Hide modal
    overlay.classList.remove('active');
    isOpen = false;
    
    // Unlock body scroll
    document.body.style.overflow = '';
    
    // Destroy scroll engine
    if (window.ScrollEngine) {
      window.ScrollEngine.destroy();
    }
    
    // Clear content after animation
    setTimeout(() => {
      if (container) {
        container.innerHTML = '';
      }
    }, 300);
  }
  
  /**
   * Initialize components within template
   */
  function initializeTemplateComponents() {
    // Initialize audio players
    if (window.AudioPlayer) {
      const audioPlayers = container.querySelectorAll('.audio-player');
      audioPlayers.forEach(player => {
        window.AudioPlayer.init(player);
      });
    }
    
    // Initialize image galleries
    if (window.ImageGallery) {
      const galleries = container.querySelectorAll('.image-gallery');
      galleries.forEach(gallery => {
        window.ImageGallery.init(gallery);
      });
    }
  }
  
  // Public API
  return {
    init,
    open,
    close
  };
})();
