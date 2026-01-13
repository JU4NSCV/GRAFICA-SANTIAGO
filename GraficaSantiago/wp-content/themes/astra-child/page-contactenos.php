<?php

/**
 * Página Contactenos personalizada (Astra Child)
 */
defined('ABSPATH') || exit;

// Quitar el título por defecto de Astra SOLO aquí
add_filter('astra_the_title_enabled', '__return_false');

// (Opcional) Forzar full width SOLO para esta página
add_filter('astra_page_layout', function ($layout) {
  if (is_page('contactenos')) return 'full-width';
  return $layout;
});

get_header();
?>

<main class="w-full">

  <!-- HERO -->
  <header class="relative overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
    <div class="absolute inset-0 opacity-20 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700"></div>

    <!-- onda simple -->
    <svg class="absolute bottom-0 left-0 w-full h-10 md:h-14" viewBox="0 0 1440 120" preserveAspectRatio="none" aria-hidden="true">
      <path fill="white" d="M0,64L80,64C160,64,320,64,480,74.7C640,85,800,107,960,112C1120,117,1280,107,1360,101.3L1440,96L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
    </svg>

    <div class="max-w-7xl mx-auto px-6 py-10 md:py-14 relative">
      <p class="text-blue-200 text-sm font-semibold tracking-wider uppercase">Atención al cliente</p>
      <h1 class="mt-2 text-3xl md:text-5xl font-black tracking-tight text-white">
        Contáctenos
      </h1>
      <p class="mt-3 text-blue-100/90 max-w-2xl">
        Estamos listos para ayudarte. Escríbenos o llámanos y te respondemos lo más pronto posible.
      </p>
    </div>
  </header>

  <!-- CONTENIDO -->
  <section class="max-w-7xl mx-auto px-6 py-10 md:py-14">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      <!-- COLUMNA IZQUIERDA -->
      <aside class="space-y-6">

        <!-- DIRECCIONES -->
        <div class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-extrabold text-[#3B4D64]">¿Dónde estamos?</h2>
            <p class="mt-1 text-sm text-gray-600">Visítanos en nuestras ubicaciones.</p>
          </div>

          <div class="p-6 space-y-4">
            <!-- item -->
            <div class="flex gap-3">
              <span class="mt-1 text-blue-800">
                <!-- icon pin -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2c-3.866 0-7 3.134-7 7 0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z" />
                </svg>
              </span>
              <div>
                <p class="font-bold text-gray-900">Matriz</p>
                <p class="text-sm text-gray-700">Azuay 152-48 entre 18 de Noviembre y Avenida Universitaria</p>
              </div>
            </div>

            <div class="flex gap-3">
              <span class="mt-1 text-blue-800">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2c-3.866 0-7 3.134-7 7 0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z" />
                </svg>
              </span>
              <div>
                <p class="font-bold text-gray-900">Sucursal 1</p>
                <p class="text-sm text-gray-700">Almacén UTPL</p>
              </div>
            </div>

            <div class="flex gap-3">
              <span class="mt-1 text-blue-800">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2c-3.866 0-7 3.134-7 7 0 5.25 7 13 7 13s7-7.75 7-13c0-3.866-3.134-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z" />
                </svg>
              </span>
              <div>
                <p class="font-bold text-gray-900">Sucursal 2</p>
                <p class="text-sm text-gray-700">Calle Guaranda y Avenida Cuxibamba</p>
              </div>
            </div>
          </div>
        </div>

        <!-- TELÉFONOS -->
        <div class="bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-extrabold text-[#3B4D64]">Llámenos</h2>
            <p class="mt-1 text-sm text-gray-600">Atención rápida por teléfono o celular.</p>
          </div>

          <div class="p-6 space-y-4">
            <div class="flex items-start gap-3">
              <span class="mt-1 text-blue-800">
                <!-- phone -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M6.62 10.79a15.053 15.053 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.07 21 3 13.93 3 5a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.24 1.01l-2.21 2.2z" />
                </svg>
              </span>
              <div>
                <p class="font-bold text-gray-900">Teléfonos</p>
                <a class="text-sm text-blue-800 font-semibold hover:underline" href="tel:072573358">072573358</a>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <span class="mt-1 text-blue-800">
                <!-- mobile -->
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M17 1H7C5.9 1 5 1.9 5 3v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 18H7V5h10v14z" />
                </svg>
              </span>
              <div>
                <p class="font-bold text-gray-900">Celular por mayor</p>
                <p class="text-sm text-gray-700">
                  <a class="text-blue-800 font-semibold hover:underline" href="tel:0939826491">0939826491</a>
                  <span class="text-gray-400"> / </span>
                  <a class="text-blue-800 font-semibold hover:underline" href="tel:0939522690">0939522690</a>
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <span class="mt-1 text-blue-800">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M17 1H7C5.9 1 5 1.9 5 3v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 18H7V5h10v14z" />
                </svg>
              </span>
              <div>
                <p class="font-bold text-gray-900">Celular por menor</p>
                <a class="text-sm text-blue-800 font-semibold hover:underline" href="tel:0987667459">0987667459</a>
              </div>
            </div>

            <!-- Opcional: Ver extensiones -->
            <details class="rounded-2xl bg-gray-50 ring-1 ring-gray-200 p-4">
              <summary class="cursor-pointer font-bold text-[#3B4D64]">Ver Extensiones</summary>
              <div class="mt-3 text-sm text-gray-700 space-y-1">
                <p><strong>Administración:</strong> Ext. 110</p>
                <p><strong>Facturación:</strong> Ext. 0</p>
                <p><strong>Finanzas:</strong> Ext. 116</p>
                <p><strong>Compras e importaciones:</strong> Ext. 113</p>
                <p><strong>Contabilidad</strong> Ext. 109</p>

              </div>
            </details>

          </div>
        </div>

      </aside>

      <!-- COLUMNA DERECHA (FORM) -->
      <article class="lg:col-span-2 bg-white rounded-3xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-gray-200">
          <?php if (isset($_GET['sent'])) : ?>
            <?php if ($_GET['sent'] === '1') : ?>
              <div class="mb-5 rounded-2xl bg-green-50 ring-1 ring-green-200 p-4 text-sm text-green-900">
                ✅ ¡Mensaje enviado! Te responderemos lo más pronto posible.
              </div>
            <?php else : ?>
              <div class="mb-5 rounded-2xl bg-red-50 ring-1 ring-red-200 p-4 text-sm text-red-900">
                ❌ No se pudo enviar el mensaje. Revisa los campos e inténtalo nuevamente.
              </div>
            <?php endif; ?>
          <?php endif; ?>

          <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" class="space-y-4">
            <input type="hidden" name="action" value="gs_contact_submit">
            <?php wp_nonce_field('gs_contact_submit'); ?>
            <input type="hidden" name="ts" value="<?php echo esc_attr(time()); ?>">

            <!-- Honeypot invisible -->
            <div class="hidden">
              <label>Website <input type="text" name="website" autocomplete="off" tabindex="-1"></label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-[#3B4D64] mb-1">Nombre</label>
                <input required name="name" type="text"
                  class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                  placeholder="Tu nombre">
              </div>

              <div>
                <label class="block text-sm font-bold text-[#3B4D64] mb-1">Correo</label>
                <input required name="email" type="email"
                  class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                  placeholder="tucorreo@ejemplo.com">
              </div>
            </div>

            <div>
              <label class="block text-sm font-bold text-[#3B4D64] mb-1">Área</label>
              <select name="area"
                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200">
                <option value="ventas">Ventas</option>
                <option value="servicios">Dudas y Reclamos</option>
                <option value="facturas">Facturación</option>
                <option value="general">General</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-bold text-[#3B4D64] mb-1">Mensaje</label>
              <textarea required name="message" rows="6"
                class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200"
                placeholder="Escribe tu mensaje..."></textarea>
            </div>

            <div class="pt-2 flex justify-end">
              <button type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-yellow-400 px-6 py-3 text-sm font-extrabold text-blue-900 hover:bg-yellow-300 transition">
                Enviar
              </button>
            </div>
          </form>

        </div>

        <div class="p-6 md:p-8">
          <?php
          // IMPORTANTE:
          // Aquí se imprime el contenido de la página (pega tu shortcode del formulario en el editor de WP).
          if (have_posts()) :
            while (have_posts()) : the_post();
              the_content();
            endwhile;
          endif;
          ?>
        </div>

        <!-- Correos por área -->
        <div class="p-6 md:p-8 border-t border-gray-200 bg-gray-50">
          <h3 class="text-base font-extrabold text-[#3B4D64]">Escríbanos</h3>

          <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="mailto:ventas@santiagopapeleria.com"
              class="group bg-white rounded-2xl p-5 ring-1 ring-gray-200 hover:ring-blue-300 transition">
              <p class="text-sm font-bold text-gray-900">Ventas</p>
              <p class="mt-1 text-sm text-blue-800 font-semibold group-hover:underline">
                ventas@santiagopapeleria.com
              </p>
            </a>

            <a href="mailto:servicios@santiagopapeleria.com"
              class="group bg-white rounded-2xl p-5 ring-1 ring-gray-200 hover:ring-blue-300 transition">
              <p class="text-sm font-bold text-gray-900">Dudas y Reclamos</p>
              <p class="mt-1 text-sm text-blue-800 font-semibold group-hover:underline">
                servicios@santiagopapeleria.com
              </p>
            </a>

            <a href="mailto:facturas@santiagopapeleria.com"
              class="group bg-white rounded-2xl p-5 ring-1 ring-gray-200 hover:ring-blue-300 transition">
              <p class="text-sm font-bold text-gray-900">Facturación</p>
              <p class="mt-1 text-sm text-blue-800 font-semibold group-hover:underline">
                facturas@santiagopapeleria.com
              </p>
            </a>
          </div>
        </div>

      </article>

    </div>
  </section>

</main>

<?php get_footer(); ?>