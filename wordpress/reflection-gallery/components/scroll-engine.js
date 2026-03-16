/**
 * Scroll Engine
 * Handles scroll-based animations and progressive reveals
 */

window.ScrollEngine = (function() {
  let observers = [];
  let scrollProgress = 0;
  let isScrolling = false;
  let scrollTimeout;
  
  /**
   * Initialize scroll engine
   */
  function init() {
    setupIntersectionObserver();
    setupScrollListeners();
    setupParallax();
    console.log('Scroll Engine initialized');
  }
  
  /**
   * Setup Intersection Observer for scroll-triggered reveals
   */
  function setupIntersectionObserver() {
    const options = {
      root: null,
      rootMargin: '0px',
      threshold: [0, 0.1, 0.25, 0.5, 0.75, 1.0]
    };
    
    const callback = (entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const target = entry.target;
          const animation = target.dataset.animation || 'fade-in-up';
          const delay = target.dataset.delay || 0;
          
          setTimeout(() => {
            target.classList.add('revealed');
            target.classList.add(`animate-${animation}`);
          }, delay);
          
          // Trigger custom event
          target.dispatchEvent(new CustomEvent('scene-revealed', {
            detail: { target, animation }
          }));
        }
      });
    };
    
    const observer = new IntersectionObserver(callback, options);
    observers.push(observer);
    
    // Observe all reveal elements
    observeRevealElements();
  }
  
  /**
   * Observe elements that should reveal on scroll
   */
  function observeRevealElements() {
    const elements = document.querySelectorAll('.reveal-on-scroll, .scene');
    elements.forEach(el => {
      if (observers[0]) {
        observers[0].observe(el);
      }
    });
  }
  
  /**
   * Setup scroll event listeners
   */
  function setupScrollListeners() {
    let ticking = false;
    
    window.addEventListener('scroll', () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          updateScrollProgress();
          handleScrollEffects();
          ticking = false;
        });
        ticking = true;
      }
      
      // Detect scroll start/end
      isScrolling = true;
      clearTimeout(scrollTimeout);
      scrollTimeout = setTimeout(() => {
        isScrolling = false;
      }, 150);
    });
  }
  
  /**
   * Update scroll progress
   */
  function updateScrollProgress() {
    const windowHeight = window.innerHeight;
    const documentHeight = document.documentElement.scrollHeight;
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    scrollProgress = (scrollTop / (documentHeight - windowHeight)) * 100;
    
    // Update progress indicator if exists
    const progressBar = document.querySelector('.scroll-progress-bar');
    if (progressBar) {
      progressBar.style.width = `${scrollProgress}%`;
    }
    
    // Dispatch progress event
    window.dispatchEvent(new CustomEvent('scroll-progress', {
      detail: { progress: scrollProgress, scrollTop }
    }));
  }
  
  /**
   * Handle scroll-based effects
   */
  function handleScrollEffects() {
    // Parallax elements
    const parallaxElements = document.querySelectorAll('.parallax');
    parallaxElements.forEach(el => {
      const speed = el.dataset.parallaxSpeed || 0.5;
      const rect = el.getBoundingClientRect();
      const scrolled = window.pageYOffset;
      const offset = rect.top + scrolled;
      const yPos = -(scrolled - offset) * speed;
      
      el.style.transform = `translate3d(0, ${yPos}px, 0)`;
    });
    
    // Zoom on scroll elements
    const zoomElements = document.querySelectorAll('.zoom-on-scroll');
    zoomElements.forEach(el => {
      const rect = el.getBoundingClientRect();
      const elementCenter = rect.top + rect.height / 2;
      const viewportCenter = window.innerHeight / 2;
      const distance = Math.abs(elementCenter - viewportCenter);
      const maxDistance = window.innerHeight;
      const scale = 1 + (1 - Math.min(distance / maxDistance, 1)) * 0.2;
      
      el.style.transform = `scale(${scale})`;
    });
  }
  
  /**
   * Setup parallax backgrounds
   */
  function setupParallax() {
    const parallaxBgs = document.querySelectorAll('.parallax-bg');
    
    window.addEventListener('scroll', () => {
      parallaxBgs.forEach(bg => {
        const scrolled = window.pageYOffset;
        const rate = scrolled * 0.5;
        bg.style.backgroundPositionY = `${rate}px`;
      });
    });
  }
  
  /**
   * Smooth scroll to element
   */
  function scrollToElement(element, offset = 0) {
    const targetPosition = element.getBoundingClientRect().top + window.pageYOffset - offset;
    
    window.scrollTo({
      top: targetPosition,
      behavior: 'smooth'
    });
  }
  
  /**
   * Smooth scroll to next section
   */
  function scrollToNext() {
    const sections = document.querySelectorAll('.scene, .immersive-section');
    const scrollTop = window.pageYOffset;
    
    for (let section of sections) {
      const rect = section.getBoundingClientRect();
      if (rect.top > 100) {
        scrollToElement(section);
        break;
      }
    }
  }
  
  /**
   * Smooth scroll to previous section
   */
  function scrollToPrevious() {
    const sections = Array.from(document.querySelectorAll('.scene, .immersive-section'));
    const scrollTop = window.pageYOffset;
    
    for (let i = sections.length - 1; i >= 0; i--) {
      const section = sections[i];
      const rect = section.getBoundingClientRect();
      if (rect.top < -100) {
        scrollToElement(section);
        break;
      }
    }
  }
  
  /**
   * Get current scroll progress
   */
  function getProgress() {
    return scrollProgress;
  }
  
  /**
   * Refresh observers (call after adding new elements)
   */
  function refresh() {
    observeRevealElements();
  }
  
  /**
   * Destroy scroll engine
   */
  function destroy() {
    observers.forEach(observer => observer.disconnect());
    observers = [];
  }
  
  // Public API
  return {
    init,
    scrollToElement,
    scrollToNext,
    scrollToPrevious,
    getProgress,
    refresh,
    destroy
  };
})();
