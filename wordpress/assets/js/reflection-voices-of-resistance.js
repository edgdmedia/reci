(() => {
  const api = window.RECIReflection;
  if (!api || typeof api.createStageController !== 'function') return;

  const audio = document.getElementById('bgAudio');
  const detailPanel = document.getElementById('detailPanel');
  const dTitle = document.getElementById('dTitle');
  const dText = document.getElementById('dText');
  const detailClose = document.getElementById('detailClose');
  const exploreContinue = document.getElementById('exploreContinue');
  const holdRevealBtn = document.getElementById('holdRevealBtn');
  const holdParagraphs = Array.from(document.querySelectorAll('.hold-paragraph'));
  const toThresholdBtn = document.getElementById('toThresholdBtn');
  const marchTrack = document.getElementById('marchTrack');
  const vorThresholdText = document.getElementById('vorThresholdText');
  const vorCompleteBtn = document.getElementById('vorCompleteBtn');
  const backBtn = document.getElementById('reflectionStageBack');

  const details = {
    strategy: { title: 'The Strategy', text: 'We gathered in the church basement not just to pray, but to plan. Every route was mapped, every risk calculated.' },
    resolve: { title: 'The Resolve', text: "Standing tall wasn't just physical. It was a reclaiming of space we were told we couldn't occupy." },
    legacy: { title: 'The Community', text: 'Young and old, doctors and janitors. In this room, titles dissolved. We were simply one people.' },
  };

  let pIndex = 0;
  let marchPanelIndex = 0;
  let marchWheelBound = false;

  const controller = api.createStageController({
    stages: ['s-intro', 's-explore', 's-hold', 's-threshold', 's-march', 's-reflect'],
    initial: 's-intro',
    displays: {
      's-intro': 'flex',
      's-explore': 'block',
      's-hold': 'flex',
      's-threshold': 'flex',
      's-march': 'block',
      's-reflect': 'flex',
    },
    aliases: {
      explore: 's-explore',
      hold: 's-hold',
      threshold: 's-threshold',
      march: 's-march',
      reflect: 's-reflect',
    },
    backButton: backBtn,
    transitions: {
      's-intro->s-explore': async ({ fromStage, to, show, duration }) => {
        if (!fromStage) {
          show(to);
          return;
        }
        show(to);
        fromStage.style.pointerEvents = 'none';
        fromStage.style.transition = 'opacity 700ms ease';
        requestAnimationFrame(() => {
          fromStage.style.opacity = '0';
        });
        await new Promise((resolve) => {
          window.setTimeout(() => {
            fromStage.style.display = 'none';
            fromStage.style.visibility = 'hidden';
            resolve();
          }, Math.min(duration, 700));
        });
      },
    },
  });

  function showDetail(key) {
    const entry = details[key];
    if (!entry || !detailPanel || !dTitle || !dText) return;
    dTitle.innerText = entry.title;
    dText.innerText = entry.text;
    detailPanel.classList.add('visible');
    exploreContinue?.classList.add('visible');
  }

  function closeDetail() {
    detailPanel?.classList.remove('visible');
  }

  function revealNextParagraph() {
    if (pIndex >= holdParagraphs.length) return;
    holdParagraphs[pIndex].classList.add('visible');
    pIndex += 1;
    if (pIndex === holdParagraphs.length && toThresholdBtn) {
      toThresholdBtn.style.display = 'inline-flex';
    }
  }

  function updateMarchPosition() {
    if (!marchTrack) return;
    marchTrack.style.transform = `translateX(-${marchPanelIndex * 100}vw)`;
  }

  function initMarchScroll() {
    if (marchWheelBound) return;
    marchWheelBound = true;
    window.addEventListener('wheel', (event) => {
      const current = controller.current();
      if (current !== 's-march' || !marchTrack) return;
      if (event.deltaY !== 0) {
        event.preventDefault();
        const maxIndex = Math.max(0, marchTrack.children.length - 1);
        if (event.deltaY > 0) {
          marchPanelIndex = Math.min(maxIndex, marchPanelIndex + 1);
        } else {
          marchPanelIndex = Math.max(0, marchPanelIndex - 1);
        }
        updateMarchPosition();
      }
    }, { passive: false });
  }

  document.addEventListener('click', (event) => {
    const detailTrigger = event.target.closest('[data-detail-key]');
    if (detailTrigger) {
      showDetail(detailTrigger.getAttribute('data-detail-key') || '');
      return;
    }

    const stageTarget = event.target.closest('[data-stage-target]');
    if (stageTarget) {
      event.preventDefault();
      const target = stageTarget.getAttribute('data-stage-target');
      if (target === 'explore') {
        audio?.play?.().catch(() => {});
      }
      controller.goTo(target || '');
      return;
    }

    if (event.target === detailClose) {
      closeDetail();
      return;
    }

    if (event.target === vorCompleteBtn && vorCompleteBtn instanceof HTMLElement) {
      const href = vorCompleteBtn.dataset.completeHref;
      if (href) window.location.href = href;
    }
  });

  holdRevealBtn?.addEventListener('click', revealNextParagraph);
  exploreContinue?.addEventListener('click', (event) => {
    event.preventDefault();
    controller.goTo('hold');
  });
  backBtn?.addEventListener('click', (event) => {
    event.preventDefault();
    controller.back();
  });

  document.addEventListener('reci:stage:change', (event) => {
    const detail = event.detail || {};
    if (detail.to === 's-threshold') {
      document.getElementById('s-threshold')?.classList.add('active');
      vorThresholdText?.classList.add('active');
    }
    if (detail.to === 's-march') {
      initMarchScroll();
      updateMarchPosition();
    }
    if (detail.to !== 's-explore') {
      closeDetail();
      exploreContinue?.classList.remove('visible');
    }
  });

  controller.init();
})();
