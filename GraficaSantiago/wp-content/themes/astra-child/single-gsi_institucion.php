<?php
get_header();

$inst_id = get_the_ID(); // <-- ahora viene del CPT, no de ?inst=

// Validación básica
if (!$inst_id || get_post_type($inst_id) !== 'gsi_institucion') : ?>
    <main class="max-w-4xl mx-auto px-6 py-16">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
            <h1 class="text-2xl font-bold text-gray-900">Institución no encontrada</h1>
            <p class="text-gray-600 mt-2">Vuelve al buscador y selecciona una institución válida.</p>
            <a href="<?php echo esc_url(home_url('/')); ?>"
                class="inline-flex mt-4 px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-gray-800 transition">
                Volver al inicio
            </a>
        </div>
    </main>
<?php
    get_footer();
    exit;
endif;

// 2) Datos dinámicos (meta simples)
$nombre      = get_the_title($inst_id);
$direccion   = get_post_meta($inst_id, 'direccion', true);
$telefono    = get_post_meta($inst_id, 'telefono', true);
$anio        = get_post_meta($inst_id, 'anio_lectivo', true);

$cover = get_the_post_thumbnail_url($inst_id, 'full');
if (!$cover) $cover = get_stylesheet_directory_uri() . '/assets/img/ESCUELA.jpg';

$logo = get_post_meta($inst_id, 'logo_url', true);
if (!$logo) $logo = 'https://cdn-icons-png.flaticon.com/512/2997/2997235.png';

// Defaults si no hay meta
if (!$anio) $anio = '2025-2026';
if (!$direccion) $direccion = 'Dirección no registrada';
if (!$telefono) $telefono = '—';
$has_escuela = get_post_meta($inst_id, 'has_escuela', true) === '1';
$has_colegio = get_post_meta($inst_id, 'has_colegio', true) === '1';

// Si por error no marcaron nada, por defecto muestra ambos
if (!$has_escuela && !$has_colegio) {
    $has_escuela = true;
    $has_colegio = true;
}


