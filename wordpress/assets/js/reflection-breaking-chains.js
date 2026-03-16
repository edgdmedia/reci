(() => {
  const audio = document.getElementById('bcAudio');
  const playBtn = document.getElementById('bcPlayBtn');
  const progressLine = document.getElementById('bcProgressLine');
  const freedomBg = document.getElementById('bcFreedomBg');
  const completeBtn = document.getElementById('bcCompleteBtn');
  const sections = Array.from(document.querySelectorAll('.bc-stage'));
  const link = document.getElementById('breakableLink');
  const instruction = document.getElementById('chainInstruction');
  let startY = 0;
  let isDragging = false;

  function updateScrollState() {
    const denominator = Math.max(document.body.scrollHeight - window.innerHeight, 1);
    const scrollPct = window.scrollY / denominator;
    if (progressLine) progressLine.style.height = `${scrollPct * 100}%`;

    sections.forEach((section, index) => {
      const rect = section.getBoundingClientRect();
      const active = rect.top < window.innerHeight * 0.7 && rect.bottom > 0;
      section.classList.toggle('active', active);
      if (index === sections.length - 1 && active && freedomBg) {
        freedomBg.style.opacity = '1';
      }
    });

    if (freedomBg && !sections[sections.length - 1]?.classList.contains('active')) {
      freedomBg.style.opacity = '0';
    }
  }

  function endDrag() {
    if (!isDragging) return;
    isDragging = false;
    document.removeEventListener('mousemove', onDrag);
    document.removeEventListener('mouseup', endDrag);
    document.removeEventListener('touchmove', onDrag);
    document.removeEventListener('touchend', endDrag);
    if (link && !link.classList.contains('link-broken')) {
      link.style.transform = 'translateY(0)';
      if (instruction) instruction.textContent = 'Drag Down to Break';
    }
  }

  function breakLink() {
    isDragging = false;
    document.removeEventListener('mousemove', onDrag);
    document.removeEventListener('mouseup', endDrag);
    document.removeEventListener('touchmove', onDrag);
    document.removeEventListener('touchend', endDrag);
    if (!link) return;
    link.classList.add('link-broken');
    link.style.animation = 'breakAndFall 1s forwards';
    if (instruction) {
      instruction.textContent = 'BROKEN';
      instruction.style.color = 'var(--reflection-accent)';
    }
    if (audio?.paused) {
      audio.play();
      if (playBtn) playBtn.textContent = '||';
    }
    window.setTimeout(() => {
      document.getElementById('s3')?.scrollIntoView({ behavior: 'smooth' });
    }, 1500);
  }

  function onDrag(e) {
    if (!isDragging || !link) return;
    e.preventDefault();
    const clientY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
    const delta = clientY - startY;
    if (delta > 0 && delta < 150) {
      link.style.transform = `translateY(${delta}px)`;
      if (instruction && delta > 50) instruction.textContent = 'Pull Harder...';
    }
    if (delta >= 150) breakLink();
  }

  window.reciStartChainDrag = (e) => {
    isDragging = true;
    startY = e.type.includes('mouse') ? e.clientY : e.touches[0].clientY;
    document.addEventListener('mousemove', onDrag);
    document.addEventListener('mouseup', endDrag);
    document.addEventListener('touchmove', onDrag, { passive: false });
    document.addEventListener('touchend', endDrag);
  };

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

  document.querySelectorAll('.shift-word').forEach((span) => {
    span.addEventListener('mouseenter', function () {
      if (!this.dataset.orig) this.dataset.orig = this.innerText;
      this.innerText = this.dataset.shift;
      this.classList.add('shifted');
    });
    span.addEventListener('mouseleave', function () {
      this.innerText = this.dataset.orig || this.innerText;
      this.classList.remove('shifted');
    });
  });

  document.addEventListener('click', (event) => {
    if (event.target === completeBtn && completeBtn instanceof HTMLElement) {
      const href = completeBtn.dataset.completeHref;
      if (href) window.location.href = href;
    }
  });

  window.addEventListener('scroll', updateScrollState, { passive: true });
  updateScrollState();
})();
