(() => {
  const api = window.RECIReflection;
  if (!api || typeof api.createStageController !== 'function') return;

  const audio = document.getElementById('marchAudio');
  const playBtn = document.getElementById('marchPlayBtn');
  let world = document.getElementById('marchWorld');
  const backBtn = document.getElementById('marchStageBack');
  const completeBtn = document.getElementById('marchCompleteBtn');
  const startBtn = document.getElementById('marchStartBtn');
  const contextBtn = document.getElementById('marchContextBtn');
  const layers = [document.querySelector('#march-crowd [data-parallax-layer="0"]'), document.querySelector('#march-crowd [data-parallax-layer="1"]')];
  
  const isNewSystem = !!document.querySelector('.reci-reflection-system-page');
  
  if (isNewSystem) {
    const initWorldStyles = () => {
      const currentWorld = document.querySelector('.reci-immersive-mode');
      if (currentWorld && !currentWorld.dataset.marchInit) {
        currentWorld.style.display = 'flex';
        currentWorld.style.transition = 'transform 1s cubic-bezier(0.23, 1, 0.32, 1)';
        currentWorld.style.height = '100vh';
        currentWorld.style.overflow = 'hidden';
        currentWorld.dataset.marchInit = 'true';
      }
      return currentWorld;
    };
    world = initWorldStyles();
    
    // Stop the standard GSAP opacity transitions
    if (!document.getElementById('reci-march-styles')) {
      const style = document.createElement('style');
      style.id = 'reci-march-styles';
      style.textContent = '.reci-stage { opacity: 1 !important; visibility: visible !important; pointer-events: auto !important; position: relative !important; flex-shrink: 0 !important; width: 100vw !important; max-height: 100vh !important; overflow-y: auto !important; overflow-x: hidden !important; }';
      document.head.appendChild(style);
    }
    
    // Re-init if React re-renders the container
    const observer = new MutationObserver(() => {
      world = initWorldStyles();
    });
    observer.observe(document.body, { childList: true, subtree: true });
  }
  let controller = null;
  const stages = ['march-title', 'march-context', 'march-selma', 'march-1968', 'march-2020', 'march-crowd', 'march-reflect'];

  if (!isNewSystem) {
    const transitions = {};
    for (const from of stages) {
      for (const to of stages) {
        transitions[`${from}->${to}`] = ({ to }) => {
          const index = stages.indexOf(to);
          if (index >= 0 && world) {
            world.style.transform = `translateX(-${index * window.innerWidth}px)`;
          }
        };
      }
    }

    controller = api.createStageController({
      stages,
      initial: 'march-title',
      manualVisibility: true,
      backButton: backBtn,
      displays: Object.fromEntries(stages.map((id) => [id, 'block'])),
      transitions,
    });
  }

  function marchToReveal(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return;
    let count = Number(section.dataset.count || 0) + 1;
    section.dataset.count = String(count);
    const footprints = section.querySelectorAll('[data-footprint]');
    if (footprints[count - 1]) {
      // Brighten via the .active state in the march CSS (.mini-fp.active { opacity: 1 }).
      // Toggling Tailwind opacity utilities loses to the base .mini-fp rule on source
      // order, so .active (higher specificity) is what actually brightens the footprint.
      footprints[count - 1].classList.remove('opacity-20');
      footprints[count - 1].classList.add('active');
    }
    if (count >= 3) {
      section.querySelector('[data-march-step-layer]')?.classList.add('opacity-0', 'pointer-events-none');
      const card = section.querySelector('[data-march-card]');
      card?.classList.remove('invisible', 'opacity-0');
      card?.classList.add('visible', 'opacity-100');
    }
  }

  document.addEventListener('click', (event) => {
    const target = event.target.closest('[data-stage-target]');
    if (target) {
      event.preventDefault();
      if (!isNewSystem && controller) controller.goTo(target.getAttribute('data-stage-target') || '');
      return;
    }
    const stepButton = event.target.closest('[data-march-step]');
    if (stepButton) {
      event.preventDefault();
      const section = stepButton.closest('section');
      if (section?.id) marchToReveal(section.id);
      return;
    }
    if (event.target === completeBtn && completeBtn instanceof HTMLElement) {
      const href = completeBtn.dataset.completeHref;
      if (href) window.location.href = href;
    }
  });

  if (!isNewSystem && controller) {
    startBtn?.addEventListener('click', (e) => { e.preventDefault(); controller.goTo('march-context'); });
    contextBtn?.addEventListener('click', (e) => { e.preventDefault(); controller.goTo('march-selma'); });
    backBtn?.addEventListener('click', (e) => { e.preventDefault(); controller.back(); });
    controller.init();
  }

  document.addEventListener('reci:stage:change', (event) => {
    const detail = event.detail || {};
    if (isNewSystem && world) {
      const allStages = Array.from(document.querySelectorAll('.reci-stage'));
      const index = allStages.findIndex(s => s.id === detail.to);
      if (index >= 0) {
        world.style.transform = `translateX(-${index * window.innerWidth}px)`;
      }
    }
  });

  playBtn?.addEventListener('click', () => {
    if (!audio || !playBtn) return;
    if (audio.paused) {
      audio.play();
      playBtn.textContent = '||';
    } else {
      audio.pause();
      playBtn.textContent = '▶';
    }
  });

  let lastWheelAt = 0;
  window.addEventListener('wheel', (event) => {
    if (!controller) return;
    const now = Date.now();
    if (now - lastWheelAt < 700) return;
    const current = controller.current();
    const index = stages.indexOf(current);
    if (event.deltaY > 8 && index >= 0 && index < stages.length - 1) {
      lastWheelAt = now;
      controller.goTo(stages[index + 1]);
    } else if (event.deltaY < -8 && index > 0) {
      lastWheelAt = now;
      controller.goTo(stages[index - 1]);
    }
  }, { passive: true });

  window.addEventListener('resize', () => {
    if (isNewSystem || !controller) return;
    const index = stages.indexOf(controller.current());
    if (index >= 0 && world) {
      world.style.transform = `translateX(-${index * window.innerWidth}px)`;
    }
  });

  document.addEventListener('mousemove', (e) => {
    if (isNewSystem || !controller) return;
    const current = controller.current();
    if (current !== 'march-crowd') return;
    const x = e.clientX / window.innerWidth;
    if (layers[0]) layers[0].style.transform = `translateX(${x * 20}px) scale(1.1)`;
    if (layers[1]) layers[1].style.transform = `translateX(-${x * 40}px)`;
  });

  if (!isNewSystem && controller) {
    controller.init();
  }
})();
