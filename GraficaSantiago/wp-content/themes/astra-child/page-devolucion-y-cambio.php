<?php

/**
 * Página Devolución y cambio (Astra Child)
 */
defined('ABSPATH') || exit;

// Quitar título default de Astra
add_filter('astra_the_title_enabled', '__return_false');

// Full width solo para esta página (y alternativa de slug)
add_filter('astra_page_layout', function ($layout) {
    if (is_page(['devolucion-y-cambio', 'devoluciones-y-cambios'])) return 'full-width';
    return $layout;
});

get_header();

// Ajusta datos reales
$correo = 'servicios@santiagopapeleria.com';
$tel    = '072573358 ext 113';

// Link actual de la página (sirve para compartir)
$page_url = get_permalink();
?>

<main class="w-full">

    <!-- HERO -->
    <header class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
        <div class="absolute inset-0 opacity-20 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700"></div>

        <svg class="absolute bottom-0 left-0 w-full h-10 md:h-14" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
            <path fill="white" d="M0,64L80,64C160,64,320,64,480,74.7C640,85,800,107,960,112C1120,117,1280,107,1360,101.3L1440,96L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
        </svg>

        <div class="max-w-7xl mx-auto px-6 py-10 md:py-14 relative">
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-400 text-blue-900 text-xs font-black">
                AYUDA / POLÍTICAS
            </div>

            <h1 class="mt-3 text-3xl md:text-5xl font-black tracking-tight text-white">
                Devolución y cambio
            </h1>

            <p class="mt-3 text-blue-100/90 max-w-2xl">
                Revisa causales, plazos y pasos para solicitar cambios o devoluciones en tus compras.
            </p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#politicas"
                    class="inline-flex items-center justify-center rounded-2xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-blue-900 hover:bg-yellow-300 transition">
                    Ver políticas ↓
                </a>
                <a href="mailto:<?php echo esc_attr($correo); ?>?subject=Devoluci%C3%B3n%20o%20Cambio%20-%20Solicitud"
                    class="inline-flex items-center justify-center rounded-2xl bg-white/90 px-6 py-3 text-sm font-extrabold text-blue-900 hover:bg-white transition">
                    Escribir a soporte
                </a>
            </div>
        </div>
    </header>

    <!-- CONTENIDO -->
    <section class="max-w-7xl mx-auto px-6 py-10 md:py-14" id="politicas">

        <!-- Accesos rápidos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
            <a href="#causales" class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4 hover:ring-blue-300 transition">
                <p class="text-xs text-gray-600">Sección</p>
                <p class="text-base font-extrabold text-[#3B4D64]">Causales</p>
            </a>
            <a href="#procedimiento" class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4 hover:ring-blue-300 transition">
                <p class="text-xs text-gray-600">Sección</p>
                <p class="text-base font-extrabold text-[#3B4D64]">Procedimiento</p>
            </a>
            <a href="#restricciones" class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4 hover:ring-blue-300 transition">
                <p class="text-xs text-gray-600">Sección</p>
                <p class="text-base font-extrabold text-[#3B4D64]">Restricciones</p>
            </a>
            <a href="#contacto" class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4 hover:ring-blue-300 transition">
                <p class="text-xs text-gray-600">Sección</p>
                <p class="text-base font-extrabold text-[#3B4D64]">Contacto</p>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- MAIN -->
            <article class="lg:col-span-2 bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-6 md:p-8">

                    <!-- Si quieres contenido editable: pega tu política en el editor de WP de esta página -->
                    <div class="gs-policy text-gray-800 leading-relaxed">
                        <?php
                        while (have_posts()) : the_post();
                            the_content();
                        endwhile;
                        ?>
                    </div>

                    <!-- Por si quieres forzar anchors aunque el contenido venga del editor -->
                    <div class="sr-only">
                        <span id="causales"></span>
                        <span id="procedimiento"></span>
                        <span id="restricciones"></span>
                        <span id="contacto"></span>
                    </div>

                </div>
            </article>

            <!-- ASIDE -->
            <aside class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden lg:sticky lg:top-28">
                <div class="p-6 md:p-7">

                    <h2 class="text-lg font-extrabold text-[#3B4D64]">Resumen rápido</h2>

                    <div class="mt-4 rounded-2xl bg-blue-50 ring-1 ring-blue-100 p-5">
                        <p class="text-sm font-extrabold text-blue-900">Canales de atención</p>
                        <p class="mt-2 text-sm text-blue-900/90">
                            Email:
                            <a class="font-extrabold text-blue-900 hover:underline"
                                href="mailto:<?php echo esc_attr($correo); ?>?subject=Devoluci%C3%B3n%20o%20Cambio%20-%20Solicitud">
                                <?php echo esc_html($correo); ?>
                            </a>
                        </p>
                        <p class="mt-2 text-sm text-blue-900/90">
                            Tel: <span class="font-extrabold"><?php echo esc_html($tel); ?></span>
                        </p>
                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-3">
                        <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
                            <p class="text-xs text-gray-600">Recomendación</p>
                            <p class="text-sm font-extrabold text-[#3B4D64]">Guarda factura y empaque</p>
                        </div>
                        <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
                            <p class="text-xs text-gray-600">Importante</p>
                            <p class="text-sm font-extrabold text-[#3B4D64]">Explica el motivo del reclamo</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col gap-3">
                        <a href="mailto:<?php echo esc_attr($correo); ?>?subject=Devoluci%C3%B3n%20o%20Cambio%20-%20Solicitud&body=Hola,%20deseo%20solicitar%20una%20devoluci%C3%B3n/cambio.%20Mi%20n%C3%BAmero%20de%20factura%20es:%20____%20y%20el%20motivo:%20____"
                            class="inline-flex items-center justify-center rounded-2xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-blue-900 hover:bg-yellow-300 transition">
                            Solicitar devolución/cambio
                        </a>

                        <button type="button"
                            onclick="window.print()"
                            class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-extrabold text-blue-900 ring-1 ring-gray-200 hover:ring-blue-300 transition">
                            Imprimir / Guardar PDF
                        </button>
                    </div>

                </div>
            </aside>

        </div>
    </section>

    <!-- Estilos mínimos para que el contenido del editor se vea pro -->
    <style>
        .gs-policy h2,
        .gs-policy h3 {
            font-weight: 800;
            color: #3B4D64;
            margin-top: 1.2rem;
        }

        .gs-policy p {
            margin-top: .75rem;
        }

        .gs-policy ul,
        .gs-policy ol {
            margin-top: .75rem;
            padding-left: 1.25rem;
        }

        .gs-policy li {
            margin-top: .35rem;
        }

        .gs-policy a {
            color: #1E40AF;
            font-weight: 800;
            text-decoration: underline;
        }
    </style>

</main>

<?php get_footer(); ?>