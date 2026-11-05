/* Moudo Fashion — interactions : thème, filtres, fiche produit (taille/couleur), sélection WhatsApp, visionneuse */
(() => {
  'use strict';
  document.documentElement.classList.add('js');

  const D = window.MF || { produits: [], categories: {}, tailles: {}, whatsapp: '', t: {}, lang: 'fr' };
  const T = D.t;
  const byId = Object.fromEntries(D.produits.map(p => [p.id, p]));
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const waLink = msg => `https://wa.me/${D.whatsapp}?text=${encodeURIComponent(msg)}`;
  const openWa = msg => {
    const a = document.createElement('a');
    a.href = waLink(msg); a.target = '_blank'; a.rel = 'noopener';
    document.body.appendChild(a); a.click(); a.remove();
  };

  /* ---------- Mode sombre ---------- */
  const root = document.documentElement;
  const themeBtn = $('#themeToggle');
  const mq = window.matchMedia ? matchMedia('(prefers-color-scheme: dark)') : null;
  const isDark = () => root.dataset.theme ? root.dataset.theme === 'dark' : !!(mq && mq.matches);
  const syncThemeBtn = () => {
    if (!themeBtn) return;
    const dark = isDark();
    themeBtn.setAttribute('aria-label', dark ? T.light : T.dark);
    themeBtn.setAttribute('aria-pressed', String(dark));
  };
  themeBtn?.addEventListener('click', () => {
    const next = isDark() ? 'light' : 'dark';
    root.dataset.theme = next;
    try { localStorage.setItem('mf_theme', next); } catch (_) {}
    syncThemeBtn();
  });
  mq?.addEventListener?.('change', syncThemeBtn);
  syncThemeBtn();

  /* ---------- Header au scroll ---------- */
  const header = $('#header');
  const onScroll = () => header?.classList.toggle('is-scrolled', scrollY > 10);
  addEventListener('scroll', onScroll, { passive: true }); onScroll();

  /* ---------- Apparition au scroll ---------- */
  const io = 'IntersectionObserver' in window && new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
  }, { threshold: .1, rootMargin: '0px 0px -40px' });
  $$('.reveal').forEach((el, i) => {
    el.style.transitionDelay = `${(i % 4) * 60}ms`;
    io ? io.observe(el) : el.classList.add('is-in');
  });

  /* ---------- Vidéo : pause si l'utilisateur préfère moins d'animations ---------- */
  if (window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches) {
    $$('.hero__video video').forEach(v => { v.removeAttribute('autoplay'); v.pause(); });
  }

  /* ---------- Toast ---------- */
  const toast = $('#toast'); let toastT;
  const notify = txt => {
    toast.textContent = txt; toast.classList.add('is-show');
    clearTimeout(toastT); toastT = setTimeout(() => toast.classList.remove('is-show'), 2400);
  };

  /* ---------- Sélection (panier) : clé = id|taille|couleur ---------- */
  const KEY = 'mf_selection';
  let cart = [];
  try { cart = JSON.parse(localStorage.getItem(KEY)) || []; } catch (_) { cart = []; }
  if (!Array.isArray(cart)) cart = [];
  cart = cart.filter(l => l && byId[l.id] && l.q > 0);
  const save = () => { try { localStorage.setItem(KEY, JSON.stringify(cart)); } catch (_) {} };

  const counts = $$('[data-cart-count]');
  const openers = $$('[data-cart-open]');
  const itemsEl = $('#cartItems'), totalEl = $('#cartTotal'), form = $('#orderForm'), cta = $('#cartCta');
  const fcfa = n => n.toLocaleString(D.lang === 'en' ? 'en-US' : 'fr-FR').replace(/ | /g, ' ') + ' FCFA';
  /* Lien « commande en photos » : base64url(JSON [[id, qté, taille, couleur], …]) */
  const b64url = str => btoa(unescape(encodeURIComponent(str))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
  const orderLink = (lines, name) => {
    if (!D.orderUrl || !lines.length) return '';
    const c = b64url(JSON.stringify(lines.map(l => [l.id, l.q, l.s || '', l.c || ''])));
    return `${D.orderUrl}?c=${c}${name ? '&n=' + encodeURIComponent(name) : ''}`;
  };
  const optsTxt = l => [l.s ? `${T.size} ${l.s}` : '', l.c ? `${T.color} ${l.c}` : ''].filter(Boolean).join(', ');

  function renderCart() {
    const n = cart.reduce((s, l) => s + l.q, 0);
    counts.forEach(c => { c.textContent = n; c.classList.toggle('has-items', n > 0); });
    form.hidden = n === 0; cta.hidden = n === 0;

    if (!n) {
      itemsEl.innerHTML = `<div class="empty-cart"><strong>${esc(T.emptyTitle)}</strong>${esc(T.emptyText)}
        <div><a class="btn btn--dark" href="${esc(D.collection)}" data-close>${esc(T.seeColl)}</a></div></div>`;
      return;
    }
    itemsEl.innerHTML = cart.map((l, i) => {
      const p = byId[l.id];
      const tags = [l.s && `${esc(T.size)} ${esc(l.s)}`, l.c && esc(l.c)].filter(Boolean).map(t => `<span>${t}</span>`).join('');
      return `<div class="line">
        <img src="${esc(photoFor(p, l.c))}" alt="">
        <div><h4>${esc(p.nom)}</h4><p>${esc(D.categories[p.cat])} · ${esc(p.prixTxt)}</p>${tags ? `<div class="line__opts">${tags}</div>` : ''}</div>
        <div class="line__ctrl">
          <div class="qty"><button type="button" data-dec="${i}" aria-label="${esc(T.less)}">−</button><span>${l.q}</span><button type="button" data-inc="${i}" aria-label="${esc(T.more)}">+</button></div>
          <button type="button" class="line__remove" data-del="${i}">${esc(T.remove)}</button>
        </div></div>`;
    }).join('');

    const priced = cart.filter(l => byId[l.id].prix !== null);
    const total = priced.reduce((s, l) => s + byId[l.id].prix * l.q, 0);
    totalEl.innerHTML = priced.length === cart.length
      ? `<span>${esc(T.total)}</span><span>${fcfa(total)}</span>`
      : `<span>${n} ${esc(n > 1 ? T.itemsPl : T.items)}</span><small>${esc(T.confirm)}</small>`;
  }

  function add(id, q = 1, s = '', c = '') {
    if (!byId[id]) return;
    const ex = cart.find(l => l.id === id && l.s === s && l.c === c);
    ex ? ex.q += q : cart.push({ id, q, s, c });
    save(); renderCart();
    openers.forEach(b => { b.classList.remove('bump'); void b.offsetWidth; b.classList.add('bump'); });
    notify(`✓ ${byId[id].nom} ${T.added}`);
  }

  itemsEl.addEventListener('click', e => {
    const t = e.target.closest('button'); if (!t) return;
    const { inc, dec, del } = t.dataset;
    if (inc !== undefined) cart[+inc].q++;
    else if (dec !== undefined) { cart[+dec].q--; if (cart[+dec].q < 1) cart.splice(+dec, 1); }
    else if (del !== undefined) cart.splice(+del, 1);
    else return;
    save(); renderCart();
  });

  /* ---------- Panneaux ---------- */
  const drawer = $('#drawer'), modal = $('#modal');
  let lastFocus;
  const openPanel = el => {
    lastFocus = document.activeElement; el.classList.add('is-open'); el.setAttribute('aria-hidden', 'false');
    document.body.classList.add('no-scroll'); setTimeout(() => $('.icon-btn[data-close]', el)?.focus(), 50);
  };
  const closePanel = el => {
    el.classList.remove('is-open'); el.setAttribute('aria-hidden', 'true');
    if (!$('.drawer.is-open, .modal.is-open, .lightbox.is-open')) document.body.classList.remove('no-scroll');
    lastFocus?.focus?.();
  };
  [drawer, modal].forEach(el => el.addEventListener('click', e => { if (e.target.closest('[data-close]')) closePanel(el); }));
  addEventListener('keydown', e => {
    if (e.key !== 'Escape') return;
    if (modal.classList.contains('is-open')) closePanel(modal);
    else if (drawer.classList.contains('is-open')) closePanel(drawer);
  });
  openers.forEach(b => b.addEventListener('click', () => { toast.classList.remove('is-show'); openPanel(drawer); }));

  /* ---------- Fiche produit avec taille / couleur ---------- */
  let current = null, qty = 1, size = '', color = '';
  const mQty = $('#modalQty'), hint = $('#optHint');
  const sizeBox = $('#optSize'), sizeList = $('#sizeList'), sizeVal = $('#optSizeVal');
  const colorBox = $('#optColor'), colorList = $('#colorList'), colorVal = $('#optColorVal');
  const sizesFor = p => (p.tailles && p.tailles.length ? p.tailles : D.tailles[p.cat]) || [];

  const productMsg = () => {
    const o = optsTxt({ s: size, c: color });
    if (current.stock === false) return `${T.hello}\n${T.backMsg}\n• *${current.nom}* (${D.categories[current.cat]})${color ? ' — ' + T.color + ' ' + color : ''}`;
    const link = orderLink([{ id: current.id, q: qty, s: size, c: color }]);
    return `${T.hello}\n${T.iWant}\n• ${qty} × *${current.nom}* (${D.categories[current.cat]})${o ? ' — ' + o : ''}\n\n${T.seePhotos}\n${link}\n\n${T.avail}`;
  };
  const sync = () => {
    $('#modalWa').href = waLink(productMsg());
    sizeVal.textContent = size ? `: ${size}` : '';
    colorVal.textContent = color ? `: ${color}` : '';
    $$('.opt__btn', sizeList).forEach(b => b.classList.toggle('is-on', b.dataset.v === size));
    $$('.opt__btn', colorList).forEach(b => b.classList.toggle('is-on', b.dataset.v === color));
    if (size) hint.hidden = true;
  };
  const buttons = (list, extra) => list.map(v => `<button type="button" class="opt__btn" data-v="${esc(v)}">${esc(v)}</button>`).join('')
    + (extra ? `<button type="button" class="opt__btn opt__btn--ghost" data-v="${esc(extra)}">${esc(extra)}</button>` : '');

  const addLabel = $('#modalAdd').textContent, waLabel = $('#modalWa').textContent;
  function openProduct(id) {
    current = byId[id]; if (!current) return;
    qty = 1; mQty.textContent = qty; hint.hidden = true;
    const sizes = sizesFor(current), cols = current.couleurs || [];
    size = sizes.length ? '' : ''; color = cols.length === 1 ? cols[0] : '';
    sizeBox.hidden = !sizes.length; colorBox.hidden = cols.length < 2;
    sizeList.innerHTML = buttons(sizes, T.adviseMe);
    colorList.innerHTML = buttons(cols);
    if (cols.length >= 2) color = cols[0];
    $('#modalImg').src = photoFor(current, color); $('#modalImg').alt = current.nom;
    $('#modalCat').textContent = D.categories[current.cat];
    $('#modalTitle').textContent = current.nom;
    $('#modalPrice').textContent = current.prixTxt;
    $('#modalDesc').textContent = current.desc;
    const out = current.stock === false, addBtn = $('#modalAdd'), waBtn = $('#modalWa');
    addBtn.disabled = out;
    addBtn.textContent = out ? T.soldOut : addLabel;
    waBtn.textContent = out ? T.notifyMe : waLabel;
    sizeBox.hidden = out || !sizesFor(current).length; $('.modal__row').hidden = out;
    sync(); openPanel(modal);
  }
  sizeList.addEventListener('click', e => { const b = e.target.closest('.opt__btn'); if (b) { size = b.dataset.v; sync(); } });
  const photoFor = (p, c) => { const i = (p.couleurs || []).indexOf(c); return (p.photos || [])[i] || p.img; };
  colorList.addEventListener('click', e => { const b = e.target.closest('.opt__btn'); if (b) { color = b.dataset.v; $('#modalImg').src = photoFor(current, color); sync(); } });
  $$('[data-qty]', modal).forEach(b => b.addEventListener('click', () => { qty = Math.max(1, qty + Number(b.dataset.qty)); mQty.textContent = qty; sync(); }));
  const needsSize = () => sizesFor(current).length && !size;
  $('#modalAdd').addEventListener('click', () => {
    if (needsSize()) { hint.hidden = false; sizeList.querySelector('.opt__btn')?.focus(); return; }
    add(current.id, qty, size, color); closePanel(modal);
  });
  $('#modalWa').addEventListener('click', e => {
    if (current.stock !== false && needsSize()) { e.preventDefault(); hint.hidden = false; sizeList.querySelector('.opt__btn')?.focus(); }
  });

  document.addEventListener('click', e => {
    const v = e.target.closest('[data-view]'); if (v) openProduct(v.dataset.view);
  });

  /* ---------- Envoi de la commande ---------- */
  form.addEventListener('submit', e => {
    e.preventDefault();
    const f = new FormData(form);
    const lines = cart.map(l => {
      const p = byId[l.id]; const o = optsTxt(l);
      return `• ${l.q} × *${p.nom}* (${D.categories[p.cat]})${o ? ' — ' + o : ''}${p.prix !== null ? ' — ' + p.prixTxt : ''}`;
    });
    let msg = `${T.hello}\n${T.iAm} ${f.get('nom').trim()} ${T.iOrder}\n\n${lines.join('\n')}\n\n📦 ${f.get('mode')}`;
    const note = (f.get('note') || '').trim();
    if (note) msg += `\n📝 ${note}`;
    msg += `\n\n${T.seePhotos}\n${orderLink(cart, f.get('nom').trim())}`;
    msg += `\n\n${T.confirmMsg}`;
    openWa(msg);
  });

  /* ---------- Formulaire de contact ---------- */
  const contact = $('#contactForm');
  contact?.addEventListener('submit', e => {
    e.preventDefault();
    const f = new FormData(contact);
    openWa(`${T.hello}\n${T.iAm} ${f.get('nom').trim()}.\n${T.subject} : ${f.get('sujet')}\n\n${f.get('message').trim()}`);
  });

  /* ---------- Filtres de la collection ---------- */
  const grid = $('#productGrid');
  if (grid) {
    const chips = $$('.chip'), search = $('#search'), empty = $('#empty');
    const norm = s => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
    let filter = 'all';
    const apply = () => {
      const q = norm(search.value.trim());
      let shown = 0;
      $$('.card', grid).forEach(c => {
        const ok = (filter === 'all' || c.dataset.cat === filter) && (!q || norm(c.dataset.name).includes(q));
        c.classList.toggle('is-hidden', !ok);
        if (ok) { shown++; c.classList.add('is-in'); }
      });
      empty.hidden = shown > 0;
    };
    const setFilter = f => {
      filter = chips.some(c => c.dataset.filter === f) ? f : 'all';
      chips.forEach(c => { const on = c.dataset.filter === filter; c.classList.toggle('is-active', on); c.setAttribute('aria-selected', String(on)); });
      apply();
    };
    chips.forEach(c => c.addEventListener('click', () => {
      setFilter(c.dataset.filter);
      if (!window.MF_PREVIEW) history.replaceState(null, '', filter === 'all' ? location.pathname : '#' + filter);
    }));
    search.addEventListener('input', apply);
    setFilter(window.MF_PREVIEW ? 'all' : (location.hash.slice(1) || 'all'));
  }

  /* ---------- Visionneuse photos ---------- */
  const lb = $('#lightbox');
  const shots = $$('[data-lb]');
  if (lb && shots.length) {
    const lbImg = $('#lbImg'), lbCap = $('#lbCap');
    let cur = 0, lbFrom;
    const show = i => { cur = (i + shots.length) % shots.length; const im = $('img', shots[cur]); lbImg.src = im.currentSrc || im.src; lbImg.alt = im.alt; lbCap.textContent = im.alt; };
    const openLb = i => { lbFrom = document.activeElement; show(i); lb.classList.add('is-open'); lb.setAttribute('aria-hidden', 'false'); document.body.classList.add('no-scroll'); $('[data-lb-close]', lb).focus(); };
    const closeLb = () => { lb.classList.remove('is-open'); lb.setAttribute('aria-hidden', 'true'); document.body.classList.remove('no-scroll'); lbFrom?.focus?.(); };
    shots.forEach(b => b.addEventListener('click', () => openLb(shots.indexOf(b))));
    $('[data-lb-close]', lb).addEventListener('click', closeLb);
    $$('[data-lb-step]', lb).forEach(b => b.addEventListener('click', () => show(cur + Number(b.dataset.lbStep))));
    lb.addEventListener('click', e => { if (e.target === lb) closeLb(); });
    addEventListener('keydown', e => {
      if (!lb.classList.contains('is-open')) return;
      if (e.key === 'Escape') closeLb();
      if (e.key === 'ArrowRight') show(cur + 1);
      if (e.key === 'ArrowLeft') show(cur - 1);
    });
    let x0 = null;
    lb.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', e => { if (x0 === null) return; const dx = e.changedTouches[0].clientX - x0; if (Math.abs(dx) > 40) show(cur + (dx < 0 ? 1 : -1)); x0 = null; });
  }

  renderCart();
})();
