/**
 * Documentary Template - Immersive Scroll-Driven Version
 * Data-driven reveals with split-screen and interactive elements
 */

window.DocumentaryTemplate = (function() {
  
  /**
   * Render documentary template
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
      <div class="template documentary-template immersive-template">
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
    
    // Scene 1: Hero
    scenes.push({
      type: 'hero',
      background: item.thumbnail,
      title: item.title,
      subtitle: item.excerpt,
      parallax: true
    });
    
    // Scene 2: Key highlights as individual reveals
    if (content.highlights && content.highlights.length > 0) {
      content.highlights.forEach((highlight, index) => {
        scenes.push({
          type: 'text',
          content: `Key Finding ${index + 1}: ${highlight}`,
          animation: 'slide-in-left',
          background: index % 2 === 0 ? '' : 'gradient'
        });
      });
    }
    
    // Break main text into chunks
    const textChunks = breakTextIntoChunks(content.text);
    
    // Alternate between text and split-screen with images
    textChunks.forEach((chunk, index) => {
      if (content.media.images && content.media.images[index]) {
        // Split screen: text + image
        scenes.push({
          type: 'split',
          text: chunk,
          image: content.media.images[index].url,
          imageCaption: content.media.images[index].caption
        });
      } else {
        // Just text
        scenes.push({
          type: 'text',
          content: chunk,
          animation: 'fade-in-up'
        });
      }
    });
    
    // Add video if exists
    if (content.media.video && content.media.video.length > 0) {
      scenes.push({
        type: 'video',
        url: content.media.video[0].url,
        overlay: content.media.video[0].caption,
        autoplay: true,
        loop: true
      });
    }
    
    // Reflection prompts
    if (content.reflectionPrompts && content.reflectionPrompts.length > 0) {
      scenes.push({
        type: 'pause',
        text: 'Consider these questions...'
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
   * Break text into chunks
   */
  function breakTextIntoChunks(text) {
    const paragraphs = text.split('\n\n').filter(p => p.trim());
    const chunks = [];
    
    paragraphs.forEach(para => {
      const sentences = para.match(/[^.!?]+[.!?]+/g) || [para];
      for (let i = 0; i < sentences.length; i += 2) {
        const chunk = sentences.slice(i, i + 2).join(' ').trim();
        if (chunk) chunks.push(chunk);
      }
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
