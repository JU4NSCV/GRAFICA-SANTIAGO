(() => {
  const grid = document.getElementById("wishlistGrid");
  const input = document.getElementById("wishlistSearch");
  const noRes = document.getElementById("wishlistNoResults");
  const clearBtn = document.getElementById("wishlistClear");

  // Buscar (filtra por nombre)
  if (input && grid) {
    input.addEventListener("input", () => {
      const q = input.value.trim().toLowerCase();
      let shown = 0;

      grid.querySelectorAll(".wishlist-item").forEach((card) => {
        const title = (card.dataset.title || "").toLowerCase();
        const ok = title.includes(q);
        card.classList.toggle("hidden", !ok);
        if (ok) shown++;
      });

      if (noRes) noRes.classList.toggle("hidden", shown !== 0);
    });
  }

  // Eliminar item
  document.addEventListener("click", async (e) => {
    const btn = e.target.closest(".wishlist-remove");
    if (!btn) return;

    const productId = btn.dataset.productId;
    if (!productId) return;

    const fd = new FormData();
    fd.append("action", "gs_wishlist_remove");
    fd.append("nonce", GS_WISHLIST.nonce);
    fd.append("product_id", productId);

    const res = await fetch(GS_WISHLIST.ajax, { method: "POST", body: fd });
    const data = await res.json();

    if (data?.success) {
      const card = btn.closest(".wishlist-item");
      if (card) card.remove();

      // actualizar contador header
      document.querySelectorAll(".js-wishlist-count").forEach((el) => {
        el.textContent = data.data.count;
        el.classList.toggle("hidden", data.data.count <= 0);
      });
    }
  });

  // Vaciar
  if (clearBtn) {
    clearBtn.addEventListener("click", async () => {
      const fd = new FormData();
      fd.append("action", "gs_wishlist_clear");
      fd.append("nonce", GS_WISHLIST.nonce);

      const res = await fetch(GS_WISHLIST.ajax, { method: "POST", body: fd });
      const data = await res.json();

      if (data?.success) window.location.reload();
    });
  }
})();
