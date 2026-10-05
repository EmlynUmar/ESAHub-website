document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".nav-toggle");
  const navLinks = document.querySelector(".nav-links");

  if (toggle && navLinks) {
    toggle.addEventListener("click", () => {
      navLinks.classList.toggle("active");
    });
  }

  const heroSlides = document.querySelectorAll(".hero-slide");
  if (heroSlides.length > 1) {
    let index = 0;
    setInterval(() => {
      heroSlides[index].classList.remove("active");
      index = (index + 1) % heroSlides.length;
      heroSlides[index].classList.add("active");
    }, 4000);
  }

  // Programs page: AJAX category filtering and sorting
  const categoryItems = document.querySelectorAll('.category-item');
  const programList = document.querySelector('.program-list');
  const sortSelect = document.querySelector('#program-sort');

  function renderPrograms(items) {
    if (!programList) return;
    if (!items || items.length === 0) {
      programList.innerHTML = '<div class="card">No programs found.</div>';
      return;
    }

    programList.innerHTML = items.map(p => {
      const imageHtml = p.featured_image ? `<img src="${escapeHtml('assets/images/uploads/' + p.featured_image)}" alt="${escapeHtml(p.title)}">` : `<div style="width:100%;height:100%;background:linear-gradient(90deg,var(--accent-soft),#fff);display:flex;align-items:center;justify-content:center;color:var(--primary);">No Image</div>`;
      const ctaRegister = p.registration_link
        ? `<a class="btn btn-outline btn-small" href="${escapeHtml(p.registration_link)}" target="_blank" rel="noopener">Register</a>`
        : `<a class="btn btn-outline btn-small" href="contact.php">Book Now</a>`;
      return `
        <article class="program-card card">
          <div class="thumb">${imageHtml}</div>
          <div>
            <div class="program-meta"><span class="badge">${escapeHtml(capitalize(p.delivery_mode || ''))}</span><div class="muted">${escapeHtml(p.category_name || '')}</div></div>
            <h3><a href="programs/view.php?slug=${encodeURIComponent(p.slug)}">${escapeHtml(p.title)}</a></h3>
            ${p.subtitle?`<div class="text-sm"><strong>${escapeHtml(p.subtitle)}</strong></div>`:''}
            <p class="muted" style="margin-top:0.5rem">${escapeHtml(p.summary || '')}</p>
            <div class="program-cta">
              <a class="btn btn-primary btn-small" href="programs/view.php?slug=${encodeURIComponent(p.slug)}">View Details</a>
              ${ctaRegister}
            </div>
          </div>
        </article>`;
    }).join('');
  }

  function escapeHtml(s) { return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function capitalize(s) { return String(s || '').charAt(0).toUpperCase() + String(s || '').slice(1); }

  async function fetchPrograms(category = '', sort = 'latest') {
    try {
      const url = `ajax/get_programs.php?sort=${encodeURIComponent(sort)}&category=${encodeURIComponent(category)}`;
      const res = await fetch(url, { cache: 'no-store' });
      const data = await res.json();
      if (data && data.programs) renderPrograms(data.programs);
    } catch (e) {
      console.error('fetch programs error', e);
    }
  }

  if (categoryItems.length > 0) {
    categoryItems.forEach(el => {
      el.addEventListener('click', e => {
        e.preventDefault();
        const slug = el.dataset.slug || '';
        categoryItems.forEach(i => {
          i.classList.remove('active');
          i.style.background = '';
          i.style.color = '';
        });
        el.classList.add('active');
        el.style.background = 'var(--primary)';
        el.style.color = '#fff';
        const sort = (sortSelect && sortSelect.value) || 'latest';
        fetchPrograms(slug, sort);
      });
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', () => {
      const active = document.querySelector('.category-item.active');
      const slug = active ? (active.dataset.slug || '') : '';
      fetchPrograms(slug, sortSelect.value);
    });
  }
});
