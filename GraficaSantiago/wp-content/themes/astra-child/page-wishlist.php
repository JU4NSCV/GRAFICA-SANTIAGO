<?php
/* Template Name: Wishlist */
defined('ABSPATH') || exit;

get_header();

$ids = function_exists('gs_wishlist_get_ids') ? gs_wishlist_get_ids() : [];
$ids = array_values(array_unique(array_filter(array_map('absint', (array)$ids))));

$shop_link = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/productos/');
$fallback_img = get_stylesheet_directory_uri() . '/assets/img/servicios1.jpg';
?>

<main class="w-full ">
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-10">

    <!-- Header -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-blue-900">Mi lista de deseos</h1>
        <p class="text-sm text-blue-900/70 mt-2">
          Productos guardados:
          <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full bg-yellow-400 text-blue-900 font-extrabold text-xs">
            <?php echo esc_html(count($ids)); ?>
          </span>
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
        <div class="relative w-full sm:w-[320px]">
          <input id="wishlistSearch" type="text" placeholder="Buscar en tu wishlist..."
            class="w-full h-12 rounded-2xl border border-blue-900/15 bg-white px-4 pr-10 text-sm text-blue-900
                   placeholder:text-blue-900/35 shadow-sm focus:outline-none focus:ring-2 focus:ring-yellow-300/60" />
          <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-900/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3m1.8-5.2a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
          </svg>
        </div>

        <button id="wishlistClear"
          class="h-12 px-5 rounded-2xl border border-blue-900 text-blue-900 font-semibold
                 hover:bg-blue-900 hover:text-white transition">
          Vaciar
        </button>

        <a href="<?php echo esc_url($shop_link); ?>"
          class="h-12 px-5 rounded-2xl bg-yellow-400 text-blue-900 font-extrabold
                 hover:bg-blue-700 hover:text-white transition inline-flex items-center justify-center">
          Seguir comprando →
        </a>
      </div>
    </div>

    <hr class="my-8 border-blue-900/10" />

    <?php if (empty($ids)) : ?>
      <!-- Empty state -->
      <div class="rounded-3xl border border-blue-900/10 bg-white p-10 text-center shadow-sm">
        <div class="mx-auto w-14 h-14 rounded-full bg-blue-900/5 flex items-center justify-center mb-4">
          <svg class="w-7 h-7 text-blue-900/40" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.687-4.5-1.935 0-3.597 1.126-4.313 2.733C11.285 4.876 9.623 3.75 7.687 3.75 5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
          </svg>
        </div>
        <h2 class="text-xl font-extrabold text-blue-900">Tu lista de deseos está vacía</h2>
        <p class="text-sm text-blue-900/70 mt-2">Guarda productos para verlos aquí y comprarlos después.</p>
        <a href="<?php echo esc_url($shop_link); ?>"
           class="mt-6 inline-flex items-center justify-center px-6 py-3 rounded-2xl bg-blue-900 text-white font-semibold hover:bg-yellow-400 hover:text-blue-900 transition">
          Ir al catálogo →
        </a>
      </div>

    <?php else : ?>

      <?php
      // Trae productos manteniendo el orden del wishlist
      $q = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in'       => $ids,
        'orderby'        => 'post__in',
      ]);
      ?>

      <p id="wishlistNoResults" class="hidden text-sm text-blue-900/60 mb-4">
        No hay resultados con ese filtro.
      </p>

      <div id="wishlistGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php while ($q->have_posts()) : $q->the_post();
          $product = wc_get_product(get_the_ID());
          if (!$product) continue;

          $pid   = $product->get_id();
          $plink = get_permalink($pid);

          $img_id = $product->get_image_id();
          $img = $img_id ? wp_get_attachment_image_url($img_id, 'woocommerce_thumbnail') : $fallback_img;

          $is_simple_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
          $btn_url  = $is_simple_cart ? $product->add_to_cart_url() : $plink;
          $btn_text = $is_simple_cart ? $product->add_to_cart_text() : 'Ver opciones';
          $btn_cls  = $is_simple_cart ? 'ajax_add_to_cart add_to_cart_button' : '';
        ?>

          <article class="wishlist-item group rounded-2xl border border-blue-900/10 bg-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition overflow-hidden"
                   data-title="<?php echo esc_attr(mb_strtolower($product->get_name())); ?>">

            <!-- Imagen -->
            <a href="<?php echo esc_url($plink); ?>" class="block relative bg-white">
              <div class="aspect-square w-full flex items-center justify-center bg-white">
                <img src="<?php echo esc_url($img); ?>"
                     alt="<?php echo esc_attr($product->get_name()); ?>"
                     class="w-full h-full object-contain p-3 group-hover:scale-[1.03] transition"
                     loading="lazy" />
              </div>

              <!-- Remove -->
              <button type="button"
                      class="wishlist-remove absolute top-3 right-3 w-10 h-10 rounded-full bg-white/95 border border-blue-900/15
                             flex items-center justify-center shadow hover:bg-yellow-400 transition"
                      data-product-id="<?php echo esc_attr($pid); ?>"
                      aria-label="Eliminar de wishlist">
                <svg class="w-5 h-5 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
              </button>

              <!-- Stock badge -->
              <div class="absolute left-3 top-3">
                <?php if ($product->is_in_stock()) : ?>
                  <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-blue-900 text-white shadow">
                    En stock
                  </span>
                <?php else : ?>
                  <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-gray-200 text-gray-800 shadow">
                    Sin stock
                  </span>
                <?php endif; ?>
              </div>
            </a>

            <!-- Info -->
            <div class="p-3 flex flex-col gap-2">
              <h3 class="text-sm font-extrabold text-blue-900 line-clamp-2">
                <a href="<?php echo esc_url($plink); ?>"><?php echo esc_html($product->get_name()); ?></a>
              </h3>

              <div class="text-sm font-extrabold text-blue-900">
                <?php echo wp_kses_post($product->get_price_html()); ?>
              </div>

              <a href="<?php echo esc_url($btn_url); ?>"
                 class="mt-2 inline-flex items-center justify-center w-full h-11 rounded-2xl
                        bg-blue-900 text-white font-semibold hover:bg-yellow-400 hover:text-blue-900 transition <?php echo esc_attr($btn_cls); ?>"
                 data-product_id="<?php echo esc_attr($pid); ?>"
                 data-quantity="1" rel="nofollow">
                <?php echo esc_html($btn_text); ?> →
              </a>
            </div>
          </article>

        <?php endwhile; wp_reset_postdata(); ?>
      </div>

    <?php endif; ?>

  </div>
</main>

<?php get_footer(); ?>
