(function () {

  function getKullanici() {
    try { return JSON.parse(localStorage.getItem('kullanici')) || null; } catch { return null; }
  }

  async function girisYap() {
    const emailEl = document.getElementById('loginEmail');
    const passEl  = document.getElementById('loginPass');
    const err     = document.getElementById('loginErr');
    const btn     = document.querySelector('#panel-giris .dp-btn');
    if (!emailEl || !passEl) return;

    const email = emailEl.value.trim();
    const pass  = passEl.value;

    if (!email || !pass) {
      if (err) { err.textContent = 'E-posta ve şifre zorunludur.'; err.style.display = 'block'; }
      return;
    }

    if (btn) { btn.textContent = 'Giriş yapılıyor...'; btn.disabled = true; }

    try {
      const res  = await fetch('/Loba/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, sifre: pass })
      });
      const data = await res.json();

      if (!data.basari) {
        if (err) { err.textContent = data.mesaj || 'Giriş başarısız.'; err.style.display = 'block'; }
        return;
      }

      const k = data.kullanici;
      const kullanici = {
        user_id: k.user_id,
        ad:      k.user_ad,
        soyad:   k.user_soyad   || '',
        email:   k.user_mail,
        telefon: k.user_telefon || '',
        adres:   k.user_adres   || '',
        il:      k.user_il      || '',
        ilce:    k.user_ilce    || ''
      };

      localStorage.setItem('kullanici', JSON.stringify(kullanici));
      renderNavAccount();
      const dd = document.getElementById('accountDropdown');
      if (dd) dd.classList.remove('open');

    } catch (e) {
      if (err) { err.textContent = 'Sunucuya ulaşılamadı.'; err.style.display = 'block'; }
    } finally {
      if (btn) { btn.textContent = 'Giriş Yap'; btn.disabled = false; }
    }
  }

  async function kayitOl() {
    const adEl    = document.getElementById('regAd');
    const emailEl = document.getElementById('regEmail');
    const passEl  = document.getElementById('regPass');
    const err     = document.getElementById('regErr');
    const btn     = document.querySelector('#panel-kayit .dp-btn');
    if (!adEl || !emailEl || !passEl) return;

    const ad    = adEl.value.trim();
    const email = emailEl.value.trim();
    const pass  = passEl.value;

    if (!ad || !email || !pass) {
      if (err) { err.textContent = 'Ad, e-posta ve şifre zorunludur.'; err.style.display = 'block'; }
      return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      if (err) { err.textContent = 'Geçerli bir e-posta girin.'; err.style.display = 'block'; }
      return;
    }

    if (btn) { btn.textContent = 'Kaydediliyor...'; btn.disabled = true; }

    const payload = {
      ad,
      soyad:   (document.getElementById('regSoyad')  || {}).value?.trim() || '',
      email,
      sifre:   pass,
      telefon: (document.getElementById('regTel')    || {}).value?.trim() || '',
      adres:   (document.getElementById('regAdres')  || {}).value?.trim() || '',
      il:      (document.getElementById('regIl')     || {}).value?.trim() || '',
      ilce:    (document.getElementById('regIlce')   || {}).value?.trim() || ''
    };

    try {
      const res  = await fetch('/Loba/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (!data.basari) {
        if (err) { err.textContent = data.mesaj || 'Kayıt başarısız.'; err.style.display = 'block'; }
        return;
      }

      const kullanici = {
        user_id: data.user_id || null,
        ad:      payload.ad,
        soyad:   payload.soyad,
        email:   payload.email,
        telefon: payload.telefon,
        adres:   payload.adres,
        il:      payload.il,
        ilce:    payload.ilce
      };
      localStorage.setItem('kullanici', JSON.stringify(kullanici));
      renderNavAccount();
      const dd = document.getElementById('accountDropdown');
      if (dd) dd.classList.remove('open');

    } catch (e) {
      if (err) { err.textContent = 'Sunucuya ulaşılamadı.'; err.style.display = 'block'; }
    } finally {
      if (btn) { btn.textContent = 'Kayıt Ol'; btn.disabled = false; }
    }
  }

  function cikisYap() {
    localStorage.removeItem('kullanici');
    window.location.href = 'index.html';
  }

  function toggleDropdown(e) {
    e.preventDefault();
    const dd = document.getElementById('accountDropdown');
    if (dd) dd.classList.toggle('open');
  }

  function switchTab(tab) {
    document.querySelectorAll('.dropdown-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.dropdown-panel').forEach(p => p.classList.remove('active'));
    const activeTab = document.querySelector(`.dropdown-tab[data-tab="${tab}"]`);
    if (activeTab) activeTab.classList.add('active');
    const panel = document.getElementById('panel-' + tab);
    if (panel) panel.classList.add('active');
  }

  function updateCartBadge() {
    try {
      const cart  = JSON.parse(localStorage.getItem('novaCart')) || [];
      const total = cart.reduce((s, i) => s + i.qty, 0);
      const badge = document.getElementById('cartBadge');
      if (badge) { badge.textContent = total; badge.style.display = total > 0 ? 'flex' : 'none'; }
    } catch (e) {}
  }

  function renderNavAccount() {
    const kullanici = getKullanici();
    const area = document.getElementById('navAccountArea');
    if (!area) return;

    if (kullanici) {
      area.innerHTML = `
        <div style="position:relative;">
          <a onclick="window.__novaAuth.toggleDropdown(event)" href="#"
             style="display:flex;align-items:center;gap:6px;background:var(--accent);color:#0d0d0d;
                    font-weight:600;font-size:0.9rem;padding:8px 18px;border-radius:24px;text-decoration:none;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            ${kullanici.ad} ▾
          </a>
          <div class="account-dropdown" id="accountDropdown">
            <div style="padding:8px;">
              <div style="padding:14px 14px 10px;border-bottom:1px solid var(--border);margin-bottom:4px;">
                <div style="font-weight:600;font-size:0.9rem;color:var(--text);">
                  ${kullanici.ad} ${kullanici.soyad || ''}
                </div>
                <div style="font-size:0.78rem;color:var(--muted);margin-top:2px;">${kullanici.email}</div>
              </div>
              <a href="hesap.html"
                 style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;
                        color:var(--text);text-decoration:none;font-size:0.875rem;font-weight:500;transition:background 0.2s;"
                 onmouseover="this.style.background='rgba(232,192,125,0.08)'"
                 onmouseout="this.style.background='none'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                Profilim
              </a>
              <a href="sepet.html"
                 style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;
                        color:var(--text);text-decoration:none;font-size:0.875rem;font-weight:500;transition:background 0.2s;"
                 onmouseover="this.style.background='rgba(232,192,125,0.08)'"
                 onmouseout="this.style.background='none'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                  <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                Sepetim
              </a>
              <div style="border-top:1px solid var(--border);margin:4px 0;"></div>
              <a onclick="window.__novaAuth.cikisYap()" href="#"
                 style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:10px;
                        color:#e07070;text-decoration:none;font-size:0.875rem;font-weight:500;transition:background 0.2s;"
                 onmouseover="this.style.background='rgba(224,112,112,0.08)'"
                 onmouseout="this.style.background='none'">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                  <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Çıkış Yap
              </a>
            </div>
          </div>
        </div>
      `;
    } else {
      area.innerHTML = `
        <div style="position:relative;">
          <a onclick="window.__novaAuth.toggleDropdown(event)" href="#"
             style="display:flex;align-items:center;gap:6px;background:var(--accent);color:#0d0d0d;
                    font-weight:600;font-size:0.9rem;padding:8px 18px;border-radius:24px;text-decoration:none;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            Hesabım ▾
          </a>
          <div class="account-dropdown" id="accountDropdown">
            <div class="dropdown-tabs">
              <div class="dropdown-tab active" data-tab="giris" onclick="window.__novaAuth.switchTab('giris')">Giriş Yap</div>
              <div class="dropdown-tab" data-tab="kayit" onclick="window.__novaAuth.switchTab('kayit')">Kayıt Ol</div>
            </div>
            <div class="dropdown-panel active" id="panel-giris">
              <div class="dp-title">Hoş Geldiniz 👋</div>
              <div class="form-row full"><input class="dp-input" id="loginEmail" type="email" placeholder="E-posta adresi"/></div>
              <div class="form-row full"><input class="dp-input" id="loginPass" type="password" placeholder="Şifre"/></div>
              <p style="color:#e07070;font-size:0.78rem;margin-top:6px;display:none;" id="loginErr"></p>
              <button class="dp-btn" onclick="window.__novaAuth.girisYap()">Giriş Yap</button>
            </div>
            <div class="dropdown-panel" id="panel-kayit">
              <div class="dp-title">Yeni Hesap</div>
              <div class="form-row">
                <input class="dp-input" id="regAd"    type="text"  placeholder="Ad"/>
                <input class="dp-input" id="regSoyad" type="text"  placeholder="Soyad"/>
              </div>
              <div class="form-row full"><input class="dp-input" id="regTel"   type="tel"      placeholder="Telefon"/></div>
              <div class="form-row full"><input class="dp-input" id="regEmail" type="email"    placeholder="E-posta"/></div>
              <div class="form-row full"><input class="dp-input" id="regPass"  type="password" placeholder="Şifre oluştur"/></div>
              <div class="form-row full"><input class="dp-input" id="regAdres" type="text"     placeholder="Adres"/></div>
              <div class="form-row">
                <input class="dp-input" id="regIl"   type="text" placeholder="İl"/>
                <input class="dp-input" id="regIlce" type="text" placeholder="İlçe"/>
              </div>
              <p style="color:#e07070;font-size:0.78rem;margin-top:6px;display:none;" id="regErr"></p>
              <button class="dp-btn" onclick="window.__novaAuth.kayitOl()">Kayıt Ol</button>
            </div>
          </div>
        </div>
      `;
    }

    document.addEventListener('click', function(e) {
      const area = document.getElementById('navAccountArea');
      const dd   = document.getElementById('accountDropdown');
      if (area && dd && !area.contains(e.target)) dd.classList.remove('open');
    });

    updateCartBadge();
  }

  window.__novaAuth = { girisYap, kayitOl, cikisYap, toggleDropdown, switchTab, renderNavAccount, updateCartBadge, getKullanici };

  document.addEventListener('DOMContentLoaded', function () {
    renderNavAccount();
  });

})();
