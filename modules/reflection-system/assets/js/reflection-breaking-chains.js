(() => {
  let startY = 0;
  let isDragging = false;
  let currentLink = null;
  let currentInstruction = null;

  function updateScrollState() {
    // This is a fallback for raw scrolling if the stage controller isn't present
    const progressLine = document.getElementById('bcProgressLine');
    if (progressLine && !window.RECIReflectionController) {
      const denominator = Math.max(document.body.scrollHeight - window.innerHeight, 1);
      const scrollPct = window.scrollY / denominator;
      progressLine.style.height = `${scrollPct * 100}%`;
    }
  }

  // Hook into the stage controller for progress updates
  document.addEventListener('reci:stage:enter', (e) => {
    const progressLine = document.getElementById('bcProgressLine');
    const freedomBg = document.getElementById('bcFreedomBg');
    const stages = Array.from(document.querySelectorAll('.reci-reflection-stage'));
    
    if (stages.length === 0) return;
    
    const currentIndex = stages.findIndex(s => s.id === e.detail.id);
    if (progressLine && currentIndex >= 0) {
      const pct = stages.length > 1 ? currentIndex / (stages.length - 1) : 1;
      progressLine.style.height = `${pct * 100}%`;
    }

    if (freedomBg) {
      if (currentIndex === stages.length - 1) {
        freedomBg.style.opacity = '1';
      } else {
        freedomBg.style.opacity = '0';
      }
    }
  });

  function endDrag() {
    if (!isDragging) return;
    isDragging = false;
    document.removeEventListener('mousemove', onDrag);
    document.removeEventListener('mouseup', endDrag);
    document.removeEventListener('touchmove', onDrag);
    document.removeEventListener('touchend', endDrag);
    if (currentLink && !currentLink.classList.contains('link-broken')) {
      currentLink.style.transform = 'translateY(0)';
      if (currentInstruction) currentInstruction.textContent = 'Drag Down to Break';
    }
    currentLink = null;
    currentInstruction = null;
  }

  function breakLink() {
    isDragging = false;
    document.removeEventListener('mousemove', onDrag);
    document.removeEventListener('mouseup', endDrag);
    document.removeEventListener('touchmove', onDrag);
    document.removeEventListener('touchend', endDrag);
    if (!currentLink) return;
    currentLink.classList.add('link-broken');
    currentLink.style.animation = 'breakAndFall 1s forwards';
    if (currentInstruction) {
      currentInstruction.textContent = 'BROKEN';
      currentInstruction.style.color = 'var(--reflection-accent)';
    }
    
    // Play audio via window API from navigation bar
    if (window.reciToggleRacialAudio) {
      const audio = document.getElementById('rdAudio');
      if (audio && audio.paused) {
          window.reciToggleRacialAudio();
      }
    }

    window.setTimeout(() => {
      const section = currentLink ? currentLink.closest('section') : null;
      const tmode = section ? section.dataset.transitionMode : 'button';
      
      if (tmode === 'auto') {
        const target = section.dataset.continueTarget;
        if (target && window.RECIReflectionController) {
          window.RECIReflectionController.goTo(target);
        }
      } else {
        const continueWrapper = document.getElementById('dragRevealContinueWrapper');
        if (continueWrapper) {
          continueWrapper.classList.remove('opacity-0', 'pointer-events-none');
          continueWrapper.style.opacity = '1';
          continueWrapper.style.pointerEvents = 'auto';
        }
      }
    }, 1500);
  }

  function onDrag(e) {
    if (!isDragging || !currentLink) return;
    e.preventDefault();
    const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
    const delta = clientY - startY;
    if (delta > 0 && delta < 150) {
      currentLink.style.transform = `translateY(${delta}px)`;
      if (currentInstruction && delta > 50) currentInstruction.textContent = 'Pull Harder...';
    }
    if (delta >= 150) breakLink();
  }

  window.reciStartChainDrag = (e, linkEl) => {
    isDragging = true;
    currentLink = linkEl;
    currentInstruction = currentLink.closest('section').querySelector('#chainInstruction');
    startY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
    document.addEventListener('mousemove', onDrag);
    document.addEventListener('mouseup', endDrag);
    document.addEventListener('touchmove', onDrag, { passive: false });
    document.addEventListener('touchend', endDrag);
  };

  // Event delegation for dragging the chain link
  document.addEventListener('mousedown', (e) => {
    const linkEl = e.target.closest('#breakableLink');
    if (linkEl) {
      e.preventDefault();
      window.reciStartChainDrag(e, linkEl);
    }
  });
  
  document.addEventListener('touchstart', (e) => {
    const linkEl = e.target.closest('#breakableLink');
    if (linkEl) {
      // Do not prevent default here, but start drag
      window.reciStartChainDrag(e, linkEl);
    }
  }, { passive: false });

  // Event delegation for Word Shift
  document.addEventListener('mouseover', (e) => {
    const span = e.target.closest('.shift-word');
    if (span) {
      if (!span.dataset.orig) span.dataset.orig = span.innerText;
      span.innerText = span.dataset.shift;
      span.classList.add('shifted');
    }
  });

  document.addEventListener('mouseout', (e) => {
    const span = e.target.closest('.shift-word');
    if (span) {
      span.innerText = span.dataset.orig || span.innerText;
      span.classList.remove('shifted');
    }
  });

  window.addEventListener('scroll', updateScrollState, { passive: true });
  
  // Re-run on load in case loaded in middle of page
  document.addEventListener('DOMContentLoaded', updateScrollState);

})();
