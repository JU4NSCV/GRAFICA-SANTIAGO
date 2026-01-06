<?php
defined('ABSPATH') || exit;
global $product;

if (empty($product) || !$product->is_visible()) return;

$pid = $product->get_id();
$link = get_permalink($pid);


$ids = function_exists('gs_wishlist_get_ids') ? gs_wishlist_get_ids() : [];
$in_wishlist = in_array($pid, (array) $ids, true);

// % descuento
$badge = '';
if ($product->is_on_sale()) {
  $reg = (float) $product->get_regular_price();
  $sale = (float) $product->get_sale_price();
  $price = (float) $product->get_price();

  // variable → tomamos min
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
?>

<li <?php wc_product_class('group', $product); ?>>
  <article class="h-full rounded-2xl border border-blue-900/10 bg-white shadow-sm
                  hover:shadow-lg hover:-translate-y-0.5 transition overflow-hidden">

    <a href="<?php echo esc_url($link); ?>" class="block p-3">
      <!-- Imagen -->
      <div class="relative rounded-2xl bg-white border border-blue-900/5 overflow-hidden">
        <div class="aspect-square flex items-center justify-center">
          <?php echo $product->get_image('woocommerce_thumbnail', [
            'class' => 'w-full h-full object-contain p-4 group-hover:scale-[1.04] transition'
          ]); ?>
        </div>

        <!-- Badge oferta -->
        <?php if ($badge) : ?>
          <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[11px] font-extrabold
                       bg-yellow-400 text-blue-900 shadow">
            <?php echo esc_html($badge); ?>
          </span>
        <?php endif; ?>

        <!-- Badge stock -->
        <?php if (!$product->is_in_stock()) : ?>
          <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[11px] font-extrabold
                       bg-gray-200 text-gray-800 shadow">
            Sin stock
          </span>
        <?php endif; ?>

        <!-- Wishlist (mantén tu JS: usa data-product-id) -->
        <button type="button"
          class="js-wishlist-toggle absolute bottom-3 right-3 w-11 h-11 rounded-full bg-white/95
                 border border-blue-900/15 shadow flex items-center justify-center
                 hover:bg-yellow-400 transition <?php echo $in_wishlist ? 'is-active' : ''; ?>"
          data-product-id="<?php echo esc_attr($pid); ?>"
          aria-label="Wishlist">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-900" fill="<?php echo $in_wishlist ? 'currentColor' : 'none'; ?>" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 4.6c-1.5-1.7-3.9-2-5.7-.8-.6.4-1.1 1-1.4 1.6-.3-.6-.8-1.2-1.4-1.6-1.8-1.2-4.2-.9-5.7.8-1.7 2-1.5 4.9.4 6.7l6.7 6.1 6.7-6.1c1.9-1.8 2.1-4.7.4-6.7z"/>
          </svg>
        </button>
      </div>

      <!-- Info -->
      <h2 class="mt-3 text-sm font-extrabold text-blue-900 line-clamp-2 min-h-[40px]">
        <?php echo esc_html($product->get_name()); ?>
      </h2>

      <div class="mt-1 text-sm font-extrabold text-blue-900">
        <?php echo wp_kses_post($product->get_price_html()); ?>
      </div>
    </a>

    <!-- CTA -->
    <div class="p-3 pt-0">
      <?php woocommerce_template_loop_add_to_cart(); ?>
    </div>
  </article>
</li>
