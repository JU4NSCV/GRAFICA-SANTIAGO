<?php
/* Template Name: Blog */
get_header();

// =========================
// IMÁGENES (URLs directas)
// =========================
$banner1 = "https://images.unsplash.com/photo-1519681393784-d120267933ba?auto=format&fit=crop&w=2000&q=80";
$banner2 = "https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=2000&q=80";
$banner3 = "https://images.unsplash.com/photo-1588072432836-7fb78b5a2b6f?auto=format&fit=crop&w=2000&q=80";

$img40      = "https://images.unsplash.com/photo-1513475382585-d06e58bcb0e0?auto=format&fit=crop&w=2000&q=80";
$imgAprende = "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=2000&q=80";
$imgCrono   = "https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?auto=format&fit=crop&w=2000&q=80";

if (!function_exists('gsi_badge_for_post')) {
    function gsi_badge_for_post()
    {
        $cats = get_the_category();
        if (empty($cats)) return ['BLOG', 'bg-white/90 text-azulOsc'];

        $slug = $cats[0]->slug;
        $name = strtoupper($cats[0]->name);

        if (in_array($slug, ['tip', 'tips'], true))           return ['TIP', 'bg-amarillo text-azulOsc'];
        if (in_array($slug, ['aprende', 'tutoriales'], true)) return ['APRENDE', 'bg-azulOsc text-white'];
        if (in_array($slug, ['novedad', 'novedades'], true))  return ['NOVEDAD', 'bg-amarillo text-azulOsc'];

        return [$name, 'bg-white/90 text-azulOsc'];
    }
}

$shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/productos/');
?>

