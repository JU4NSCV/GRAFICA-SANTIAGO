<?php
/* Template Name: Home */
get_header();
?>

<main class="w-full">

    <!-- BANNER DE PROMOCIONES -->
    <?php $promo = gs_home_promos_context(); ?>

    <?php if ($promo['enabled']): ?>
        <section class="w-screen relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] bg-gradient-to-b from-blue-700 to-blue-500 py-0">
            <div class="w-full">

                <?php if (empty($promo['items'])): ?>
                    <div class="text-white/80 text-sm">No hay promociones cargadas todavía.</div>
                <?php else: ?>

                    <?php if ($promo['mode'] === 'manual'): ?>
                        <?php $it = $promo['items'][$promo['active_index']] ?? $promo['items'][0]; ?>

                        <div class="relative w-full h-[220px] sm:h-[320px] md:h-[420px] lg:h-[520px] overflow-hidden">
                            <?php if (!empty($it['link'])): ?>
                                <a href="<?php echo esc_url($it['link']); ?>" class="block h-full w-full" target="_blank" rel="noopener">
                                <?php endif; ?>

                                <img
                                    src="<?php echo esc_url($it['url']); ?>"
                                    alt="<?php echo esc_attr($it['alt']); ?>"
                                    class="absolute inset-0 w-full h-full object-cover"
                                    loading="eager" />

                                <?php if (!empty($it['link'])): ?>
                                </a>
                            <?php endif; ?>
                        </div>

                    <?php else: ?>
                        <div
                            class="relative w-full h-[220px] sm:h-[320px] md:h-[420px] lg:h-[520px] overflow-hidden"
                            data-gs-promo-carousel
                            data-interval="<?php echo esc_attr($promo['interval']); ?>">
                            <?php foreach ($promo['items'] as $k => $it): ?>
                                <div class="absolute inset-0 transition-opacity duration-700 <?php echo $k === 0 ? 'opacity-100' : 'opacity-0'; ?>" data-slide>
                                    <?php if (!empty($it['link'])): ?>
                                        <a href="<?php echo esc_url($it['link']); ?>" class="block h-full w-full" target="_blank" rel="noopener">
                                        <?php endif; ?>

                                        <img
                                            src="<?php echo esc_url($it['url']); ?>"
                                            alt="<?php echo esc_attr($it['alt']); ?>"
                                            class="w-full h-full object-cover"
                                            loading="<?php echo $k === 0 ? 'eager' : 'lazy'; ?>" />

                                        <?php if (!empty($it['link'])): ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <script>
                            (function() {
                                const root = document.querySelector('[data-gs-promo-carousel]');
                                if (!root) return;
                                const slides = Array.from(root.querySelectorAll('[data-slide]'));
                                if (slides.length <= 1) return;

                                const interval = parseInt(root.getAttribute('data-interval') || '4500', 10);
                                let i = 0;

                                setInterval(() => {
                                    slides[i].classList.remove('opacity-100');
                                    slides[i].classList.add('opacity-0');
                                    i = (i + 1) % slides.length;
                                    slides[i].classList.remove('opacity-0');
                                    slides[i].classList.add('opacity-100');
                                }, interval);
                            })();
                        </script>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </section>
    <?php endif; ?>




    <!-- DESTACADOS Y RECOMENDADOS -->
    <section class="mt-12 px-4 md:px-6 max-w-7xl mx-auto">
        <?php if (!gs_wc_active()): ?>
            <p class="text-sm text-red-500">WooCommerce no está activo.</p>
        <?php else: ?>
            <?php
            $data = gs_home_featured_recommended(3);
            $main = $data['main'];
            $side = $data['side'];
            $shop_link = gs_shop_link();
            ?>

            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900 leading-tight">
                        Destacados y recomendados
                    </h2>
                    <p class="text-sm text-blue-900/70 mt-1">
                        Productos seleccionados para ti. Aprovecha las mejores promociones.
                    </p>
                </div>

                <a href="<?php echo esc_url($shop_link); ?>"
                    class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-900 text-blue-900 font-semibold
				hover:bg-blue-900 hover:text-white transition">
                    Ver todo <span aria-hidden="true">→</span>
                </a>
            </div>

            <?php if (!$main): ?>
                <p class="text-sm text-gray-500">No hay productos disponibles.</p>
            <?php else: ?>

                <?php
                $main_link  = $main->get_permalink();
                $main_img   = gs_home_product_img($main);
                $main_desc  = gs_home_product_short($main, 18);
                $main_reg   = $main->get_regular_price();
                $main_price = $main->get_price();
                $main_disc  = gs_home_discount_label($main);
                ?>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <article class="lg:col-span-7">
                        <a href="<?php echo esc_url($main_link); ?>"
                            class="group block relative overflow-hidden rounded-2xl shadow-xl border border-blue-900/10 bg-white">
                            <div class="relative h-[340px] md:h-[420px]">
                                <img
                                    src="<?php echo esc_url($main_img); ?>"
                                    alt="<?php echo esc_attr($main->get_name()); ?>"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.02] transition duration-500"
                                    loading="lazy" />

                                <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-yellow-400 text-blue-900 shadow">
                                        <?php echo $main->is_on_sale() ? esc_html($main_disc) : '⭐ Destacado'; ?>
                                    </span>
                                </div>

                                <div class="absolute bottom-0 left-0 right-0 p-5 md:p-6 text-white">
                                    <h3 class="text-lg md:text-2xl font-extrabold leading-snug !text-white">
                                        <?php echo esc_html($main->get_name()); ?>
                                    </h3>

                                    <p class="text-sm text-white/85 mt-1 line-clamp-2">
                                        <?php echo esc_html($main_desc); ?>
                                    </p>

                                    <div class="mt-4 flex items-center justify-between gap-4">
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-2xl font-extrabold text-yellow-300">
                                                <?php echo wp_kses_post(wc_price((float)$main_price)); ?>
                                            </span>

                                            <?php if ($main->is_on_sale() && $main_reg): ?>
                                                <span class="text-xs text-white/70 line-through">
                                                    <?php echo wp_kses_post(wc_price((float)$main_reg)); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-blue-900 font-semibold
										group-hover:bg-yellow-400 transition">
                                            Ver producto <span aria-hidden="true">→</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </article>

                    <div class="lg:col-span-5 grid grid-rows-2 gap-6">
                        <?php foreach ($side as $p): ?>
                            <?php
                            $link  = $p->get_permalink();
                            $img   = gs_home_product_img($p);
                            $desc  = gs_home_product_short($p, 18);
                            $price = $p->get_price();
                            $disc  = gs_home_discount_label($p);
                            ?>
                            <article>
                                <a href="<?php echo esc_url($link); ?>"
                                    class="group block relative overflow-hidden rounded-2xl shadow-xl border border-blue-900/10 bg-white h-[200px] md:h-[205px]">

                                    <img
                                        src="<?php echo esc_url($img); ?>"
                                        alt="<?php echo esc_attr($p->get_name()); ?>"
                                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-[1.02] transition duration-500"
                                        loading="lazy" />

                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold
										bg-blue-900 text-white shadow">
                                            <?php echo $p->is_on_sale() ? esc_html($disc) : '👍 Recomendado'; ?>
                                        </span>
                                    </div>

                                    <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                                        <div class="flex items-end justify-between gap-3">
                                            <div>
                                                <h3 class="text-base font-extrabold leading-snug line-clamp-1 !text-white">
                                                    <?php echo esc_html($p->get_name()); ?>
                                                </h3>
                                                <p class="text-xs text-white/80 mt-1 line-clamp-1">
                                                    <?php echo esc_html($desc); ?>
                                                </p>
                                            </div>

                                            <div class="text-right">
                                                <div class="text-lg font-extrabold text-yellow-300">
                                                    <?php echo wp_kses_post(wc_price((float)$price)); ?>
                                                </div>
                                                <div class="text-[11px] text-white/70">Ver →</div>
                                            </div>
                                        </div>
                                    </div>

                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>

                </div>

                <div class="mt-6 sm:hidden">
                    <a href="<?php echo esc_url($shop_link); ?>"
                        class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-blue-900 text-white font-semibold
					hover:bg-yellow-400 hover:text-blue-900 transition">
                        Ver todo <span aria-hidden="true">→</span>
                    </a>
                </div>

            <?php endif; ?>
        <?php endif; ?>
    </section>



    <!-- CATEGORÍAS POPULARES -->

    <section class="mt-16 px-4 md:px-6 max-w-7xl mx-auto">
        <div class="flex items-end justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900">Categorías populares</h2>
                <p class="text-sm text-blue-900/70 mt-1">Explora lo más buscado en la tienda.</p>
            </div>

            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-900 text-blue-900 font-semibold
              hover:bg-blue-900 hover:text-white transition">
                Ver catálogo <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php
            // Cambia slugs + imágenes (puedes crear assets/img/categorias/)
            $cats = [
                [
                    'name' => 'ESCOLAR',
                    'slug' => 'escolar',
                    'img'   => get_stylesheet_directory_uri() . '/assets/img/categorias/escolar.jpg',
                    'badge' => '📚',
                ],
                [
                    'name' => 'BOLSOS',
                    'slug' => 'bolsos',
                    'img'   => get_stylesheet_directory_uri() . '/assets/img/categorias/bolsos.jpg',
                    'badge' => '🗂️',
                ],
                [
                    'name' => 'FIESTAS Y CUMPLEAÑOS',
                    'slug' => 'fiestas-y-cumpleanos',
                    'img'   => get_stylesheet_directory_uri() . '/assets/img/categorias/fiesta.jpg',
                    'badge' => '🎨',
                ],
                [
                    'name' => 'BISUTERIA',
                    'slug' => 'bisuteria',
                    'img'   => get_stylesheet_directory_uri() . '/assets/img/categorias/bisuteria.jpg',
                    'badge' => '🧺',
                ],
            ];

            foreach ($cats as $c):
                $term = get_term_by('slug', $c['slug'], 'product_cat');
                if (!$term || is_wp_error($term)) continue;

                $url = get_term_link($term);
                if (is_wp_error($url)) continue;
            ?>
                <a href="<?php echo esc_url($url); ?>"
                    class="group relative overflow-hidden rounded-2xl shadow-sm border border-blue-900/10
                h-[170px] sm:h-[190px] md:h-[210px]
                hover:-translate-y-1 hover:shadow-xl transition">

                    <!-- Imagen fondo -->
                    <img
                        src="<?php echo esc_url($c['img']); ?>"
                        alt="<?php echo esc_attr($c['name']); ?>"
                        class="absolute inset-0 w-full h-full object-cover
                 transition duration-500 ease-out
                 group-hover:scale-110 group-hover:blur-sm"
                        loading="lazy" />

                    <!-- Overlay para legibilidad -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-black/10
                    transition group-hover:from-black/60"></div>

                    <!-- Contenido -->
                    <div class="relative h-full p-4 sm:p-5 flex flex-col justify-between">
                        <div class="flex items-start justify-between gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold
                         bg-white/90 text-blue-900 backdrop-blur">
                                <?php echo esc_html($c['badge']); ?> Popular
                            </span>

                            <span class="text-xs font-bold text-white/90">
                                Ver →
                            </span>
                        </div>

                        <div>
                            <h3 class="text-white text-sm sm:text-base md:text-lg font-extrabold uppercase tracking-wide">
                                <?php echo esc_html($c['name']); ?>
                            </h3>
                            <p class="text-white/80 text-xs mt-1">
                                Explorar productos
                            </p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="mt-6 sm:hidden">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-blue-900 text-white font-semibold
              hover:bg-yellow-400 hover:text-blue-900 transition">
                Ver catálogo <span aria-hidden="true">→</span>
            </a>
        </div>
    </section>



    <!-- OFERTAS DESTACADAS -->
    <section class="mt-16 px-4 md:px-6 max-w-7xl mx-auto">
        <?php if (!gs_wc_active()): ?>
            <p class="text-sm text-red-500">WooCommerce no está activo.</p>
        <?php else: ?>
            <?php
            $offers    = gs_home_offers(4);
            $shop_link = gs_shop_link();
            ?>

            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900">Ofertas destacadas</h2>
                    <p class="text-sm text-blue-900/70 mt-1">Promociones por tiempo limitado.</p>
                </div>

                <a href="<?php echo esc_url($shop_link); ?>"
                    class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-900 text-blue-900 font-semibold
				hover:bg-blue-900 hover:text-white transition">
                    Ver ofertas <span aria-hidden="true">→</span>
                </a>
            </div>

            <?php if (empty($offers)) : ?>
                <p class="text-sm text-gray-500">
                    Por el momento no hay productos en oferta. (Debes asignar “Precio rebajado” a algún producto en WooCommerce).
                </p>
            <?php else : ?>
                <?php
                $left  = array_slice($offers, 0, 2);
                $right = array_slice($offers, 2, 2);
                ?>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

                    <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <?php foreach ($left as $p): ?>
                            <?php
                            $img   = gs_home_product_img($p);
                            $link  = $p->get_permalink();
                            $desc  = gs_home_product_short($p, 14);
                            $badge = gs_home_offer_badge($p);
                            [$price, $reg] = gs_home_offer_prices($p);
                            $cta = gs_home_cta($p);
                            ?>
                            <!-- aquí dejas tu HTML igual (solo cambia variables) -->
                            <!-- ... tu card grande ... -->
                        <?php endforeach; ?>
                    </div>

                    <div class="lg:col-span-4 grid grid-cols-1 gap-6">
                        <?php foreach ($right as $p): ?>
                            <?php
                            $img   = gs_home_product_img($p);
                            $link  = $p->get_permalink();
                            $badge = gs_home_offer_badge($p);
                            [$price, $reg] = gs_home_offer_prices($p);
                            ?>
                            <!-- aquí dejas tu HTML igual (solo cambia variables) -->
                            <!-- ... tu card pequeña ... -->
                        <?php endforeach; ?>
                    </div>

                </div>

                <div class="mt-6 sm:hidden">
                    <a href="<?php echo esc_url($shop_link); ?>"
                        class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-blue-900 text-white font-semibold
					hover:bg-yellow-400 hover:text-blue-900 transition">
                        Ver ofertas <span aria-hidden="true">→</span>
                    </a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>



    <!-- FUNCION PARA MOSTRAR PRODUCTOS POPULARES -->
    <?php
    if (!function_exists('gs_render_products_section')) {
        function gs_render_products_section($title, $args = [], $more_url = '')
        {

            if (!function_exists('wc_get_products')) {
                echo '<p class="text-sm text-red-500">WooCommerce no está activo.</p>';
                return;
            }

            $defaults = [
                'status'  => 'publish',
                'limit'   => 8,
                'orderby' => 'popularity',
            ];

            $products = wc_get_products(array_merge($defaults, $args));
            $fallback_img = get_stylesheet_directory_uri() . '/assets/img/servicios1.jpg';
    ?>

            <section class="mt-16 px-4 md:px-6 max-w-7xl mx-auto">
                <!-- Header -->
                <div class="flex items-end justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900"><?php echo esc_html($title); ?></h2>
                        <p class="text-sm text-blue-900/70 mt-1">Productos seleccionados para ti.</p>
                    </div>

                    <?php if (!empty($more_url)) : ?>
                        <a href="<?php echo esc_url($more_url); ?>"
                            class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-900 text-blue-900 font-semibold
                    hover:bg-blue-900 hover:text-white transition">
                            Ver más <span aria-hidden="true">→</span>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($products)) : ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($products as $product) :
                            $product_id = $product->get_id();
                            $permalink  = get_permalink($product_id);
                            $thumb_url  = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
                            $img_url    = $thumb_url ? $thumb_url : $fallback_img;

                            $is_simple_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
                            $btn_url   = $is_simple_cart ? $product->add_to_cart_url() : $permalink;
                            $btn_text  = $is_simple_cart ? $product->add_to_cart_text() : 'Ver opciones';
                            $btn_class = $is_simple_cart ? 'ajax_add_to_cart add_to_cart_button' : '';
                        ?>
                            <article class="group border border-gray-200 rounded-2xl p-3 shadow-sm bg-white flex flex-col
                            hover:shadow-lg hover:-translate-y-0.5 transition">

                                <a href="<?php echo esc_url($permalink); ?>" class="block mb-2">
                                    <div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-white border border-gray-100">
                                        <img src="<?php echo esc_url($img_url); ?>"
                                            alt="<?php echo esc_attr($product->get_name()); ?>"
                                            class="w-full h-full object-contain p-2 transition duration-300 group-hover:scale-[1.03]"
                                            loading="lazy">

                                        <?php if ($product->is_on_sale()) : ?>
                                            <span class="absolute top-2 left-2 px-2 py-1 rounded-full text-[11px] font-extrabold bg-yellow-400 text-blue-900 shadow">
                                                Oferta
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </a>

                                <h3 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
                                    <a href="<?php echo esc_url($permalink); ?>">
                                        <?php echo esc_html($product->get_name()); ?>
                                    </a>
                                </h3>

                                <p class="text-sm font-extrabold text-blue-900 mb-3">
                                    <?php echo wp_kses_post($product->get_price_html()); ?>
                                </p>

                                <a href="<?php echo esc_url($btn_url); ?>"
                                    class="mt-auto inline-flex items-center justify-center w-full text-xs font-semibold
                        border border-blue-900 rounded-2xl px-3 py-2
                        hover:bg-blue-900 hover:text-white transition <?php echo esc_attr($btn_class); ?>"
                                    data-product_id="<?php echo esc_attr($product_id); ?>"
                                    data-quantity="1"
                                    rel="nofollow">
                                    <?php echo esc_html($btn_text); ?>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>

                <?php else : ?>
                    <p class="text-sm text-gray-500">No hay productos disponibles por el momento.</p>
                <?php endif; ?>

                <?php if (!empty($more_url)) : ?>
                    <div class="mt-6 flex justify-center sm:hidden">
                        <a href="<?php echo esc_url($more_url); ?>"
                            class="px-5 py-3 bg-yellow-400 text-blue-900 font-extrabold shadow-md rounded-2xl
                    hover:shadow-lg hover:bg-blue-700 hover:text-white transition tracking-[0.10em]">
                            Ver más
                        </a>
                    </div>
                <?php endif; ?>
            </section>

    <?php
        }
    }
    ?>


    <!-- PRODUCTOS POPULARES -->
    <?php
    gs_render_products_section(
        'Productos populares',
        [
            'orderby' => 'popularity',
            'limit'   => 8,
        ],
        wc_get_page_permalink('shop')
    );
    ?>



    <!-- ESCOLARES -->
    <?php
    $term = get_term_by('slug', 'ESCOLAR', 'product_cat');
    $more = ($term && !is_wp_error($term)) ? get_term_link($term) : wc_get_page_permalink('shop');

    gs_render_products_section(
        'ESCOLAR',
        [
            'limit' => 8,
            'orderby' => 'popularity',
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => ['escolar'],
                    'include_children' => true,
                ],
            ],
        ],
        $more
    );
    ?>



    <!-- BOLSOS -->
    <?php
    $term = get_term_by('slug', 'BOLSOS', 'product_cat');
    $more = ($term && !is_wp_error($term)) ? get_term_link($term) : wc_get_page_permalink('shop');

    gs_render_products_section(
        'BOLSOS',
        [
            'limit' => 8,
            'orderby' => 'popularity',
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => ['bolsos'],
                    'include_children' => true,
                ],
            ],
        ],
        $more
    );
    ?>



    <!-- FIESTAS Y CUMPLEANOS -->
    <?php
    $term = get_term_by('slug', 'FIESTAS Y CUMPLEAÑOS', 'product_cat');
    $more = ($term && !is_wp_error($term)) ? get_term_link($term) : wc_get_page_permalink('shop');

    gs_render_products_section(
        'FIESTAS Y CUMPLEAÑOS',
        [
            'limit' => 8,
            'orderby' => 'popularity',
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => ['fiestas-y-cumpleanos'],
                    'include_children' => true,
                ],
            ],
        ],
        $more
    );
    ?>
    <!-- BISUTERIA -->
    <?php
    $term = get_term_by('slug', 'BISUTERIA', 'product_cat');
    $more = ($term && !is_wp_error($term)) ? get_term_link($term) : wc_get_page_permalink('shop');

    gs_render_products_section(
        'BISUTERIA',
        [
            'limit' => 8,
            'orderby' => 'popularity',
            'tax_query' => [
                [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => ['bisuteria'],
                    'include_children' => true,
                ],
            ],
        ],
        $more
    );
    ?>


    <!-- EXPLORA POR CATEGORÍA -->
    <section class="mt-16 px-4 md:px-6 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-end justify-between gap-4 mb-5">
            <div>
                <h2 class="text-2xl md:text-3xl font-extrabold text-blue-900">
                    Explora por categoría
                </h2>
                <p class="text-sm text-blue-900/70 mt-1">
                    Encuentra productos rápido por secciones.
                </p>
            </div>

            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-900 text-blue-900 font-semibold
              hover:bg-blue-900 hover:text-white transition">
                Ver catálogo <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="relative">
            <!-- Fade laterales (señal de scroll) -->
            <div class="pointer-events-none absolute left-0 top-0 h-full w-16 z-20 bg-gradient-to-r from-white via-white/80 to-transparent"></div>
            <div class="pointer-events-none absolute right-0 top-0 h-full w-16 z-20 bg-gradient-to-l from-white via-white/80 to-transparent"></div>

            <!-- Flechas -->
            <button type="button" id="catPrev"
                class="hidden md:flex absolute left-2 top-1/2 -translate-y-1/2 z-30 h-11 w-11 rounded-full
         bg-white/70 backdrop-blur-md border border-blue-900/10 shadow-lg
         items-center justify-center text-blue-900
         hover:bg-blue-900 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-blue-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>


            <button type="button" id="catNext"
                class="hidden md:flex absolute right-2 top-1/2 -translate-y-1/2 z-30 h-11 w-11 rounded-full
         bg-white/70 backdrop-blur-md border border-blue-900/10 shadow-lg
         items-center justify-center text-blue-900
         hover:bg-blue-900 hover:text-white transition focus:outline-none focus:ring-2 focus:ring-blue-900/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>


            <!-- Contenedor scroll -->
            <div
                id="categoryScroller"
                class="flex gap-4 overflow-x-auto px-10 md:px-14 py-3 scroll-smooth snap-x snap-mandatory
         [scrollbar-width:none] [-ms-overflow-style:none]
         rounded-3xl">
                <style>
                    #categoryScroller::-webkit-scrollbar {
                        display: none;
                    }
                </style>

                <?php
                $default_image = get_stylesheet_directory_uri() . '/assets/img/libro.jpg';

                $product_cats = get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'parent'     => 0,
                    'orderby'    => 'count',
                    'order'      => 'DESC',
                ]);

                if (!empty($product_cats) && !is_wp_error($product_cats)) :
                    foreach ($product_cats as $cat) :
                        $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                        $image_url    = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : $default_image;
                        $cat_link     = get_term_link($cat);
                ?>
                        <a
                            href="<?php echo esc_url($cat_link); ?>"
                            aria-label="Ver categoría <?php echo esc_attr($cat->name); ?>"
                            class="group relative snap-center flex-shrink-0
         w-[220px] sm:w-[240px] md:w-[260px]
         h-[155px] sm:h-[175px] md:h-[195px]
         rounded-3xl overflow-hidden
         border border-white/40 bg-white shadow-sm
         hover:shadow-xl hover:-translate-y-1 transition
         ring-1 ring-blue-900/5">
                            <!-- Fondo (zoom suave, SIN blur) -->
                            <img
                                src="<?php echo esc_url($image_url); ?>"
                                alt="<?php echo esc_attr($cat->name); ?>"
                                class="absolute inset-0 w-full h-full object-cover
           transition duration-700 ease-out
           group-hover:scale-[1.07]"
                                loading="lazy" />

                            <!-- Overlay más limpio (mejor lectura) -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                            <!-- “Glow” sutil -->
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition">
                                <div class="absolute -inset-10 bg-blue-600/10 blur-2xl"></div>
                            </div>

                            <!-- Badge (mejor) -->
                            <div class="absolute top-3 left-3 z-10">
                                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-extrabold
                 bg-yellow-400 text-blue-900 shadow">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-900"></span>
                                    Popular
                                </span>
                            </div>

                            <!-- Texto -->
                            <div class="absolute bottom-0 left-0 right-0 z-10 p-4">
                                <h3 class="text-sm md:text-base font-extrabold leading-tight line-clamp-1 !text-white drop-shadow">
                                    <?php echo esc_html($cat->name); ?>
                                </h3>

                                <div class="mt-1 flex items-center justify-between">
                                    <p class="text-xs text-white/85">
                                        <?php echo intval($cat->count); ?> producto<?php echo ($cat->count == 1) ? '' : 's'; ?>
                                    </p>

                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-2xl
                   bg-white/10 text-white border border-white/20 backdrop-blur-md
                   text-xs font-semibold">
                                        Ver <span aria-hidden="true">→</span>
                                    </span>
                                </div>
                            </div>
                        </a>

                    <?php
                    endforeach;
                else :
                    ?>
                    <p class="text-sm text-gray-500">No hay categorías disponibles.</p>
                <?php endif; ?>

            </div>

            <!-- Dots -->
            <div id="categoryDots" class="mt-4 flex justify-center gap-2"></div>
        </div>

        <!-- Botón móvil -->
        <div class="mt-6 sm:hidden">
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
                class="inline-flex w-full items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-blue-900 text-white font-semibold
              hover:bg-yellow-400 hover:text-blue-900 transition">
                Ver catálogo <span aria-hidden="true">→</span>
            </a>
        </div>

        <!-- JS (flechas + dots) -->
        <script>
            (function() {
                const scroller = document.getElementById('categoryScroller');
                const prev = document.getElementById('catPrev');
                const next = document.getElementById('catNext');
                const dotsWrap = document.getElementById('categoryDots');
                if (!scroller || !dotsWrap) return;

                const cards = Array.from(scroller.children).filter(el => el.tagName === 'A');
                if (!cards.length) return;

                // Crea dots
                dotsWrap.innerHTML = '';
                const dots = cards.map((_, i) => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'h-2 w-2 rounded-full bg-blue-900/15 hover:bg-blue-900/35 transition';
                    b.setAttribute('aria-label', 'Ir a categoría ' + (i + 1));
                    b.addEventListener('click', () => {
                        cards[i].scrollIntoView({
                            behavior: 'smooth',
                            inline: 'center',
                            block: 'nearest'
                        });
                    });
                    dotsWrap.appendChild(b);
                    return b;
                });

                const setActiveDot = () => {
                    // calcula el card más cercano al centro
                    const rect = scroller.getBoundingClientRect();
                    const center = rect.left + rect.width / 2;

                    let bestIdx = 0;
                    let bestDist = Infinity;

                    cards.forEach((card, i) => {
                        const r = card.getBoundingClientRect();
                        const c = r.left + r.width / 2;
                        const d = Math.abs(center - c);
                        if (d < bestDist) {
                            bestDist = d;
                            bestIdx = i;
                        }
                    });

                    dots.forEach((d, i) => {
                        d.className = (i === bestIdx) ?
                            'h-2 w-8 rounded-full bg-blue-900 transition' :
                            'h-2 w-2 rounded-full bg-blue-900/15 hover:bg-blue-900/35 transition';
                    });

                };

                // Flechas (scroll por ~1 card)
                const scrollByCard = (dir) => {
                    const cardWidth = cards[0].getBoundingClientRect().width;
                    scroller.scrollBy({
                        left: dir * (cardWidth + 16),
                        behavior: 'smooth'
                    });
                };

                if (prev && next) {
                    prev.addEventListener('click', () => scrollByCard(-1));
                    next.addEventListener('click', () => scrollByCard(1));
                }

                // Update dots
                let t = null;
                scroller.addEventListener('scroll', () => {
                    if (t) cancelAnimationFrame(t);
                    t = requestAnimationFrame(setActiveDot);
                });

                // Inicial
                setActiveDot();
            })();
        </script>
    </section>

</main>

<?php
get_footer();
?>