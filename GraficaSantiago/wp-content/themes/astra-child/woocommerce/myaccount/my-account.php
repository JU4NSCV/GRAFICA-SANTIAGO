<?php
defined('ABSPATH') || exit;

$user = wp_get_current_user();
$user_id = get_current_user_id();

function gs_initials($name) {
  $name = trim((string) $name);
  if ($name === '') return 'U';
  $parts = preg_split('/\s+/', $name);
  $ini = '';
  foreach ($parts as $p) { $ini .= mb_strtoupper(mb_substr($p, 0, 1)); if (mb_strlen($ini) >= 2) break; }
  return $ini ?: 'U';
}

$display_name = trim($user->first_name . ' ' . $user->last_name) ?: $user->display_name;
$initials = gs_initials($display_name);

do_action('woocommerce_before_my_account');
?>

<section class="gs-myaccount w-full to-white">
  <div class="max-w-7xl mx-auto px-4 md:px-6 py-10">

    <!-- Header usuario -->
    <div class="bg-white rounded-3xl border border-blue-900/10 shadow-sm p-6 md:p-7 mb-6">
      <div class="flex flex-col md:flex-row md:items-center gap-5">
        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-900 to-blue-700 text-white flex items-center justify-center text-2xl font-extrabold shadow">
          <?php echo esc_html($initials); ?>
        </div>

        <div class="flex-1">
          <h1 class="text-2xl md:text-3xl font-extrabold text-blue-900 leading-tight">
            Hola, <?php echo esc_html($display_name); ?>
          </h1>
          <p class="text-sm text-blue-900/60 mt-1">
            <?php echo esc_html($user->user_email); ?>
          </p>
        </div>

        <div class="flex gap-3">
          <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
             class="inline-flex items-center justify-center px-4 py-2 rounded-2xl border border-blue-900/15 text-blue-900 font-semibold hover:bg-blue-900 hover:text-white transition">
            Ir al catálogo
          </a>
          <a href="<?php echo esc_url(wc_logout_url(wc_get_page_permalink('myaccount'))); ?>"
             class="inline-flex items-center justify-center px-4 py-2 rounded-2xl bg-yellow-400 text-blue-900 font-extrabold hover:bg-blue-900 hover:text-white transition">
            Cerrar sesión
          </a>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

      <!-- Sidebar -->
      <aside class="lg:col-span-3">
        <div class="bg-white rounded-3xl border border-blue-900/10 shadow-sm p-4 sticky top-24">
          <h2 class="text-xs font-extrabold tracking-[0.22em] uppercase text-blue-900/50 mb-4">Mi cuenta</h2>
          <?php woocommerce_account_navigation(); ?>
        </div>
      </aside>

      <!-- Contenido -->
      <main class="lg:col-span-9">
        <div class="bg-white rounded-3xl border border-blue-900/10 shadow-sm p-6 md:p-7">
          <?php woocommerce_account_content(); ?>
        </div>
      </main>

    </div>
  </div>
</section>

<?php do_action('woocommerce_after_my_account'); ?>
