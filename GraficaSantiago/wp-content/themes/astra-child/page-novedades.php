<?php
defined('ABSPATH') || exit;
get_header();

$paged = max(1, get_query_var('paged') ?: (isset($_GET['paged']) ? (int)$_GET['paged'] : 1));

$q = new WP_Query([
  'post_type' => 'post',
  'post_status' => 'publish',
  'posts_per_page' => 9,
  'paged' => $paged,
  'category_name' => 'novedades',
]);
?>

<main class="w-full">
  <div class="w-screen relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]"
       style="background: linear-gradient(180deg, var(--gs-crema,#FBEDD8) 0%, #fff 55%, #fff 100%);">
    <div class="max-w-7xl mx-auto px-4 py-8">

      <header class="rounded-[28px] bg-gradient-to-r from-blue-800 to-blue-600 text-white p-8 md:p-10 shadow-soft">
        <h1 class="text-3xl md:text-4xl font-black !text-white">Eventos y promociones</h1>
        <p class="mt-2 text-white/90 max-w-2xl">Anuncios oficiales, campañas y fechas importantes.</p>
      </header>

      <!-- Texto editable (tú lo editas en la página Novedades) -->
      <section class="mt-6 bg-white rounded-[28px] border border-azulOsc/10 shadow-soft p-6 md:p-8">
        <div class="gs-prose">
          <?php while(have_posts()): the_post(); the_content(); endwhile; ?>
        </div>
      </section>

      <section class="mt-8">
        <?php if ($q->have_posts()): ?>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($q->have_posts()): $q->the_post();
              $thumb = get_the_post_thumbnail_url(get_the_ID(),'large');
            ?>
              <article class="bg-white rounded-[28px] border border-azulOsc/10 shadow-soft overflow-hidden hover:shadow-xl hover:-translate-y-1 transition">
                <a href="<?php the_permalink(); ?>" class="block">
                  <div class="relative aspect-[16/10]">
                    <?php if ($thumb): ?>
                      <img src="<?php echo esc_url($thumb); ?>" class="absolute inset-0 w-full h-full object-cover" alt="">
                    <?php endif; ?>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                  </div>
                  <div class="p-5">
                    <p class="text-xs text-azulOsc/60 font-semibold"><?php echo esc_html(get_the_date('d M, Y')); ?></p>
                    <h3 class="mt-1 text-azulOsc font-black"><?php the_title(); ?></h3>
                    <p class="mt-2 text-sm text-azulOsc/70"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?></p>
                    <div class="mt-4 inline-flex px-4 py-2 rounded-2xl bg-azulOsc text-white font-extrabold">Ver detalle →</div>
                  </div>
                </a>
              </article>
            <?php endwhile; wp_reset_postdata(); ?>
          </div>
        <?php else: ?>
          <div class="mt-6 bg-white rounded-[28px] border border-azulOsc/10 shadow-soft p-10 text-center">
            <h3 class="text-azulOsc font-black text-lg">Aún no hay novedades</h3>
            <p class="text-azulOsc/70 mt-2">Crea entradas en la categoría “novedades”.</p>
          </div>
        <?php endif; ?>
      </section>

    </div>
  </div>
</main>

<?php get_footer(); ?>
