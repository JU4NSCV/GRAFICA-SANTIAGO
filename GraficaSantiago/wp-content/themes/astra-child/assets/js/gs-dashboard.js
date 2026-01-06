(() => {
    const $ = (s) => document.querySelector(s);

    const statusEl = $("#gsd-status");
    const drawer = $("#gsd-drawer");
    const backdrop = $("#gsd-backdrop");
    const closeBtn = $("#gsd-close");
    const cancelBtn = $("#gsd-cancel");
    const saveBtn = $("#gsd-save");
    const drawerTitle = $("#gsd-drawer-title");
    const drawerBody = $("#gsd-drawer-body");
    const formStatus = $("#gsd-form-status");
    let productsSortBy = "date";   // default actual
    let productsSortDir = "DESC";  // default actual

    let activeTab = "products";

    let productsPage = 1;
    let usersPage = 1;

    let currentFormType = null; // "product" | "user"
    let currentEditingId = 0;

    function setStatus(msg) {
        if (statusEl) statusEl.textContent = msg || "";
    }

    function post(action, payload = {}) {
        const fd = new FormData();
        fd.append("action", action);
        fd.append("nonce", GSD.nonce);
        Object.entries(payload).forEach(([k, v]) => fd.append(k, v ?? ""));
        return fetch(GSD.ajaxUrl, { method: "POST", body: fd })
            .then(r => r.json());
    }

    // Tabs
    function showTab(tab) {
        activeTab = tab;
        const tabProducts = $("#tab-products");
        const tabUsers = $("#tab-users");
        const panelProducts = $("#panel-products");
        const panelUsers = $("#panel-users");

        if (tab === "products") {
            tabProducts.setAttribute("aria-selected", "true");
            tabUsers.setAttribute("aria-selected", "false");
            panelProducts.classList.remove("hidden");
            panelUsers.classList.add("hidden");
            tabProducts.classList.add("bg-yellow-400", "border-yellow-400");
            tabUsers.classList.remove("bg-yellow-400", "border-yellow-400");
            loadProducts();
        } else {
            tabProducts.setAttribute("aria-selected", "false");
            tabUsers.setAttribute("aria-selected", "true");
            panelProducts.classList.add("hidden");
            panelUsers.classList.remove("hidden");
            tabUsers.classList.add("bg-yellow-400", "border-yellow-400");
            tabProducts.classList.remove("bg-yellow-400", "border-yellow-400");
            loadUsers();
        }
    }

    // Drawer
    function openDrawer({ title, bodyHtml, type, id }) {
        currentFormType = type;
        currentEditingId = id || 0;
        drawerTitle.textContent = title;
        drawerBody.innerHTML = bodyHtml;
        formStatus.textContent = "";
        drawer.classList.remove("hidden");
        drawer.setAttribute("aria-hidden", "false");

        // Focus al primer input
        const first = drawerBody.querySelector("input, select, textarea, button");
        if (first) first.focus();
    }

    function closeDrawer() {
        drawer.classList.add("hidden");
        drawer.setAttribute("aria-hidden", "true");
        currentFormType = null;
        currentEditingId = 0;
    }

    [backdrop, closeBtn, cancelBtn].forEach(el => el?.addEventListener("click", closeDrawer));
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !drawer.classList.contains("hidden")) closeDrawer();
    });

    // Render helpers
    function money(v) {
        if (v === "" || v == null) return "—";
        return `$${String(v)}`;
    }
    function badgeStock(item) {
        const instock = item.stock_status === "instock";
        const txt = instock ? "En stock" : "Sin stock";
        const cls = instock ? "bg-emerald-50 text-emerald-700 border-emerald-200" : "bg-rose-50 text-rose-700 border-rose-200";
        return `<span class="inline-flex items-center px-2 py-1 rounded-xl border text-xs ${cls}">${txt}</span>`;
    }
    function btn(text, cls, attrs = "") {
        return `<button type="button" class="rounded-2xl px-3 py-2 text-xs font-semibold ${cls}
      focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900" ${attrs}>${text}</button>`;
    }

    // PRODUCTS
    async function loadProducts() {
        const tbody = $("#gsd-products-tbody");
        const pageEl = $("#gsd-products-page");
        const search = $("#gsd-product-search")?.value || "";

        setStatus("Cargando productos…");
        const res = await post("gsd_list_products", {
            page: productsPage,
            search,
            orderby: productsSortBy,
            order: productsSortDir
        });


        if (!res.success) {
            setStatus(res.data?.message || "Error cargando productos");
            return;
        }

        const items = res.data.items || [];
        tbody.innerHTML = items.map(p => `
      <tr>
        <td class="py-3 pr-4">
          <div class="font-semibold text-slate-800">${escapeHtml(p.name)}</div>
          <div class="text-xs text-slate-500">ID: ${p.id}</div>
        </td>
        <td class="py-3 pr-4 text-slate-700">${escapeHtml(p.sku || "—")}</td>
        <td class="py-3 pr-4 text-slate-700">${money(p.price)}</td>
        <td class="py-3 pr-4">${badgeStock(p)} <span class="text-xs text-slate-500 ml-2">${p.stock_quantity ?? ""}</span></td>
        <td class="py-3 pr-2 text-right">
          <div class="inline-flex gap-2 justify-end">
            ${btn("Editar", "border border-blue-900 text-blue-900 hover:bg-slate-50", `data-edit-product="${p.id}"`)}
            ${btn("Eliminar", "border border-rose-300 text-rose-700 hover:bg-rose-50", `data-del-product="${p.id}"`)}
          </div>
        </td>
      </tr>
    `).join("");

        pageEl.textContent = `Página ${res.data.page}${res.data.hasMore ? "" : " (fin)"}`;
        setStatus("");
    }

    async function openProductForm(id = 0) {
        if (id) {
            const res = await post("gsd_get_product", { id });
            if (!res.success) return alert(res.data?.message || "Error");
            const p = res.data;
            openDrawer({
                title: "Editar producto",
                type: "product",
                id,
                bodyHtml: productFormHtml(p)
            });
        } else {
            openDrawer({
                title: "Nuevo producto",
                type: "product",
                id: 0,
                bodyHtml: productFormHtml({ name: "", sku: "", price: "", manage_stock: true, stock_quantity: 0 })
            });
        }
    }

    function productFormHtml(p) {
        return `
      <div class="grid gap-4">
        <div>
          <label class="block text-sm font-semibold text-slate-700">Nombre</label>
          <input id="p-name" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
            focus:outline-none focus:ring-2 focus:ring-blue-900" value="${escapeAttr(p.name || "")}" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">SKU</label>
            <input id="p-sku" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
              focus:outline-none focus:ring-2 focus:ring-blue-900" value="${escapeAttr(p.sku || "")}" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Precio</label>
            <input id="p-price" inputmode="decimal" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
              focus:outline-none focus:ring-2 focus:ring-blue-900" value="${escapeAttr(p.price || "")}" />
          </div>
        </div>

        <div class="flex items-center gap-3">
          <input id="p-manage" type="checkbox" class="h-4 w-4" ${p.manage_stock ? "checked" : ""}/>
          <label for="p-manage" class="text-sm text-slate-700">Gestionar stock</label>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Cantidad</label>
          <input id="p-stock" inputmode="numeric" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
            focus:outline-none focus:ring-2 focus:ring-blue-900" value="${escapeAttr(p.stock_quantity ?? 0)}" />
          <p class="mt-1 text-xs text-slate-500">Tip: si cantidad es 0, se marcará como “Sin stock”.</p>
        </div>
      </div>
    `;
    }

    async function saveProduct() {
        const name = $("#p-name")?.value.trim();
        const sku = $("#p-sku")?.value.trim();
        const price = $("#p-price")?.value.trim();
        const manage_stock = $("#p-manage")?.checked ? "1" : "0";
        const stock_quantity = $("#p-stock")?.value.trim();

        formStatus.textContent = "Guardando…";

        const res = await post("gsd_save_product", {
            id: currentEditingId,
            name, sku, price, manage_stock, stock_quantity
        });

        if (!res.success) {
            formStatus.textContent = res.data?.message || "Error";
            return;
        }

        formStatus.textContent = "Listo ✅";
        closeDrawer();
        loadProducts();
    }

    async function deleteProduct(id) {
        if (!confirm("¿Eliminar este producto?")) return;
        const res = await post("gsd_delete_product", { id });
        if (!res.success) return alert(res.data?.message || "Error eliminando");
        loadProducts();
    }

    // USERS
    async function loadUsers() {
        const tbody = $("#gsd-users-tbody");
        const pageEl = $("#gsd-users-page");
        const search = $("#gsd-user-search")?.value || "";

        setStatus("Cargando usuarios…");
        const res = await post("gsd_list_users", { page: usersPage, search });

        if (!res.success) {
            setStatus(res.data?.message || "Error cargando usuarios");
            return;
        }

        const items = res.data.items || [];
        tbody.innerHTML = items.map(u => `
      <tr>
        <td class="py-3 pr-4">
          <div class="font-semibold text-slate-800">${escapeHtml(u.name || u.login)}</div>
          <div class="text-xs text-slate-500">@${escapeHtml(u.login)} — ID: ${u.id}</div>
        </td>
        <td class="py-3 pr-4 text-slate-700">${escapeHtml(u.email)}</td>
        <td class="py-3 pr-4 text-slate-700">${escapeHtml((u.roles && u.roles[0]) || "—")}</td>
        <td class="py-3 pr-2 text-right">
          <div class="inline-flex gap-2 justify-end">
            ${btn("Editar", "border border-blue-900 text-blue-900 hover:bg-slate-50", `data-edit-user="${u.id}"`)}
            ${btn("Eliminar", "border border-rose-300 text-rose-700 hover:bg-rose-50", `data-del-user="${u.id}"`)}
          </div>
        </td>
      </tr>
    `).join("");

        pageEl.textContent = `Página ${res.data.page}${res.data.hasMore ? "" : " (fin)"}`;
        setStatus("");
    }

    async function openUserForm(id = 0) {
        if (id) {
            const res = await post("gsd_get_user", { id });
            if (!res.success) return alert(res.data?.message || "Error");
            openDrawer({
                title: "Editar usuario",
                type: "user",
                id,
                bodyHtml: userFormHtml(res.data, true)
            });
        } else {
            openDrawer({
                title: "Nuevo usuario",
                type: "user",
                id: 0,
                bodyHtml: userFormHtml({ login: "", email: "", name: "", role: "customer" }, false)
            });
        }
    }

    function userFormHtml(u, isEdit) {
        return `
      <div class="grid gap-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Usuario (login)</label>
            <input id="u-login" ${isEdit ? "disabled" : ""} class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
              focus:outline-none focus:ring-2 focus:ring-blue-900 ${isEdit ? "bg-slate-100" : ""}"
              value="${escapeAttr(u.login || "")}" />
            ${isEdit ? `<p class="mt-1 text-xs text-slate-500">El login no se cambia.</p>` : ``}
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Email</label>
            <input id="u-email" type="email" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
              focus:outline-none focus:ring-2 focus:ring-blue-900" value="${escapeAttr(u.email || "")}" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700">Nombre para mostrar</label>
          <input id="u-name" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
            focus:outline-none focus:ring-2 focus:ring-blue-900" value="${escapeAttr(u.name || "")}" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-slate-700">Rol</label>
            <select id="u-role" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
              focus:outline-none focus:ring-2 focus:ring-blue-900">
              ${roleOption("customer", u.role)}
              ${roleOption("subscriber", u.role)}
              ${roleOption("shop_manager", u.role)}
              ${roleOption("administrator", u.role)}
            </select>
          </div>
          <div>
            <label class="block text-sm font-semibold text-slate-700">Contraseña ${isEdit ? "(opcional)" : ""}</label>
            <input id="u-pass" type="password" class="mt-1 w-full rounded-2xl border px-4 py-2 text-sm
              focus:outline-none focus:ring-2 focus:ring-blue-900" value="" />
            <p class="mt-1 text-xs text-slate-500">
              ${isEdit ? "Déjala vacía si no deseas cambiarla." : "Obligatoria al crear."}
            </p>
          </div>
        </div>
      </div>
    `;
    }

    function roleOption(val, current) {
        const sel = (val === current) ? "selected" : "";
        return `<option value="${val}" ${sel}>${val}</option>`;
    }

    async function saveUser() {
        const login = $("#u-login")?.value.trim();
        const email = $("#u-email")?.value.trim();
        const name = $("#u-name")?.value.trim();
        const role = $("#u-role")?.value;
        const password = $("#u-pass")?.value;

        formStatus.textContent = "Guardando…";

        const res = await post("gsd_save_user", {
            id: currentEditingId,
            login, email, name, role, password
        });

        if (!res.success) {
            formStatus.textContent = res.data?.message || "Error";
            return;
        }

        formStatus.textContent = "Listo ✅";
        closeDrawer();
        loadUsers();
    }
    function updateProductSortUI() {
        document.querySelectorAll(".gsd-sort").forEach(btn => {
            const icon = btn.querySelector(".gsd-sort-icon");
            const key = btn.dataset.sort;

            if (key === productsSortBy) {
                btn.setAttribute("aria-sort", productsSortDir === "ASC" ? "ascending" : "descending");
                if (icon) icon.textContent = productsSortDir === "ASC" ? "↑" : "↓";
            } else {
                btn.setAttribute("aria-sort", "none");
                if (icon) icon.textContent = "";
            }
        });
    }

    function initProductSort() {
        document.querySelectorAll(".gsd-sort").forEach(btn => {
            btn.addEventListener("click", () => {
                const key = btn.dataset.sort;

                if (productsSortBy === key) {
                    productsSortDir = (productsSortDir === "ASC") ? "DESC" : "ASC";
                } else {
                    productsSortBy = key;
                    productsSortDir = "ASC"; // primer click ordena ASC, segundo click DESC
                }

                productsPage = 1;
                updateProductSortUI();
                loadProducts();
            });
        });

        updateProductSortUI();
    }


    async function deleteUser(id) {
        if (!confirm("¿Eliminar este usuario?")) return;
        const res = await post("gsd_delete_user", { id });
        if (!res.success) return alert(res.data?.message || "Error eliminando");
        loadUsers();
    }

    // Events
    $("#tab-products")?.addEventListener("click", () => showTab("products"));
    $("#tab-users")?.addEventListener("click", () => showTab("users"));

    $("#gsd-add-product")?.addEventListener("click", () => openProductForm(0));
    $("#gsd-add-user")?.addEventListener("click", () => openUserForm(0));

    $("#gsd-product-search")?.addEventListener("input", () => { productsPage = 1; loadProducts(); });
    $("#gsd-user-search")?.addEventListener("input", () => { usersPage = 1; loadUsers(); });

    $("#gsd-prev-products")?.addEventListener("click", () => { productsPage = Math.max(1, productsPage - 1); loadProducts(); });
    $("#gsd-next-products")?.addEventListener("click", () => { productsPage += 1; loadProducts(); });

    $("#gsd-prev-users")?.addEventListener("click", () => { usersPage = Math.max(1, usersPage - 1); loadUsers(); });
    $("#gsd-next-users")?.addEventListener("click", () => { usersPage += 1; loadUsers(); });

    document.addEventListener("click", (e) => {
        const btnEditP = e.target.closest("[data-edit-product]");
        const btnDelP = e.target.closest("[data-del-product]");
        const btnEditU = e.target.closest("[data-edit-user]");
        const btnDelU = e.target.closest("[data-del-user]");

        if (btnEditP) openProductForm(parseInt(btnEditP.dataset.editProduct, 10));
        if (btnDelP) deleteProduct(parseInt(btnDelP.dataset.delProduct, 10));
        if (btnEditU) openUserForm(parseInt(btnEditU.dataset.editUser, 10));
        if (btnDelU) deleteUser(parseInt(btnDelU.dataset.delUser, 10));
    });

    saveBtn?.addEventListener("click", () => {
        if (currentFormType === "product") return saveProduct();
        if (currentFormType === "user") return saveUser();
    });

    // Init
    showTab("products");
    initProductSort();


    // Security escaping
    function escapeHtml(str) {
        return String(str ?? "").replace(/[&<>"']/g, s => ({
            "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;"
        }[s]));
    }
    function escapeAttr(str) { return escapeHtml(str).replace(/"/g, "&quot;"); }
})();
