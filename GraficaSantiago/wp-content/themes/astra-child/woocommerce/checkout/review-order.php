<?php
defined('ABSPATH') || exit;
?>

<div class="woocommerce-checkout-review-order">

    <div class="overflow-hidden rounded-2xl border border-gray-100">
        <table class=class="min-w-full w-full table-fixed text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="w-3/4 text-left px-4 py-3 font-black text-gray-900">Producto</th>
                    <th class="w-1/4 text-right px-4 py-3 font-black text-gray-900">Subtotal</th>

                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">
                <?php
                do_action('woocommerce_review_order_before_cart_contents');

                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                ?>
                        <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                            <td class="px-4 py-3 align-top">
                                <div class="font-bold text-gray-900 leading-snug">
                                    <?php echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key)); ?>
                                    <span class="text-gray-500 font-semibold">× <?php echo esc_html($cart_item['quantity']); ?></span>
                                </div>

                                <?php echo wp_kses_post(WC()->cart->get_item_data($cart_item)); ?>
                            </td>

                            <td class="px-4 py-3 text-right font-bold text-gray-900 align-top">
                                <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
                            </td>
                        </tr>
                <?php
                    }
                }

                do_action('woocommerce_review_order_after_cart_contents');
                ?>
            </tbody>

            <tfoot class="bg-white">
                <tr class="border-t border-gray-100">
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Subtotal</th>
                    <td class="px-4 py-3 text-right font-black text-gray-900"><?php wc_cart_totals_subtotal_html(); ?></td>
                </tr>

                <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                    <?php do_action('woocommerce_review_order_before_shipping'); ?>
                    <tr class="border-t border-gray-100">
                        <th class="px-4 py-3 text-left font-bold text-gray-700">Envío</th>
                        <td class="px-4 py-3 text-right text-gray-900"><?php wc_cart_totals_shipping_html(); ?></td>
                    </tr>
                    <?php do_action('woocommerce_review_order_after_shipping'); ?>
                <?php endif; ?>

                <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                    <tr class="border-t border-gray-100">
                        <th class="px-4 py-3 text-left font-bold text-gray-700"><?php echo esc_html($fee->name); ?></th>
                        <td class="px-4 py-3 text-right font-bold text-gray-900"><?php wc_cart_totals_fee_html($fee); ?></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
                    <?php if ('itemized' === get_option('woocommerce_tax_total_display')) : ?>
                        <?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
                            <tr class="border-t border-gray-100">
                                <th class="px-4 py-3 text-left font-bold text-gray-700"><?php echo esc_html($tax->label); ?></th>
                                <td class="px-4 py-3 text-right font-bold text-gray-900"><?php echo wp_kses_post($tax->formatted_amount); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr class="border-t border-gray-100">
                            <th class="px-4 py-3 text-left font-bold text-gray-700"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
                            <td class="px-4 py-3 text-right font-bold text-gray-900"><?php wc_cart_totals_taxes_total_html(); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

                <tr class="border-t border-gray-100">
                    <th class="px-4 py-4 text-left font-black text-gray-900">Total</th>
                    <td class="px-4 py-4 text-right font-black text-gray-900 text-base"><?php wc_cart_totals_order_total_html(); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>