<main class="w-full text-slate-900">

    <!-- Fondo general (suave, estilo marca) -->
    <div class="w-screen relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]">
        <div class="bg-gradient-to-b from-blue-50 via-white to-white">
            <div class="max-w-7xl mx-auto px-4 pt-6 pb-10">

                <!-- HERO SLIDER -->
                <section class="relative">
                    <div class="relative rounded-[28px] overflow-hidden shadow-soft border border-azulOsc/10 bg-white">
                        <div class="relative aspect-[16/8] min-h-[220px] sm:min-h-[320px] md:min-h-[420px] lg:min-h-[520px]" id="heroSlider">

                            <?php
                            $slides = [
                                [
                                    'img' => $banner1,
                                    'tag' => ['PROMO', 'bg-amarillo text-azulOsc'],
                                    'title' => 'Útiles, bazar y más',
                                    'desc' => 'Encuentra ofertas y productos para escuela, oficina y emprendimientos.',
                                    'btn1' => ['#apartados', 'Ver blog →', 'bg-amarillo text-azulOsc'],
                                    'btn2' => [$shop_url, 'Ir a catálogo', 'bg-white/90 text-azulOsc hover:bg-white'],
                                ],
                                [
                                    'img' => $banner2,
                                    'tag' => ['APRENDE', 'bg-white/90 text-azulOsc'],
                                    'title' => 'Tips y tutoriales',
                                    'desc' => 'Lettering, organización y decoración usando productos de la tienda.',
                                    'btn1' => ['#aprende', 'Entrar a Aprende →', 'bg-amarillo text-azulOsc'],
                                    'btn2' => ['', '', ''],
                                ],
                                [
                                    'img' => $banner3,
                                    'tag' => ['HISTORIA', 'bg-amarillo text-azulOsc'],
                                    'title' => 'Creciendo contigo',
                                    'desc' => 'Conoce nuestras secciones, eventos, cronogramas y novedades.',
                                    'btn1' => ['#historia', 'Ver 40 años →', 'bg-white/90 text-azulOsc hover:bg-white'],
                                    'btn2' => ['', '', ''],
                                ],
                            ];
                            ?>

                            <?php foreach ($slides as $i => $s): ?>
                                <div class="absolute inset-0 transition-opacity duration-700 <?php echo $i === 0 ? 'opacity-100' : 'opacity-0'; ?>" data-slide>
                                    <img src="<?php echo esc_url($s['img']); ?>" class="w-full h-full object-cover" alt="Slide">
                                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/20 to-transparent"></div>

                                    <div class="absolute inset-0 flex items-center">
                                        <div class="p-6 sm:p-10 md:p-14 max-w-2xl text-white">
                                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold shadow <?php echo esc_attr($s['tag'][1]); ?>">
                                                <?php echo esc_html($s['tag'][0]); ?>
                                            </div>

                                            <h1 class="mt-3 text-2xl sm:text-4xl md:text-5xl font-black leading-tight">
                                                <?php echo esc_html($s['title']); ?>
                                            </h1>

                                            <p class="mt-2 text-sm sm:text-base text-white/90 max-w-xl">
                                                <?php echo esc_html($s['desc']); ?>
                                            </p>

                                            <div class="mt-5 flex flex-wrap gap-3">
                                                <?php if (!empty($s['btn1'][0])): ?>
                                                    <a href="<?php echo esc_url($s['btn1'][0]); ?>"
                                                        class="px-5 py-3 rounded-2xl font-extrabold shadow transition hover:brightness-95 <?php echo esc_attr($s['btn1'][2]); ?>">
                                                        <?php echo esc_html($s['btn1'][1]); ?>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (!empty($s['btn2'][0])): ?>
                                                    <a href="<?php echo esc_url($s['btn2'][0]); ?>"
                                                        class="px-5 py-3 rounded-2xl font-extrabold shadow transition <?php echo esc_attr($s['btn2'][2]); ?>">
                                                        <?php echo esc_html($s['btn2'][1]); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Flechas (más finas) -->
                            <button type="button" data-prev
                                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full
                       bg-white/70 backdrop-blur-md border border-white/30 shadow-lg
                       grid place-items-center text-azulOsc hover:bg-azulOsc hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <button type="button" data-next
                                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full
                       bg-white/70 backdrop-blur-md border border-white/30 shadow-lg
                       grid place-items-center text-azulOsc hover:bg-azulOsc hover:text-white transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <!-- Dots -->
                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2 z-20" data-dots></div>
                        </div>
                    </div>

                    <!-- mini barra de confianza -->
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="rounded-2xl border border-azulOsc/10 bg-white shadow-soft p-4">
                            <p class="text-xs font-extrabold text-azulOsc">Contenido útil</p>
                            <p class="text-sm text-azulOsc/70 mt-1">Tips, tutoriales y novedades de la tienda.</p>
                        </div>
                        <div class="rounded-2xl border border-azulOsc/10 bg-white shadow-soft p-4">
                            <p class="text-xs font-extrabold text-azulOsc">Inspiración</p>
                            <p class="text-sm text-azulOsc/70 mt-1">Ideas para oficina, escuela y creatividad.</p>
                        </div>
                        <div class="rounded-2xl border border-azulOsc/10 bg-white shadow-soft p-4">
                            <p class="text-xs font-extrabold text-azulOsc">A un clic del catálogo</p>
                            <p class="text-sm text-azulOsc/70 mt-1">Recomendamos productos desde el blog.</p>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>

    <!-- APARTADOS -->
    <section class="max-w-7xl mx-auto px-4 mt-10" id="apartados">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-azulOsc">Explora el blog</h2>
                <p class="text-sm text-azulOsc/70 mt-1">40 años, aprende con nosotros y cronograma de novedades.</p>
            </div>
            <a href="#posts"
                class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-azulOsc text-azulOsc font-extrabold hover:bg-azulOsc hover:text-white transition">
                Ver publicaciones →
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Grande -->
            <a href="#"
                id="historia"
                class="lg:col-span-7 group relative overflow-hidden rounded-[28px] bg-white border border-azulOsc/10 shadow-soft
                hover:shadow-xl hover:-translate-y-1 transition">
                <div class="relative min-h-[340px] md:min-h-[440px]">
                    <img src="<?php echo esc_url($img40); ?>" alt="40 años"
                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.04] transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>

                    <div class="absolute top-5 left-5">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-amarillo text-azulOsc text-xs font-black shadow">
                            40 AÑOS
                        </span>
                    </div>

                    <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                        <h3 class="text-xl md:text-2xl font-black drop-shadow">40 años de historia</h3>
                        <p class="mt-1 text-sm text-white/85 line-clamp-2 max-w-xl">
                            Una trayectoria de servicio y variedad: escolar, bazar, oficina y creatividad.
                        </p>
                        <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/90 text-azulOsc font-extrabold
                        group-hover:bg-amarillo transition shadow">
                            Entrar →
                        </div>
                    </div>
                </div>
            </a>

            <!-- Derecha -->
            <div class="lg:col-span-5 grid grid-rows-2 gap-6">

                <a href="#"
                    id="aprende"
                    class="group relative overflow-hidden rounded-[28px] bg-white border border-azulOsc/10 shadow-soft hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="relative min-h-[210px] md:min-h-[220px]">
                        <img src="<?php echo esc_url($imgAprende); ?>" alt="Aprende con nosotros"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.04] transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>

                        <div class="absolute top-5 left-5">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-azulOsc text-white text-xs font-black shadow">
                                APRENDE
                            </span>
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                            <h3 class="text-base md:text-lg font-black drop-shadow">Aprende con nosotros</h3>
                            <p class="mt-1 text-xs text-white/85 line-clamp-2">
                                Tips, mini cursos y lives de lettering, decoración y organización.
                            </p>
                            <div class="mt-3 inline-flex items-center gap-2 text-xs font-extrabold text-white/95">
                                Entrar <span aria-hidden="true">→</span>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="#"
                    class="group relative overflow-hidden rounded-[28px] bg-white border border-azulOsc/10 shadow-soft hover:shadow-xl hover:-translate-y-1 transition">
                    <div class="relative min-h-[210px] md:min-h-[220px]">
                        <img src="<?php echo esc_url($imgCrono); ?>" alt="Cronograma"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.04] transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/10 to-transparent"></div>

                        <div class="absolute top-5 left-5">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-amarillo text-azulOsc text-xs font-black shadow">
                                NOVEDADES
                            </span>
                        </div>

                        <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
                            <h3 class="text-base md:text-lg font-black drop-shadow">Cronograma y eventos</h3>
                            <p class="mt-1 text-xs text-white/85 line-clamp-2">
                                Giveaways, envíos gratis, descuentos y fechas importantes.
                            </p>
                            <div class="mt-3 inline-flex items-center gap-2 text-xs font-extrabold text-white/95">
                                Entrar <span aria-hidden="true">→</span>
                            </div>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </section>

    <!-- POSTS -->
    <section class="max-w-7xl mx-auto px-4 mt-14" id="posts">
        <?php
        $paged    = max(1, get_query_var('paged') ? get_query_var('paged') : (isset($_GET['paged']) ? (int)$_GET['paged'] : 1));
        $cat_slug = isset($_GET['cat']) ? sanitize_title(wp_unslash($_GET['cat'])) : '';
        $search_q = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';

        $args = [
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 9,
            'paged'          => $paged,
        ];
        if ($cat_slug) $args['category_name'] = $cat_slug;
        if ($search_q) $args['s'] = $search_q;

        $q = new WP_Query($args);
        $cats = get_categories(['hide_empty' => true]);
        $base_url = get_permalink();
        ?>

        <!-- Header + search -->
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-black text-azulOsc">Blog Mega Santiago</h2>
                <p class="text-sm text-azulOsc/70 mt-1">Tips, novedades y contenido para escuela, oficina y creatividad.</p>
            </div>

            <form method="get" action="<?php echo esc_url($base_url); ?>" class="w-full lg:w-[640px]">
                <div class="bg-white p-2 rounded-full shadow-soft border border-azulOsc/10 flex items-center gap-2">
                    <div class="pl-3 text-azulOsc/50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <input type="text" name="q" value="<?php echo esc_attr($search_q); ?>"
                        placeholder="Buscar en el blog..."
                        class="w-full px-3 py-2 text-azulOsc outline-none bg-transparent placeholder-azulOsc/40" />

                    <div class="hidden md:block h-8 w-px bg-azulOsc/10"></div>

                    <select name="cat"
                        class="hidden md:block px-3 py-2 rounded-full border border-azulOsc/10 bg-white text-azulOsc font-semibold outline-none">
                        <option value="">Todas</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?php echo esc_attr($c->slug); ?>" <?php selected($cat_slug, $c->slug); ?>>
                                <?php echo esc_html($c->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit"
                        class="px-6 py-2.5 rounded-full bg-amarillo text-azulOsc font-extrabold hover:brightness-95 transition shadow">
                        Buscar
                    </button>
                </div>

                <div class="md:hidden mt-2">
                    <select name="cat"
                        class="w-full px-4 py-3 rounded-2xl border border-azulOsc/10 bg-white text-azulOsc font-semibold outline-none">
                        <option value="">Todas las categorías</option>
                        <?php foreach ($cats as $c): ?>
                            <option value="<?php echo esc_attr($c->slug); ?>" <?php selected($cat_slug, $c->slug); ?>>
                                <?php echo esc_html($c->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <!-- Chips -->
        <div class="flex flex-wrap gap-2 mb-8">
            <?php
            $chips = [
                ['Todos', ''],
                ['Tips', 'tips'],
                ['Aprende', 'aprende'],
                ['Novedades', 'novedades'],
            ];
            foreach ($chips as $chip):
                $active = ($cat_slug === $chip[1]) || ($chip[1] === '' && $cat_slug === '');
                $chip_url = add_query_arg(array_filter(['cat' => $chip[1] ?: null, 'q' => $search_q ?: null]), $base_url);
            ?>
                <a href="<?php echo esc_url($chip_url); ?>"
                    class="px-4 py-2 rounded-full text-sm font-extrabold border transition shadow-sm
           <?php echo $active
                    ? 'bg-azulOsc text-white border-azulOsc'
                    : 'bg-white text-azulOsc border-azulOsc/15 hover:border-azulOsc hover:bg-azulOsc hover:text-white'; ?>">
                    <?php echo esc_html($chip[0]); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($q->have_posts()): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($q->have_posts()): $q->the_post();
                    $thumb = get_the_post_thumbnail_url(get_the_ID(), 'large');
                    if (!$thumb) $thumb = $img40;

                    [$badge_text, $badge_class] = gsi_badge_for_post();
                    $date = get_the_date('d M, Y');

                    $content_words = str_word_count(wp_strip_all_tags(get_the_content(null, false, get_the_ID())));
                    $read_min = max(1, (int)ceil($content_words / 200));
                    $excerpt = wp_trim_words(get_the_excerpt(), 22, '…');
                ?>
                    <article class="group bg-white rounded-[28px] border border-azulOsc/10 shadow-soft overflow-hidden
                         hover:shadow-xl hover:-translate-y-1 transition flex flex-col">
                        <a href="<?php the_permalink(); ?>" class="block">
                            <div class="relative aspect-[16/10] overflow-hidden">
                                <img src="<?php echo esc_url($thumb); ?>"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.05] transition duration-700"
                                    alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-black shadow <?php echo esc_attr($badge_class); ?>">
                                        <?php echo esc_html($badge_text); ?>
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex items-center justify-between gap-2 text-[12px] text-azulOsc/60 font-semibold">
                                    <span><?php echo esc_html($date); ?></span>
                                    <span class="inline-flex px-2 py-1 rounded-full bg-azulOsc/5 text-azulOsc/70">
                                        <?php echo esc_html($read_min . ' min'); ?>
                                    </span>
                                </div>

                                <h3 class="mt-2 text-azulOsc font-black leading-snug line-clamp-2 group-hover:text-blue-700 transition-colors">
                                    <?php the_title(); ?>
                                </h3>

                                <p class="mt-2 text-sm text-azulOsc/70 line-clamp-3">
                                    <?php echo esc_html($excerpt); ?>
                                </p>

                                <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-azulOsc text-white font-extrabold
                            group-hover:bg-amarillo group-hover:text-azulOsc transition w-fit">
                                    Leer →
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endwhile;
                wp_reset_postdata(); ?>
            </div>

            <?php
            $links = paginate_links([
                'total'     => $q->max_num_pages,
                'current'   => $paged,
                'type'      => 'array',
                'prev_text' => '‹',
                'next_text' => '›',
                'add_args'  => array_filter(['q' => $search_q ?: null, 'cat' => $cat_slug ?: null]),
            ]);

            if ($links):
                foreach ($links as &$l) {
                    $l = str_replace('page-numbers', 'page-numbers inline-flex items-center justify-center min-w-[44px] h-11 px-4 rounded-full border border-azulOsc/15 bg-white text-azulOsc font-extrabold hover:bg-azulOsc hover:text-white transition shadow-sm', $l);
                    $l = str_replace('current', 'current !bg-amarillo !text-azulOsc !border-amarillo', $l);
                }
            ?>
                <nav class="mt-10 flex flex-wrap gap-2 justify-center">
                    <?php foreach ($links as $l) echo $l; ?>
                </nav>
            <?php endif; ?>

        <?php else: ?>
            <div class="bg-white rounded-3xl border border-azulOsc/10 p-10 text-center shadow-soft">
                <h3 class="text-lg font-black text-azulOsc">No encontramos publicaciones</h3>
                <p class="mt-2 text-sm text-azulOsc/70">Prueba cambiando el filtro o buscando con otra palabra.</p>
            </div>
        <?php endif; ?>

        <!-- CTA final -->
        <div class="mt-14 bg-azulOsc rounded-[28px] p-8 md:p-10 text-white shadow-soft relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                </svg>
            </div>
            <div class="relative flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-black">¿Listo para comprar más rápido?</h3>
                    <p class="text-white/80 mt-1">Visita el catálogo y encuentra ofertas en útiles, oficina y bazar.</p>
                </div>
                <a href="<?php echo esc_url($shop_url); ?>"
                    class="px-6 py-3 rounded-2xl bg-amarillo text-azulOsc font-extrabold hover:brightness-95 transition shadow">
                    Ir a catálogo →
                </a>
            </div>
        </div>

    </section>

    <!-- JS Slider (autoplay + dots + arrows) -->
    <script>
        (function() {
            const root = document.getElementById('heroSlider');
            if (!root) return;

            const slides = Array.from(root.querySelectorAll('[data-slide]'));
            const dotsWrap = root.querySelector('[data-dots]');
            const prev = root.querySelector('[data-prev]');
            const next = root.querySelector('[data-next]');
            if (!slides.length || !dotsWrap) return;

            let idx = 0;
            let timer = null;

            dotsWrap.innerHTML = '';
            const dots = slides.map((_, i) => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = (i === 0) ? 'h-2 w-8 rounded-full bg-white/90' : 'h-2 w-2 rounded-full bg-white/40';
                b.addEventListener('click', () => go(i));
                dotsWrap.appendChild(b);
                return b;
            });

            function render() {
                slides.forEach((s, i) => {
                    s.classList.toggle('opacity-100', i === idx);
                    s.classList.toggle('opacity-0', i !== idx);
                });

                dots.forEach((d, i) => {
                    d.className = (i === idx) ? 'h-2 w-8 rounded-full bg-white/90 transition' : 'h-2 w-2 rounded-full bg-white/40 transition';
                });
            }

            function go(i) {
                idx = (i + slides.length) % slides.length;
                render();
                restart();
            }

            function restart() {
                if (timer) clearInterval(timer);
                timer = setInterval(() => go(idx + 1), 7000);
            }

            if (prev) prev.addEventListener('click', () => go(idx - 1));
            if (next) next.addEventListener('click', () => go(idx + 1));

            // Pausa si el usuario pone el mouse encima
            root.addEventListener('mouseenter', () => timer && clearInterval(timer));
            root.addEventListener('mouseleave', restart);

            render();
            restart();
        })();
    </script>

</main>

<?php get_footer(); ?>