<?php
defined('ABSPATH') || exit;

if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}
?>

<div id="payment" class="woocommerce-checkout-payment mt-6 !w-full !float-none clear-both">

    <div class="border-t border-gray-100 pt-6">
        <h3 class="text-lg font-black text-gray-900 mb-4">Método de pago</h3>

        <?php if (WC()->cart->needs_payment()) : ?>
            <ul class="wc_payment_methods payment_methods methods space-y-3">
                <?php
                $available_gateways = WC()->payment_gateways->get_available_payment_gateways();

                if (!empty($available_gateways)) {
                    foreach ($available_gateways as $gateway) {
                        wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
                    }
                } else {
                    echo '<li class="bg-yellow-50 border border-yellow-100 text-yellow-800 rounded-2xl p-4 font-semibold">
                  No hay métodos de pago disponibles. Verifica la configuración.
                </li>';
                }
                ?>
            </ul>
        <?php endif; ?>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <?php if (function_exists('wc_terms_and_conditions_checkbox_enabled') && wc_terms_and_conditions_checkbox_enabled()) : ?>
                <div class="mb-4 text-xs text-gray-600">
                    <?php do_action('woocommerce_checkout_terms_and_conditions'); ?>
                </div>
            <?php endif; ?>

            <?php do_action('woocommerce_review_order_before_submit_button'); ?>

            <?php
            echo apply_filters(
                'woocommerce_order_button_html',
                '<button type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr__('Realizar el pedido', 'woocommerce') . '">' . esc_html__('Realizar el pedido', 'woocommerce') . '</button>'
            );
            ?>

            <?php do_action('woocommerce_review_order_after_submit_button'); ?>

            <?php if (function_exists('wc_checkout_privacy_policy_text')) wc_checkout_privacy_policy_text(); ?>
        </div>
    </div>
</div>

<?php
if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
?>