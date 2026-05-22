/* ===================================================
   Greenwood School - Shared JS
   - Mobile menu
   - Stats counter
   - Language toggle (EN / Hindi)
   - Form validation (admission, contact)
   - Gallery filter + Lightbox
   =================================================== */

/* ---------- Mobile Menu ---------- */
const navToggle = document.getElementById('navToggle');
const nav = document.getElementById('nav');
if (navToggle && nav) {
  navToggle.addEventListener('click', () => {
    nav.classList.toggle('is-open');
    const icon = navToggle.querySelector('i');
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-xmark');
  });
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
}

/* ---------- Animated Stats Counter ---------- */
const stats = document.querySelectorAll('.stat h3');
if (stats.length) {
  const animateCount = (el) => {
    const target = parseInt(el.textContent, 10);
    let current = 0;
    const step = Math.max(1, Math.ceil(target / 60));
    const tick = () => {
      current += step;
      if (current >= target) { el.textContent = target + '+'; return; }
      el.textContent = current + '+';
      requestAnimationFrame(tick);
    };
    tick();
  };
  const obs = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) { animateCount(e.target); obs.unobserve(e.target); }
    });
  }, { threshold: 0.4 });
  stats.forEach((s) => obs.observe(s));
}

/* ---------- Language Toggle ---------- */
const LANG_KEY = 'greenwood_lang';

function applyLanguage(lang) {
  document.documentElement.setAttribute('lang', lang);
  document.querySelectorAll('[data-en]').forEach((el) => {
    const txt = el.getAttribute('data-' + lang);
    if (txt !== null && txt !== undefined) el.textContent = txt;
  });
  // Update active button state
  document.querySelectorAll('.lang-switch button').forEach((btn) => {
    btn.classList.toggle('active', btn.dataset.lang === lang);
  });
  localStorage.setItem(LANG_KEY, lang);
}

document.querySelectorAll('.lang-switch button').forEach((btn) => {
  btn.addEventListener('click', () => applyLanguage(btn.dataset.lang));
});

// Apply saved language on load
const savedLang = localStorage.getItem(LANG_KEY) || 'en';
if (savedLang !== 'en') applyLanguage(savedLang);

/* ---------- Generic Form Validation ---------- */
function validateForm(form, messageEl) {
  let valid = true;
  form.querySelectorAll('[required]').forEach((field) => {
    const value = (field.value || '').trim();
    field.classList.remove('invalid');
    if (!value) {
      field.classList.add('invalid');
      valid = false;
      return;
    }
    if (field.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
      field.classList.add('invalid');
      valid = false;
    }
    if (field.type === 'tel' && field.pattern && !new RegExp('^' + field.pattern + '$').test(value)) {
      field.classList.add('invalid');
      valid = false;
    }
  });

  const lang = localStorage.getItem(LANG_KEY) || 'en';
  if (!valid) {
    messageEl.className = 'form-message error';
    messageEl.textContent = lang === 'hi'
      ? 'कृपया सभी आवश्यक फ़ील्ड सही ढंग से भरें।'
      : 'Please fill all required fields correctly.';
    return false;
  }

  messageEl.className = 'form-message success';
  messageEl.textContent = lang === 'hi'
    ? 'धन्यवाद! आपका फॉर्म सफलतापूर्वक जमा हो गया। हम जल्द ही आपसे संपर्क करेंगे।'
    : 'Thank you! Your form was submitted successfully. We will contact you soon.';
  form.reset();
  return true;
}

const admissionForm = document.getElementById('admissionForm');
if (admissionForm) {
  admissionForm.addEventListener('submit', (e) => {
    e.preventDefault();
    validateForm(admissionForm, document.getElementById('formMessage'));
  });
}

const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', (e) => {
    e.preventDefault();
    validateForm(contactForm, document.getElementById('contactMsg'));
  });
}

/* ---------- Gallery Filter + Lightbox ---------- */
const galFilter = document.getElementById('galFilter');
const galleryGrid = document.getElementById('galleryGrid');

if (galFilter && galleryGrid) {
  const items = galleryGrid.querySelectorAll('.gal-item');

  galFilter.addEventListener('click', (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;
    galFilter.querySelectorAll('button').forEach((b) => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;
    items.forEach((it) => {
      const show = filter === 'all' || it.dataset.cat === filter;
      it.classList.toggle('hidden', !show);
    });
  });

  // Lightbox
  const lightbox = document.getElementById('lightbox');
  const lbImg = document.getElementById('lbImg');
  const lbCaption = document.getElementById('lbCaption');
  const lbClose = document.getElementById('lbClose');
  const lbPrev = document.getElementById('lbPrev');
  const lbNext = document.getElementById('lbNext');
  let currentIndex = 0;

  const visibleItems = () => Array.from(items).filter((i) => !i.classList.contains('hidden'));

  const openLightbox = (idx) => {
    const list = visibleItems();
    currentIndex = idx;
    const item = list[idx];
    lbImg.src = item.querySelector('img').src;
    const cap = item.querySelector('figcaption');
    lbCaption.textContent = cap ? cap.textContent : '';
    lightbox.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  };

  const closeLightbox = () => {
    lightbox.classList.remove('is-open');
    document.body.style.overflow = '';
  };

  const navigate = (dir) => {
    const list = visibleItems();
    currentIndex = (currentIndex + dir + list.length) % list.length;
    const item = list[currentIndex];
    lbImg.src = item.querySelector('img').src;
    const cap = item.querySelector('figcaption');
    lbCaption.textContent = cap ? cap.textContent : '';
  };

  items.forEach((item) => {
    item.addEventListener('click', () => {
      const list = visibleItems();
      const idx = list.indexOf(item);
      if (idx >= 0) openLightbox(idx);
    });
  });

  lbClose.addEventListener('click', closeLightbox);
  lbPrev.addEventListener('click', () => navigate(-1));
  lbNext.addEventListener('click', () => navigate(1));
  lightbox.addEventListener('click', (e) => { if (e.target === lightbox) closeLightbox(); });
  document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('is-open')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') navigate(-1);
    if (e.key === 'ArrowRight') navigate(1);
  });
}
