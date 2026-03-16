(() => {
  const audio = document.getElementById('rdAudio');
  const playBtn = document.getElementById('rdPlayBtn');
  const backButton = document.getElementById('rdStageBack');
  const enterButton = document.getElementById('rdEnterStory');

  const api = window.RECIReflection;
  if (!api || typeof api.createStageController !== 'function') return;

  const controller = api.createStageController({
    stages: ['rd-hero', 'rd-analysis'],
    initial: 'rd-hero',
    backButton,
  });

  function updateBackState() {
    if (!backButton) return;
    backButton.classList.toggle('hidden', controller.current() === 'rd-hero');
  }

  controller.init();
  updateBackState();

  enterButton?.addEventListener('click', () => {
    controller.goTo('rd-analysis');
    window.setTimeout(updateBackState, 20);
  });

  backButton?.addEventListener('click', (event) => {
    event.preventDefault();
    controller.back();
    window.setTimeout(updateBackState, 20);
  });

  document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-toggle-solution]');
    if (toggle) {
      event.stopPropagation();
      const card = toggle.closest('[data-data-card]');
      const problem = card?.querySelector('.rd-view-problem');
      const solution = card?.querySelector('.rd-view-solution');
      if (!card || !problem || !solution) return;
      const showingProblem = !problem.classList.contains('hidden');
      problem.classList.toggle('hidden', showingProblem);
      solution.classList.toggle('hidden', !showingProblem);
      toggle.textContent = showingProblem ? 'View Data' : 'View Solution';
      toggle.classList.toggle('bg-[var(--reflection-accent)]', showingProblem);
      toggle.classList.toggle('text-white', showingProblem);
      toggle.classList.toggle('bg-black/5', !showingProblem);
      toggle.classList.toggle('text-black/60', !showingProblem);
      return;
    }

    const card = event.target.closest('[data-data-card]');
    if (!card) return;
    document.querySelectorAll('[data-data-card]').forEach((node) => {
      node.classList.remove('active', 'bg-[#111]', 'text-white', 'md:col-span-2');
      node.querySelector('.rd-card-detail')?.classList.add('hidden');
      node.querySelector('.rd-view-problem')?.classList.remove('hidden');
      node.querySelector('.rd-view-solution')?.classList.add('hidden');
      const btn = node.querySelector('[data-toggle-solution]');
      if (btn) {
        btn.textContent = 'View Solution';
        btn.classList.remove('bg-[var(--reflection-accent)]', 'text-white');
        btn.classList.add('bg-black/5', 'text-black/60');
      }
    });
    card.classList.add('active', 'bg-[#111]', 'text-white', 'md:col-span-2');
    card.querySelector('.rd-card-detail')?.classList.remove('hidden');
  });

  window.reciToggleRacialAudio = () => {
    if (!audio || !playBtn) return;
    if (audio.paused) {
      audio.play();
      playBtn.textContent = '||';
    } else {
      audio.pause();
      playBtn.textContent = '▶';
    }
  };
})();