?>
<main class="w-full">

    <section class="min-h-screen pb-20">

        <!-- HERO -->
        <div class="relative h-64 md:h-80 w-full overflow-hidden">
            <a href="<?php echo esc_url(home_url('/')); ?>"
                class="absolute top-6 left-6 z-20 bg-white/20 backdrop-blur-md border border-white/30 text-white px-4 py-2 rounded-full text-sm font-bold hover:bg-white hover:text-blue-900 transition">
                ← Volver al buscador
            </a>

            <img src="<?php echo esc_url($cover); ?>" class="w-full h-full object-cover" alt="Campus">
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

            <div class="absolute bottom-0 left-0 w-full p-6 md:p-12 text-white flex flex-col md:flex-row items-end md:items-center justify-between gap-4">
                <div class="flex items-end gap-6">
                    <div class="w-24 h-24 md:w-32 md:h-32 bg-white rounded-2xl shadow-xl flex items-center justify-center -mb-10 md:-mb-14 relative z-10 border-4 border-white overflow-hidden">
                        <img src="<?php echo esc_url($logo); ?>" alt="Escudo" class="w-20 h-20 object-contain">
                    </div>

                    <div class="mb-2">
                        <span class="bg-yellow-500 text-black text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-2 inline-block">
                            Año Lectivo <?php echo esc_html($anio); ?>
                        </span>

                        <h1 class="text-3xl md:text-5xl font-bold tracking-tight !text-white">
                            <?php echo esc_html($nombre); ?>
                        </h1>

                        <p class="text-gray-300 text-sm md:text-base mt-1 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <?php echo esc_html($direccion); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 md:pt-24 space-y-12">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- Columna info -->
                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">👋 Estimados Padres</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">
                            Bienvenidos a la tienda oficial. Las listas han sido verificadas por la institución.
                            Seleccione el curso para ver los materiales requeridos.
                        </p>

                        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Soporte</p>
                                <p class="text-sm font-semibold text-gray-900"><?php echo esc_html($telefono); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna selectores -->
                <div class="lg:col-span-8">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Selecciona el nivel del estudiante</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <?php if ($has_escuela): ?>
                                <div class="group cursor-pointer relative">
                                    <div class="absolute inset-0 bg-blue-50 rounded-2xl transform group-hover:scale-105 transition-transform duration-200"></div>
                                    <div class="relative p-6 border-2 border-blue-100 rounded-2xl group-hover:border-blue-500 transition-colors">
                                        <div class="flex items-center gap-3 mb-4">
                                            <span class="text-3xl">🧸</span>
                                            <h4 class="font-bold text-gray-900">Escuela / Básica</h4>
                                        </div>
                                        <select id="escuelaSelect" onchange="mostrarTarjeta('Escuela', this.value)"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                                            <option value="" selected disabled>Elegir grado...</option>
                                            <option value="1ero de Básica">1ero de Básica</option>
                                            <option value="2do de Básica">2do de Básica</option>
                                            <option value="3ero de Básica">3ero de Básica</option>
                                            <option value="4to de Básica">4to de Básica</option>
                                            <option value="5to de Básica">5to de Básica</option>
                                            <option value="6to de Básica">6to de Básica</option>
                                            <option value="7mo de Básica">7mo de Básica</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($has_colegio): ?>
                                <div class="group cursor-pointer relative">
                                    <div class="absolute inset-0 bg-gray-50 rounded-2xl transform group-hover:scale-105 transition-transform duration-200"></div>
                                    <div class="relative p-6 border-2 border-gray-200 rounded-2xl group-hover:border-gray-900 transition-colors">
                                        <div class="flex items-center gap-3 mb-4">
                                            <span class="text-3xl">🎓</span>
                                            <h4 class="font-bold text-gray-900">Colegio / BGU</h4>
                                        </div>
                                        <select id="colegioSelect" onchange="mostrarTarjeta('Colegio', this.value)"
                                            class="w-full p-3 bg-white border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-gray-900 outline-none">
                                            <option value="" selected disabled>Elegir curso...</option>
                                            <option value="8vo EGB">8vo EGB</option>
                                            <option value="9no EGB">9no EGB</option>
                                            <option value="10mo EGB">10mo EGB</option>
                                            <option value="1BGU">1BGU</option>
                                            <option value="2BGU">2BGU</option>
                                            <option value="3BGU">3BGU</option>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>


                        <!-- Tarjeta acciones -->
                        <div id="actionCard" class="hidden mt-8 fade-in">
                            <div class="bg-gray-900 text-white rounded-2xl p-6 flex flex-col md:flex-row items-center justify-between gap-4 shadow-2xl shadow-gray-400">
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-widest mb-1">Has seleccionado:</p>
                                    <p class="text-2xl font-bold"><span id="nivelSeleccionado">--</span></p>
                                    <p id="kitResumen" class="text-sm text-gray-300 mt-1">Cargando lista...</p>
                                </div>

                                <div class="flex gap-3 w-full md:w-auto">
                                    <button id="btnVerItems"
                                        onclick="toggleItems()"
                                        class="flex-1 md:flex-none px-6 py-3 rounded-xl border border-gray-600 hover:bg-gray-800 transition text-sm font-bold">
                                        Ver items
                                    </button>

                                    <button id="btnComprarKit"
                                        onclick="comprarKit()"
                                        class="flex-1 md:flex-none px-6 py-3 rounded-xl bg-yellow-500 text-black font-bold hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20 text-sm">
                                        Comprar Kit
                                    </button>
                                </div>
                            </div>

                            <!-- Tabla items -->
                            <div id="itemsWrap" class="hidden mt-6 bg-white border border-gray-100 rounded-2xl overflow-hidden">
                                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                    <p class="font-bold text-gray-900">Lista de útiles</p>
                                    <p id="totalKit" class="font-bold text-gray-900"></p>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50 text-gray-600">
                                            <tr>
                                                <th class="text-left px-5 py-3 font-semibold">Producto</th>
                                                <th class="text-center px-5 py-3 font-semibold">Cant.</th>
                                                <th class="text-right px-5 py-3 font-semibold">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemsTbody" class="divide-y divide-gray-100"></tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

            <!-- Tus 4 cards de beneficios (igual que tenías) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pb-12">
                <!-- ... (tu bloque tal cual) ... -->
            </div>

        </div>
    </section>
