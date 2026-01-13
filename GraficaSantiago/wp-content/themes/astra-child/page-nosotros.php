<?php

/**
 * Página Nosotros personalizada (Astra Child)
 */

defined('ABSPATH') || exit;

// Quitar el título/encabezado por defecto de Astra SOLO en esta plantilla
add_filter('astra_the_title_enabled', '__return_false');

get_header();
?>

<main class="w-full">

    <!-- HERO -->
    <header class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
        <div class="absolute -top-24 -right-24 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -bottom-24 -left-24 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>

        <div class="max-w-7xl mx-auto px-6 py-12 md:py-16 relative">
            <p class="text-blue-200 text-sm font-semibold tracking-wider uppercase">
                La empresa
            </p>
            <h1 class="mt-2 text-3xl md:text-5xl font-black tracking-tight text-white">
                Sobre Santiago Papelería
            </h1>
            <p class="mt-3 text-blue-100/90 max-w-2xl">
                Una historia que nace en Loja y crece con calidad, variedad y servicio.
            </p>
        </div>
    </header>

    <!-- CONTENIDO -->
    <section class="max-w-7xl mx-auto px-6 py-10 md:py-14">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- HISTORIA -->
            <article class="lg:col-span-2 bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-200">
                    <h2 class="text-xl md:text-2xl font-extrabold text-[#3B4D64]">Historia</h2>

                    <p class="mt-4 text-gray-700 leading-relaxed">
                        Gráficas Santiago fue fundada el <strong>25 de junio de 1980</strong> en la ciudad de <strong>Loja</strong>
                        por el <strong>Dr. Santiago Alejandro</strong> y <strong>Zoilita Matamoros</strong>. En sus inicios, el local
                        funcionaba como <strong>imprenta</strong> y ofrecía algunos artículos de papelería.
                        En <strong>1987</strong>, la sección de papelería y el nombre de la empresa fueron adquiridos por el
                        <strong>Ing. Julio Cesar Luna Cruz</strong> y <strong>Rosemary Alejandro Matamoros</strong>.
                    </p>

                    <p class="mt-4 text-gray-700 leading-relaxed">
                        A lo largo de los años, la empresa ha crecido fortaleciendo sus servicios, ampliando sus
                        <strong>importaciones internacionales</strong> y ofreciendo <strong>variedad y calidad</strong> con
                        <strong>precios competitivos</strong>.
                    </p>

                    <p class="mt-4 text-gray-700 leading-relaxed">
                        En <strong>2016</strong>, Santiago Papelería implementa su marca propia: <strong>CREANDO</strong>.
                        El <strong>06 de julio de 2018</strong>, abrimos nuestras puertas con una nueva imagen y el nombre
                        <strong>“Santiago Papelería”</strong>, convirtiéndonos en el <strong>primer autoservicio</strong> de la ciudad de Loja.
                    </p>
                </div>

                <!-- MISIÓN / VISIÓN -->
                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-2xl p-5 ring-1 ring-gray-200">
                        <h3 class="text-base font-extrabold text-[#3B4D64]">Misión</h3>
                        <p class="mt-2 text-gray-700 leading-relaxed">
                            Ofrecer variedad en útiles escolares, suministros de oficina, productos tecnológicos y bazar en general,
                            con los mejores precios, excelente calidad brindando el mejor servicio a nuestros clientes, con un gran
                            equipo de trabajo comprometidos con la empresa.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-5 ring-1 ring-gray-200">
                        <h3 class="text-base font-extrabold text-[#3B4D64]">Visión</h3>
                        <p class="mt-2 text-gray-700 leading-relaxed">
                            Ser una empresa líder en ventas en el mercado local y nacional contribuyendo con la innovación y desarrollo
                            económico de nuestro país.
                        </p>
                    </div>
                </div>
            </article>

            <!-- LATERAL: HITOS + VALORES -->
            <aside class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-lg font-extrabold text-[#3B4D64]">Hitos</h2>

                    <ol class="mt-4 space-y-4">
                        <li class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-700"></span>
                            <div>
                                <p class="text-sm font-bold text-gray-900">1980</p>
                                <p class="text-sm text-gray-700">Fundación en Loja como imprenta y papelería.</p>
                            </div>
                        </li>

                        <li class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-700"></span>
                            <div>
                                <p class="text-sm font-bold text-gray-900">1987</p>
                                <p class="text-sm text-gray-700">Compra de la sección de papelería y del nombre de la empresa.</p>
                            </div>
                        </li>

                        <li class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-700"></span>
                            <div>
                                <p class="text-sm font-bold text-gray-900">2016</p>
                                <p class="text-sm text-gray-700">Implementación de la marca propia <strong>CREANDO</strong>.</p>
                            </div>
                        </li>

                        <li class="flex gap-3">
                            <span class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-700"></span>
                            <div>
                                <p class="text-sm font-bold text-gray-900">2018</p>
                                <p class="text-sm text-gray-700">
                                    Nueva imagen y nombre <strong>“Santiago Papelería”</strong>. Primer autoservicio en Loja.
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>

                <div class="p-6">
                    <h2 class="text-lg font-extrabold text-[#3B4D64]">Valores</h2>

                    <ul class="mt-4 flex flex-wrap gap-2">
                        <li class="px-3 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-900 ring-1 ring-blue-100">Trabajo en equipo</li>
                        <li class="px-3 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-900 ring-1 ring-blue-100">Responsabilidad</li>
                        <li class="px-3 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-900 ring-1 ring-blue-100">Equidad</li>
                        <li class="px-3 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-900 ring-1 ring-blue-100">Mejora continua</li>
                        <li class="px-3 py-2 rounded-full text-xs font-bold bg-blue-50 text-blue-900 ring-1 ring-blue-100">Disciplina</li>
                    </ul>
                </div>
            </aside>

        </div>
    </section>

</main>

<?php get_footer(); ?>