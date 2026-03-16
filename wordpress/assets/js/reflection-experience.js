(function () {
  function parseConfig() {
    var node = document.getElementById('reci-reflection-experience-data');
    if (!node) return null;

    try {
      return JSON.parse(node.textContent || '{}');
    } catch (error) {
      console.error('Invalid reflection experience config', error);
      return null;
    }
  }

  function byId(id) {
    return document.querySelector('[data-chapter-id="' + id + '"]');
  }

  function readJson(node) {
    if (!node) return null;
    try {
      return JSON.parse(node.textContent || '{}');
    } catch (error) {
      return null;
    }
  }

  function Experience(config) {
    this.config = config || {};
    this.chapters = Array.isArray(this.config.chapters) ? this.config.chapters : [];
    this.progression = this.config.progression || {};
    this.chapterMap = {};
    this.state = {};

    for (var i = 0; i < this.chapters.length; i += 1) {
      this.chapterMap[this.chapters[i].id] = this.chapters[i];
      this.state[this.chapters[i].id] = this.chapters[i].state && this.chapters[i].state.initial ? this.chapters[i].state.initial : (i === 0 ? 'active' : 'locked');
    }

    this.activeChapterId = this.progression.initial || (this.chapters[0] ? this.chapters[0].id : null);
  }

  Experience.prototype.init = function () {
    if (!this.chapters.length) return;

    document.documentElement.setAttribute('data-reflection-mode', this.config.mode || 'standard');
    document.documentElement.setAttribute('data-reflection-template', this.config.template || 'narrative');

    this.mountChapters();
    this.bindGlobalActions();

    document.dispatchEvent(new CustomEvent('reci:reflection-experience:init', {
      detail: {
        mode: this.config.mode || 'standard',
        template: this.config.template || 'narrative',
        chapters: this.chapters,
        progression: this.progression || {}
      }
    }));
  };

  Experience.prototype.mountChapters = function () {
    for (var i = 0; i < this.chapters.length; i += 1) {
      var chapter = this.chapters[i];
      var el = byId(chapter.id);
      if (!el) continue;

      el.hidden = this.state[chapter.id] !== 'active';
      el.classList.toggle('is-active', this.state[chapter.id] === 'active');
      el.classList.toggle('is-locked', this.state[chapter.id] === 'locked');
      el.classList.toggle('is-completed', this.state[chapter.id] === 'completed');

      this.bindChapter(el, chapter);
    }
  };

  Experience.prototype.bindGlobalActions = function () {
    var self = this;
    document.addEventListener('click', function (event) {
      var button = event.target.closest('[data-reflection-action="continue"]');
      if (!button) return;

      var chapterEl = button.closest('[data-reflection-chapter]');
      if (!chapterEl) return;

      self.completeChapter(chapterEl.getAttribute('data-chapter-id'));
    });
  };

  Experience.prototype.bindChapter = function (el, chapter) {
    var type = chapter.type;

    if (type === 'hotspot_stage') {
      this.bindHotspots(el);
    } else if (type === 'progressive_text') {
      this.bindProgressiveText(el);
    } else if (type === 'horizontal_panels') {
      this.bindHorizontalPanels(el);
    } else if (type === 'step_sequence') {
      this.bindStepSequence(el, chapter);
    } else if (type === 'data_cards') {
      this.bindDataCards(el);
    } else if (type === 'drag_reveal') {
      this.bindDragReveal(el, chapter);
    } else if (type === 'word_shift') {
      this.bindWordShift(el);
    } else if (type === 'parallax_stage') {
      this.bindParallaxStage(el);
    }
  };

  Experience.prototype.bindHotspots = function (el) {
    var hotspots = Array.prototype.slice.call(el.querySelectorAll('[data-hotspot]'));
    var title = el.querySelector('[data-hotspot-title]');
    var content = el.querySelector('[data-hotspot-content]');
    var detail = el.querySelector('[data-hotspot-detail]');
    var close = el.querySelector('[data-hotspot-close]');
    var continueBtn = el.querySelector('[data-hotspot-continue]');
    var seen = {};
    var required = parseInt(el.getAttribute('data-hotspot-min') || String(hotspots.length), 10);

    function refreshContinue() {
      var count = Object.keys(seen).length;
      if (continueBtn) continueBtn.hidden = count < required;
    }

    hotspots.forEach(function (hotspot) {
      hotspot.addEventListener('click', function () {
        var index = hotspot.getAttribute('data-hotspot-index') || '';
        seen[index] = true;
        hotspots.forEach(function (item) { item.classList.remove('is-active'); });
        hotspot.classList.add('is-active');
        if (detail) detail.classList.add('is-visible');
        if (title) title.textContent = hotspot.getAttribute('data-title') || 'Detail';
        if (content) content.textContent = hotspot.getAttribute('data-content') || '';
        refreshContinue();
      });
    });

    if (close && detail) {
      close.addEventListener('click', function () { detail.classList.remove('is-visible'); });
    }
  };

  Experience.prototype.bindProgressiveText = function (el) {
    var paragraphs = Array.prototype.slice.call(el.querySelectorAll('[data-progressive-item]'));
    var next = el.querySelector('[data-progressive-next]');
    var continueBtn = el.querySelector('[data-progressive-continue]');
    var index = 0;

    function reveal() {
      if (index < paragraphs.length) {
        paragraphs[index].hidden = false;
        paragraphs[index].classList.add('is-visible');
        index += 1;
      }

      if (index >= paragraphs.length) {
        if (next) next.hidden = true;
        if (continueBtn) continueBtn.hidden = false;
      }
    }

    if (next) {
      next.addEventListener('click', reveal);
      reveal();
    }
  };

  Experience.prototype.bindHorizontalPanels = function (el) {
    var track = el.querySelector('[data-horizontal-track]');
    var panels = Array.prototype.slice.call(el.querySelectorAll('[data-horizontal-panel]'));
    var prev = el.querySelector('[data-horizontal-prev]');
    var next = el.querySelector('[data-horizontal-next]');
    var continueBtn = el.querySelector('[data-horizontal-continue]');
    var index = 0;

    function paint() {
      if (!track || !panels.length) return;
      track.style.transform = 'translateX(' + (index * -100) + 'vw)';
      if (prev) prev.disabled = index === 0;
      if (next) next.disabled = index === panels.length - 1;
      if (continueBtn) continueBtn.hidden = index !== panels.length - 1;
    }

    if (prev) prev.addEventListener('click', function () { index = Math.max(0, index - 1); paint(); });
    if (next) next.addEventListener('click', function () { index = Math.min(panels.length - 1, index + 1); paint(); });

    el.addEventListener('wheel', function (event) {
      if (Math.abs(event.deltaY) < 16) return;
      event.preventDefault();
      if (event.deltaY > 0) index = Math.min(panels.length - 1, index + 1);
      else index = Math.max(0, index - 1);
      paint();
    }, { passive: false });

    paint();
  };

  Experience.prototype.bindStepSequence = function (el, chapter) {
    var button = el.querySelector('[data-step-sequence-button]');
    var card = el.querySelector('[data-step-sequence-card]');
    var title = el.querySelector('[data-step-sequence-title]');
    var content = el.querySelector('[data-step-sequence-content]');
    var continueBtn = el.querySelector('[data-step-sequence-continue]');
    var footprints = Array.prototype.slice.call(el.querySelectorAll('[data-step-footprint]'));
    var data = readJson(el.querySelector('[data-step-sequence-items]'));
    var items = Array.isArray(data) ? data : [];
    var index = 0;

    function renderStep() {
      if (!items[index]) return;
      if (card) card.hidden = false;
      if (title) title.textContent = items[index].title || items[index].label || 'Step';
      if (content) content.textContent = items[index].content || '';
      footprints.forEach(function (footprint, footprintIndex) {
        footprint.classList.toggle('is-active', footprintIndex <= index);
      });
      index += 1;
      if (index >= items.length) {
        if (button) button.hidden = true;
        if (continueBtn) continueBtn.hidden = false;
      }
    }

    if (button) button.addEventListener('click', renderStep);
  };

  Experience.prototype.bindDataCards = function (el) {
    var cards = Array.prototype.slice.call(el.querySelectorAll('[data-data-card]'));
    cards.forEach(function (card) {
      card.addEventListener('click', function () {
        cards.forEach(function (item) { item.classList.remove('is-active'); });
        card.classList.add('is-active');
      });
      var toggle = card.querySelector('[data-data-card-toggle]');
      var solution = card.querySelector('[data-data-card-solution]');
      if (toggle && solution) {
        toggle.addEventListener('click', function (event) {
          event.stopPropagation();
          var hidden = solution.hidden;
          solution.hidden = !hidden;
          toggle.textContent = hidden ? 'View Data' : 'View Solution';
        });
      }
    });
  };

  Experience.prototype.bindDragReveal = function (el, chapter) {
    var link = el.querySelector('[data-drag-reveal-link]');
    var status = el.querySelector('[data-drag-reveal-status]');
    var continueBtn = el.querySelector('[data-drag-reveal-continue]');
    var config = readJson(el.querySelector('[data-drag-reveal-config]')) || {};
    var startY = 0;
    var dragging = false;
    var broken = false;

    function clientY(event) {
      if (event.touches && event.touches[0]) return event.touches[0].clientY;
      return event.clientY;
    }

    function stop() {
      dragging = false;
      document.removeEventListener('mousemove', move);
      document.removeEventListener('mouseup', end);
      document.removeEventListener('touchmove', move);
      document.removeEventListener('touchend', end);
    }

    function breakLink() {
      if (broken || !link) return;
      broken = true;
      link.classList.add('is-broken');
      if (status) status.textContent = config.success || 'Broken';
      if (continueBtn) continueBtn.hidden = false;
      stop();
    }

    function move(event) {
      if (!dragging || !link) return;
      var delta = clientY(event) - startY;
      if (delta > 0 && delta < 160) link.style.transform = 'translateY(' + delta + 'px)';
      if (delta >= 160) breakLink();
      if (event.cancelable) event.preventDefault();
    }

    function end() {
      if (!broken && link) link.style.transform = 'translateY(0)';
      stop();
    }

    if (link) {
      link.addEventListener('mousedown', function (event) {
        startY = clientY(event);
        dragging = true;
        document.addEventListener('mousemove', move);
        document.addEventListener('mouseup', end);
      });
      link.addEventListener('touchstart', function (event) {
        startY = clientY(event);
        dragging = true;
        document.addEventListener('touchmove', move, { passive: false });
        document.addEventListener('touchend', end);
      }, { passive: true });
    }
  };

  Experience.prototype.bindWordShift = function (el) {
    var words = Array.prototype.slice.call(el.querySelectorAll('[data-shift-word]'));
    words.forEach(function (word) {
      word.addEventListener('mouseenter', function () {
        word.textContent = word.getAttribute('data-shift') || word.textContent;
        word.classList.add('is-shifted');
      });
      word.addEventListener('mouseleave', function () {
        word.textContent = word.getAttribute('data-default') || word.textContent;
        word.classList.remove('is-shifted');
      });
    });
  };

  Experience.prototype.bindParallaxStage = function (el) {
    var stage = el.querySelector('[data-parallax-stage]');
    var layers = Array.prototype.slice.call(el.querySelectorAll('[data-parallax-layer]'));
    if (!stage || !layers.length) return;

    stage.addEventListener('mousemove', function (event) {
      var rect = stage.getBoundingClientRect();
      var x = (event.clientX - rect.left) / rect.width - 0.5;
      layers.forEach(function (layer) {
        var speed = parseFloat(layer.getAttribute('data-speed') || '0.03');
        layer.style.transform = 'translateX(' + (x * 100 * speed) + 'px) scale(1.05)';
      });
    });
  };

  Experience.prototype.getNextChapterId = function (chapterId) {
    var order = this.progression && Array.isArray(this.progression.order) ? this.progression.order : [];
    var index = order.indexOf(chapterId);
    if (index === -1) return '';
    return order[index + 1] || '';
  };

  Experience.prototype.completeChapter = function (chapterId) {
    if (!chapterId || !this.chapterMap[chapterId]) return;

    var chapter = this.chapterMap[chapterId];
    var targetId = (chapter.state && chapter.state.completion && chapter.state.completion.target) || this.getNextChapterId(chapterId);
    var currentEl = byId(chapterId);

    this.state[chapterId] = 'completed';
    if (currentEl) {
      currentEl.classList.remove('is-active');
      currentEl.classList.add('is-completed');
    }

    if (targetId && this.chapterMap[targetId]) {
      if (currentEl) currentEl.hidden = true;
      this.activateChapter(targetId);
      return;
    }

    if (currentEl) {
      currentEl.hidden = false;
      currentEl.classList.add('is-active');
    }

    var related = document.querySelector('.reci-reflection-related');
    if (related) {
      window.requestAnimationFrame(function () {
        related.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    }
  };

  Experience.prototype.activateChapter = function (chapterId) {
    if (!chapterId || !this.chapterMap[chapterId]) return;

    this.activeChapterId = chapterId;
    this.state[chapterId] = 'active';

    var nextEl = byId(chapterId);
    if (nextEl) {
      nextEl.hidden = false;
      nextEl.classList.remove('is-locked');
      nextEl.classList.add('is-active');
      window.requestAnimationFrame(function () {
        nextEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    var config = parseConfig();
    if (!config) return;

    window.RECIReflectionExperience = new Experience(config);
    window.RECIReflectionExperience.init();
  });
})();
