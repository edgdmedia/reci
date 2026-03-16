/**
 * Progress Indicator
 * Shows scroll progress through the story
 */

window.ProgressIndicator = (function() {
  let progressBar = null;
  let progressText = null;
  
  /**
   * Initialize progress indicator
   */
  function init() {
    createProgressBar();
    setupListeners();
  }
  
  /**
   * Create progress bar element
   */
  function createProgressBar() {
    const container = document.createElement('div');
    container.className = 'scroll-progress-container';
    container.innerHTML = `
      <div class="scroll-progress-bar"></div>
      <div class="scroll-progress-text">0%</div>
    `;
    
    document.body.appendChild(container);
    
    progressBar = container.querySelector('.scroll-progress-bar');
    progressText = container.querySelector('.scroll-progress-text');
    
    // Add styles
    addStyles();
  }
  
  /**
   * Add progress indicator styles
   */
  function addStyles() {
    const style = document.createElement('style');
    style.textContent = `
      .scroll-progress-container {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background-color: rgba(0, 0, 0, 0.1);
        z-index: 10000;
        pointer-events: none;
      }
      
      .scroll-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
        width: 0%;
        transition: width 0.1s ease-out;
      }
      
      .scroll-progress-text {
        position: fixed;
        top: 16px;
        right: 16px;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        opacity: 0;
        transition: opacity 0.3s ease;
      }
      
      body.scrolling .scroll-progress-text {
        opacity: 1;
      }
      
      @media (max-width: 768px) {
        .scroll-progress-text {
          top: 12px;
          right: 12px;
          font-size: 11px;
          padding: 3px 10px;
        }
      }
    `;
    document.head.appendChild(style);
  }
  
  /**
   * Setup event listeners
   */
  function setupListeners() {
    window.addEventListener('scroll-progress', (e) => {
      updateProgress(e.detail.progress);
    });
    
    // Show/hide percentage on scroll
    let scrollTimeout;
    window.addEventListener('scroll', () => {
      document.body.classList.add('scrolling');
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        document.body.classList.remove('scrolling');
      }, 1000);
    });
  }
  
  /**
   * Update progress
   */
  function updateProgress(progress) {
    if (progressBar) {
      progressBar.style.width = `${progress}%`;
    }
    if (progressText) {
      progressText.textContent = `${Math.round(progress)}%`;
    }
  }
  
  /**
   * Hide progress indicator
   */
  function hide() {
    const container = document.querySelector('.scroll-progress-container');
    if (container) {
      container.style.display = 'none';
    }
  }
  
  /**
   * Show progress indicator
   */
  function show() {
    const container = document.querySelector('.scroll-progress-container');
    if (container) {
      container.style.display = 'block';
    }
  }
  
  // Public API
  return {
    init,
    updateProgress,
    hide,
    show
  };
})();
