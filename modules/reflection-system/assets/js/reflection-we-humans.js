(() => {
  const api = window.RECIReflection;
  if (!api || typeof api.createStageController !== 'function') return;

  const panelAnnotations = {
    'We Humans - Case I, Panel A': [
      { x: 22, y: 24, title: 'Opening proposition', body: 'This opening panel uses confident exhibit language to establish authority quickly. The rhetorical style is punchy and public-facing, consistent with the curator’s description of “We Humans” as an educational intervention aimed at workers, students, and citizens.' },
      { x: 68, y: 63, title: 'Scientific framing', body: 'A recurring move in the exhibit is to use the language of science to confront prejudice. The narrative asks visitors to hold onto both the progressive ambition of that move and the fact that the science itself was still tied to racial categorization that later anthropology rejected.' }
    ],
    'We Humans - Case I, Panel B': [
      { x: 28, y: 34, title: 'Instructional voice', body: 'The panel reads less like a neutral display and more like a lesson. That educational tone was central to how “We Humans” circulated in schools and public institutions.' },
      { x: 73, y: 72, title: 'Public persuasion', body: 'Notice how the panel tries to persuade rather than merely describe. This aligns with the exhibit’s broader role in postwar anti-racist public education.' }
    ],
    'We Humans - Case II, Panel A': [
      { x: 31, y: 28, title: 'Classification logic', body: 'This part of the exhibit reflects the mid-century effort to argue for human equality while still speaking in terms of racial groups. That contradiction is one of the core interpretive tensions named in the curatorial narrative.' },
      { x: 63, y: 58, title: 'Visual evidence', body: 'The display uses visual comparison to make an argument about people, difference, and similarity. The museum setting gave those comparisons an additional layer of authority.' }
    ],
    'We Humans - Case II, Panel B': [
      { x: 21, y: 66, title: 'Museum authority', body: '“We Humans” was shaped by anthropology curators working inside a museum. The exhibit’s persuasive force depended partly on that institutional setting and on the authority visitors attached to it.' },
      { x: 76, y: 33, title: 'Race as lesson', body: 'This panel exemplifies how the exhibit translated anthropological and civic ideas about race into a teachable public format.' }
    ],
    'We Humans - Case III, Panel A': [
      { x: 24, y: 36, title: 'Ethnocentrism and prejudice', body: 'The newspaper responses included in the exhibit narrative show that anti-ethnocentric messaging could provoke backlash. Read this panel with that public friction in mind.' },
      { x: 70, y: 71, title: 'From concept to audience', body: 'The exhibit was designed to reach broad audiences, not specialists. The combination of short statements, graphics, and comparisons reflects that public-facing design logic.' }
    ],
    'We Humans - Case III, Panel B': [
      { x: 33, y: 25, title: 'Portable pedagogy', body: 'When the exhibit moved into schools, panels like this became teaching tools. The portable version and booklet were meant to carry the message beyond the original downtown display.' },
      { x: 61, y: 61, title: 'Civic education', body: 'The panel belongs to a larger project of civic instruction. It asks not only what race is, but how citizens should understand and respond to prejudice.' }
    ],
    'We Humans - Case IV, Panel A': [
      { x: 29, y: 30, title: 'Intent versus impact', body: 'This closing movement in the exhibit can be read alongside the curatorial prompt about intent versus impact. The ambition to challenge racism sits uneasily beside the outdated scientific framework used to do so.' },
      { x: 72, y: 68, title: 'Historical distance', body: 'The panel invites a historical reading: what felt persuasive or progressive in the 1950s may now read very differently. That shift is central to the exhibit’s contemporary relevance.' }
    ],
    'We Humans - Case IV, Panel B': [
      { x: 25, y: 54, title: 'Exhibit legacy', body: 'This final panel is best read as part of the exhibit’s afterlife. The narrative asks what “We Humans” can still teach today about the possibilities and limitations of anti-racist public education.' },
      { x: 76, y: 26, title: 'Reflective exit', body: 'Rather than offering a simple verdict, the exhibit encourages reflection. That open-endedness is what makes it legible as both historical document and prompt for present-day thought.' }
    ]
  };

  const reflectionConfig = window.RECIReflectionConfig || {};
  const menuToggle = document.getElementById('menuToggle');
  const menuOverlay = document.getElementById('menuOverlay');
  const lightbox = document.getElementById('lightbox');
  const lightboxImage = document.getElementById('lightboxImage');
  const lightboxClose = document.getElementById('lightboxClose');
  const hotspotLayer = document.getElementById('hotspotLayer');
  const lightboxTitle = document.getElementById('lightboxTitle');
  const annotationTitle = document.getElementById('annotationTitle');
  const annotationBody = document.getElementById('annotationBody');
  const annotationList = document.getElementById('annotationList');
  const lightboxIntro = document.getElementById('lightboxIntro');
  const responseGate = document.getElementById('responseGate');
  const responseFormShell = document.getElementById('responseFormShell');
  const responseStatus = document.getElementById('responseStatus');
  const responseList = document.getElementById('responseList');
  const responseInput = document.getElementById('reflectionResponse');
  const saveButton = document.getElementById('saveResponseBtn');
  const timelineWorld = document.getElementById('timelineWorld');
  const timelinePrev = document.getElementById('timelinePrev');
  const timelineNext = document.getElementById('timelineNext');
  const holdReveal = document.getElementById('vorHoldReveal');
  const holdContinue = document.getElementById('vorHoldContinue');
  const holdParagraphs = Array.from(document.querySelectorAll('.vor-hold-paragraph'));
  const promptText = 'What will people in 70 years say about the anti-racist education and initiatives of your moment?';
  let activePanelKey = '';
  let timelineIndex = 0;
  let revealIndex = 0;
  const timelineCount = timelineWorld ? timelineWorld.querySelectorAll('[data-timeline-index]').length : 0;
  const stages = ['wh-hero', 'wh-setup-1', 'wh-setup-2', 'wh-menu', 'wh-origins', 'wh-timeline', 'wh-panels', 'wh-reflection', 'wh-about'];

  const controller = api.createStageController({
    stages,
    initial: 'wh-hero',
    displays: Object.fromEntries(stages.map((id) => [id, 'block'])),
  });

  function escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function syncMenuState() {
    document.querySelectorAll('[data-stage-target]').forEach((trigger) => {
      if (!trigger.closest('.reci-stage-menu')) return;
      trigger.classList.toggle('active', trigger.dataset.stageTarget === controller.current());
    });
  }

  function updateTimeline() {
    if (!timelineWorld) return;
    timelineWorld.style.transform = `translateX(-${timelineIndex * 100}vw)`;
    if (timelinePrev) timelinePrev.disabled = timelineIndex === 0;
    if (timelineNext) timelineNext.disabled = timelineIndex >= timelineCount - 1;
  }

  function renderAnnotation(panelKey, annotationIndex = 0) {
    const notes = panelAnnotations[panelKey] || [];
    const note = notes[annotationIndex];
    if (!note) {
      annotationTitle.textContent = 'No annotation';
      annotationBody.textContent = 'No annotation is available for this panel yet.';
      annotationList.innerHTML = '';
      hotspotLayer.innerHTML = '';
      return;
    }
    annotationTitle.textContent = note.title;
    annotationBody.textContent = note.body;
    annotationList.innerHTML = notes.map((item, index) => `
      <button type="button" class="annotation-chip w-full rounded-[14px] border px-4 py-3 text-left text-sm transition ${index === annotationIndex ? 'active' : ''}" style="border-color:${index === annotationIndex ? 'var(--reflection-accent)' : 'var(--reflection-border)'}; background:${index === annotationIndex ? 'rgba(167, 199, 150, 0.18)' : 'var(--reflection-card)'}; color:var(--reflection-text);" data-annotation-index="${index}">
        ${index + 1}. ${item.title}
      </button>
    `).join('');
    hotspotLayer.innerHTML = notes.map((item, index) => `
      <button type="button" class="panel-hotspot ${index === annotationIndex ? 'active' : ''}" data-annotation-index="${index}" style="left:${item.x}%; top:${item.y}%;">${index + 1}</button>
    `).join('');
  }

  function closeLightbox() {
    lightbox.classList.remove('active');
    hotspotLayer.innerHTML = '';
    annotationList.innerHTML = '';
    activePanelKey = '';
  }

  function revealNextParagraph() {
    const paragraph = holdParagraphs[revealIndex];
    if (!paragraph) {
      if (holdContinue) holdContinue.classList.remove('hidden');
      return;
    }
    paragraph.classList.remove('hidden', 'translate-y-5', 'opacity-0');
    paragraph.classList.add('translate-y-0', 'opacity-100');
    revealIndex += 1;
    if (revealIndex >= holdParagraphs.length && holdContinue) {
      holdContinue.classList.remove('hidden');
    }
  }

  document.addEventListener('click', (event) => {
    const stageTrigger = event.target.closest('[data-stage-target]');
    if (stageTrigger) {
      event.preventDefault();
      const target = stageTrigger.dataset.stageTarget;
      if (target) {
        controller.goTo(target);
        window.setTimeout(() => {
          window.scrollTo({ top: 0, behavior: 'instant' });
          syncMenuState();
        }, 20);
        menuOverlay?.classList.remove('active');
      }
      return;
    }

    const annotationTrigger = event.target.closest('[data-annotation-index]');
    if (annotationTrigger && activePanelKey) {
      renderAnnotation(activePanelKey, Number(annotationTrigger.dataset.annotationIndex || 0));
    }
  });

  menuToggle?.addEventListener('click', () => menuOverlay?.classList.toggle('active'));
  menuOverlay?.addEventListener('click', (event) => {
    if (event.target === menuOverlay) menuOverlay.classList.remove('active');
  });

  holdReveal?.addEventListener('click', revealNextParagraph);

  timelinePrev?.addEventListener('click', () => {
    timelineIndex = Math.max(0, timelineIndex - 1);
    updateTimeline();
  });
  timelineNext?.addEventListener('click', () => {
    timelineIndex = Math.min(timelineCount - 1, timelineIndex + 1);
    updateTimeline();
  });
  timelineWorld?.addEventListener('wheel', (event) => {
    if (controller.current() !== 'wh-timeline') return;
    event.preventDefault();
    if (event.deltaY > 0 || event.deltaX > 0) {
      timelineIndex = Math.min(timelineCount - 1, timelineIndex + 1);
    } else {
      timelineIndex = Math.max(0, timelineIndex - 1);
    }
    updateTimeline();
  }, { passive: false });

  document.querySelectorAll('.panel-image').forEach((img) => {
    img.addEventListener('click', () => {
      activePanelKey = img.alt;
      lightboxImage.src = img.src;
      lightboxImage.alt = img.alt;
      lightboxTitle.textContent = img.alt;
      lightboxIntro.textContent = 'Select an annotation point or note to read a guided comment on this panel.';
      renderAnnotation(activePanelKey, 0);
      lightbox.classList.add('active');
    });
  });

  lightboxClose?.addEventListener('click', closeLightbox);
  lightbox?.addEventListener('click', (event) => {
    if (event.target === lightbox) closeLightbox();
  });

  async function loadResponses() {
    if (!reflectionConfig.isLoggedIn || !reflectionConfig.restUrl || !reflectionConfig.reflectionId) {
      if (responseList) {
        responseList.innerHTML = '<div class="rounded-[18px] px-4 py-4 text-sm" style="background: var(--reflection-card); color: var(--reflection-soft-text);">Log in to save and review your reflections.</div>';
      }
      return;
    }
    const url = new URL(reflectionConfig.restUrl);
    url.searchParams.set('reflection_id', reflectionConfig.reflectionId);
    const res = await fetch(url.toString(), {
      credentials: 'same-origin',
      headers: { 'X-WP-Nonce': reflectionConfig.nonce }
    });
    const data = await res.json();
    const items = Array.isArray(data.items) ? data.items : [];
    if (!items.length) {
      responseList.innerHTML = '<div class="rounded-[18px] px-4 py-4 text-sm" style="background: var(--reflection-card); color: var(--reflection-soft-text);">No saved responses yet for this exhibit.</div>';
      return;
    }
    responseList.innerHTML = items.map((item) => `
      <article class="rounded-[18px] border p-4" style="border-color: var(--reflection-border-soft); background: var(--reflection-surface);">
        <strong class="block" style="color: var(--reflection-text);">${escapeHtml(item.prompt)}</strong>
        <p class="mt-3 text-sm leading-7" style="color: var(--reflection-soft-text);">${escapeHtml(item.raw_response)}</p>
        <time class="mt-3 block text-xs" style="color: var(--reflection-muted);" datetime="${item.created_at}">${new Date(item.created_at).toLocaleString()}</time>
      </article>
    `).join('');
  }

  if (!reflectionConfig.isLoggedIn) {
    if (responseGate) responseGate.style.display = 'block';
    if (responseFormShell) responseFormShell.style.display = 'none';
  }

  saveButton?.addEventListener('click', async () => {
    const response = responseInput.value.trim();
    if (!response) {
      responseStatus.textContent = 'Write a response before saving.';
      responseStatus.style.display = 'block';
      return;
    }
    saveButton.disabled = true;
    responseStatus.textContent = 'Saving...';
    responseStatus.style.display = 'block';
    try {
      const res = await fetch(reflectionConfig.restUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce': reflectionConfig.nonce,
        },
        body: JSON.stringify({
          reflection_id: reflectionConfig.reflectionId,
          prompt: promptText,
          response,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Unable to save your response.');
      responseInput.value = '';
      responseStatus.textContent = 'Response saved to your account.';
      await loadResponses();
    } catch (error) {
      responseStatus.textContent = error.message || 'Something went wrong.';
    } finally {
      saveButton.disabled = false;
    }
  });

  document.addEventListener('reci:stage:change', () => {
    syncMenuState();
    window.scrollTo({ top: 0, behavior: 'instant' });
  });

  controller.init();
  syncMenuState();
  updateTimeline();
  loadResponses();
})();
