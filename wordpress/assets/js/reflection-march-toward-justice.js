(() => {
  const api = window.RECIReflection;
  if (!api || typeof api.createStageController !== 'function') return;

  const audio = document.getElementById('marchAudio');
  const playBtn = document.getElementById('marchPlayBtn');
  const world = document.getElementById('marchWorld');
  const backBtn = document.getElementById('marchStageBack');
  const completeBtn = document.getElementById('marchCompleteBtn');
  const startBtn = document.getElementById('marchStartBtn');
  const contextBtn = document.getElementById('marchContextBtn');
  const layers = [document.querySelector('#march-crowd [data-parallax-layer="0"]'), document.querySelector('#march-crowd [data-parallax-layer="1"]')];

  const stages = ['march-title', 'march-context', 'march-selma', 'march-1968', 'march-2020', 'march-crowd', 'march-reflect'];
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

  const controller = api.createStageController({
    stages,
    initial: 'march-title',
    manualVisibility: true,
    backButton: backBtn,
    displays: Object.fromEntries(stages.map((id) => [id, 'block'])),
    transitions,
  });

  function marchToReveal(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return;
    let count = Number(section.dataset.count || 0) + 1;
    section.dataset.count = String(count);
    const footprints = section.querySelectorAll('[data-footprint]');
    if (footprints[count - 1]) footprints[count - 1].classList.add('text-[var(--reflection-accent)]', 'opacity-100');
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
      controller.goTo(target.getAttribute('data-stage-target') || '');
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

  backBtn?.addEventListener('click', (event) => {
    event.preventDefault();
    controller.back();
  });

  startBtn?.addEventListener('click', () => controller.goTo('march-context'));
  contextBtn?.addEventListener('click', () => controller.goTo('march-selma'));

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
    const index = stages.indexOf(controller.current());
    if (index >= 0 && world) {
      world.style.transform = `translateX(-${index * window.innerWidth}px)`;
    }
  });

  document.addEventListener('mousemove', (e) => {
    const current = controller.current();
    if (current !== 'march-crowd') return;
    const x = e.clientX / window.innerWidth;
    if (layers[0]) layers[0].style.transform = `translateX(${x * 20}px) scale(1.1)`;
    if (layers[1]) layers[1].style.transform = `translateX(-${x * 40}px)`;
  });

  controller.init();
})();
