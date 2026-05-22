// ===== Mobile Menu Toggle =====
const navToggle = document.getElementById('navToggle');
const nav = document.getElementById('nav');

navToggle.addEventListener('click', () => {
  nav.classList.toggle('is-open');
  const icon = navToggle.querySelector('i');
  icon.classList.toggle('fa-bars');
  icon.classList.toggle('fa-xmark');
});

// Close menu when a link is clicked (mobile)
nav.querySelectorAll('a').forEach((link) => {
  link.addEventListener('click', () => {
    if (nav.classList.contains('is-open')) {
      nav.classList.remove('is-open');
      const icon = navToggle.querySelector('i');
      icon.classList.add('fa-bars');
      icon.classList.remove('fa-xmark');
    }
  });
});

// ===== Animated Stats Counter =====
const stats = document.querySelectorAll('.stat h3');
const animateCount = (el) => {
  const target = parseInt(el.textContent, 10);
  let current = 0;
  const step = Math.max(1, Math.ceil(target / 60));
  const tick = () => {
    current += step;
    if (current >= target) {
      el.textContent = target + '+';
      return;
    }
    el.textContent = current + '+';
    requestAnimationFrame(tick);
  };
  tick();
};

const statObserver = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        animateCount(entry.target);
        statObserver.unobserve(entry.target);
      }
    });
  },
  { threshold: 0.4 }
);
stats.forEach((s) => statObserver.observe(s));
