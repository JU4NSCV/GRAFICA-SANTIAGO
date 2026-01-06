<?php
/* Template Name: InstitucioneViewCliente */
get_header();

?>

<main class="w-full">

  <div class="bg-blue-900 text-white pt-16 pb-24 rounded-b-[3rem] relative overflow-hidden">
    <div class="absolute top-0 left-0 w-full h-full opacity-10">
      <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
        <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
      </svg>
    </div>

    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
      <h1 class="text-3xl md:text-5xl font-bold mb-4">Encuentra tu Institución</h1>
      <p class="text-blue-200 text-lg mb-8">Descarga la lista oficial de útiles escolares 2025.</p>

      <div class="bg-white p-2 rounded-full shadow-2xl flex items-center max-w-2xl mx-auto transform translate-y-8">
        <div class="pl-6 text-gray-400">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <input
          id="institucionesSearch"
          type="text"
          placeholder="Busca por nombre (Ej: San Andrés, Mejía...)"
          class="w-full px-4 py-3 text-gray-900 outline-none rounded-full bg-transparent placeholder-gray-400">
        <button class="bg-yellow-500 hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-full transition-colors hidden md:block">
          Buscar
        </button>
      </div>
    </div>
  </div>

  <div class="max-w-7xl mx-auto px-6 mt-16 pb-20">

    <div class="flex flex-wrap justify-center gap-3 mb-10">
      <span class="text-sm text-gray-400 self-center mr-2 uppercase tracking-widest font-bold text-[10px]">Filtrar por:</span>

      <button data-filter-tipo="particular" class="px-5 py-2 bg-white border border-yellow-400 rounded-full text-sm font-bold text-yellow-700 hover:bg-yellow-50 transition shadow-sm flex items-center gap-2">
        ⭐ Particulares
      </button>

      <button data-filter-tipo="fiscal" class="px-5 py-2 bg-white border border-gray-200 rounded-full text-sm font-semibold text-gray-600 hover:border-blue-500 hover:text-blue-600 transition shadow-sm flex items-center gap-2">
        🏛️ Fiscales
      </button>

      <button data-filter-tipo="fiscomisional" class="px-5 py-2 bg-white border border-gray-200 rounded-full text-sm font-semibold text-gray-600 hover:border-blue-500 hover:text-blue-600 transition shadow-sm flex items-center gap-2">
        🤝 Fiscomisionales
      </button>
    </div>
    <div id="institucionesGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php
      // Buscar la página que usa el template "page-institucion.php"
      $inst_pages = get_pages([
        'post_type'  => 'page',
        'number'     => 1,
        'meta_key'   => '_wp_page_template',
        'meta_value' => 'page-institucion.php',
      ]);

      $institucion_view_url = !empty($inst_pages)
        ? get_permalink($inst_pages[0]->ID)
        : home_url('/institucion/'); // fallback por slug

      $q = new WP_Query([
        'post_type' => 'gsi_institucion',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
      ]);

      if ($q->have_posts()) :
        while ($q->have_posts()) : $q->the_post();
          $id = get_the_ID();

          // Link correcto al SINGLE del CPT:
          $link = get_permalink($id);

          $tipo   = get_post_meta($id, 'tipo', true) ?: 'particular';
          $ciudad = get_post_meta($id, 'ciudad', true) ?: '';
          $sector = get_post_meta($id, 'sector', true) ?: '';

          $cover = get_the_post_thumbnail_url($id, 'large');
          if (!$cover) $cover = 'https://images.unsplash.com/photo-1562774053-701939374585?q=80&w=1000&auto=format&fit=crop';

          $logo = get_post_meta($id, 'logo_url', true);
          if (!$logo) $logo = 'https://cdn-icons-png.flaticon.com/512/2997/2997235.png';

          $badge_text  = strtoupper($tipo);
          $badge_class = 'bg-yellow-100 text-yellow-700';
          if ($tipo === 'fiscal') $badge_class = 'bg-gray-100 text-gray-600';
          if ($tipo === 'fiscomisional') $badge_class = 'bg-blue-100 text-blue-700';
      ?>
          <article
            class="group bg-white rounded-3xl shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 overflow-hidden flex flex-col relative"
            data-name="<?php echo esc_attr(mb_strtolower(get_the_title())); ?>"
            data-tipo="<?php echo esc_attr($tipo); ?>">
            <div class="absolute top-4 right-4 z-10 bg-green-500 text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1">
              Listas 2025
            </div>

            <div class="h-36 bg-gray-200 overflow-hidden relative">
              <img src="<?php echo esc_url($cover); ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
            </div>

            <div class="px-6 pb-6 pt-10 relative flex-grow flex flex-col">
              <div class="absolute -top-10 left-6 w-16 h-16 bg-white rounded-2xl shadow-md border-4 border-white flex items-center justify-center overflow-hidden">
                <img src="<?php echo esc_url($logo); ?>" class="w-10 h-10 object-contain">
              </div>

              <div class="mb-4">
                <div class="flex items-center gap-2 mb-1">
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo esc_attr($badge_class); ?>">
                    <?php echo esc_html($badge_text); ?>
                  </span>
                </div>

                <h3 class="text-lg font-bold text-gray-900 leading-tight group-hover:text-blue-700 transition-colors">
                  <?php the_title(); ?>
                </h3>

                <p class="text-xs text-gray-500 mt-1">
                  <?php echo esc_html(trim($ciudad . ($sector ? ", $sector" : ""))); ?>
                </p>
              </div>

              <div class="mt-auto pt-4 border-t border-gray-100">
                <a href="<?php echo esc_url($link); ?>"
                  class="block w-full text-center py-2.5 rounded-xl bg-gray-50 text-gray-900 font-bold text-sm hover:bg-black hover:text-white transition-all">
                  Ver Listas &rarr;
                </a>
              </div>
            </div>
          </article>
        <?php
        endwhile;
        wp_reset_postdata();
      else :
        ?>
        <div class="col-span-full text-center text-gray-500 bg-white border border-gray-100 rounded-2xl p-10">
          No hay instituciones creadas todavía.
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>
<script>
  const input = document.getElementById('institucionesSearch');
  const cards = Array.from(document.querySelectorAll('#institucionesGrid article'));

  function applyFilter({
    text = '',
    tipo = ''
  }) {
    const q = (text || '').toLowerCase().trim();
    cards.forEach(card => {
      const name = card.dataset.name || '';
      const t = card.dataset.tipo || '';
      const okText = !q || name.includes(q);
      const okTipo = !tipo || t === tipo;
      card.style.display = (okText && okTipo) ? '' : 'none';
    });
  }

  input?.addEventListener('input', () => applyFilter({
    text: input.value
  }));

  // Si quieres activar los botones:
  document.querySelectorAll('[data-filter-tipo]').forEach(btn => {
    btn.addEventListener('click', () => {
      applyFilter({
        text: input.value,
        tipo: btn.dataset.filterTipo
      });
    });
  });
</script>


<?php
get_footer();
?>