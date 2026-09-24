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
      displays: Object.fromEntries(stageNodes.map((node) => {
        const isFlex = node.classList.contains('flex') || 
                       Array.from(node.classList).some(cls => cls.startsWith('chapter-'));
        return [node.id, isFlex ? 'flex' : 'block'];
      })),
    });

    controller.init();

    document.addEventListener('click', (event) => {
      const trigger = event.target.closest('[data-stage-target]');
      if (!trigger) return;
      event.preventDefault();
      let target = trigger.getAttribute('data-stage-target') || '';
      
      // The render service always prefixes a continue target with "stage-",
      // but only prefixes a chapter id when the author left it blank. So a
      // target naming an author-set id never matches it literally. Try both
      // spellings before falling back.
      if (target && !document.getElementById(target)) {
        const alt = target.startsWith('stage-') ? target.slice(6) : 'stage-' + target;
        if (document.getElementById(alt)) target = alt;
      }

      // If the target is empty or broken, auto-advance to the next chronological chapter
      if (!target || !document.getElementById(target)) {
        const currentStage = trigger.closest('.reci-stage');
        if (currentStage) {
          const currentIndex = stageNodes.findIndex(node => node === currentStage);
          if (currentIndex !== -1 && currentIndex < stageNodes.length - 1) {
            target = stageNodes[currentIndex + 1].id;
          } else {
            target = '';
          }
        }
      }
      
      if (target) controller.goTo(target);
    });

    backButton?.addEventListener('click', (event) => {
      event.preventDefault();
      controller.back();
    });

    window.RECIReflectionController = controller;
    
    let isTransitioning = false;
    document.addEventListener('reci:stage:change', () => {
      isTransitioning = true;
      setTimeout(() => isTransitioning = false, 800);
    });

    function handleScrollAdvance(direction) {
      if (isTransitioning || !controller.current) return;
      const currentId = controller.current();
      if (!currentId) return;
      const currentStage = document.getElementById(currentId);
      if (!currentStage) return;

      const mode = currentStage.dataset.transitionMode || 'button';
      if (mode !== 'scroll') return;

      if (direction > 0) {
        if (window.innerHeight + window.scrollY < document.documentElement.scrollHeight - 10) return;
        let target = currentStage.dataset.continueTarget;
        
        if (!target || !document.getElementById(target)) {
          const currentIndex = stageNodes.findIndex(node => node === currentStage);
          if (currentIndex !== -1 && currentIndex < stageNodes.length - 1) {
            target = stageNodes[currentIndex + 1].id;
          } else {
            target = '';
          }
        }
        
        if (target) controller.goTo(target);
      } else if (direction < 0) {
        if (window.scrollY > 10) return;
        controller.back();
      }
    }

    let wheelAccumulator = 0;
    window.addEventListener('wheel', (e) => {
      wheelAccumulator += e.deltaY;
      if (wheelAccumulator > 100) {
        handleScrollAdvance(1);
        wheelAccumulator = 0;
      } else if (wheelAccumulator < -100) {
        handleScrollAdvance(-1);
        wheelAccumulator = 0;
      }
      setTimeout(() => { wheelAccumulator = 0; }, 200);
    }, { passive: true });

    let touchStartY = 0;
    window.addEventListener('touchstart', (e) => {
      touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    window.addEventListener('touchend', (e) => {
      if (!touchStartY) return;
      const touchEndY = e.changedTouches[0].clientY;
      const delta = touchStartY - touchEndY;
      if (delta > 50) handleScrollAdvance(1);
      else if (delta < -50) handleScrollAdvance(-1);
      touchStartY = 0;
    }, { passive: true });

    document.addEventListener('click', (event) => {
      const revealBtn = event.target.closest('.reci-progressive-reveal');
      if (revealBtn) {
        event.preventDefault();
        const container = revealBtn.closest('.reci-stage');
        if (!container) return;
        
        const paragraphs = Array.from(container.querySelectorAll('.reci-progressive-paragraph.hidden'));
        if (!paragraphs.length) return;
        
        const revealedParagraphs = Array.from(container.querySelectorAll('.reci-progressive-paragraph:not(.hidden)'));
        revealedParagraphs.forEach(p => p.classList.remove('progressive-latest'));
        
        const nextParagraph = paragraphs[0];
        nextParagraph.classList.remove('hidden');
        
        // Force the browser to recalculate layout so the fade-in and slide-up animations actually play
        void nextParagraph.offsetWidth;
        
        nextParagraph.classList.remove('translate-y-5', 'opacity-0');
        nextParagraph.classList.add('translate-y-0', 'opacity-100', 'progressive-latest');
        
        // Auto-scroll the container to the newly revealed paragraph
        setTimeout(() => {
          nextParagraph.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
        
        if (paragraphs.length === 1) {
          // Last paragraph was just revealed, hide controls and show continue button
          const controls = container.querySelector('.hold-controls');
          if (controls) {
            controls.style.transition = 'opacity 0.3s ease';
            controls.style.opacity = '0';
            setTimeout(() => controls.style.display = 'none', 300);
          }
          const continueBtn = container.querySelector('.reci-progressive-continue');
          if (continueBtn) {
            continueBtn.classList.remove('hidden');
          }
        }
      }

      const stepButton = event.target.closest('[data-march-step]');
      if (stepButton) {
        event.preventDefault();
        const section = stepButton.closest('.reci-stage, section');
        if (!section) return;
        let count = Number(section.dataset.count || 0) + 1;
        section.dataset.count = String(count);
        const footprints = section.querySelectorAll('[data-footprint]');
        if (footprints[count - 1]) {
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

      const toggle = event.target.closest('[data-toggle-solution]');
      if (toggle) {
        event.stopPropagation();
        const card = toggle.closest('[data-data-card]');
        const problem = card?.querySelector('.rd-view-problem');
        const solution = card?.querySelector('.rd-view-solution');
        if (card && problem && solution) {
          const showingProblem = !problem.classList.contains('hidden');
          problem.classList.toggle('hidden', showingProblem);
          solution.classList.toggle('hidden', !showingProblem);
          toggle.textContent = showingProblem ? 'View Data' : 'View Solution';
          toggle.classList.toggle('bg-[var(--reflection-accent)]', showingProblem);
          toggle.classList.toggle('text-[var(--reflection-accent-contrast)]', showingProblem);
          toggle.classList.toggle('bg-[var(--reflection-heading)]/5', !showingProblem);
          toggle.classList.toggle('text-[var(--reflection-heading)]', !showingProblem);
          toggle.classList.toggle('opacity-60', !showingProblem);
        }
        return;
      }

      const dataCard = event.target.closest('[data-data-card]');
      if (dataCard) {
        const grid = dataCard.closest('[data-reci-data-grid]') || dataCard.parentElement;
        if (grid) {
          grid.querySelectorAll('[data-data-card]').forEach((node) => {
            node.classList.remove('active', 'md:col-span-2');
            node.querySelector('.rd-card-detail')?.classList.add('hidden');
            node.querySelector('.rd-view-problem')?.classList.remove('hidden');
            node.querySelector('.rd-view-solution')?.classList.add('hidden');
            const btn = node.querySelector('[data-toggle-solution]');
            if (btn) {
              btn.textContent = 'View Solution';
              btn.classList.remove('bg-[var(--reflection-accent)]', 'text-[var(--reflection-accent-contrast)]');
              btn.classList.add('bg-[var(--reflection-heading)]/5', 'text-[var(--reflection-heading)]', 'opacity-60');
            }
          });
          dataCard.classList.add('active', 'md:col-span-2');
          dataCard.querySelector('.rd-card-detail')?.classList.remove('hidden');
        }
      }
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

  function escapeHtml(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function renderAnnotation(notes, annotationIndex = 0, targets = {}) {
    const { annotationTitle, annotationBody, annotationList, hotspotLayer } = targets;
    const note = notes[annotationIndex];
    if (!note) {
      if (annotationTitle) annotationTitle.textContent = 'No annotation';
      if (annotationBody) annotationBody.textContent = 'No annotation is available for this panel yet.';
      if (annotationList) annotationList.innerHTML = '';
      if (hotspotLayer) hotspotLayer.innerHTML = '';
      return;
    }
    if (annotationTitle) annotationTitle.textContent = note.title;
    if (annotationBody) annotationBody.innerHTML = note.body;
    if (annotationList) {
      annotationList.innerHTML = notes.map((item, index) => `
        <button type="button" class="annotation-chip w-full rounded-[14px] border px-4 py-3 text-left text-sm transition ${index === annotationIndex ? 'active' : ''}" style="border-color:${index === annotationIndex ? 'var(--reflection-accent)' : 'var(--reflection-border)'}; background:${index === annotationIndex ? 'rgba(167, 199, 150, 0.28)' : 'rgba(255,255,255,0.12)'}; color:var(--reflection-text);" data-annotation-index="${index}">
          ${index + 1}. ${escapeHtml(item.title)}
        </button>
      `).join('');
    }
    if (hotspotLayer) {
      hotspotLayer.innerHTML = notes.map((item, index) => `
        <button type="button" class="panel-hotspot ${index === annotationIndex ? 'active' : ''}" data-annotation-index="${index}" style="left:${Number(item.x) || 0}%; top:${Number(item.y) || 0}%;">${index + 1}</button>
      `).join('');
    }
  }

  function parsePanelAnnotations(panel) {
    if (panel?.dataset?.annotationsJson) {
      try {
        const binary = atob(panel.dataset.annotationsJson);
        const bytes = Uint8Array.from(binary, (char) => char.charCodeAt(0));
        const decoded = new TextDecoder().decode(bytes);
        const parsed = JSON.parse(decoded);
        return Array.isArray(parsed) ? parsed : [];
      } catch (error) {
        return [];
      }
    }

    try {
      const parsed = JSON.parse(panel?.dataset?.annotations || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
      return [];
    }
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

    function openPlainImage(src, alt, caption) {
      image.src = src || '';
      image.alt = alt || '';
      image.dataset.annotations = '[]';
      if (title) title.textContent = alt || caption || 'Image viewer';
      if (intro) intro.textContent = caption || '';
      if (annotationTitle) annotationTitle.textContent = 'Image';
      if (annotationBody) annotationBody.textContent = caption || '';
      if (annotationList) annotationList.innerHTML = '';
      if (hotspotLayer) hotspotLayer.innerHTML = '';
      lightbox.classList.add('lightbox--plain');
      lightbox.classList.add('active');
      document.body.style.overflow = 'hidden';
    }

    // Delegated handlers (on document) so they keep working after the builder
    // preview swaps out a chapter's DOM via the "update-chapter" message.
    document.addEventListener('click', (event) => {
      const trigger = event.target.closest('[data-lightbox-image]');
      if (trigger) {
        openPlainImage(
          trigger.getAttribute('data-lightbox-src') || '',
          trigger.getAttribute('data-lightbox-alt') || '',
          trigger.getAttribute('data-lightbox-caption') || ''
        );
        return;
      }
      const panel = event.target.closest('.panel-image');
      if (!panel) return;
      const notes = parsePanelAnnotations(panel);
      image.src = panel.getAttribute('src') || '';
      image.alt = panel.getAttribute('alt') || '';
      image.dataset.annotations = JSON.stringify(notes);
      if (title) title.textContent = panel.getAttribute('alt') || 'Panel reader';
      if (intro) intro.textContent = 'Select an annotation point or note to read a guided comment on this panel.';
      lightbox.classList.remove('lightbox--plain');
      renderAnnotation(notes, 0, { annotationTitle, annotationBody, annotationList, hotspotLayer });
      lightbox.classList.add('active');
      document.body.style.overflow = 'hidden';
    });

    lightbox.addEventListener('click', (event) => {
      const annotationTrigger = event.target.closest('[data-annotation-index]');
      if (annotationTrigger) {
        let notes = [];
        try {
          const parsed = JSON.parse(image.dataset ? image.dataset.annotations : '[]');
          if (Array.isArray(parsed)) notes = parsed;
        } catch (error) {
          notes = [];
        }
        renderAnnotation(notes, Number(annotationTrigger.dataset.annotationIndex || 0), { annotationTitle, annotationBody, annotationList, hotspotLayer });
      }
    });

    function closeLightbox() {
      lightbox.classList.remove('active', 'lightbox--plain');
      if (hotspotLayer) hotspotLayer.innerHTML = '';
      if (annotationList) annotationList.innerHTML = '';
      document.body.style.overflow = '';
    }

    close.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (event) => {
      if (event.target === lightbox) closeLightbox();
    });
  }

  /**
   * Saving a reflection, for every prompt style.
   *
   * Each style used to bring its own markup and its own handler: one keyed on
   * #saveResponseBtn, another on .reci-complete-btn, which is why the styles
   * behaved differently and why one of them had no button that said save.
   * They now share template-parts/reflection/prompt-form.php, and this is the
   * only code that saves.
   */
  function initPromptForms() {
    const config = window.RECIReflectionConfig || {};

    document.addEventListener('click', async (event) => {
      const button = event.target.closest('[data-reci-save]');
      if (!button) return;

      const form = button.closest('[data-reci-prompt]');
      if (!form) return;

      event.preventDefault();

      const input = form.querySelector('[data-reci-response]');
      const status = form.querySelector('[data-reci-status]');
      const response = (input?.value || '').trim();

      const say = (message) => {
        if (!status) return;
        status.textContent = message;
        status.hidden = false;
      };

      if (!response) {
        say('Write something first.');
        return;
      }

      // Writing is always open; the account is only needed to keep it.
      if (typeof window.reciShowAuthModal === 'function') {
        const signedIn = await window.reciShowAuthModal();
        if (!signedIn) {
          say('Your reflection was not saved. Sign in to keep it.');
          return;
        }
      }

      const restUrl = config.restUrl || (window.reciDashboard && window.reciDashboard.restUrl + 'reci/v1/journals');
      if (!restUrl) {
        say('Saving is unavailable right now.');
        return;
      }

      button.disabled = true;
      say('Saving...');

      try {
        const res = await fetch(restUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': (window.reciDashboard && window.reciDashboard.restNonce) || config.nonce || ''
          },
          body: JSON.stringify({
            reflection_id: form.dataset.reflectionId,
            prompt: form.dataset.prompt,
            response
          })
        });

        const data = await res.json();
        if (!res.ok) throw new Error(data.message || 'Unable to save your response.');

        const intent = window.reciReadShareIntent
          ? window.reciReadShareIntent(button)
          : { share: false, anonymous: false };

        let message = 'Reflection saved to your journal.';

        if (intent.share && data.id && window.reciPatchJournalShare) {
          try {
            await window.reciPatchJournalShare(data.id, true, intent.anonymous);
            if (intent.reset) intent.reset();
            message = 'Saved and sent for review.';
          } catch (shareError) {
            message = 'Saved privately. Sharing failed — try again from your dashboard.';
          }
        }

        if (input) input.value = '';

        // Every style confirms the same way: the writing half swaps for the
        // confirmation, which the shared form renders in that style's own
        // palette. This used to differ - three styles showed a panel and two
        // showed a line of text, so the same action told you different things.
        const body = form.querySelector('[data-reci-form-body]');
        const success = form.querySelector('[data-reci-success]');

        if (body && success) {
          if (status) status.textContent = message;
          body.hidden = true;
          success.hidden = false;
          return;
        }

        say(message);
      } catch (error) {
        say(error.message || 'Something went wrong.');
      } finally {
        button.disabled = false;
      }
    });

    document.addEventListener('click', (event) => {
      const restart = event.target.closest('[data-reci-restart]');
      if (!restart) return;

      const form = restart.closest('[data-reci-prompt]');
      if (!form) return;

      event.preventDefault();

      // Back to the writing half in place: a reload would lose the reader's
      // position in the reflection.
      const body = form.querySelector('[data-reci-form-body]');
      const success = form.querySelector('[data-reci-success]');
      const status = form.querySelector('[data-reci-status]');

      if (success) success.hidden = true;
      if (body) body.hidden = false;
      if (status) status.hidden = true;

      const input = form.querySelector('[data-reci-response]');
      if (input) input.focus();
    });

    // A continue button that leaves the reflection entirely.
    document.addEventListener('click', (event) => {
      const link = event.target.closest('[data-reci-continue-href]');
      if (!link) return;
      event.preventDefault();
      window.location.href = link.getAttribute('data-reci-continue-href');
    });
  }

  let booted = false;
  function boot() {
    if (booted) return;
    booted = true;
    initStageFlow();
    initMenuOverlay();
    initTimelineWorld();
    initLightbox();
    initPromptForms();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
