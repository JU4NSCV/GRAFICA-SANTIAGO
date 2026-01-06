<?php
defined('ABSPATH') || exit;

global $product;

do_action('woocommerce_before_single_product');

if (post_password_required()) {
  echo get_the_password_form();
  return;
}

$pid  = $product->get_id();
$ids  = function_exists('gs_wishlist_get_ids') ? gs_wishlist_get_ids() : [];
$in_wishlist = in_array($pid, (array)$ids, true);

// Badge descuento %
$badge = '';
if ($product->is_on_sale()) {
  $reg = (float) $product->get_regular_price();
  $price = (float) $product->get_price();

  if ($product->is_type('variable')) {
    $price = (float) $product->get_variation_price('min', true);
    $reg   = (float) $product->get_variation_regular_price('min', true);
  }

  if ($reg > 0 && $price > 0 && $price < $reg) {
    $pct = round((($reg - $price) / $reg) * 100);
    $badge = "-{$pct}%";
  } else {
    $badge = "Oferta";
  }
}

$in_stock = $product->is_in_stock();
?>

<section class="w-full bg-gradient-to-b from-blue-50 to-white">
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-10">

    <!-- Breadcrumbs -->
    <div class="text-xs text-blue-900/60 mb-4">
      <?php woocommerce_breadcrumb(); ?>
    </div>

    <div id="product-<?php the_ID(); ?>" <?php wc_product_class('w-full', $product); ?>>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- GALERÍA -->
        <div class="lg:col-span-7">
          <div class="relative rounded-3xl bg-white border border-blue-900/10 shadow-sm overflow-hidden p-4 md:p-6">
            <!-- Badges -->
            <div class="absolute z-10 top-4 left-4 flex gap-2">
              <?php if ($badge) : ?>
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-yellow-400 text-blue-900 shadow">
                  <?php echo esc_html($badge); ?>
                </span>
              <?php endif; ?>

              <?php if ($in_stock) : ?>
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-900 text-white shadow">
                  En stock
                </span>
              <?php else : ?>
                <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-gray-200 text-gray-800 shadow">
                  Agotado
                </span>
              <?php endif; ?>
            </div>

            <!-- Wishlist -->
            <button type="button"
              class="js-wishlist-toggle absolute z-10 top-4 right-4 w-12 h-12 rounded-full bg-white/95 border border-blue-900/15 shadow
                     flex items-center justify-center hover:bg-yellow-400 transition <?php echo $in_wishlist ? 'is-active' : ''; ?>"
              data-product-id="<?php echo esc_attr($pid); ?>"
              aria-label="Wishlist">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                width="22" height="22" fill="<?php echo $in_wishlist ? 'currentColor' : 'none'; ?>" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"
                class="<?php echo $in_wishlist ? 'text-blue-900' : 'text-blue-900'; ?>">
                <path d="M20.8 4.6c-1.5-1.7-3.9-2-5.7-.8-.6.4-1.1 1-1.4 1.6-.3-.6-.8-1.2-1.4-1.6-1.8-1.2-4.2-.9-5.7.8-1.7 2-1.5 4.9.4 6.7l6.7 6.1 6.7-6.1c1.9-1.8 2.1-4.7.4-6.7z" />
              </svg>
            </button>

            <!-- Woo gallery -->
            <div class="[&_.woocommerce-product-gallery]:w-full">
              <?php do_action('woocommerce_before_single_product_summary'); ?>
            </div>
          </div>

          <!-- Trust badges -->
          <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-2xl bg-white border border-blue-900/10 p-4 shadow-sm">
              <div class="text-sm font-extrabold text-blue-900">Compra segura</div>
              <div class="text-xs text-blue-900/60 mt-1">Pagos y datos protegidos</div>
            </div>
            <div class="rounded-2xl bg-white border border-blue-900/10 p-4 shadow-sm">
              <div class="text-sm font-extrabold text-blue-900">Envío / Retiro</div>
              <div class="text-xs text-blue-900/60 mt-1">Coordina la entrega</div>
            </div>
            <div class="rounded-2xl bg-white border border-blue-900/10 p-4 shadow-sm">
              <div class="text-sm font-extrabold text-blue-900">Soporte</div>
              <div class="text-xs text-blue-900/60 mt-1">Te ayudamos en la compra</div>
            </div>
          </div>
        </div>

        <!-- RESUMEN -->
        <div class="lg:col-span-5">
          <div class="lg:sticky lg:top-28 rounded-3xl bg-white border border-blue-900/10 shadow-sm p-5 md:p-7">

            <!-- Categorías mini -->
            <div class="text-xs text-blue-900/60 mb-2">
              <?php echo wc_get_product_category_list($pid, ', '); ?>
            </div>

            <h1 class="text-2xl md:text-3xl font-extrabold text-blue-900 leading-tight">
              <?php the_title(); ?>
            </h1>

            <div class="mt-3 flex items-center gap-3">
              <div class="text-xl md:text-2xl font-extrabold text-blue-900">
                <?php woocommerce_template_single_price(); ?>
              </div>

              <?php if (function_exists('woocommerce_template_single_rating')) : ?>
                <div class="text-sm">
                  <?php woocommerce_template_single_rating(); ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="mt-4 text-sm text-blue-900/75 leading-relaxed">
              <?php woocommerce_template_single_excerpt(); ?>
            </div>

            <!-- Add to cart -->
            <div class="mt-6 rounded-2xl border border-blue-900/10 p-4">
              <div class="text-sm font-extrabold text-blue-900 mb-3">Elige cantidad y añade al carrito</div>
              <?php woocommerce_template_single_add_to_cart(); ?>

              <?php if (!$in_stock) : ?>
                <div class="mt-3 text-xs text-blue-900/60">
                  Este producto está agotado. Puedes revisar opciones similares abajo.
                </div>
              <?php endif; ?>
            </div>

            <!-- Meta -->
            <div class="mt-5 text-xs text-blue-900/60">
              <?php woocommerce_template_single_meta(); ?>
            </div>

          </div>
        </div>

      </div>

      <!-- Tabs + Related -->
      <div class="mt-10 rounded-3xl bg-white border border-blue-900/10 shadow-sm p-5 md:p-7">
        <?php do_action('woocommerce_after_single_product_summary'); ?>
      </div>

    </div>
  </div>
</section>

<?php do_action('woocommerce_after_single_product'); ?>
