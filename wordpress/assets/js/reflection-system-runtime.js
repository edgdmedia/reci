(() => {
  function byId(id) {
    return id ? document.getElementById(id) : null;
  }

  function initStageFlow() {
    if (!window.RECIReflection || typeof window.RECIReflection.createStageController !== 'function') {
      return null;
    }

    const stageNodes = Array.from(document.querySelectorAll('.reci-stage[id]'));
    if (!stageNodes.length) {
      return null;
    }

    const backButton = byId('reciSystemBack');
    const controller = window.RECIReflection.createStageController({
      stages: stageNodes.map((node) => node.id),
      initial: stageNodes[0].id,
      backButton,
      duration: 450,
      displays: Object.fromEntries(stageNodes.map((node) => [node.id, 'block'])),
    });

    controller.init();

    document.addEventListener('click', (event) => {
      const trigger = event.target.closest('[data-stage-target]');
      if (!trigger) return;
      event.preventDefault();
      const target = trigger.getAttribute('data-stage-target') || '';
      controller.goTo(target);
    });

    backButton?.addEventListener('click', (event) => {
      event.preventDefault();
      controller.back();
    });

    return controller;
  }

  function initMenuOverlay() {
    const menuToggle = byId('menuToggle');
    const menuOverlay = byId('menuOverlay');
    if (!menuToggle || !menuOverlay) return;

    menuToggle.addEventListener('click', () => {
      menuOverlay.classList.toggle('active');
    });

    menuOverlay.addEventListener('click', (event) => {
      if (event.target === menuOverlay || event.target.closest('a')) {
        menuOverlay.classList.remove('active');
      }
    });
  }

  function initTimelineWorld() {
    const world = byId('timelineWorld');
    const prev = byId('timelinePrev');
    const next = byId('timelineNext');
    if (!world || !prev || !next) return;

    const panels = Array.from(world.querySelectorAll('[data-timeline-index]'));
    if (!panels.length) return;

    let index = 0;
    function paint() {
      world.style.transform = `translateX(${index * -100}vw)`;
      prev.disabled = index === 0;
      next.disabled = index === panels.length - 1;
    }

    prev.addEventListener('click', () => {
      index = Math.max(0, index - 1);
      paint();
    });

    next.addEventListener('click', () => {
      index = Math.min(panels.length - 1, index + 1);
      paint();
    });

    paint();
  }

  function initLightbox() {
    const lightbox = byId('lightbox');
    const image = byId('lightboxImage');
    const close = byId('lightboxClose');
    const title = byId('lightboxTitle');
    const intro = byId('lightboxIntro');
    const annotationTitle = byId('annotationTitle');
    const annotationBody = byId('annotationBody');
    const annotationList = byId('annotationList');
    const hotspotLayer = byId('hotspotLayer');
    if (!lightbox || !image || !close) return;

    document.querySelectorAll('.panel-image').forEach((panel) => {
      panel.addEventListener('click', () => {
        image.src = panel.getAttribute('src') || '';
        image.alt = panel.getAttribute('alt') || '';
        if (title) title.textContent = panel.getAttribute('alt') || 'Panel reader';
        if (intro) intro.textContent = 'Panel enlarged for closer reading.';
        if (annotationTitle) annotationTitle.textContent = 'Panel note';
        if (annotationBody) annotationBody.textContent = 'Annotations can be attached to this panel style.';
        if (annotationList) annotationList.innerHTML = '';
        if (hotspotLayer) hotspotLayer.innerHTML = '';
        lightbox.classList.add('active');
      });
    });

    function closeLightbox() {
      lightbox.classList.remove('active');
    }

    close.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (event) => {
      if (event.target === lightbox) closeLightbox();
    });
  }

  function initResponses() {
    const config = window.RECIReflectionConfig || {};
    const gate = byId('responseGate');
    const formShell = byId('responseFormShell');
    const status = byId('responseStatus');
    const responseList = byId('responseList');
    const responseInput = byId('reflectionResponse');
    const saveButton = byId('saveResponseBtn');
    const promptTextNode = document.querySelector('#responseFormShell')?.previousElementSibling;
    const promptText = promptTextNode ? promptTextNode.textContent.replace(/^Prompt:\s*/, '').trim() : '';
    if (!responseList) return;

    function escapeHtml(value) {
      return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }

    async function loadResponses() {
      if (!config.isLoggedIn || !config.restUrl || !config.reflectionId) {
        responseList.innerHTML = '<div class="rounded-[18px] bg-[var(--reflection-card)] px-4 py-4 text-sm text-[var(--reflection-soft-text)]">Log in to save and review your reflections.</div>';
        return;
      }
      const url = new URL(config.restUrl);
      url.searchParams.set('reflection_id', config.reflectionId);
      const res = await fetch(url.toString(), {
        credentials: 'same-origin',
        headers: { 'X-WP-Nonce': config.nonce }
      });
      const data = await res.json();
      const items = Array.isArray(data.items) ? data.items : [];
      if (!items.length) {
        responseList.innerHTML = '<div class="rounded-[18px] bg-[var(--reflection-card)] px-4 py-4 text-sm text-[var(--reflection-soft-text)]">No saved responses yet for this reflection.</div>';
        return;
      }
      responseList.innerHTML = items.map((item) => `
        <article class="rounded-[18px] border border-[color:var(--reflection-border)] bg-[var(--reflection-card)] p-4">
          <strong>${escapeHtml(item.prompt)}</strong>
          <p class="mt-2 text-sm leading-7 text-[var(--reflection-soft-text)]">${escapeHtml(item.raw_response)}</p>
          <time class="mt-3 block text-xs text-[var(--reflection-muted)]" datetime="${item.created_at}">${new Date(item.created_at).toLocaleString()}</time>
        </article>
      `).join('');
    }

    if (!config.isLoggedIn) {
      if (gate) gate.style.display = 'block';
      if (formShell) formShell.style.display = 'none';
    }

    saveButton?.addEventListener('click', async () => {
      const response = (responseInput?.value || '').trim();
      if (!response || !config.restUrl) {
        if (status) {
          status.textContent = 'Write a response before saving.';
          status.style.display = 'block';
        }
        return;
      }
      saveButton.disabled = true;
      if (status) {
        status.textContent = 'Saving...';
        status.style.display = 'block';
      }
      try {
        const res = await fetch(config.restUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': config.nonce,
          },
          body: JSON.stringify({
            reflection_id: config.reflectionId,
            prompt: promptText,
            response,
          }),
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Unable to save your response.');
        if (responseInput) responseInput.value = '';
        if (status) status.textContent = 'Response saved to your account.';
        await loadResponses();
      } catch (error) {
        if (status) status.textContent = error.message || 'Something went wrong.';
      } finally {
        saveButton.disabled = false;
      }
    });

    loadResponses();
  }

  document.addEventListener('DOMContentLoaded', () => {
    initStageFlow();
    initMenuOverlay();
    initTimelineWorld();
    initLightbox();
    initResponses();
  });
})();
