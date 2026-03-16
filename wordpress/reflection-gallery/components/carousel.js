/**
 * Carousel Component
 * Horizontal scrolling carousel with drag support
 */

window.Carousel = (function() {
  let carousel = null;
  let track = null;
  let isDragging = false;
  let startPos = 0;
  let currentTranslate = 0;
  let prevTranslate = 0;
  let animationID = 0;
  let currentIndex = 0;
  
  /**
   * Initialize carousel
   */
  function init(carouselId) {
    carousel = document.getElementById(carouselId);
    if (!carousel) return;
    
    track = carousel.querySelector('.carousel-track');
    if (!track) return;
    
    setupEventListeners();
  }
  
  /**
   * Setup event listeners for drag functionality
   */
  function setupEventListeners() {
    // Mouse events
    track.addEventListener('mousedown', dragStart);
    track.addEventListener('mouseup', dragEnd);
    track.addEventListener('mouseleave', dragEnd);
    track.addEventListener('mousemove', drag);
    
    // Touch events
    track.addEventListener('touchstart', dragStart);
    track.addEventListener('touchend', dragEnd);
    track.addEventListener('touchmove', drag);
    
    // Prevent context menu on long press
    track.addEventListener('contextmenu', (e) => e.preventDefault());
  }
  
  /**
   * Start dragging
   */
  function dragStart(event) {
    isDragging = true;
    startPos = getPositionX(event);
    animationID = requestAnimationFrame(animation);
    carousel.classList.add('dragging');
  }
  
  /**
   * During drag
   */
  function drag(event) {
    if (!isDragging) return;
    
    const currentPosition = getPositionX(event);
    currentTranslate = prevTranslate + currentPosition - startPos;
  }
  
  /**
   * End dragging
   */
  function dragEnd() {
    isDragging = false;
    cancelAnimationFrame(animationID);
    carousel.classList.remove('dragging');
    
    const movedBy = currentTranslate - prevTranslate;
    
    // Snap to nearest item if dragged enough
    if (movedBy < -100 && currentIndex < getItemCount() - 1) {
      currentIndex += 1;
    }
    
    if (movedBy > 100 && currentIndex > 0) {
      currentIndex -= 1;
    }
    
    setPositionByIndex();
    updateNavigationDots();
  }
  
  /**
   * Get X position from mouse or touch event
   */
  function getPositionX(event) {
    return event.type.includes('mouse') 
      ? event.pageX 
      : event.touches[0].clientX;
  }
  
  /**
   * Animation loop
   */
  function animation() {
    setSliderPosition();
    if (isDragging) requestAnimationFrame(animation);
  }
  
  /**
   * Set slider position
   */
  function setSliderPosition() {
    carousel.scrollLeft = -currentTranslate;
  }
  
  /**
   * Set position by index
   */
  function setPositionByIndex() {
    const items = track.querySelectorAll('.carousel-item');
    if (items.length === 0) return;
    
    const item = items[currentIndex];
    const scrollAmount = item.offsetLeft - (carousel.offsetWidth / 2) + (item.offsetWidth / 2);
    
    carousel.scrollTo({
      left: scrollAmount,
      behavior: 'smooth'
    });
    
    currentTranslate = -scrollAmount;
    prevTranslate = currentTranslate;
  }
  
  /**
   * Go to specific slide
   */
  function goToSlide(index) {
    currentIndex = index;
    setPositionByIndex();
    updateNavigationDots();
  }
  
  /**
   * Update navigation dots
   */
  function updateNavigationDots() {
    const dots = document.querySelectorAll('.carousel-dot');
    dots.forEach((dot, index) => {
      if (index === currentIndex) {
        dot.classList.add('active');
      } else {
        dot.classList.remove('active');
      }
    });
  }
  
  /**
   * Get total item count
   */
  function getItemCount() {
    return track.querySelectorAll('.carousel-item').length;
  }
  
  // Public API
  return {
    init,
    goToSlide
  };
})();
