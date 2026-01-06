<?php
defined('ABSPATH') || exit;
if (!wc_coupons_enabled()) return;
?>

<section class="max-w-7xl mx-auto px-6 mt-6">
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-4">
        <button type="button"
            class="gsi-showcoupon w-full text-left font-black text-blue-900 flex items-center justify-between">
            <span>¿Tienes un cupón?</span>
            <span class="text-yellow-600">Haz clic para ingresar</span>
        </button>

        <form class="checkout_coupon woocommerce-form-coupon mt-4 hidden" method="post">
            <div class="flex flex-col sm:flex-row gap-3">
                <input type="text" name="coupon_code" placeholder="Código de cupón"
                    class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 outline-none" />
                <button type="submit" name="apply_coupon"
                    class="px-6 py-3 rounded-2xl bg-yellow-500 text-blue-900 font-black hover:bg-yellow-400 transition">
                    Aplicar
                </button>
            </div>
        </form>
    </div>
</section>

<script>
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.gsi-showcoupon');
        if (!btn) return;
        const form = btn.parentElement.querySelector('form.checkout_coupon');
        if (form) form.classList.toggle('hidden');
    });
</script>