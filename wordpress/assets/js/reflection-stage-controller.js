(() => {
  function createStageController(options = {}) {
    const stageIds = Array.isArray(options.stages) ? options.stages.slice() : [];
    const stages = new Map();
    const displays = options.displays || {};
    const transitions = options.transitions || {};
    const duration = Number(options.duration || 1000);
    const manualVisibility = Boolean(options.manualVisibility);
    const history = [];
    let currentId = options.initial || stageIds[0] || '';
    const backButton = options.backButton || null;

    function getStage(id) {
      if (!id) return null;
      if (stages.has(id)) return stages.get(id);
      const node = document.getElementById(id);
      if (!node) return null;
      stages.set(id, node);
      return node;
    }

    function stageDisplay(id) {
      return displays[id] || 'flex';
    }

    function updateBackButton() {
      if (!backButton) return;
      backButton.style.display = history.length ? 'inline-flex' : 'none';
    }

    function show(id) {
      const stage = getStage(id);
      if (!stage) return;
      stage.hidden = false;
      stage.removeAttribute('hidden');
      stage.style.display = stageDisplay(id);
      void stage.offsetWidth;
      stage.style.opacity = '1';
      stage.removeAttribute('aria-hidden');
      stage.dispatchEvent(new CustomEvent('reci:stage:enter', { bubbles: true, detail: { id } }));
    }

    function hide(id) {
      const stage = getStage(id);
      if (!stage) return Promise.resolve();
      stage.dispatchEvent(new CustomEvent('reci:stage:leave', { bubbles: true, detail: { id } }));
      stage.style.opacity = '0';
      stage.setAttribute('aria-hidden', 'true');
      return new Promise((resolve) => {
        window.setTimeout(() => {
          stage.style.display = 'none';
          stage.hidden = true;
          stage.setAttribute('hidden', 'hidden');
          resolve();
        }, duration);
      });
    }

    function normalizeTarget(target) {
      if (!target) return '';
      return options.aliases && options.aliases[target] ? options.aliases[target] : target;
    }

    async function goTo(rawTarget, navOptions = {}) {
      const target = normalizeTarget(rawTarget);
      if (!target || target === currentId) return;
      const from = currentId;
      const fromStage = getStage(from);
      const toStage = getStage(target);
      if (!toStage) return;
      const key = `${from}->${target}`;
      const transition = transitions[key] || transitions[target] || null;
      if (from && navOptions.pushHistory !== false) history.push(from);
      updateBackButton();
      if (typeof transition === 'function') {
        await transition({ from, to: target, fromStage, toStage, show, hide, duration });
      } else {
        if (!manualVisibility) {
          if (from) await hide(from);
          show(target);
        }
      }
      currentId = target;
      document.dispatchEvent(new CustomEvent('reci:stage:change', { detail: { from, to: target, history: history.slice() } }));
      updateBackButton();
    }

    function back() {
      const prev = history.pop();
      updateBackButton();
      if (!prev) return;
      return goTo(prev, { pushHistory: false });
    }

    function init() {
      stageIds.forEach((id) => {
        const stage = getStage(id);
        if (!stage) return;
        if (manualVisibility) {
          stage.hidden = false;
          stage.removeAttribute('hidden');
          stage.style.opacity = '1';
          stage.removeAttribute('aria-hidden');
          return;
        }
        if (id === currentId) {
          stage.hidden = false;
          stage.removeAttribute('hidden');
          stage.style.display = stageDisplay(id);
          stage.style.opacity = '1';
        } else {
          stage.style.display = 'none';
          stage.hidden = true;
          stage.setAttribute('hidden', 'hidden');
          stage.style.opacity = '0';
          stage.setAttribute('aria-hidden', 'true');
        }
      });
      updateBackButton();
    }

    return { init, goTo, back, current: () => currentId, getStage, history: () => history.slice() };
  }

  window.RECIReflection = window.RECIReflection || {};
  window.RECIReflection.createStageController = createStageController;
})();
