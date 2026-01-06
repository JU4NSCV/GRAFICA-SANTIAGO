(() => {
  // Config inyectada por wp_localize_script
  const cfg = window.GS_WISHLIST || {};
  const ajaxUrl = cfg.ajax_url || cfg.ajax || cfg.ajaxUrl || (window.ajaxurl || '/wp-admin/admin-ajax.php');
  const nonce = cfg.nonce || '';

  if (!ajaxUrl || !nonce) {
    // No interrumpimos el sitio, pero esto te ayuda a ver el problema en consola.
    console.warn('[Wishlist] Falta configuración (ajaxUrl/nonce). Revisa wp_localize_script.');
  }

  const setCount = (count) => {
    document.querySelectorAll('.js-wishlist-count').forEach((el) => {
      el.textContent = String(count);
      el.classList.toggle('hidden', Number(count) <= 0);
    });
  };

  const setBtnState = (btn, inWishlist) => {
    btn.classList.toggle('is-active', !!inWishlist);
    btn.setAttribute('aria-pressed', inWishlist ? 'true' : 'false');

    // Rellenar el corazón
    const svg = btn.querySelector('svg');
    if (svg) {
      // Si el SVG tiene fill="none" en el tag, lo sobreescribimos.
      svg.setAttribute('fill', inWishlist ? 'currentColor' : 'none');
    }
  };

  const post = async (productId, mode = 'toggle') => {
    const fd = new FormData();
    fd.append('action', 'gs_toggle_wishlist');
    fd.append('nonce', nonce);
    fd.append('product_id', String(productId));
    fd.append('mode', mode);

    const res = await fetch(ajaxUrl, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
    });

    // WordPress suele responder JSON; si no, esto te deja ver el HTML de error
    const text = await res.text();
    try {
      return JSON.parse(text);
    } catch (e) {
      console.error('[Wishlist] Respuesta no-JSON:', text);
      return { success: false, data: { message: 'Respuesta inválida del servidor.' } };
    }
  };

  // Click handler global (funciona también con productos cargados por AJAX)
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.js-wishlist-toggle, .gs-wishlist-btn');
    if (!btn) return;

    const productId = btn.dataset.productId || btn.getAttribute('data-product-id');
    if (!productId) return;

    // Evita doble click
    if (btn.dataset.loading === '1') return;
    btn.dataset.loading = '1';

    try {
      const mode = btn.dataset.mode || 'toggle';
      const resp = await post(productId, mode);

      if (resp?.success) {
        const data = resp.data || {};
        setBtnState(btn, !!data.in_wishlist);
        if (typeof data.count !== 'undefined') setCount(data.count);
      } else {
        const msg = resp?.data?.message || 'No se pudo actualizar la wishlist.';
        console.warn('[Wishlist]', msg);
      }
    } catch (err) {
      console.error('[Wishlist] Error:', err);
    } finally {
      btn.dataset.loading = '0';
    }
  });

  // Estado inicial (por si quieres marcar en JS algunos botones)
  // Aquí no hacemos nada extra porque ya lo marcamos desde PHP en templates.
})();
