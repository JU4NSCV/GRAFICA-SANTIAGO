<?php
defined('ABSPATH') || exit;

if (! wp_doing_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}

$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
?>
<main class="w-full ">

    <div class="woocommerce-checkout-payment mt-6 !w-full !float-none clear-both">

        <div class="border-t border-gray-100 pt-6">
            <h3 class="text-lg font-black text-gray-900 mb-4">Método de pago</h3>

            <?php if (WC()->cart->needs_payment()) : ?>
                <ul class="wc_payment_methods payment_methods methods space-y-3">
                    <?php if (! empty($available_gateways)) : ?>
                        <?php foreach ($available_gateways as $gateway) : ?>
                            <?php wc_get_template('checkout/payment-method.php', array('gateway' => $gateway)); ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <li class="bg-yellow-50 border border-yellow-100 text-yellow-800 rounded-2xl p-4 font-semibold">
                            <?php echo esc_html(apply_filters(
                                'woocommerce_no_available_payment_methods_message',
                                __('No hay métodos de pago disponibles. Verifica la configuración.', 'woocommerce')
                            )); ?>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>

            <div class="mt-6 border-t border-gray-100 pt-6">

                <?php
                // ✅ Esto imprime términos/privacidad + checkbox si está habilitado
                wc_get_template('checkout/terms.php');
                ?>

                <?php do_action('woocommerce_review_order_before_submit_button'); ?>

                <?php
                // ✅ Botón correcto (WooCommerce usa data-value en JS)
                echo apply_filters(
                    'woocommerce_order_button_html',
                    '<button type="submit"
					class="button alt w-full h-12 rounded-2xl bg-yellow-400 text-blue-900 font-black border-2 border-yellow-400 hover:bg-blue-900 hover:text-white hover:border-blue-900 transition"
					name="woocommerce_checkout_place_order"
					id="place_order"
					value="' . esc_attr__('Realizar el pedido', 'woocommerce') . '"
					data-value="' . esc_attr__('Realizar el pedido', 'woocommerce') . '">'
                        . esc_html__('Realizar el pedido', 'woocommerce') .
                        '</button>'
                );
                ?>

                <?php do_action('woocommerce_review_order_after_submit_button'); ?>

                <?php
                // ✅ IMPORTANTÍSIMO: sin esto aparece “no hemos podido procesar tu pedido”
                wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce');
                ?>

            </div>
        </div>
    </div>
</main>
<?php
if (! wp_doing_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
?>