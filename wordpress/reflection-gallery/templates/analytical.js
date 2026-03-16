/**
 * Analytical Template - Immersive Scroll-Driven Version
 * Interactive research journey with data reveals
 */

window.AnalyticalTemplate = (function() {
  
  /**
   * Render analytical template
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
      <div class="template analytical-template immersive-template">
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
    
    // Scene 1: Data visualization hero
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
    
    // Scene 2-N: Key findings as individual cards
    if (content.highlights && content.highlights.length > 0) {
      scenes.push({
        type: 'text',
        content: 'Key Findings',
        animation: 'fade-in',
        background: 'gradient'
      });
      
      content.highlights.forEach((finding, index) => {
        scenes.push({
          type: 'text',
          content: `${index + 1}. ${finding}`,
          animation: index % 2 === 0 ? 'slide-in-left' : 'slide-in-right',
          background: ''
        });
      });
    }
    
    // Break text into analytical chunks
    const textChunks = breakTextIntoChunks(content.text);
    
    // Alternate text with data visualizations
    textChunks.forEach((chunk, index) => {
      scenes.push({
        type: 'text',
        content: chunk,
        animation: 'fade-in-up',
        background: index % 2 === 0 ? '' : 'gradient'
      });
      
      // Insert data visualizations between text
      const imgIndex = index + 1; // Skip first image (used in hero)
      if (content.media.images && content.media.images[imgIndex]) {
        scenes.push({
          type: 'image',
          url: content.media.images[imgIndex].url,
          caption: content.media.images[imgIndex].caption,
          effect: 'zoom'
        });
      }
    });
    
    // Discussion questions
    if (content.reflectionPrompts && content.reflectionPrompts.length > 0) {
      scenes.push({
        type: 'pause',
        text: 'Discussion Questions'
      });
      
      content.reflectionPrompts.forEach((question, index) => {
        scenes.push({
          type: 'text',
          content: `Question ${index + 1}: ${question}`,
          animation: 'fade-in',
          background: index % 2 === 0 ? 'gradient' : ''
        });
      });
    }
    
    return scenes;
  }
  
  /**
   * Break text into analytical chunks
   */
  function breakTextIntoChunks(text) {
    const paragraphs = text.split('\n\n').filter(p => p.trim());
    const chunks = [];
    
    paragraphs.forEach(para => {
      const sentences = para.match(/[^.!?]+[.!?]+/g) || [para];
      // Group 2-3 sentences for analytical depth
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
