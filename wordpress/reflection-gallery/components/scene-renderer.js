/**
 * Scene Renderer - Enhanced with Visual Variety
 * Each scene type has multiple layout variations
 */

window.SceneRenderer = (function() {
  let sceneLayoutCounter = 0;
  
  /**
   * Render a scene based on its type
   */
  function renderScene(scene, index) {
    const sceneTypes = {
      hero: renderHeroScene,
      text: renderTextScene,
      quote: renderQuoteScene,
      image: renderImageScene,
      video: renderVideoScene,
      split: renderSplitScene,
      interactive: renderInteractiveScene,
      pause: renderPauseScene,
      'text-image-left': renderTextImageLeftScene,
      'text-image-right': renderTextImageRightScene,
      'image-overlay-text': renderImageOverlayTextScene
    };
    
    const renderer = sceneTypes[scene.type];
    if (!renderer) {
      console.warn(`Unknown scene type: ${scene.type}`);
      return '';
    }
    
    return renderer(scene, index);
  }
  
  /**
   * Render hero scene (full-screen intro)
   */
  function renderHeroScene(scene, index) {
    const parallaxClass = scene.parallax ? 'parallax-bg' : '';
    const bgStyle = scene.background ? `background-image: url('${scene.background}');` : '';
    
    return `
      <section class="scene immersive-section hero-scene ${parallaxClass}" 
               data-scene-index="${index}"
               style="${bgStyle}">
        <div class="scene-overlay"></div>
        <div class="scene-content hero-content reveal-on-scroll" data-animation="fade-in" data-delay="300">
          <h1 class="hero-title">${scene.title}</h1>
          ${scene.subtitle ? `<p class="hero-subtitle">${scene.subtitle}</p>` : ''}
        </div>
        <div class="scroll-hint reveal-on-scroll" data-animation="bounce" data-delay="1000">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M19 12l-7 7-7-7"/>
          </svg>
          <span>Scroll to explore</span>
        </div>
      </section>
    `;
  }
  
  /**
   * Render text scene with varied layouts
   */
  function renderTextScene(scene, index) {
    sceneLayoutCounter++;
    const layoutVariant = sceneLayoutCounter % 4;
    
    // Variant 1: Centered text on gradient
    if (layoutVariant === 0) {
      return `
        <section class="scene immersive-section text-scene animated-gradient" 
                 data-scene-index="${index}">
          <div class="scene-content reveal-on-scroll" data-animation="fade-in-up">
            <p class="scene-text">${scene.content}</p>
          </div>
        </section>
      `;
    }
    
    // Variant 2: Text on left, color block on right
    if (layoutVariant === 1) {
      return `
        <section class="scene immersive-section text-scene layout-asymmetric" 
                 data-scene-index="${index}">
          <div class="asymmetric-container">
            <div class="text-side reveal-on-scroll" data-animation="slide-in-left">
              <p class="scene-text">${scene.content}</p>
            </div>
            <div class="color-block reveal-on-scroll" data-animation="slide-in-right" data-delay="200"></div>
          </div>
        </section>
      `;
    }
    
    // Variant 3: Text with decorative elements
    if (layoutVariant === 2) {
      return `
        <section class="scene immersive-section text-scene layout-decorated" 
                 data-scene-index="${index}">
          <div class="decorated-bg"></div>
          <div class="scene-content reveal-on-scroll" data-animation="zoom-in">
            <div class="text-decoration"></div>
            <p class="scene-text">${scene.content}</p>
            <div class="text-decoration bottom"></div>
          </div>
        </section>
      `;
    }
    
    // Variant 4: Text in corner with large space
    return `
      <section class="scene immersive-section text-scene layout-corner" 
               data-scene-index="${index}">
        <div class="corner-content reveal-on-scroll" data-animation="fade-in-up">
          <p class="scene-text">${scene.content}</p>
        </div>
      </section>
    `;
  }
  
  /**
   * Render quote scene with variety
   */
  function renderQuoteScene(scene, index) {
    const variant = index % 2;
    
    if (variant === 0) {
      // Large centered quote
      return `
        <section class="scene immersive-section quote-scene" data-scene-index="${index}">
          <div class="scene-content reveal-on-scroll" data-animation="zoom-in">
            <blockquote class="immersive-quote">
              <svg class="quote-icon" width="60" height="60" viewBox="0 0 24 24" fill="currentColor" opacity="0.2">
                <path d="M6 17h3l2-4V7H5v6h3zm8 0h3l2-4V7h-6v6h3z"/>
              </svg>
              <p class="quote-text">${scene.text}</p>
              ${scene.author ? `<cite class="quote-author">— ${scene.author}</cite>` : ''}
            </blockquote>
          </div>
        </section>
      `;
    }
    
    // Offset quote with accent bar
    return `
      <section class="scene immersive-section quote-scene quote-offset" data-scene-index="${index}">
        <div class="quote-container reveal-on-scroll" data-animation="slide-in-left">
          <div class="quote-accent-bar"></div>
          <blockquote class="offset-quote">
            <p class="quote-text">${scene.text}</p>
            ${scene.author ? `<cite class="quote-author">— ${scene.author}</cite>` : ''}
          </blockquote>
        </div>
      </section>
    `;
  }
  
  /**
   * Render text with image on left
   */
  function renderTextImageLeftScene(scene, index) {
    return `
      <section class="scene immersive-section split-scene layout-image-left" 
               data-scene-index="${index}">
        <div class="split-container">
          <div class="image-side reveal-on-scroll" data-animation="slide-in-left">
            <img src="${scene.image}" alt="${scene.imageCaption || ''}">
          </div>
          <div class="text-side reveal-on-scroll" data-animation="slide-in-right" data-delay="200">
            <p class="scene-text">${scene.text}</p>
          </div>
        </div>
      </section>
    `;
  }
  
  /**
   * Render text with image on right
   */
  function renderTextImageRightScene(scene, index) {
    return `
      <section class="scene immersive-section split-scene layout-image-right" 
               data-scene-index="${index}">
        <div class="split-container">
          <div class="text-side reveal-on-scroll" data-animation="slide-in-left">
            <p class="scene-text">${scene.text}</p>
          </div>
          <div class="image-side reveal-on-scroll" data-animation="slide-in-right" data-delay="200">
            <img src="${scene.image}" alt="${scene.imageCaption || ''}">
          </div>
        </div>
      </section>
    `;
  }
  
  /**
   * Render image with text overlay
   */
  function renderImageOverlayTextScene(scene, index) {
    return `
      <section class="scene immersive-section image-overlay-scene" 
               data-scene-index="${index}"
               style="background-image: url('${scene.image}');">
        <div class="scene-overlay dark"></div>
        <div class="overlay-content reveal-on-scroll" data-animation="fade-in-up">
          <p class="overlay-text">${scene.text}</p>
        </div>
      </section>
    `;
  }
  
  /**
   * Render image scene
   */
  function renderImageScene(scene, index) {
    const effectClass = scene.effect === 'parallax' ? 'parallax' : 
                       scene.effect === 'zoom' ? 'zoom-on-scroll' : '';
    
    return `
      <section class="scene immersive-section image-scene" data-scene-index="${index}">
        <div class="scene-image-container ${effectClass}" ${scene.effect === 'parallax' ? 'data-parallax-speed="0.3"' : ''}>
          <img src="${scene.url}" alt="${scene.caption || ''}" class="scene-image reveal-on-scroll" data-animation="fade-in">
        </div>
        ${scene.caption ? `
          <div class="scene-caption reveal-on-scroll" data-animation="fade-in-up" data-delay="500">
            <p>${scene.caption}</p>
          </div>
        ` : ''}
      </section>
    `;
  }
  
  /**
   * Render video scene
   */
  function renderVideoScene(scene, index) {
    return `
      <section class="scene immersive-section video-scene" data-scene-index="${index}">
        <video class="scene-video" ${scene.autoplay ? 'autoplay' : ''} ${scene.loop ? 'loop' : ''} muted playsinline>
          <source src="${scene.url}" type="video/mp4">
        </video>
        ${scene.overlay ? `
          <div class="scene-overlay"></div>
          <div class="scene-content reveal-on-scroll" data-animation="fade-in">
            <p class="scene-text">${scene.overlay}</p>
          </div>
        ` : ''}
      </section>
    `;
  }
  
  /**
   * Render split scene (text + image side by side)
   */
  function renderSplitScene(scene, index) {
    return `
      <section class="scene immersive-section split-scene" data-scene-index="${index}">
        <div class="split-content">
          <div class="split-left reveal-on-scroll" data-animation="slide-in-left">
            <p class="scene-text">${scene.text}</p>
          </div>
          <div class="split-right reveal-on-scroll" data-animation="slide-in-right" data-delay="200">
            <img src="${scene.image}" alt="${scene.imageCaption || ''}">
          </div>
        </div>
      </section>
    `;
  }
  
  /**
   * Render interactive scene (timeline, chart, etc.)
   */
  function renderInteractiveScene(scene, index) {
    if (scene.component === 'timeline') {
      return renderTimelineScene(scene, index);
    }
    
    return `
      <section class="scene immersive-section interactive-scene" data-scene-index="${index}">
        <div class="scene-content reveal-on-scroll" data-animation="fade-in">
          <div class="interactive-component" data-component="${scene.component}">
            <!-- Component will be initialized by JavaScript -->
          </div>
        </div>
      </section>
    `;
  }
  
  /**
   * Render timeline scene
   */
  function renderTimelineScene(scene, index) {
    return `
      <section class="scene immersive-section timeline-scene" data-scene-index="${index}">
        <div class="scene-content">
          <h3 class="timeline-title reveal-on-scroll" data-animation="fade-in">Timeline</h3>
          <div class="immersive-timeline">
            ${scene.data.map((event, i) => `
              <div class="timeline-item reveal-on-scroll" 
                   data-animation="slide-in-left" 
                   data-delay="${i * 150}">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                  <span class="timeline-date">${event.date}</span>
                  <p class="timeline-description">${event.description}</p>
                </div>
              </div>
            `).join('')}
          </div>
        </div>
      </section>
    `;
  }
  
  /**
   * Render pause scene (breathing room)
   */
  function renderPauseScene(scene, index) {
    return `
      <section class="scene immersive-section pause-scene" data-scene-index="${index}">
        <div class="scene-content reveal-on-scroll" data-animation="fade-in">
          ${scene.text ? `<p class="pause-text">${scene.text}</p>` : ''}
        </div>
      </section>
    `;
  }
  
  /**
   * Render all scenes
   */
  function renderScenes(scenes) {
    sceneLayoutCounter = 0; // Reset counter
    return scenes.map((scene, index) => renderScene(scene, index)).join('');
  }
  
  // Public API
  return {
    renderScene,
    renderScenes
  };
})();
