/**
 * Audio Player Component
 * Custom audio player with controls
 */

window.AudioPlayer = (function() {
  
  /**
   * Initialize audio player
   */
  function init(playerElement) {
    if (!playerElement) return;
    
    const audio = playerElement.querySelector('audio');
    const playBtn = playerElement.querySelector('.audio-play');
    const progress = playerElement.querySelector('.audio-progress');
    const progressBar = playerElement.querySelector('.audio-progress-bar');
    const currentTime = playerElement.querySelector('.audio-current-time');
    const duration = playerElement.querySelector('.audio-duration');
    
    if (!audio) return;
    
    // Play/pause toggle
    if (playBtn) {
      playBtn.addEventListener('click', () => {
        if (audio.paused) {
          audio.play();
          playBtn.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <rect x="6" y="4" width="4" height="16" rx="1"/>
              <rect x="14" y="4" width="4" height="16" rx="1"/>
            </svg>
          `;
          playBtn.setAttribute('aria-label', 'Pause');
        } else {
          audio.pause();
          playBtn.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M8 5v14l11-7z"/>
            </svg>
          `;
          playBtn.setAttribute('aria-label', 'Play');
        }
      });
    }
    
    // Update progress bar
    audio.addEventListener('timeupdate', () => {
      if (progressBar && audio.duration) {
        const percent = (audio.currentTime / audio.duration) * 100;
        progressBar.style.width = `${percent}%`;
      }
      
      if (currentTime) {
        currentTime.textContent = formatTime(audio.currentTime);
      }
    });
    
    // Set duration when loaded
    audio.addEventListener('loadedmetadata', () => {
      if (duration) {
        duration.textContent = formatTime(audio.duration);
      }
    });
    
    // Seek functionality
    if (progress) {
      progress.addEventListener('click', (e) => {
        const rect = progress.getBoundingClientRect();
        const percent = (e.clientX - rect.left) / rect.width;
        audio.currentTime = percent * audio.duration;
      });
    }
    
    // Reset play button when audio ends
    audio.addEventListener('ended', () => {
      if (playBtn) {
        playBtn.innerHTML = `
          <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
            <path d="M8 5v14l11-7z"/>
          </svg>
        `;
        playBtn.setAttribute('aria-label', 'Play');
      }
    });
  }
  
  /**
   * Format time in MM:SS
   */
  function formatTime(seconds) {
    if (isNaN(seconds)) return '0:00';
    
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  }
  
  // Public API
  return {
    init
  };
})();
