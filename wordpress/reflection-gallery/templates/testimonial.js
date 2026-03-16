/**
 * Testimonial Template - Immersive Scroll-Driven Version
 * Intimate first-person journey with audio and photos
 */

window.TestimonialTemplate = (function() {
  
  /**
   * Render testimonial template
   */
  function render(item) {
    const { content } = item;
    
    // Convert content to scenes
    const scenes = convertToScenes(item);
    
    // Render all scenes
    const scenesHTML = window.SceneRenderer ? 
      window.SceneRenderer.renderScenes(scenes) :
      renderFallback(item);
    
    return `
      <div class="template testimonial-template immersive-template">
        ${scenesHTML}
      </div>
    `;
  }
  
  /**
   * Convert content to scene-based format
   */
  function convertToScenes(item) {
    const { content } = item;
    const scenes = [];
    
    // Scene 1: Portrait intro
    if (content.media.images && content.media.images.length > 0) {
      scenes.push({
        type: 'image',
        url: content.media.images[0].url,
        caption: item.title,
        effect: 'zoom'
      });
    } else {
      scenes.push({
        type: 'hero',
        background: item.thumbnail,
        title: item.title,
        subtitle: item.excerpt,
        parallax: true
      });
    }
    
    // Scene 2: Opening quote if available
    if (content.highlights && content.highlights.length > 0) {
      scenes.push({
        type: 'quote',
        text: content.highlights[0],
        animation: 'fade-in'
      });
    }
    
    // Break text into small, intimate chunks (1-2 sentences)
    const textChunks = breakTextIntoSmallChunks(content.text);
    
    // Intersperse text with quotes and pauses
    textChunks.forEach((chunk, index) => {
      // Text scene
      scenes.push({
        type: 'text',
        content: chunk,
        animation: 'fade-in-up',
        background: index % 3 === 0 ? 'gradient' : ''
      });
      
      // Add breathing room every few chunks
      if ((index + 1) % 3 === 0) {
        scenes.push({
          type: 'pause',
          text: '...'
        });
      }
      
      // Insert pull quotes
      const quoteIndex = Math.floor(index / 3);
      if (content.highlights && content.highlights[quoteIndex + 1]) {
        scenes.push({
          type: 'quote',
          text: content.highlights[quoteIndex + 1],
          animation: 'zoom-in'
        });
      }
    });
    
    // Add remaining photos
    if (content.media.images && content.media.images.length > 1) {
      content.media.images.slice(1).forEach(img => {
        scenes.push({
          type: 'image',
          url: img.url,
          caption: img.caption,
          effect: 'parallax'
        });
      });
    }
    
    // Reflection prompts
    if (content.reflectionPrompts && content.reflectionPrompts.length > 0) {
      scenes.push({
        type: 'pause',
        text: 'Reflect on this story...'
      });
      
      content.reflectionPrompts.forEach(prompt => {
        scenes.push({
          type: 'text',
          content: prompt,
          animation: 'fade-in',
          background: 'gradient'
        });
      });
    }
    
    return scenes;
  }
  
  /**
   * Break text into very small chunks for intimate pacing
   */
  function breakTextIntoSmallChunks(text) {
    const paragraphs = text.split('\n\n').filter(p => p.trim());
    const chunks = [];
    
    paragraphs.forEach(para => {
      const sentences = para.match(/[^.!?]+[.!?]+/g) || [para];
      // Each sentence becomes its own scene for intimate pacing
      sentences.forEach(sentence => {
        const trimmed = sentence.trim();
        if (trimmed) chunks.push(trimmed);
      });
    });
    
    return chunks;
  }
  
  /**
   * Fallback rendering
   */
  function renderFallback(item) {
    return `
      <div class="immersive-section">
        <div class="scene-content">
          <h1>${item.title}</h1>
          <p>${item.excerpt}</p>
        </div>
      </div>
    `;
  }
  
  // Public API
  return {
    render
  };
})();
