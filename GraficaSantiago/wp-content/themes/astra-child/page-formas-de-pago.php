<?php

/**
 * Template Name: Formas de Pago
 * Astra Child - Formas de Pago
 */
defined('ABSPATH') || exit;

add_filter('astra_the_title_enabled', '__return_false');

add_filter('astra_page_layout', function ($layout) {
    // Si tu página tiene slug "formas-de-pago"
    if (is_page('formas-de-pago')) return 'full-width';
    return $layout;
});

get_header();

/**
 * Datos de transferencia (edítalos aquí si cambian)
 */
$ruc     = '1102021464';
$nombre  = 'JULIO CESAR LUNA CRUZ';
$banco   = 'BANCO DE LOJA';
$tipo_cta = 'CUENTA CORRIENTE';
$num_cta = '2900037571';

// Canales de contacto (ajusta a los tuyos)
$email_soporte = 'servicios@santiagopapeleria.com';
$tel_soporte   = '072573358 ext 113';
$wa_soporte    = '593987667459';
$wa_message = 'Hola *Santiago Papeleria*. Adjunto mi comprobante de transferencia para validar mi pago. Gracias.';
$wa_url = 'https://wa.me/' . preg_replace('/\D+/', '', $wa_soporte) . '?text=' . rawurlencode($wa_message);

// QR: usa la IMAGEN DESTACADA de esta página
$qr_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
?>

