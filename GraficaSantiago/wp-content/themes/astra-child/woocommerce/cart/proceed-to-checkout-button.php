<?php
defined('ABSPATH') || exit;
?>
<a href="<?php echo esc_url(wc_get_checkout_url()); ?>"
   class="checkout-button button alt wc-forward w-full inline-flex items-center justify-center h-12 px-6 rounded-2xl
          bg-yellow-400 text-blue-900 font-black border-2 border-yellow-400
          hover:bg-blue-900 hover:text-white hover:border-blue-900 transition">
	<?php esc_html_e('Finalizar compra', 'woocommerce'); ?>
</a>
