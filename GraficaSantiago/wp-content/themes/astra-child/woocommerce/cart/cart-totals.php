<?php
defined('ABSPATH') || exit;
?>

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 cart_totals <?php echo (WC()->customer->has_calculated_shipping() ? 'calculated_shipping' : ''); ?>">

	<div class="flex items-center justify-between gap-3 mb-4">
		<h2 class="text-xl font-black text-gray-900">
			<?php esc_html_e('Resumen', 'woocommerce'); ?>
		</h2>
		<span class="text-[10px] font-black px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 uppercase tracking-wider">
			Seguro
		</span>
	</div>

	<?php do_action('woocommerce_before_cart_totals'); ?>

	<div class="space-y-3 text-sm">
		<!-- Subtotal -->
		<div class="flex items-center justify-between border-b border-gray-100 pb-3">
			<span class="font-semibold text-gray-600"><?php esc_html_e('Subtotal', 'woocommerce'); ?></span>
			<span class="font-black text-gray-900"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<!-- Shipping -->
		<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
			<div class="border-b border-gray-100 pb-3">
				<div class="flex items-center justify-between mb-2">
					<span class="font-semibold text-gray-600"><?php esc_html_e('Envío', 'woocommerce'); ?></span>
				</div>
				<div class="text-gray-700">
					<?php do_action('woocommerce_cart_totals_before_shipping'); ?>
					<?php wc_cart_totals_shipping_html(); ?>
					<?php do_action('woocommerce_cart_totals_after_shipping'); ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Fees -->
		<?php foreach (WC()->cart->get_fees() as $fee) : ?>
			<div class="flex items-center justify-between border-b border-gray-100 pb-3">
				<span class="font-semibold text-gray-600"><?php echo esc_html($fee->name); ?></span>
				<span class="font-black text-gray-900"><?php wc_cart_totals_fee_html($fee); ?></span>
			</div>
		<?php endforeach; ?>

		<!-- Taxes -->
		<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
			<?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
				<?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
					<div class="flex items-center justify-between border-b border-gray-100 pb-3">
						<span class="font-semibold text-gray-600"><?php echo esc_html($tax->label); ?></span>
						<span class="font-black text-gray-900"><?php echo wp_kses_post($tax->formatted_amount); ?></span>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="flex items-center justify-between border-b border-gray-100 pb-3">
					<span class="font-semibold text-gray-600"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></span>
					<span class="font-black text-gray-900"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<!-- Total -->
		<div class="flex items-center justify-between pt-2">
			<span class="text-base font-black text-blue-900"><?php esc_html_e('Total', 'woocommerce'); ?></span>
			<span class="text-base font-black text-blue-900"><?php wc_cart_totals_order_total_html(); ?></span>
		</div>
	</div>

	<div class="mt-5">
		<?php do_action('woocommerce_proceed_to_checkout'); ?>
	</div>

	<?php do_action('woocommerce_after_cart_totals'); ?>
</div>
