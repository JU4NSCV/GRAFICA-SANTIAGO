<?php
defined('ABSPATH') || exit;

do_action('woocommerce_cart_is_empty');

$shop_url = wc_get_page_permalink('shop');
?>

<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8 text-center">
	<div class="text-5xl mb-3">🛒</div>
	<h2 class="text-xl sm:text-2xl font-black text-blue-900">Tu carrito está vacío</h2>
	<p class="text-sm text-gray-600 mt-2">
		Agrega productos y vuelve aquí para finalizar tu compra.
	</p>

	<?php if ($shop_url) : ?>
		<a href="<?php echo esc_url($shop_url); ?>"
		   class="mt-6 inline-flex items-center justify-center h-12 px-6 rounded-2xl bg-yellow-400 text-blue-900 font-black border-2 border-yellow-400 hover:bg-blue-900 hover:text-white hover:border-blue-900 transition">
			Ver productos →
		</a>
	<?php endif; ?>
</div>