<main class="w-full">

    <!-- HERO -->
    <header class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
        <div class="absolute inset-0 opacity-15 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700"></div>

        <svg class="absolute bottom-0 left-0 w-full h-10 md:h-14" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
            <path fill="white" d="M0,64L80,64C160,64,320,64,480,74.7C640,85,800,107,960,112C1120,117,1280,107,1360,101.3L1440,96L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>

        <div class="max-w-7xl mx-auto px-6 py-10 md:py-14 relative">
            <p class="text-blue-200 text-sm font-semibold tracking-wider uppercase">Pagos seguros</p>
            <h1 class="mt-2 text-3xl md:text-5xl font-black tracking-tight text-white">
                Formas de Pago
            </h1>
            <p class="mt-3 text-blue-100/90 max-w-2xl">
                Elige el método que más te convenga. Si pagas por transferencia, puedes usar nuestro QR y enviarnos el comprobante para validar tu pedido.
            </p>
        </div>
    </header>

    <!-- CONTENIDO -->
    <section class="max-w-7xl mx-auto px-6 py-10 md:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- COLUMNA PRINCIPAL -->
            <div class="lg:col-span-7 space-y-6">

                <!-- TARJETAS -->
                <article class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                    <div class="p-6 md:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-extrabold text-blue-900/70 uppercase tracking-wider">Método 1</p>
                                <h2 class="mt-1 text-xl md:text-2xl font-extrabold text-[#3B4D64]">
                                    Pago con tarjeta (crédito / débito)
                                </h2>
                                <p class="mt-3 text-gray-700 leading-relaxed">
                                    Puedes pagar de forma rápida y segura con tarjeta. La compra está sujeta a verificación y autorización del pago.
                                </p>
                            </div>

                            <div class="shrink-0 rounded-2xl bg-blue-50 ring-1 ring-blue-100 px-4 py-3">
                                <p class="text-xs text-blue-900/70">Recomendado</p>
                                <p class="text-sm font-extrabold text-blue-900">Pago inmediato</p>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
                                <p class="text-xs text-gray-600">Seguridad</p>
                                <p class="text-base font-extrabold text-[#3B4D64]">Validación</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
                                <p class="text-xs text-gray-600">Tiempo</p>
                                <p class="text-base font-extrabold text-[#3B4D64]">Rápido</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
                                <p class="text-xs text-gray-600">Soporte</p>
                                <p class="text-base font-extrabold text-[#3B4D64]">Atención</p>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- TRANSFERENCIA -->
                <article class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden" id="transferencia">
                    <div class="p-6 md:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-extrabold text-blue-900/70 uppercase tracking-wider">Método 2</p>
                                <h2 class="mt-1 text-xl md:text-2xl font-extrabold text-[#3B4D64]">
                                    Depósito o transferencia bancaria
                                </h2>
                                <p class="mt-3 text-gray-700 leading-relaxed">
                                    Realiza la transferencia con los datos de cuenta y luego envíanos el comprobante para verificar tu pago y continuar con el proceso de facturación y entrega.
                                </p>
                            </div>

                            <div class="shrink-0 rounded-2xl bg-yellow-50 ring-1 ring-yellow-200 px-4 py-3">
                                <p class="text-xs text-yellow-900/70">Tip</p>
                                <p class="text-sm font-extrabold text-yellow-900">Usa el QR</p>
                            </div>
                        </div>

                        <!-- DATOS DE CUENTA -->
                        <div class="mt-6 rounded-3xl bg-blue-50 ring-1 ring-blue-100 p-5 md:p-6">
                            <p class="text-sm font-extrabold text-blue-900">Datos para transferencia</p>

                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="rounded-2xl bg-white ring-1 ring-blue-100 p-4">
                                    <p class="text-xs text-blue-900/60">Número de RUC</p>
                                    <p class="text-lg font-extrabold text-blue-900"><?php echo esc_html($ruc); ?></p>
                                </div>
                                <div class="rounded-2xl bg-white ring-1 ring-blue-100 p-4">
                                    <p class="text-xs text-blue-900/60">Nombre</p>
                                    <p class="text-lg font-extrabold text-blue-900"><?php echo esc_html($nombre); ?></p>
                                </div>
                                <div class="rounded-2xl bg-white ring-1 ring-blue-100 p-4">
                                    <p class="text-xs text-blue-900/60">Banco</p>
                                    <p class="text-lg font-extrabold text-blue-900"><?php echo esc_html($banco); ?></p>
                                </div>
                                <div class="rounded-2xl bg-white ring-1 ring-blue-100 p-4">
                                    <p class="text-xs text-blue-900/60"><?php echo esc_html($tipo_cta); ?></p>
                                    <p class="text-lg font-extrabold text-blue-900"><?php echo esc_html($num_cta); ?></p>
                                </div>
                            </div>

                            <div class="mt-5 text-sm text-blue-900/90 leading-relaxed">
                                <p class="font-extrabold">Luego de transferir:</p>
                                <ul class="mt-2 list-disc pl-5 space-y-1">
                                    <li>Envía el comprobante y tu número de pedido (si aplica).</li>
                                    <li>El valor debe coincidir con el total del pedido.</li>
                                    <li>Una vez verificado, te confirmamos por correo y seguimos con la entrega.</li>
                                </ul>
                            </div>

                            <div class="mt-5 flex flex-col sm:flex-row gap-3">
                                <a href="mailto:<?php echo esc_attr($email_soporte); ?>?subject=Comprobante%20de%20pago%20-%20Transferencia"
                                    class="inline-flex items-center justify-center rounded-2xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-blue-900 hover:bg-yellow-300 transition">
                                    Enviar comprobante por correo
                                </a>

                                <a href="<?php echo esc_url($wa_url); ?>"
                                    target="_blank" rel="noopener"
                                    class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-extrabold text-blue-900 ring-1 ring-blue-200 hover:ring-blue-400 transition">
                                    Enviar por WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

            </div>

            <!-- COLUMNA LATERAL (QR + RESUMEN) -->
            <aside class="lg:col-span-5 space-y-6">

                <!-- QR -->
                <div class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                    <div class="p-6 md:p-8">
                        <h3 class="text-lg font-extrabold text-[#3B4D64]">QR para transferencia</h3>
                        <p class="mt-2 text-sm text-gray-700">
                            Escanea el QR desde tu app bancaria para completar la transferencia más rápido.
                        </p>

                        <div class="mt-4 rounded-2xl overflow-hidden ring-1 ring-gray-200 bg-gray-50">
                            <?php if ($qr_url): ?>
                                <img
                                    src="<?php echo esc_url($qr_url); ?>"
                                    alt="Código QR para transferencia"
                                    class="w-full h-auto object-contain" />
                            <?php else: ?>
                                <div class="p-5">
                                    <p class="text-sm font-extrabold text-blue-900">Aún no has subido el QR</p>
                                    <p class="mt-2 text-sm text-gray-700">
                                        Ve al editor de esta página y sube el QR como <strong>Imagen destacada</strong>.
                                    </p>
                                    <div class="mt-3 text-xs text-gray-500">
                                        Sugerencia: usa una imagen cuadrada (mínimo 800x800).
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-4 rounded-2xl bg-blue-50 ring-1 ring-blue-100 p-4">
                            <p class="text-xs font-extrabold text-blue-900">Tip</p>
                            <p class="text-sm text-blue-900/90">
                                Al enviar el comprobante, incluye tu nombre y el valor transferido para agilizar la validación.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- RESUMEN -->
                <div class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                    <div class="p-6 md:p-8">
                        <h3 class="text-lg font-extrabold text-[#3B4D64]">Canales de atención</h3>

                        <div class="mt-4 rounded-2xl bg-blue-50 ring-1 ring-blue-100 p-5">
                            <p class="text-sm font-extrabold text-blue-900">Contáctanos</p>
                            <p class="mt-2 text-sm text-blue-900/90">
                                Email:
                                <a class="font-extrabold text-blue-900 hover:underline"
                                    href="mailto:<?php echo esc_attr($email_soporte); ?>">
                                    <?php echo esc_html($email_soporte); ?>
                                </a>
                            </p>
                            <p class="mt-1 text-sm text-blue-900/90">
                                Tel: <span class="font-extrabold"><?php echo esc_html($tel_soporte); ?></span>
                            </p>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-3">
                            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
                                <p class="text-xs text-gray-600">Recomendación</p>
                                <p class="text-sm font-extrabold text-[#3B4D64]">Guarda tu comprobante</p>
                            </div>
                            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
                                <p class="text-xs text-gray-600">Importante</p>
                                <p class="text-sm font-extrabold text-[#3B4D64]">Monto exacto del pedido</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button type="button" id="btnVerTransferencia"
                                class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-extrabold text-blue-900 ring-1 ring-gray-200 hover:ring-blue-300 transition">
                                Ver transferencia
                            </button>

                            <a href="<?php echo esc_url(home_url('/contactenos/')); ?>"
                                class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-extrabold text-blue-900 ring-1 ring-gray-200 hover:ring-blue-300 transition">
                                ¿Dudas? Contáctanos
                            </a>
                        </div>
                    </div>
                </div>

            </aside>

        </div>
    </section>
    <div id="qrModal" class="fixed inset-0 hidden items-center justify-center z-[9999]">
        <div class="absolute inset-0 bg-black/60" data-close></div>

        <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-[92vw] p-5 md:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-extrabold text-blue-900/70 uppercase tracking-wider">Transferencia bancaria</p>
                    <h3 class="text-xl md:text-2xl font-black text-blue-900">Escanea el QR</h3>
                    <p class="mt-1 text-sm text-slate-600">También puedes abrirlo y guardarlo.</p>
                </div>

                <button type="button" data-close
                    class="w-10 h-10 rounded-full grid place-items-center bg-slate-100 hover:bg-slate-200 transition"
                    aria-label="Cerrar">
                    ✕
                </button>
            </div>

            <div class="mt-4 rounded-2xl overflow-hidden ring-1 ring-slate-200 bg-slate-50">
                <img src="<?php echo esc_url($qr_url); ?>" alt="QR para transferencia"
                    class="w-full h-auto object-contain">
            </div>

            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                <a href="<?php echo esc_url($qr_url); ?>" target="_blank" rel="noopener"
                    class="inline-flex items-center justify-center rounded-2xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-blue-900 hover:bg-yellow-300 transition">
                    Abrir en grande
                </a>
                <button type="button" data-close
                    class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-extrabold text-blue-900 ring-1 ring-gray-200 hover:ring-blue-300 transition">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</main>
<script>
    (function() {
        const btn = document.getElementById('btnVerTransferencia');
        const modal = document.getElementById('qrModal');
        if (!btn || !modal) return;

        const open = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
        const close = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        btn.addEventListener('click', open);

        modal.querySelectorAll('[data-close]').forEach(el => {
            el.addEventListener('click', close);
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
        });
    })();
</script>

<?php get_footer(); ?>