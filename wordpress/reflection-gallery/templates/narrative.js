/**
 * Narrative Template - Enhanced with Visual Variety
 * Story-driven content with varied layouts and more images
 */

window.NarrativeTemplate = (function() {
  
  // Pool of additional images for variety
  const additionalImages = [
    'assets/images/community-gathering.png',
    'assets/images/hands-unity.png',
    'assets/images/voting-rights.png',
    'assets/images/education-equity.png'
  ];
  
  /**
   * Render narrative template
   */
  function render(item) {
    const { content } = item;
    
    // Convert content to scenes with variety
    const scenes = convertToScenes(item);
    
    // Render all scenes
    const scenesHTML = window.SceneRenderer ? 
      window.SceneRenderer.renderScenes(scenes) :
      renderFallback(item);
    
    return `
      <div class="template narrative-template immersive-template">
        ${scenesHTML}
      </div>
    `;
  }
  
  /**
   * Convert content to scene-based format with visual variety
   */
  function convertToScenes(item) {
    const { content } = item;
    const scenes = [];
    let imageIndex = 0;
    
    // Scene 1: Hero with parallax background
    scenes.push({
      type: 'hero',
      background: item.thumbnail,
      title: item.title,
      subtitle: item.excerpt,
      parallax: true
    });
    
    // Break text into chunks
    const textChunks = breakTextIntoChunks(content.text);
    
    // Create varied scenes - alternate between different layouts
    textChunks.forEach((chunk, index) => {
      const layoutType = index % 5;
      
      // Get image for this section
      const allImages = [...(content.media.images || []), ...additionalImages.map(url => ({ url, caption: '' }))];
      const sceneImage = allImages[imageIndex % allImages.length];
      
      if (layoutType === 0) {
        // Text with image on left
        scenes.push({
          type: 'text-image-left',
          text: chunk,
          image: sceneImage.url,
          imageCaption: sceneImage.caption
        });
        imageIndex++;
      } else if (layoutType === 1) {
        // Text with image on right
        scenes.push({
          type: 'text-image-right',
          text: chunk,
          image: sceneImage.url,
          imageCaption: sceneImage.caption
        });
        imageIndex++;
      } else if (layoutType === 2) {
        // Image with text overlay
        scenes.push({
          type: 'image-overlay-text',
          text: chunk,
          image: sceneImage.url
        });
        imageIndex++;
      } else if (layoutType === 3) {
        // Just text (varied layout)
        scenes.push({
          type: 'text',
          content: chunk,
          animation: 'fade-in-up'
        });
      } else {
        // Quote if available, otherwise text
        if (content.highlights && content.highlights[Math.floor(index / 5)]) {
          scenes.push({
            type: 'quote',
            text: content.highlights[Math.floor(index / 5)],
            animation: 'zoom-in'
          });
        } else {
          scenes.push({
            type: 'text',
            content: chunk,
            animation: 'fade-in-up',
            background: 'gradient'
          });
        }
      }
    });
    
    // Add timeline if exists
    if (content.timeline && content.timeline.length > 0) {
      scenes.push({
        type: 'interactive',
        component: 'timeline',
        data: content.timeline
      });
    }
    
    // Add remaining images as full-screen scenes
    if (content.media.images && content.media.images.length > 0) {
      content.media.images.forEach(img => {
        scenes.push({
          type: 'image',
          url: img.url,
          caption: img.caption,
          effect: 'parallax'
        });
      });
    }
    
    // Add pause scene before reflection
    scenes.push({
      type: 'pause',
      text: 'Take a moment to reflect...'
    });
    
    // Add reflection prompts
    if (content.reflectionPrompts && content.reflectionPrompts.length > 0) {
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
   * Break text into digestible chunks (2-3 sentences)
   */
  function breakTextIntoChunks(text) {
    const paragraphs = text.split('\n\n').filter(p => p.trim());
    const chunks = [];
    
    paragraphs.forEach(para => {
      const sentences = para.match(/[^.!?]+[.!?]+/g) || [para];
      
      // Group into chunks of 2-3 sentences
      for (let i = 0; i < sentences.length; i += 2) {
        const chunk = sentences.slice(i, i + 2).join(' ').trim();
        if (chunk) {
          chunks.push(chunk);
        }
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
