<?php
defined('ABSPATH') || exit;

get_header('shop');
?>

<section class="w-full bg-gradient-to-b from-blue-50 to-white">
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-10">

    <div class="flex flex-col lg:flex-row gap-6">

      <!-- SIDEBAR filtros -->
      <aside class="lg:w-80 shrink-0">
        <div class="bg-white rounded-3xl border border-blue-900/10 shadow-sm p-5 lg:sticky lg:top-28">

          <div class="flex items-center justify-between gap-3 mb-4">
            <h3 class="text-base font-extrabold text-blue-900">Categorías</h3>

            <!-- Toggle stock (opcional, funciona con el hook de functions.php que te dejo abajo) -->
            <?php
            $instock_on = (isset($_GET['instock']) && $_GET['instock'] === '1');
            $toggle_url = add_query_arg('instock', $instock_on ? '0' : '1');
            ?>
            <a href="<?php echo esc_url($toggle_url); ?>"
              class="text-xs font-bold px-3 py-1 rounded-full border border-blue-900/20
                      <?php echo $instock_on ? 'bg-blue-900 text-white' : 'bg-white text-blue-900'; ?>
                      hover:bg-blue-900 hover:text-white transition">
              Stock
            </a>
          </div>

          <div class="space-y-2 max-h-[62vh] overflow-auto pr-2">
            <?php
            $parents = get_terms([
              'taxonomy'   => 'product_cat',
              'hide_empty' => true,
              'parent'     => 0,
              'orderby'    => 'name',
              'order'      => 'ASC',
            ]);

            if (!empty($parents) && !is_wp_error($parents)) :
              foreach ($parents as $parent) :

                $children = get_terms([
                  'taxonomy'   => 'product_cat',
                  'hide_empty' => true,
                  'parent'     => $parent->term_id,
                  'orderby'    => 'name',
                  'order'      => 'ASC',
                ]);

                $parent_link = get_term_link($parent);
            ?>
                <details class="group rounded-2xl border border-blue-900/10 bg-blue-50/40 open:bg-white open:shadow-sm transition">
                  <summary class="cursor-pointer list-none px-4 py-3 flex items-center justify-between gap-2">
                    <a href="<?php echo esc_url($parent_link); ?>"
                      class="text-sm font-extrabold text-blue-900 hover:text-yellow-500 transition">
                      <?php echo esc_html($parent->name); ?>
                    </a>

                    <span class="text-blue-900/50 group-open:rotate-180 transition">
                      ▾
                    </span>
                  </summary>

                  <?php if (!empty($children) && !is_wp_error($children)) : ?>
                    <div class="px-4 pb-3">
                      <ul class="space-y-1">
                        <?php foreach ($children as $child) : ?>
                          <li>
                            <a href="<?php echo esc_url(get_term_link($child)); ?>"
                              class="block text-sm text-blue-900/80 hover:text-blue-900 hover:underline transition">
                              <?php echo esc_html($child->name); ?>
                            </a>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                  <?php endif; ?>
                </details>
            <?php
              endforeach;
            else :
              echo '<p class="text-sm text-blue-900/60">No hay categorías.</p>';
            endif;
            ?>
          </div>
        </div>
      </aside>

      <!-- MAIN -->
      <main class="flex-1">

        <!-- TOP BAR -->
        <div class="bg-white rounded-3xl border border-blue-900/10 shadow-sm p-5 mb-6">
          <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
              <h1 class="text-2xl md:text-3xl font-extrabold text-blue-900">
                <?php woocommerce_page_title(); ?>
              </h1>
              <div class="text-sm text-blue-900/60 mt-1">
                <?php woocommerce_result_count(); ?>
              </div>
            </div>

            <div class="flex items-center justify-between md:justify-end gap-3 w-full md:w-auto">
              <span class="text-sm font-semibold text-blue-900/70 hidden md:block">Ordenar:</span>

              <div class="relative">
                <?php woocommerce_catalog_ordering(); ?>

                <!-- Icono flecha -->
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-blue-900/45"
                  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                </svg>
              </div>
            </div>

          </div>
        </div>

        <?php if (woocommerce_product_loop()) : ?>

          <?php woocommerce_product_loop_start(); ?>

          <?php
          while (have_posts()) :
            the_post();
            wc_get_template_part('content', 'product'); // usa tu content-product.php
          endwhile;
          ?>

          <?php woocommerce_product_loop_end(); ?>

          <div class="mt-8">
            <?php woocommerce_pagination(); ?>
          </div>

        <?php else : ?>
          <?php do_action('woocommerce_no_products_found'); ?>
        <?php endif; ?>

      </main>

    </div>

  </div>
</section>

<?php
get_footer('shop');
