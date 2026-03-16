(function () {
  function initCompareBlocks() {
    document.querySelectorAll('[data-reflection-compare]').forEach(function (block) {
      var image = block.querySelector('[data-compare-image]');
      var title = block.querySelector('[data-compare-title]');
      var content = block.querySelector('[data-compare-content]');
      var triggers = block.querySelectorAll('[data-compare-trigger]');

      triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
          if (image) {
            image.src = trigger.getAttribute('data-panel-image') || '';
            image.alt = trigger.getAttribute('data-panel-alt') || '';
          }
          if (title) {
            title.textContent = trigger.getAttribute('data-panel-title') || '';
          }
          if (content) {
            content.textContent = trigger.getAttribute('data-panel-content') || '';
          }
          triggers.forEach(function (button) {
            button.classList.remove('bg-neutral-800', 'text-white', 'border-neutral-800');
          });
          trigger.classList.add('bg-neutral-800', 'text-white', 'border-neutral-800');
        });
      });
    });
  }

  function initHotspotBlocks() {
    document.querySelectorAll('[data-reflection-hotspots]').forEach(function (block) {
      var title = block.querySelector('[data-hotspot-title]');
      var content = block.querySelector('[data-hotspot-content]');
      var label = block.querySelector('[data-hotspot-label]');
      var triggers = block.querySelectorAll('[data-hotspot-trigger]');

      triggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
          if (title) {
            title.textContent = trigger.getAttribute('data-hotspot-title') || '';
          }
          if (content) {
            content.textContent = trigger.getAttribute('data-hotspot-content') || '';
          }
          if (label) {
            label.textContent = trigger.getAttribute('data-hotspot-label') || '';
          }
          triggers.forEach(function (button) {
            button.classList.remove('ring-2', 'ring-white');
          });
          trigger.classList.add('ring-2', 'ring-white');
        });
      });
    });
  }

  function initSceneNavigation() {
    var shell = document.querySelector('[data-reflection-shell]');
    if (!shell) {
      return;
    }

    var scenes = Array.prototype.slice.call(document.querySelectorAll('.reci-immersive-scene[id]'));
    var links = Array.prototype.slice.call(document.querySelectorAll('[data-reflection-nav]'));

    if (!scenes.length || !links.length || !('IntersectionObserver' in window)) {
      return;
    }

    links.forEach(function (link) {
      link.addEventListener('click', function (event) {
        var targetId = link.getAttribute('data-target');
        var target = targetId ? document.getElementById(targetId) : null;
        if (!target) {
          return;
        }
        event.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });

    var activateLink = function (id) {
      links.forEach(function (link) {
        var isActive = link.getAttribute('data-target') === id;
        link.classList.toggle('is-active', isActive);
      });
    };

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          activateLink(entry.target.id);
        }
      });
    }, {
      threshold: 0.45,
      rootMargin: '0px 0px -10% 0px'
    });

    scenes.forEach(function (scene) {
      observer.observe(scene);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initCompareBlocks();
    initHotspotBlocks();
    initSceneNavigation();
  });
})();
