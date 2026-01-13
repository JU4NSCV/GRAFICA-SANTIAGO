 <?php
/**
 * Página Postúlate / Trabaja con nosotros (Astra Child)
 */
defined('ABSPATH') || exit;

add_filter('astra_the_title_enabled', '__return_false');

add_filter('astra_page_layout', function ($layout) {
  if (is_page('postulate')) return 'full-width';
  return $layout;
});

get_header();
?>

<main class="w-full">

  <!-- HERO -->
  <header class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
    <div class="absolute inset-0 opacity-15 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700"></div>

    <svg class="absolute bottom-0 left-0 w-full h-10 md:h-14" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
      <path fill="white" d="M0,64L80,64C160,64,320,64,480,74.7C640,85,800,107,960,112C1120,117,1280,107,1360,101.3L1440,96L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
    </svg>

    <div class="max-w-7xl mx-auto px-6 py-10 md:py-14 relative">
      <p class="text-blue-200 text-sm font-semibold tracking-wider uppercase">Talento humano</p>
      <h1 class="mt-2 text-3xl md:text-5xl font-black tracking-tight text-white">
        Trabaja con nosotros
      </h1>
      <p class="mt-3 text-blue-100/90 max-w-2xl">
        Crece con un equipo que valora el compromiso, la atención al cliente y el trabajo en equipo.
      </p>
    </div>
  </header>

  <!-- CONTENIDO -->
  <section class="max-w-7xl mx-auto px-6 py-10 md:py-14">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">

      <!-- TEXTO -->
      <article class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="p-6 md:p-8">
          <h2 class="text-xl md:text-2xl font-extrabold text-[#3B4D64]">
            Únete a nuestro equipo
          </h2>

          <p class="mt-4 text-gray-700 leading-relaxed">
            En <strong>Santiago Papelería</strong> contamos con más de <strong>40 años de experiencia</strong> y seguimos
            creciendo gracias a nuestro equipo humano. Si te apasiona el servicio, el orden y el trabajo en equipo,
            queremos conocerte.
          </p>

          <div class="mt-6 rounded-2xl bg-blue-50 ring-1 ring-blue-100 p-5">
            <p class="text-sm font-extrabold text-blue-900">¡Postúlate y forma parte de nuestra empresa!</p>
            <p class="mt-2 text-sm text-blue-900/90">
              Envía tu hoja de vida a:
              <a class="font-extrabold text-blue-900 hover:underline"
                 href="mailto:servicios@santiagopapeleria.com?subject=Postulaci%C3%B3n%20-%20%C3%81rea%20o%20Cargo">
                servicios@santiagopapeleria.com
              </a>
            </p>
            <p class="mt-2 text-xs text-blue-900/70">
              Sugerencia: en el asunto coloca el cargo o área a la que postulas.
            </p>
          </div>

          <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="mailto:servicios@santiagopapeleria.com?subject=Postulaci%C3%B3n%20-%20%C3%81rea%20o%20Cargo"
               class="inline-flex items-center justify-center rounded-2xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-blue-900 hover:bg-yellow-300 transition">
              Enviar mi hoja de vida
            </a>

            <a href="<?php echo esc_url(home_url('/contactenos/')); ?>"
               class="inline-flex items-center justify-center rounded-2xl bg-white px-6 py-3 text-sm font-extrabold text-blue-900 ring-1 ring-gray-200 hover:ring-blue-300 transition">
              ¿Tienes dudas? Contáctanos
            </a>
          </div>
        </div>
      </article>

      <!-- IMAGEN -->
      <aside class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="p-4 md:p-6">
          <div class="relative overflow-hidden rounded-2xl">
            <img
              src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/trabaja-con-nosotros.jpg'); ?>"
              alt="Trabaja con nosotros"
              class="w-full h-[320px] md:h-[420px] object-cover"
              onerror="this.style.display='none'; this.parentElement.classList.add('bg-gradient-to-br','from-blue-900','via-blue-800','to-blue-700','h-[320px]','md:h-[420px]');"
            />
            <div class="absolute inset-0 pointer-events-none bg-gradient-to-t from-black/40 via-black/10 to-transparent"></div>

            <div class="absolute bottom-0 left-0 right-0 p-5 md:p-6 text-white">
              <p class="text-sm font-semibold text-white/90">Santiago Papelería</p>
              <p class="text-xl md:text-2xl font-black leading-tight">
                Tu talento + nuestra experiencia
              </p>
            </div>
          </div>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
              <p class="text-xs text-gray-600">Experiencia</p>
              <p class="text-base font-extrabold text-[#3B4D64]">40+ años</p>
            </div>
            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
              <p class="text-xs text-gray-600">Cultura</p>
              <p class="text-base font-extrabold text-[#3B4D64]">Equipo</p>
            </div>
            <div class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
              <p class="text-xs text-gray-600">Enfoque</p>
              <p class="text-base font-extrabold text-[#3B4D64]">Servicio</p>
            </div>
          </div>

        </div>
      </aside>

    </div>
  </section>

</main>

<?php get_footer(); ?>
