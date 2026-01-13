<?php
defined('ABSPATH') || exit;

if (!function_exists('gs_is_mayorista') || !gs_is_mayorista()) {
    echo '<div class="p-6 rounded-3xl border border-blue-900/10 bg-yellow-50 text-yellow-900">
          <p class="font-extrabold text-lg">Acceso restringido</p>
          <p class="text-sm mt-1">Este catálogo es exclusivo para clientes mayoristas.</p>
        </div>';
    return;
}

$q   = isset($_GET['q']) ? sanitize_text_field(wp_unslash($_GET['q'])) : '';
$cat = isset($_GET['cat']) ? sanitize_text_field(wp_unslash($_GET['cat'])) : '';
$pg  = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;

$per_page = 12;

$tax_query = [];
if ($cat !== '') {
    $tax_query[] = [
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => [$cat],
    ];
}

$args = [
    'post_type'      => 'product',
    'post_status'    => 'publish',
    's'              => $q,
    'posts_per_page' => $per_page,
    'paged'          => $pg,
];

if (!empty($tax_query)) $args['tax_query'] = $tax_query;

$wpq = new WP_Query($args);

$cats = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
]);
?>

<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-blue-900">Catálogo mayorista</h2>
            <p class="text-sm text-blue-900/60 mt-1">Aquí verás tus precios especiales de mayorista.</p>
        </div>

        <form method="get" action="" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <input type="text" name="q" value="<?php echo esc_attr($q); ?>" placeholder="Buscar productos..."
                class="w-full sm:w-72 rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300">

            <select name="cat"
                class="w-full sm:w-56 rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300">
                <option value="">Todas las categorías</option>
                <?php foreach ($cats as $c): ?>
                    <option value="<?php echo esc_attr($c->slug); ?>" <?php selected($cat, $c->slug); ?>>
                        <?php echo esc_html($c->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="pg" value="1">
            <button class="rounded-2xl bg-blue-900 px-5 py-3 text-sm font-extrabold text-white hover:bg-yellow-400 hover:text-blue-900 transition">
                Filtrar
            </button>
        </form>
    </div>

    <?php if (!$wpq->have_posts()): ?>
        <div class="p-4 rounded-2xl bg-blue-50 text-blue-900/80">
            No encontramos productos con esos filtros.
        </div>
    <?php else: ?>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            <?php while ($wpq->have_posts()): $wpq->the_post();
                $p = wc_get_product(get_the_ID());
                if (!$p) continue;

                $id = $p->get_id();
                $regular = (float) $p->get_regular_price();
                $wh_raw  = get_post_meta($id, '_gs_precio_mayorista', true);
                $wh      = $wh_raw !== '' ? (float) str_replace(',', '.', $wh_raw) : (float) $p->get_price();
            ?>
                <div class="group rounded-3xl border border-blue-900/10 bg-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition overflow-hidden">
                    <a href="<?php echo esc_url(get_permalink($id)); ?>" class="block">
                        <div class="aspect-square bg-white flex items-center justify-center p-4">
                            <?php echo $p->get_image('woocommerce_thumbnail', ['class' => 'w-full h-full object-contain group-hover:scale-[1.04] transition']); ?>
                        </div>
                    </a>

                    <div class="p-4">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[11px] font-extrabold uppercase tracking-[0.18em] text-blue-900/50">Mayorista</span>
                            <span class="text-[11px] font-extrabold bg-yellow-400 text-blue-900 px-2 py-1 rounded-full">Precio especial</span>
                        </div>

                        <a href="<?php echo esc_url(get_permalink($id)); ?>"
                            class="mt-2 block text-sm font-extrabold text-blue-900 line-clamp-2">
                            <?php echo esc_html($p->get_name()); ?>
                        </a>

                        <div class="mt-2">
                            <?php if ($regular > 0 && $wh > 0 && $wh < $regular): ?>
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-extrabold text-blue-900"><?php echo wp_kses_post(wc_price($wh)); ?></span>
                                    <span class="text-xs text-gray-400 line-through"><?php echo wp_kses_post(wc_price($regular)); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="text-lg font-extrabold text-blue-900"><?php echo wp_kses_post($p->get_price_html()); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-3">
                            <?php if ($p->is_type('simple') && $p->is_purchasable() && $p->is_in_stock()): ?>
                                <a href="<?php echo esc_url($p->add_to_cart_url()); ?>"
                                    data-quantity="1"
                                    data-product_id="<?php echo esc_attr($id); ?>"
                                    data-product_sku="<?php echo esc_attr($p->get_sku()); ?>"
                                    class="button add_to_cart_button ajax_add_to_cart w-full text-center rounded-2xl bg-blue-900 px-4 py-2 text-sm font-extrabold text-white hover:bg-yellow-400 hover:text-blue-900 transition"
                                    rel="nofollow">
                                    Añadir
                                </a>
                            <?php else: ?>
                                <a href="<?php echo esc_url(get_permalink($id)); ?>"
                                    class="w-full inline-flex items-center justify-center rounded-2xl bg-blue-50 text-blue-900 px-4 py-2 text-sm font-extrabold hover:bg-blue-900 hover:text-white transition">
                                    Ver opciones
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>

        <?php
        $total_pages = (int) $wpq->max_num_pages;

        if ($total_pages > 1):
            $current = $pg;

            // base url manteniendo filtros (q, cat) pero reemplazando pg
            $base_url = remove_query_arg('pg');
            $build_url = function ($page) use ($base_url) {
                return add_query_arg('pg', $page, $base_url);
            };

            // Ventana alrededor de la página actual
            $window = 2;

            $pages = [];

            // Siempre mostrar 1
            $pages[] = 1;

            // Dots si hay salto
            $start = max(2, $current - $window);
            $end   = min($total_pages - 1, $current + $window);

            if ($start > 2) $pages[] = 'dots';

            // Páginas del centro
            for ($i = $start; $i <= $end; $i++) $pages[] = $i;

            if ($end < $total_pages - 1) $pages[] = 'dots';

            // Siempre mostrar última
            if ($total_pages > 1) $pages[] = $total_pages;
        ?>

            <div class="pt-8 flex items-center justify-center">
                <div class="flex items-center gap-2 flex-wrap justify-center max-w-3xl">

                    <!-- Prev -->
                    <a href="<?php echo esc_url($build_url(max(1, $current - 1))); ?>"
                        class="<?php echo $current <= 1 ? 'pointer-events-none opacity-40' : 'hover:bg-blue-900 hover:text-white'; ?>
                px-4 py-2 rounded-2xl text-sm font-extrabold bg-blue-50 text-blue-900 transition">
                        ←
                    </a>

                    <?php foreach ($pages as $p): ?>
                        <?php if ($p === 'dots'): ?>
                            <span class="px-3 py-2 text-sm font-extrabold text-blue-900/50">…</span>
                        <?php else: ?>
                            <a href="<?php echo esc_url($build_url($p)); ?>"
                                class="<?php echo ((int)$p === (int)$current)
                                            ? 'bg-blue-900 text-white'
                                            : 'bg-blue-50 text-blue-900 hover:bg-blue-900 hover:text-white'; ?>
                    px-4 py-2 rounded-2xl text-sm font-extrabold transition">
                                <?php echo (int) $p; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <!-- Next -->
                    <a href="<?php echo esc_url($build_url(min($total_pages, $current + 1))); ?>"
                        class="<?php echo $current >= $total_pages ? 'pointer-events-none opacity-40' : 'hover:bg-blue-900 hover:text-white'; ?>
                px-4 py-2 rounded-2xl text-sm font-extrabold bg-blue-50 text-blue-900 transition">
                        →
                    </a>

                </div>
            </div>

        <?php endif; ?>


    <?php endif; ?>
</div>