<?php
defined('ABSPATH') || exit;

$user_id = get_current_user_id();

// Recently viewed
$recent_ids = [];
if (!empty($_COOKIE['woocommerce_recently_viewed'])) {
  $recent_ids = array_filter(array_map('absint', explode('|', wp_unslash($_COOKIE['woocommerce_recently_viewed']))));
  $recent_ids = array_slice(array_values(array_unique($recent_ids)), 0, 8);
}

// Wishlist (si existe tu función)
$wishlist_ids = function_exists('gs_wishlist_get_ids') ? array_values(array_map('absint', (array) gs_wishlist_get_ids())) : [];
$wishlist_ids = array_slice($wishlist_ids, 0, 8);

// Últimos pedidos
$orders = function_exists('wc_get_orders') ? wc_get_orders([
  'customer' => $user_id,
  'limit'    => 5,
  'orderby'  => 'date',
  'order'    => 'DESC',
]) : [];
?>

<div class="space-y-6">

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>"
       class="rounded-3xl border border-blue-900/10 p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
      <div class="text-xs font-extrabold tracking-[0.2em] uppercase text-blue-900/50">Pedidos</div>
      <div class="text-2xl font-extrabold text-blue-900 mt-2">Ver mis pedidos →</div>
    </a>

    <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-address')); ?>"
       class="rounded-3xl border border-blue-900/10 p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
      <div class="text-xs font-extrabold tracking-[0.2em] uppercase text-blue-900/50">Direcciones</div>
      <div class="text-2xl font-extrabold text-blue-900 mt-2">Actualizar →</div>
    </a>

    <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>"
       class="rounded-3xl border border-blue-900/10 p-5 hover:shadow-lg hover:-translate-y-0.5 transition">
      <div class="text-xs font-extrabold tracking-[0.2em] uppercase text-blue-900/50">Cuenta</div>
      <div class="text-2xl font-extrabold text-blue-900 mt-2">Editar datos →</div>
    </a>
  </div>

  <?php if (!empty($orders)) : ?>
    <div class="rounded-3xl border border-blue-900/10 p-6">
      <h3 class="text-xl font-extrabold text-blue-900">Últimos pedidos</h3>
      <div class="mt-4 overflow-auto rounded-2xl border border-blue-900/10">
        <table class="w-full text-sm">
          <thead class="bg-blue-50/50">
            <tr class="[&>th]:text-left [&>th]:px-4 [&>th]:py-3 [&>th]:font-extrabold [&>th]:text-blue-900/70">
              <th>Pedido</th><th>Fecha</th><th>Estado</th><th>Total</th><th class="text-right">Acción</th>
            </tr>
          </thead>
          <tbody class="[&>tr]:border-t [&>tr]:border-blue-900/10">
            <?php foreach ($orders as $order) :
              /** @var WC_Order $order */
              $view_url = wc_get_endpoint_url('view-order', $order->get_id(), wc_get_page_permalink('myaccount'));
            ?>
              <tr class="[&>td]:px-4 [&>td]:py-3">
                <td class="font-extrabold text-blue-900">#<?php echo esc_html($order->get_order_number()); ?></td>
                <td class="text-blue-900/70"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></td>
                <td><span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-900 text-white"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span></td>
                <td class="font-extrabold text-blue-900"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></td>
                <td class="text-right">
                  <a class="px-4 py-2 rounded-2xl border border-blue-900/15 text-blue-900 font-extrabold hover:bg-blue-900 hover:text-white transition"
                     href="<?php echo esc_url($view_url); ?>">Ver</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($recent_ids) && function_exists('wc_get_product')) : ?>
    <div class="rounded-3xl border border-blue-900/10 p-6">
      <h3 class="text-xl font-extrabold text-blue-900">Vistos recientemente</h3>
      <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($recent_ids as $rid) :
          $p = wc_get_product($rid); if (!$p) continue; ?>
          <a href="<?php echo esc_url(get_permalink($rid)); ?>"
            class="group rounded-2xl border border-blue-900/10 bg-white p-3 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition">
            <div class="aspect-square rounded-2xl border border-blue-900/5 bg-white overflow-hidden flex items-center justify-center">
              <?php echo $p->get_image('woocommerce_thumbnail', ['class' => 'w-full h-full object-contain p-4 group-hover:scale-[1.04] transition']); ?>
            </div>
            <div class="mt-3 text-sm font-extrabold text-blue-900 line-clamp-2"><?php echo esc_html($p->get_name()); ?></div>
            <div class="text-sm font-extrabold text-blue-900/80 mt-1"><?php echo wp_kses_post($p->get_price_html()); ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <?php if (!empty($wishlist_ids) && function_exists('wc_get_product')) : ?>
    <div class="rounded-3xl border border-blue-900/10 p-6">
      <h3 class="text-xl font-extrabold text-blue-900">Tu lista de deseos</h3>
      <div class="mt-5 grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($wishlist_ids as $wid) :
          $p = wc_get_product($wid); if (!$p) continue; ?>
          <a href="<?php echo esc_url(get_permalink($wid)); ?>"
            class="group rounded-2xl border border-blue-900/10 bg-white p-3 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition">
            <div class="aspect-square rounded-2xl border border-blue-900/5 bg-white overflow-hidden flex items-center justify-center">
              <?php echo $p->get_image('woocommerce_thumbnail', ['class' => 'w-full h-full object-contain p-4 group-hover:scale-[1.04] transition']); ?>
            </div>
            <div class="mt-3 text-sm font-extrabold text-blue-900 line-clamp-2"><?php echo esc_html($p->get_name()); ?></div>
            <div class="text-sm font-extrabold text-blue-900/80 mt-1"><?php echo wp_kses_post($p->get_price_html()); ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

</div>
