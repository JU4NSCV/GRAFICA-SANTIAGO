<?php
defined('ABSPATH') || exit;

// ✅ Mata el cupón default (aunque lo hayan enganchado en otra prioridad)
while (($p = has_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form')) !== false) {
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', $p);
}

do_action('woocommerce_before_checkout_form', $checkout);

if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters(
        'woocommerce_checkout_must_be_logged_in_message',
        __('Debes iniciar sesión para finalizar la compra.', 'woocommerce')
    ));
    return;
}
?>

<main class="w-full">
    <!-- HERO estilo Instituciones (sin curva gigante que “ensucia”) -->
    <section class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 pt-12 pb-28 md:pb-32 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-24 -left-24 w-[28rem] h-[28rem] bg-white/10 rounded-full blur-2xl"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <h1 class="text-3xl md:text-5xl font-black tracking-tight !text-white">Finalizar compra</h1>
            <p class="text-lg mt-2 !text-blue-200">Completa tus datos y revisa tu pedido antes de confirmar.</p>
        </div>
    </section>

    <section class="lg:col-span-5 space-y-6 lg:sticky lg:top-3">

        <form name="checkout" method="post"
            class="checkout woocommerce-checkout grid grid-cols-1 lg:grid-cols-12 gap-8"
            action="<?php echo esc_url(wc_get_checkout_url()); ?>"
            enctype="multipart/form-data">

            <!-- IZQUIERDA -->
            <div class="lg:col-span-7 space-y-6">

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <?php if ($checkout->get_checkout_fields()) : ?>
                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>

                        <div id="customer_details" class="space-y-8">
                            <div class="checkout-billing">
                                <?php do_action('woocommerce_checkout_billing'); ?>
                            </div>

                            <div class="checkout-shipping">
                                <?php do_action('woocommerce_checkout_shipping'); ?>
                            </div>
                        </div>

                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                    <?php endif; ?>
                </div>

            </div>

            <!-- DERECHA -->
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-24 space-y-6">

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <h2 class="text-xl font-black text-gray-900">Tu pedido</h2>
                            <span class="text-[10px] font-black px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 uppercase tracking-wider">
                                Seguro
                            </span>
                        </div>

                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <div id="order_review" class="woocommerce-checkout-review-order !w-full !float-none clear-both">
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </div>



                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </div>

                    <div class="grid grid-cols-2 gap-3 clear-both w-full">
                        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                            <div class="text-2xl">✅</div>
                            <div class="font-black text-gray-900 text-sm mt-1">Compra segura</div>
                            <div class="text-xs text-gray-500">Protegemos tus datos</div>
                        </div>
                        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
                            <div class="text-2xl">🚚</div>
                            <div class="font-black text-gray-900 text-sm mt-1">Envíos</div>
                            <div class="text-xs text-gray-500">Rápidos y confiables</div>
                        </div>
                    </div>

                </div>
            </div>

        </form>
    </section>
</main>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>