<?php
/**
 * Plugin Name: GS Portal Mayorista
 * Description: Portal para rol mayorista dentro de "Mi cuenta" (WooCommerce).
 * Version: 0.1.0
 */
if (!defined('ABSPATH')) exit;

/** Helpers */
function gs_user_has_role(string $role): bool {
  $u = wp_get_current_user();
  return $u && in_array($role, (array)$u->roles, true);
}
function gs_is_mayorista(): bool {
  return is_user_logged_in() && (gs_user_has_role('mayorista') || current_user_can('gs_mayorista'));
}

/** Crear rol mayorista (si no existe) */
register_activation_hook(__FILE__, function () {
  if (get_role('mayorista')) return;

  $customer = get_role('customer');
  $caps = $customer ? $customer->capabilities : ['read' => true];
  $caps['gs_mayorista'] = true;

  add_role('mayorista', 'Mayorista', $caps);
});

/** Endpoints */
add_action('init', function () {
  add_rewrite_endpoint('mayorista', EP_ROOT | EP_PAGES);
  add_rewrite_endpoint('catalogo-mayorista', EP_ROOT | EP_PAGES);
  add_rewrite_endpoint('pedido-rapido', EP_ROOT | EP_PAGES);
  add_filter('woocommerce_get_query_vars', function ($vars) {
  $vars['mayorista'] = 'mayorista';
  $vars['catalogo-mayorista'] = 'catalogo-mayorista';
  $vars['pedido-rapido'] = 'pedido-rapido';
  return $vars;
});

}, 10);

register_activation_hook(__FILE__, function () { flush_rewrite_rules(); });
register_deactivation_hook(__FILE__, function () { flush_rewrite_rules(); });

/** Menú Mi Cuenta (solo mayorista) */
add_filter('woocommerce_account_menu_items', function ($items) {
  if (!gs_is_mayorista()) return $items;

  $new = [];
  $new['mayorista'] = 'Panel Mayorista';
  $new['catalogo-mayorista'] = 'Catálogo Mayorista';
  $new['pedido-rapido'] = 'Pedido rápido (SKU)';

  return $new + $items;
});

/** Bloquear acceso a endpoints si NO es mayorista */
add_action('template_redirect', function () {
  if (!is_user_logged_in()) return;

  $isArea =
    is_wc_endpoint_url('mayorista') ||
    is_wc_endpoint_url('catalogo-mayorista') ||
    is_wc_endpoint_url('pedido-rapido');

  if ($isArea && !gs_is_mayorista()) {
    wp_safe_redirect(wc_get_page_permalink('myaccount'));
    exit;
  }
});

/** Redirección login */
add_filter('woocommerce_login_redirect', function ($redirect, $user) {
  if (!$user || empty($user->roles)) return $redirect;
  if (in_array('mayorista', (array)$user->roles, true)) {
    return wc_get_page_permalink('myaccount') . 'mayorista/';
  }
  return $redirect;
}, 10, 2);

/** Render: Panel Mayorista */
add_action('woocommerce_account_mayorista_endpoint', function () {
  echo '<h2>Panel Mayorista</h2>';
  echo '<p>Bienvenido. Aquí tienes acceso a precios mayoristas y pedido rápido.</p>';
  echo '<ul>';
  echo '<li><a href="'.esc_url(wc_get_page_permalink('myaccount').'catalogo-mayorista/').'">Ir a Catálogo Mayorista</a></li>';
  echo '<li><a href="'.esc_url(wc_get_page_permalink('myaccount').'pedido-rapido/').'">Ir a Pedido rápido por SKU</a></li>';
  echo '</ul>';
});

/** Render: Catálogo Mayorista (simple) */
add_action('woocommerce_account_catalogo-mayorista_endpoint', function () {
  echo '<h2>Catálogo Mayorista</h2>';

  $products = wc_get_products([
    'status' => 'publish',
    'limit'  => 40,
    'orderby'=> 'date',
    'order'  => 'DESC',
  ]);

  if (!$products) { echo '<p>No hay productos.</p>'; return; }

  echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">';


  foreach ($products as $p) {
    $id  = $p->get_id();
    $sku = $p->get_sku();
    $img = $p->get_image('woocommerce_thumbnail');
    $priceHtml = $p->get_price_html();

    echo '<div style="border:1px solid #e5e7eb;border-radius:16px;padding:12px;">';
    echo $img;
    echo '<h3 style="margin:8px 0 0 0;font-size:14px;">'.esc_html($p->get_name()).'</h3>';
    echo '<div style="font-size:12px;opacity:.8;">SKU: '.esc_html($sku).'</div>';
    echo '<div style="margin:6px 0;">'.$priceHtml.'</div>';

    echo '<form method="post">';
    wp_nonce_field('gs_add_cart_'.$id);
    echo '<input type="hidden" name="gs_add_id" value="'.esc_attr($id).'">';
    echo '<input type="number" name="gs_qty" value="1" min="1" style="width:80px;"> ';
    echo '<button type="submit">Agregar</button>';
    echo '</form>';

    echo '</div>';
  }

  echo '</div>';
});

/** Agregar al carrito desde catálogo */
add_action('init', function () {
  if (!gs_is_mayorista()) return;
  if (empty($_POST['gs_add_id'])) return;

  $id = (int) $_POST['gs_add_id'];
  if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'gs_add_cart_'.$id)) return;

  $qty = max(1, (int)($_POST['gs_qty'] ?? 1));
  WC()->cart->add_to_cart($id, $qty);

  wp_safe_redirect(wc_get_page_permalink('myaccount') . 'catalogo-mayorista/');
  exit;
});

/** Render: Pedido rápido por SKU */
add_action('woocommerce_account_pedido-rapido_endpoint', function () {
  echo '<h2>Pedido rápido (por SKU)</h2>';
  echo '<p>Pega líneas tipo: <code>SKU,cantidad</code></p>';

  if (!empty($_POST['gs_bulk_lines'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'] ?? '', 'gs_bulk_order')) return;

    $lines = explode("\n", (string)$_POST['gs_bulk_lines']);
    $added = 0;

    foreach ($lines as $line) {
      $line = trim($line);
      if ($line === '') continue;

      [$sku, $qty] = array_map('trim', array_pad(explode(',', $line), 2, '1'));
      $qty = max(1, (int)$qty);

      $pid = wc_get_product_id_by_sku($sku);
      if ($pid) {
        WC()->cart->add_to_cart($pid, $qty);
        $added++;
      }
    }

    echo '<div style="padding:10px;border:1px solid #bbf7d0;background:#f0fdf4;border-radius:12px;margin:12px 0;">
            Agregados al carrito: '.esc_html($added).'
          </div>';

    echo '<p><a href="'.esc_url(wc_get_cart_url()).'">Ir al carrito</a></p>';
  }

  echo '<form method="post">';
  wp_nonce_field('gs_bulk_order');
  echo '<textarea name="gs_bulk_lines" rows="10" style="width:100%;max-width:720px;"></textarea><br><br>';
  echo '<button type="submit">Agregar todo al carrito</button>';
  echo '</form>';
});