</main>

<script>
    // Config WP
    const GSI = {
        ajaxUrl: "<?php echo esc_url(admin_url('admin-ajax.php')); ?>",
        nonce: "<?php echo esc_html(wp_create_nonce('gsi_institucion_nonce')); ?>",
        institucionId: "<?php echo (int) $inst_id; ?>"
    };

    let currentListId = null;
    let currentItems = [];

    function mostrarTarjeta(tipo, nivel) {
        const card = document.getElementById('actionCard');
        const titulo = document.getElementById('nivelSeleccionado');
        const resumen = document.getElementById('kitResumen');

        // Selects (puede que uno no exista)
        const escuelaSel = document.getElementById('escuelaSelect');
        const colegioSel = document.getElementById('colegioSelect');

        // Resetear el otro select SOLO si existe
        if (tipo === 'Escuela' && colegioSel) colegioSel.selectedIndex = 0;
        if (tipo === 'Colegio' && escuelaSel) escuelaSel.selectedIndex = 0;

        // Mostrar tarjeta y texto
        if (titulo) titulo.innerText = nivel || '--';
        if (resumen) resumen.innerText = 'Cargando lista...';
        if (card) card.classList.remove('hidden');

        // Llamar a AJAX
        const segmento = (tipo === 'Escuela') ? 'escuela' : 'colegio';
        cargarLista(segmento, nivel);
    }

    async function cargarLista(segmento, curso) {
        const fd = new FormData();
        fd.append('action', 'gsi_get_lista');
        fd.append('nonce', GSI.nonce);
        fd.append('institucion_id', GSI.institucionId);
        fd.append('segmento', segmento);
        fd.append('curso', curso);

        const res = await fetch(GSI.ajaxUrl, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        if (!data.success) {
            currentListId = null;
            currentItems = [];
            document.getElementById('itemsWrap').classList.add('hidden');
            document.getElementById('kitResumen').innerText = data.data?.message || 'No existe lista para este curso.';
            alert(data.data?.message || 'No existe lista para este curso.');
            return;
        }

        currentListId = data.data.list_id;
        currentItems = data.data.items || [];

        renderItems(currentItems, data.data.total);
    }

    function renderItems(items, totalHtml) {
        const tbody = document.getElementById('itemsTbody');
        const totalKit = document.getElementById('totalKit');
        const resumen = document.getElementById('kitResumen');

        tbody.innerHTML = '';

        if (!items.length) {
            resumen.innerText = 'Lista vacía.';
            totalKit.innerText = '';
            return;
        }

        resumen.innerText = `Incluye ${items.length} productos (libros, cuadernos, etc.)`;
        totalKit.innerHTML = totalHtml || '';

        for (const it of items) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
        <td class="px-5 py-4 text-gray-900 font-medium">${escapeHtml(it.name || it.sku)}</td>
        <td class="px-5 py-4 text-center text-gray-700">${it.qty}</td>
        <td class="px-5 py-4 text-right text-gray-900 font-semibold">${formatMoney(it.subtotal)}</td>
      `;
            tbody.appendChild(tr);
        }
    }

    function toggleItems() {
        const wrap = document.getElementById('itemsWrap');
        wrap.classList.toggle('hidden');
    }

    async function comprarKit() {
        if (!currentListId) return alert('Primero selecciona un curso que tenga lista disponible.');

        const fd = new FormData();
        fd.append('action', 'gsi_add_kit');
        fd.append('nonce', GSI.nonce);
        fd.append('list_id', currentListId);

        const res = await fetch(GSI.ajaxUrl, {
            method: 'POST',
            body: fd
        });
        const data = await res.json();

        if (!data.success) return alert(data.data?.message || 'No se pudo agregar el kit');

        if (data.data.redirect) window.location.href = data.data.redirect;
    }

    function formatMoney(val) {
        const n = Number(val || 0);
        // Esto es solo visual (el total real viene de wc_price desde el backend)
        return n.toLocaleString('es-EC', {
            style: 'currency',
            currency: 'USD'
        });
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, (m) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        } [m]));
    }
</script>

<?php get_footer(); ?>