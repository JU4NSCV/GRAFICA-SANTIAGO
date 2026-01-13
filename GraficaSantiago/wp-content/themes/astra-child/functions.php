<?php
// Cargar estilos y scripts del tema hijo
add_action('wp_enqueue_scripts', function () {
  // Estilos del tema padre Astra
  wp_enqueue_style(
    'astra-parent',
    get_template_directory_uri() . '/style.css'
  );

  // Estilos del hijo (por si luego quieres CSS propio)
  wp_enqueue_style(
    'astra-child',
    get_stylesheet_uri(),
    ['astra-parent']
  );

  // Tailwind CDN (modo rápido para desarrollo)
  wp_enqueue_script(
    'tailwind-cdn',
    'https://cdn.tailwindcss.com',
    [],
    null,
    false // cargar en <head>
  );
});

add_filter('astra_page_layout', function ($layout) {
  if (is_shop() || is_product_category() || is_product_tag()) {
    return 'no-sidebar';
  }
  return $layout;
});

add_filter('astra_get_content_layout', function ($layout) {
  if (is_shop() || is_product_category() || is_product_tag()) {
    return 'full-width';
  }
  return $layout;
});

// ...lo que ya tengas arriba

require_once get_stylesheet_directory() . '/logica/auth.php';

//ROL///////////////////////////////////////
add_action('wp_footer', function () {
  if (!is_user_logged_in()) return;

  // Mostrar el badge solo a administradores (evita exponer roles a clientes)
  if (!current_user_can('manage_options')) return;

  $user  = wp_get_current_user();
  $roles = array_map('sanitize_text_field', (array) $user->roles);
  if (!$roles) $roles = ['sin-rol'];

  // Texto bonito
  $label = implode(' / ', $roles);

  echo '<div id="gs-role-badge" title="Rol con el que estás logueado">'
    . 'Rol: <strong>' . esc_html($label) . '</strong>'
    . '</div>';
});
///////////////////////////////////////


////quitar “Descargas” si no lo usas
add_filter('woocommerce_account_menu_items', function ($items) {
  unset($items['downloads']);
  return $items;
});
///////////////////

////Carga tu CSS desde el child theme///////////
add_action('wp_enqueue_scripts', function () {

  $file = get_stylesheet_directory() . '/assets/css/style.css';
  $url  = get_stylesheet_directory_uri() . '/assets/css/style.css';

  if (file_exists($file)) {
    wp_enqueue_style(
      'gs-custom-style',
      $url,
      array(),                 // si quieres, aquí puedes poner dependencias
      filemtime($file)         // rompe cache automático
    );
  }
}, 99);
//////////////////

////////Bloquear Catálogo Mayorista para clientes (y permitir carrito para todos)
add_action('template_redirect', function () {
  if (is_admin() || wp_doing_ajax()) return;

  // Bloquear endpoints mayorista a NO mayoristas
  if (function_exists('is_wc_endpoint_url') && (
    is_wc_endpoint_url('mayorista') ||
    is_wc_endpoint_url('catalogo-mayorista') ||
    is_wc_endpoint_url('pedido-rapido')
  )) {
    if (!gs_is_mayorista()) {
      wp_safe_redirect(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/mi-cuenta/'));
      exit;
    }
  }

  // IMPORTANTE: No bloquees carrito/checkout para nadie
  if (function_exists('is_cart') && is_cart()) return;
  if (function_exists('is_checkout') && is_checkout()) return;
});
///////////////////\

function gs_is_customer_user(): bool
{
  if (!is_user_logged_in()) return false;
  $u = wp_get_current_user();
  return in_array('customer', (array)$u->roles, true);
}

////////////Que el precio mayorista aplique solo si el rol es “mayorista”
// Precio por rol (simple products)
add_filter('woocommerce_product_get_price', function ($price, $product) {
  if (!is_user_logged_in()) return $price;
  if (!function_exists('gs_is_mayorista') || !gs_is_mayorista()) return $price;

  $wh = get_post_meta($product->get_id(), '_gs_precio_mayorista', true);
  if ($wh === '') return $price;

  // soportar coma decimal
  $wh = (float) str_replace(',', '.', $wh);

  return $wh > 0 ? (string)$wh : $price;
}, 10, 2);

// Asegura que también en el carrito/checkout use ese precio
add_action('woocommerce_before_calculate_totals', function ($cart) {
  if (is_admin() && !defined('DOING_AJAX')) return;
  if (!is_user_logged_in()) return;
  if (!function_exists('gs_is_mayorista') || !gs_is_mayorista()) return;

  foreach ($cart->get_cart() as $item) {
    $product = $item['data'];
    $wh = get_post_meta($product->get_id(), '_gs_precio_mayorista', true);
    if ($wh === '') continue;

    $wh = (float) str_replace(',', '.', $wh);
    if ($wh > 0) $product->set_price($wh);
  }
});
////////////////////////////////


//////WISHLIST
// ===============================
// WISHLIST - GRAFICA SANTIAGO
// ===============================

/**
 * Obtener IDs de wishlist (logueado: user_meta | invitado: cookie)
 */
function gs_wishlist_get_ids(): array
{
  $ids = [];

  if (is_user_logged_in()) {
    $stored = get_user_meta(get_current_user_id(), 'gs_wishlist', true);
    if (is_array($stored)) $ids = $stored;
  } else {
    if (!empty($_COOKIE['gs_wishlist'])) {
      $decoded = json_decode(stripslashes($_COOKIE['gs_wishlist']), true);
      if (is_array($decoded)) $ids = $decoded;
    }
  }

  // Normaliza
  $ids = array_values(array_unique(array_map('intval', $ids)));
  $ids = array_filter($ids, fn($v) => $v > 0);

  return $ids;
}

/**
 * Guardar IDs de wishlist
 */
function gs_wishlist_set_ids(array $ids): void
{
  $ids = array_values(array_unique(array_map('intval', $ids)));
  $ids = array_filter($ids, fn($v) => $v > 0);

  if (is_user_logged_in()) {
    update_user_meta(get_current_user_id(), 'gs_wishlist', $ids);
  } else {
    // Cookie por 1 año
    setcookie(
      'gs_wishlist',
      wp_json_encode($ids),
      time() + YEAR_IN_SECONDS,
      COOKIEPATH ?: '/',
      COOKIE_DOMAIN,
      is_ssl(),
      true // httpOnly
    );
    // Para que esté disponible en el request actual
    $_COOKIE['gs_wishlist'] = wp_json_encode($ids);
  }
}

/**
 * Toggle (agregar/quitar) un producto
 */
function gs_wishlist_toggle(int $product_id): array
{
  $product_id = absint($product_id);
  if (!$product_id || !wc_get_product($product_id)) {
    return ['ok' => false, 'message' => 'Producto no válido.'];
  }

  $ids = gs_wishlist_get_ids();
  $in  = in_array($product_id, $ids, true);

  if ($in) {
    $ids = array_values(array_diff($ids, [$product_id]));
    $in = false;
  } else {
    $ids[] = $product_id;
    $in = true;
  }

  gs_wishlist_set_ids($ids);

  return [
    'ok' => true,
    'in_wishlist' => $in,
    'count' => count($ids),
    'ids' => $ids,
  ];
}

/**
 * AJAX: toggle wishlist
 */
function gs_wishlist_update(int $product_id, string $mode = 'toggle'): array
{
  $product_id = absint($product_id);
  if (!$product_id || !wc_get_product($product_id)) {
    return ['ok' => false, 'message' => 'Producto no válido.'];
  }

  $ids = gs_wishlist_get_ids();
  $in  = in_array($product_id, $ids, true);

  $mode = in_array($mode, ['toggle', 'add', 'remove'], true) ? $mode : 'toggle';

  if ($mode === 'remove') {
    if ($in) {
      $ids = array_values(array_diff($ids, [$product_id]));
    }
    $in = false;
  } elseif ($mode === 'add') {
    if (!$in) {
      $ids[] = $product_id;
    }
    $in = true;
  } else { // toggle
    if ($in) {
      $ids = array_values(array_diff($ids, [$product_id]));
      $in = false;
    } else {
      $ids[] = $product_id;
      $in = true;
    }
  }

  gs_wishlist_set_ids($ids);

  return [
    'ok' => true,
    'in_wishlist' => $in,
    'count' => count($ids),
    'ids' => $ids,
  ];
}

function gs_ajax_toggle_wishlist()
{
  check_ajax_referer('gs_wishlist_nonce', 'nonce');

  $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
  $mode = isset($_POST['mode']) ? sanitize_text_field($_POST['mode']) : 'toggle';

  $result = gs_wishlist_update($product_id, $mode);

  if (empty($result['ok'])) {
    wp_send_json_error($result);
  }
  wp_send_json_success($result);
}
add_action('wp_ajax_gs_toggle_wishlist', 'gs_ajax_toggle_wishlist');
add_action('wp_ajax_nopriv_gs_toggle_wishlist', 'gs_ajax_toggle_wishlist');


/**
 * Enqueue JS
 */
function gs_wishlist_enqueue_assets()
{
  // JS principal (botón corazón)
  $js_path = get_stylesheet_directory_uri() . '/assets/js/wishlist.js';
  $js_file = get_stylesheet_directory() . '/assets/js/wishlist.js';
  $ver     = file_exists($js_file) ? filemtime($js_file) : '1.0.0';

  wp_enqueue_script('gs-wishlist', $js_path, [], $ver, true);

  wp_localize_script('gs-wishlist', 'GS_WISHLIST', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'ajax'     => admin_url('admin-ajax.php'), // alias por compatibilidad
    'nonce'    => wp_create_nonce('gs_wishlist_nonce'),
    'count'    => count(gs_wishlist_get_ids()),
  ]);

  // Micro CSS: corazón activo + click feedback
  $inline = "
      .gs-wishlist-btn.is-active svg, .js-wishlist-toggle.is-active svg{ fill: currentColor; }
      .gs-wishlist-btn svg, .js-wishlist-toggle svg{ transition: transform .15s ease; }
      .gs-wishlist-btn:active svg, .js-wishlist-toggle:active svg{ transform: scale(.92); }
    ";
  wp_register_style('gs-wishlist-inline', false);
  wp_enqueue_style('gs-wishlist-inline');
  wp_add_inline_style('gs-wishlist-inline', $inline);
}
add_action('wp_enqueue_scripts', 'gs_wishlist_enqueue_assets');

/**
 * Helper: render botón wishlist (corazón)
 * Úsalo en cards/single: gs_render_wishlist_button($product_id);
 */
function gs_render_wishlist_button(int $product_id, string $extra_classes = ''): void
{
  $ids = gs_wishlist_get_ids();
  $in  = in_array($product_id, $ids, true);

  $base = "gs-wishlist-btn inline-flex items-center justify-center rounded-full border-2 transition
             focus:outline-none focus:ring-2 focus:ring-blue-900/40";
  $state = $in
    ? "is-active bg-yellow-400 border-yellow-400 text-blue-900"
    : "bg-white border-blue-900 text-blue-900 hover:bg-yellow-400 hover:border-yellow-400";

  $aria_label = $in ? 'Quitar de favoritos' : 'Añadir a favoritos';

  echo '<button type="button"
        class="' . esc_attr("$base $state $extra_classes") . '"
        data-product-id="' . esc_attr($product_id) . '"
        aria-pressed="' . ($in ? 'true' : 'false') . '"
        aria-label="' . esc_attr($aria_label) . '">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             width="18" height="18" fill="' . ($in ? 'currentColor' : 'none') . '"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.8 4.6c-1.5-1.7-3.9-2-5.7-.8-.6.4-1.1 1-1.4 1.6-.3-.6-.8-1.2-1.4-1.6-1.8-1.2-4.2-.9-5.7.8-1.7 2-1.5 4.9.4 6.7l6.7 6.1 6.7-6.1c1.9-1.8 2.1-4.7.4-6.7z"/>
        </svg>
    </button>';
}

/**
 * Shortcode: [gs_wishlist]
 * Crea una página "Wishlist" y pega el shortcode.
 */
function gs_wishlist_shortcode(): string
{
  $ids = gs_wishlist_get_ids();

  ob_start();

  echo '<div class="max-w-6xl mx-auto px-4 py-8">';
  echo '<h2 class="text-2xl font-bold mb-6" style="color:#3B4D64;">Mi Wishlist</h2>';
  echo '<div class="mb-5 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">';
  echo '  <div class="text-sm text-gray-600">Productos guardados: <span class="font-semibold">' . count($ids) . '</span></div>';
  echo '  <div class="w-full sm:w-[420px]">';
  echo '    <input id="gsWishlistSearch" type="text"
             class="w-full px-4 py-3 rounded-2xl border border-gray-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-900/30"
             placeholder="Buscar en tu wishlist...">';
  echo '  </div>';
  echo '</div>';

  echo '<div id="gsWishlistNoResults" class="hidden p-6 rounded-2xl bg-white border border-gray-200 shadow-sm text-gray-600 mb-4">
        No se encontraron productos con ese texto.
      </div>';


  if (empty($ids)) {
    echo '<div class="p-6 rounded-2xl bg-white border border-gray-200 shadow-sm">
                <p class="text-gray-600 mb-4">Aún no tienes productos guardados.</p>
                <a class="inline-flex items-center px-4 py-3 rounded-2xl font-semibold bg-yellow-400 text-blue-900 border-2 border-yellow-400 hover:bg-blue-700 hover:text-white hover:border-blue-700 transition"
                   href="' . esc_url(wc_get_page_permalink('shop')) . '">Ir a la tienda</a>
              </div>';
    echo '</div>';
    return ob_get_clean();
  }

  echo '<div class="grid grid-cols-2 md:grid-cols-4 gap-4">';

  foreach ($ids as $pid) {
    $product = wc_get_product($pid);
    if (!$product) continue;

    $link = get_permalink($pid);
    $img  = get_the_post_thumbnail($pid, 'woocommerce_thumbnail', [
      'class' => 'w-full h-40 object-contain mb-2 rounded-2xl bg-white'
    ]);

    echo '<article data-wishlist-card data-title="' . esc_attr($product->get_name()) . '"
      class="border border-gray-200 rounded-2xl p-3 shadow-sm bg-white flex flex-col relative">';

    echo '<div class="absolute top-3 right-3">';
    gs_render_wishlist_button($pid, 'w-10 h-10');
    echo '</div>';

    echo '<a href="' . esc_url($link) . '" class="block mb-2">' . $img . '</a>';
    echo '<a href="' . esc_url($link) . '" class="font-semibold text-sm mb-1" style="color:#3B4D64;">' . esc_html($product->get_name()) . '</a>';
    echo '<div class="text-sm font-bold mb-3" style="color:#3B4D64;">' . $product->get_price_html() . '</div>';

    echo '<div data-wishlist-grid class="grid grid-cols-2 md:grid-cols-4 gap-4">';


    echo '<a href="' . esc_url($link) . '"
         class="inline-flex items-center justify-center px-4 py-3 rounded-2xl text-xs uppercase font-semibold
                bg-yellow-400 text-blue-900 border-2 border-yellow-400 hover:bg-blue-700 hover:text-white hover:border-blue-700 hover:shadow-md transition">
        Ver producto
      </a>';

    echo '<button type="button"
        class="gs-wishlist-remove inline-flex items-center justify-center px-4 py-3 rounded-2xl text-xs uppercase font-semibold
               bg-white text-blue-900 border-2 border-blue-900 hover:bg-red-600 hover:text-white hover:border-red-600 hover:shadow-md transition"
        data-product-id="' . esc_attr($pid) . '">
        Eliminar
      </button>';

    echo '</div>';


    echo '</article>';
  }

  echo '</div></div>';

  return ob_get_clean();
}
add_shortcode('gs_wishlist', 'gs_wishlist_shortcode');
///////////////////////


////////////

///BANNER PROMOCIONES////

add_action('customize_register', function ($wp_customize) {

  // Sección
  $wp_customize->add_section('gs_promos_section', [
    'title'    => 'Promociones',
    'priority' => 35,
  ]);

  // Activar / desactivar
  $wp_customize->add_setting('gs_promos_enabled', [
    'default'           => true,
    'sanitize_callback' => function ($v) {
      return (bool) $v;
    },
  ]);

  $wp_customize->add_control('gs_promos_enabled', [
    'section' => 'gs_promos_section',
    'label'   => 'Mostrar sección de Promociones',
    'type'    => 'checkbox',
  ]);

  // Modo
  $wp_customize->add_setting('gs_promos_mode', [
    'default'           => 'manual',
    'sanitize_callback' => function ($v) {
      return in_array($v, ['manual', 'carousel'], true) ? $v : 'manual';
    },
  ]);

  $wp_customize->add_control('gs_promos_mode', [
    'section' => 'gs_promos_section',
    'label'   => 'Modo de visualización',
    'type'    => 'select',
    'choices' => [
      'manual'   => 'Manual (elige 1 imagen)',
      'carousel' => 'Carrusel automático',
    ],
  ]);

  // Imagen activa (cuando es manual)
  $wp_customize->add_setting('gs_promos_active', [
    'default'           => 1,
    'sanitize_callback' => 'absint',
  ]);

  $wp_customize->add_control('gs_promos_active', [
    'section'     => 'gs_promos_section',
    'label'       => 'Imagen activa (si estás en modo Manual)',
    'type'        => 'number',
    'input_attrs' => ['min' => 1, 'max' => 5],
  ]);

  // Intervalo (ms) para carrusel
  $wp_customize->add_setting('gs_promos_interval', [
    'default'           => 4500,
    'sanitize_callback' => 'absint',
  ]);

  $wp_customize->add_control('gs_promos_interval', [
    'section'     => 'gs_promos_section',
    'label'       => 'Intervalo carrusel (milisegundos)',
    'type'        => 'number',
    'input_attrs' => ['min' => 1500, 'step' => 100],
  ]);

  // 5 slots de imágenes + link opcional
  for ($i = 1; $i <= 5; $i++) {

    $wp_customize->add_setting("gs_promo_img_$i", [
      'default'           => 0,
      'sanitize_callback' => 'absint', // guarda attachment ID
    ]);

    $wp_customize->add_control(new WP_Customize_Media_Control(
      $wp_customize,
      "gs_promo_img_$i",
      [
        'section'   => 'gs_promos_section',
        'label'     => "Imagen Promoción #$i",
        'mime_type' => 'image',
      ]
    ));

    $wp_customize->add_setting("gs_promo_link_$i", [
      'default'           => '',
      'sanitize_callback' => 'esc_url_raw',
    ]);

    $wp_customize->add_control("gs_promo_link_$i", [
      'section' => 'gs_promos_section',
      'label'   => "Link Promoción #$i (opcional)",
      'type'    => 'url',
    ]);
  }
});
/////////////////////////////////////

// HEADER////////////////////////
add_action('customize_register', function ($wp_customize) {

  $wp_customize->add_section('gs_header', [
    'title'    => 'Header (Gráfica Santiago)',
    'priority' => 25,
  ]);

  // Mostrar/ocultar buscador
  $wp_customize->add_setting('gs_header_search_enabled', [
    'default'           => true,
    'sanitize_callback' => function ($v) {
      return (bool) $v;
    },
  ]);

  $wp_customize->add_control('gs_header_search_enabled', [
    'section' => 'gs_header',
    'label'   => 'Mostrar barra de búsqueda',
    'type'    => 'checkbox',
  ]);

  // Placeholder
  $wp_customize->add_setting('gs_header_search_placeholder', [
    'default'           => 'Buscar productos, categorías...',
    'sanitize_callback' => 'sanitize_text_field',
  ]);

  $wp_customize->add_control('gs_header_search_placeholder', [
    'section' => 'gs_header',
    'label'   => 'Texto del buscador (placeholder)',
    'type'    => 'text',
  ]);
});
/////////////////////////////////////

/////// FUINCION OARA QUITAR LOS OUTSTOCK////
add_action('pre_get_posts', function ($q) {
  if (is_admin() || !$q->is_main_query()) return;

  // Solo en tienda y archivos de producto (categorías/etiquetas)
  if (is_shop() || is_product_taxonomy()) {
    $meta_query = (array) $q->get('meta_query');

    $meta_query[] = [
      'key'     => '_stock_status',
      'value'   => 'instock',
      'compare' => '=',
    ];

    $q->set('meta_query', $meta_query);
  }
}, 20);
/////////////

//////// WISHLIST //////////
// (Opcional) Si NO tienes implementadas estas funciones, este bloque crea una wishlist por cookie "gs_wishlist"
if (!function_exists('gs_wishlist_get_ids')) {
  function gs_wishlist_get_ids(): array
  {
    $raw = isset($_COOKIE['gs_wishlist']) ? wp_unslash($_COOKIE['gs_wishlist']) : '';
    $ids = json_decode($raw, true);
    if (!is_array($ids)) $ids = array_filter(array_map('absint', explode(',', (string)$raw)));
    $ids = array_values(array_unique(array_filter(array_map('absint', $ids))));
    return $ids;
  }
}
if (!function_exists('gs_wishlist_set_ids')) {
  function gs_wishlist_set_ids(array $ids): array
  {
    $ids = array_values(array_unique(array_filter(array_map('absint', $ids))));
    $value = wp_json_encode($ids);
    setcookie('gs_wishlist', $value, time() + 60 * 60 * 24 * 30, COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), true);
    $_COOKIE['gs_wishlist'] = $value;
    return $ids;
  }
}
if (!function_exists('gs_wishlist_remove_id')) {
  function gs_wishlist_remove_id(int $product_id): void
  {
    $ids = gs_wishlist_get_ids();
    $ids = array_values(array_diff($ids, [absint($product_id)]));
    gs_wishlist_set_ids($ids);
  }
}
if (!function_exists('gs_wishlist_clear')) {
  function gs_wishlist_clear(): void
  {
    gs_wishlist_set_ids([]);
  }
}

// JS en wishlist
add_action('wp_enqueue_scripts', function () {
  if (is_page('wishlist')) {
    wp_enqueue_script(
      'gs-wishlist-ui',
      get_stylesheet_directory_uri() . '/assets/js/wishlist-ui.js',
      [],
      null,
      true
    );
    wp_localize_script('gs-wishlist-ui', 'GS_WISHLIST', [
      'ajax'  => admin_url('admin-ajax.php'),
      'nonce' => wp_create_nonce('gs_wishlist'),
    ]);
  }
});

// AJAX remove
add_action('wp_ajax_gs_wishlist_remove', function () {
  check_ajax_referer('gs_wishlist', 'nonce');
  $id = absint($_POST['product_id'] ?? 0);
  if ($id) gs_wishlist_remove_id($id);
  wp_send_json_success(['count' => count(gs_wishlist_get_ids())]);
});
add_action('wp_ajax_nopriv_gs_wishlist_remove', function () {
  check_ajax_referer('gs_wishlist', 'nonce');
  $id = absint($_POST['product_id'] ?? 0);
  if ($id) gs_wishlist_remove_id($id);
  wp_send_json_success(['count' => count(gs_wishlist_get_ids())]);
});

// AJAX clear
add_action('wp_ajax_gs_wishlist_clear', function () {
  check_ajax_referer('gs_wishlist', 'nonce');
  gs_wishlist_clear();
  wp_send_json_success(['count' => 0]);
});
add_action('wp_ajax_nopriv_gs_wishlist_clear', function () {
  check_ajax_referer('gs_wishlist', 'nonce');
  gs_wishlist_clear();
  wp_send_json_success(['count' => 0]);
});


//////////////////////////
////// funcion para el catalogo de productos /////////
// 1) Grid de productos (2 móvil, 3 md, 4 lg)
add_filter('woocommerce_product_loop_start', function () {
  return '<ul class="products grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 !m-0 !p-0 list-none">';
});


// 2) Botón add-to-cart con Tailwind
add_filter('woocommerce_loop_add_to_cart_args', function ($args, $product) {
  $args['class'] = trim(($args['class'] ?? '') . ' w-full h-11 inline-flex items-center justify-center rounded-2xl
    bg-blue-900 text-white font-semibold hover:bg-yellow-400 hover:text-blue-900 transition');
  return $args;
}, 10, 2);

// 3) (Opcional) Mostrar SOLO stock cuando instock=1 en URL (lo usa el botón del sidebar)
add_action('pre_get_posts', function ($q) {
  if (is_admin() || !$q->is_main_query()) return;
  if (!(is_shop() || is_product_taxonomy())) return;

  if (isset($_GET['instock']) && $_GET['instock'] === '1') {
    $meta_query = (array) $q->get('meta_query');
    $meta_query[] = [
      'key'   => '_stock_status',
      'value' => 'instock',
    ];
    $q->set('meta_query', $meta_query);
  }
}, 20);


//////Fuerza el contenedor de Astra a full width real en tienda
add_filter('astra_page_layout', function ($layout) {
  if (is_shop() || is_product_taxonomy()) return 'full-width';
  return $layout;
});
/////////////////////////////////////

////////el layout sea perfecto con Astra
add_filter('astra_page_layout', function ($layout) {
  if (is_product()) return 'full-width';
  return $layout;
});
/////////////////////////////////////



////////// MY ACCOUNT - Layout personalizado ////////////
add_action('init', function () {
  add_rewrite_endpoint('historial', EP_ROOT | EP_PAGES);
  add_rewrite_endpoint('seguridad', EP_ROOT | EP_PAGES);
});

add_filter('woocommerce_account_menu_items', function ($items) {
  // Renombrar edit-account a "Detalles"
  if (isset($items['edit-account'])) $items['edit-account'] = 'Detalles de cuenta';

  // Insertar Historial después de Orders
  $new = [];
  foreach ($items as $key => $label) {
    $new[$key] = $label;

    if ($key === 'orders') {
      $new['historial'] = 'Historial';
      $new['seguridad'] = 'Seguridad';
    }
  }
  return $new;
}, 20);

// Contenido Historial
add_action('woocommerce_account_historial_endpoint', function () {
  wc_get_template('myaccount/dashboard.php'); // reutiliza el dashboard (visto + wishlist)
});

// Contenido Seguridad (simple: link a editar cuenta)
// SEGURIDAD: Mostrar formulario de cambio de contraseña
add_action('woocommerce_account_seguridad_endpoint', function () {
  wc_get_template('myaccount/seguridad.php');
});

// ENDPOINT: /mi-cuenta/catalogo-mayorista/
add_action('init', function () {
  add_rewrite_endpoint('catalogo-mayorista', EP_ROOT | EP_PAGES);
}, 20);

add_filter('woocommerce_get_query_vars', function ($vars) {
  $vars['catalogo-mayorista'] = 'catalogo-mayorista';
  return $vars;
});

// Agregar item al menú (solo mayoristas)
add_filter('woocommerce_account_menu_items', function ($items) {
  if (!function_exists('gs_is_mayorista') || !gs_is_mayorista()) {
    unset($items['catalogo-mayorista']);
    return $items;
  }

  $new = [];
  foreach ($items as $k => $v) {
    $new[$k] = $v;
    if ($k === 'mayorista') {
      $new['catalogo-mayorista'] = 'Catálogo mayorista';
    }
  }

  // Si no existía "mayorista" por alguna razón, lo añade al final
  if (!isset($new['catalogo-mayorista'])) $new['catalogo-mayorista'] = 'Catálogo mayorista';
  return $new;
}, 60);

// Render del catálogo mayorista
add_action('woocommerce_account_catalogo-mayorista_endpoint', function () {
  wc_get_template('myaccount/catalogo-mayorista.php');
});



add_action('init', function () {
  add_rewrite_endpoint('historial', EP_ROOT | EP_PAGES);
});

add_filter('woocommerce_account_menu_items', function ($items) {
  // Inserta Historial después de Pedidos
  $new = [];
  foreach ($items as $k => $v) {
    $new[$k] = $v;
    if ($k === 'orders') {
      $new['historial'] = 'Historial';
    }
  }
  return $new;
});

add_action('woocommerce_account_historial_endpoint', function () {
  echo '<h2 class="text-xl font-extrabold text-blue-900 mb-2">Historial</h2>';
  echo '<p class="text-sm text-blue-900/70 mb-5">Productos vistos recientemente.</p>';

  $viewed = ! empty($_COOKIE['woocommerce_recently_viewed'])
    ? array_reverse(array_filter(array_map('absint', explode('|', wp_unslash($_COOKIE['woocommerce_recently_viewed'])))))
    : [];

  if (empty($viewed)) {
    echo '<div class="p-4 rounded-2xl bg-blue-50 text-blue-900/80">Aún no has visto productos.</div>';
    return;
  }

  $products = wc_get_products(['include' => array_slice($viewed, 0, 12)]);

  echo '<div class="grid grid-cols-2 md:grid-cols-3 gap-4">';
  foreach ($products as $p) {
    $id = $p->get_id();
    echo '<a class="block rounded-2xl border border-blue-900/10 bg-white p-3 hover:shadow-md transition" href="' . esc_url(get_permalink($id)) . '">';
    echo $p->get_image('woocommerce_thumbnail', ['class' => 'w-full h-32 object-contain bg-white rounded-xl']);
    echo '<div class="mt-2 text-sm font-bold text-blue-900 line-clamp-2">' . esc_html($p->get_name()) . '</div>';
    echo '<div class="text-sm text-blue-900/80 font-semibold mt-1">' . wp_kses_post($p->get_price_html()) . '</div>';
    echo '</a>';
  }
  echo '</div>';
});

//////////////////////////////////////////////
///////// desactivar título de Astra solo en Mi cuenta

add_filter('astra_the_title_enabled', function ($enabled) {
  if (function_exists('is_account_page') && is_account_page()) {
    return false;
  }
  return $enabled;
});
////////////////////////////////
//////////// FUNCION PARA INSTITUCIONES//////
// ====== 1) CPT Institución + CPT Lista de útiles ======
add_action('init', function () {

  register_post_type('gsi_institucion', [
    'label' => 'Instituciones',
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'institucion'],
    'supports' => ['title', 'editor', 'thumbnail'],
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-welcome-learn-more',
  ]);

  register_post_type('gsi_lista_utiles', [
    'label' => 'Listas de útiles',
    'public' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'supports' => ['title'],
    'show_in_rest' => true,

    // Capabilities para que un rol "institucion" pueda gestionarlas sin ser admin total
    'capability_type' => ['gsi_lista', 'gsi_listas'],
    'map_meta_cap' => true,
    'menu_icon' => 'dashicons-clipboard',
  ]);
});

// ====== 2) Rol Institución Mayorista (para publicar listas) ======
add_action('init', function () {
  if (get_role('institucion_mayorista')) return;

  add_role('institucion_mayorista', 'Institución (Mayorista)', [
    'read' => true,

    // Permisos CPT listas
    'edit_gsi_listas' => true,
    'edit_gsi_lista' => true,
    'publish_gsi_listas' => true,
    'read_gsi_lista' => true,
    'delete_gsi_lista' => true,
    'edit_published_gsi_listas' => true,
    'delete_published_gsi_listas' => true,
  ]);
});

// ====== 3) Metabox simple para meta de la lista ======
add_action('add_meta_boxes', function () {
  add_meta_box('gsi_lista_meta', 'Datos de la lista', 'gsi_lista_meta_box', 'gsi_lista_utiles', 'normal', 'high');
});

function gsi_lista_meta_box($post)
{
  wp_nonce_field('gsi_lista_save', 'gsi_lista_nonce');

  $institucion_id = get_post_meta($post->ID, 'institucion_id', true);
  $segmento = get_post_meta($post->ID, 'segmento', true);
  $curso = get_post_meta($post->ID, 'curso', true);
  $paralelo = get_post_meta($post->ID, 'paralelo', true);
  $anio = get_post_meta($post->ID, 'anio_lectivo', true);
  $items_raw = get_post_meta($post->ID, 'items_raw', true);

  $instituciones = get_posts([
    'post_type' => 'gsi_institucion',
    'numberposts' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
  ]);
?>
  <p>
    <label><strong>Institución</strong></label><br>
    <select name="institucion_id" style="width:100%">
      <option value="">-- Selecciona --</option>
      <?php foreach ($instituciones as $inst): ?>
        <option value="<?php echo esc_attr($inst->ID); ?>" <?php selected($institucion_id, $inst->ID); ?>>
          <?php echo esc_html($inst->post_title); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </p>

  <p style="display:flex; gap:12px;">
    <span style="flex:1">
      <label><strong>Segmento</strong></label><br>
      <select name="segmento" style="width:100%">
        <option value="">--</option>
        <option value="escuela" <?php selected($segmento, 'escuela'); ?>>Escuela</option>
        <option value="colegio" <?php selected($segmento, 'colegio'); ?>>Colegio</option>
      </select>
    </span>
    <span style="flex:1">
      <label><strong>Curso</strong></label><br>
      <input type="text" name="curso" value="<?php echo esc_attr($curso); ?>" style="width:100%" placeholder="Ej: 3ero de Básica / 1BGU">
    </span>
    <span style="width:120px">
      <label><strong>Paralelo</strong></label><br>
      <input type="text" name="paralelo" value="<?php echo esc_attr($paralelo); ?>" style="width:100%" placeholder="A">
    </span>
  </p>

  <p>
    <label><strong>Año lectivo</strong></label><br>
    <input type="text" name="anio_lectivo" value="<?php echo esc_attr($anio); ?>" style="width:100%" placeholder="2025-2026">
  </p>

  <p>
    <label><strong>Items (SKU;CANTIDAD) uno por línea</strong></label><br>
    <textarea name="items_raw" rows="10" style="width:100%" placeholder="027872;2"><?php echo esc_textarea($items_raw); ?></textarea>
    <small>SKU = COD del producto. Formato: <code>SKU;CANTIDAD</code></small>
  </p>
  <?php
}

add_action('save_post_gsi_lista_utiles', function ($post_id) {
  if (!isset($_POST['gsi_lista_nonce']) || !wp_verify_nonce($_POST['gsi_lista_nonce'], 'gsi_lista_save')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $fields = ['institucion_id', 'segmento', 'curso', 'paralelo', 'anio_lectivo', 'items_raw'];
  foreach ($fields as $f) {
    $val = isset($_POST[$f]) ? wp_unslash($_POST[$f]) : '';
    update_post_meta($post_id, $f, $val);
  }
});

// ====== Helpers: parsear items_raw -> productos Woo ======
function gsi_parse_items_raw($items_raw)
{
  if (!function_exists('wc_get_product_id_by_sku')) return [];

  $lines = preg_split("/\r\n|\n|\r/", trim((string)$items_raw));
  $items = [];

  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;

    $parts = array_map('trim', explode(';', $line));
    $sku = $parts[0] ?? '';
    $qty = isset($parts[1]) ? (int)$parts[1] : 1;
    if ($sku === '' || $qty <= 0) continue;

    $pid = wc_get_product_id_by_sku($sku);
    if (!$pid) continue;

    $product = wc_get_product($pid);
    if (!$product) continue;

    $price = (float) $product->get_price();
    $items[] = [
      'product_id' => $pid,
      'sku' => $sku,
      'name' => $product->get_name(),
      'qty' => $qty,
      'price' => $price,
      'subtotal' => $price * $qty,
    ];
  }
  return $items;
}

// ====== 4) AJAX: obtener lista por institución + segmento + curso (+ paralelo opcional) ======
add_action('wp_ajax_nopriv_gsi_get_lista', 'gsi_get_lista');
add_action('wp_ajax_gsi_get_lista', 'gsi_get_lista');

function gsi_get_lista()
{
  check_ajax_referer('gsi_institucion_nonce', 'nonce');

  $institucion_id = isset($_POST['institucion_id']) ? (int) $_POST['institucion_id'] : 0;
  $segmento = isset($_POST['segmento']) ? sanitize_text_field($_POST['segmento']) : '';
  $curso = isset($_POST['curso']) ? sanitize_text_field($_POST['curso']) : '';
  $paralelo = isset($_POST['paralelo']) ? sanitize_text_field($_POST['paralelo']) : '';

  if (!$institucion_id || !$segmento || !$curso) {
    wp_send_json_error(['message' => 'Parámetros incompletos']);
  }

  $meta_query = [
    ['key' => 'institucion_id', 'value' => $institucion_id],
    ['key' => 'segmento', 'value' => $segmento],
    ['key' => 'curso', 'value' => $curso],
  ];
  if ($paralelo !== '') {
    $meta_query[] = ['key' => 'paralelo', 'value' => $paralelo];
  }

  $q = new WP_Query([
    'post_type' => 'gsi_lista_utiles',
    'posts_per_page' => 1,
    'meta_query' => $meta_query,
  ]);

  if (!$q->have_posts()) {
    wp_send_json_error(['message' => 'No existe lista para ese curso']);
  }

  $list_id = $q->posts[0]->ID;
  $items_raw = get_post_meta($list_id, 'items_raw', true);
  $items = gsi_parse_items_raw($items_raw);

  $total = 0;
  foreach ($items as $it) $total += $it['subtotal'];

  wp_send_json_success([
    'list_id' => $list_id,
    'items' => $items,
    'total' => function_exists('wc_price') ? wc_price($total) : $total,
  ]);
}

// ====== 5) AJAX: agregar kit al carrito ======
add_action('wp_ajax_nopriv_gsi_add_kit', 'gsi_add_kit');
add_action('wp_ajax_gsi_add_kit', 'gsi_add_kit');

function gsi_add_kit()
{
  check_ajax_referer('gsi_institucion_nonce', 'nonce');
  if (!function_exists('WC') || !WC()->cart) {
    wp_send_json_error(['message' => 'WooCommerce no disponible']);
  }

  $list_id = isset($_POST['list_id']) ? (int) $_POST['list_id'] : 0;
  if (!$list_id) wp_send_json_error(['message' => 'Lista inválida']);

  $items_raw = get_post_meta($list_id, 'items_raw', true);
  $items = gsi_parse_items_raw($items_raw);
  if (!$items) wp_send_json_error(['message' => 'La lista está vacía o no coincide con SKUs']);

  foreach ($items as $it) {
    WC()->cart->add_to_cart($it['product_id'], $it['qty']);
  }

  wp_send_json_success([
    'redirect' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
  ]);
}
////////////////////////////////////////
////////////FUNCION PARA CREAR INSTITUCIONES//////
add_action('init', function () {
  register_post_type('gsi_institucion', [
    'label' => 'Instituciones',
    'public' => true,
    'has_archive' => false,
    'rewrite' => ['slug' => 'institucion'],
    'supports' => ['title', 'thumbnail'],
    'show_in_rest' => true,
    'menu_icon' => 'dashicons-welcome-learn-more',
  ]);
});
/////Guardar datos extra de cada institución (tipo, ciudad, logo, etc.)
// ===== Metabox para Instituciones (admin) =====
add_action('add_meta_boxes', function () {
  add_meta_box(
    'gsi_inst_meta',
    'Datos de la institución',
    'gsi_inst_meta_box',
    'gsi_institucion',
    'normal',
    'high'
  );
});

if (!function_exists('gsi_inst_meta_box')) {
  function gsi_inst_meta_box($post)
  {
    wp_nonce_field('gsi_inst_save', 'gsi_inst_nonce');

    // Campos (keys deben coincidir con los que lees en page-institucion.php)
    $tipo       = get_post_meta($post->ID, 'tipo', true);         // particular | fiscal | fiscomisional
    $ciudad     = get_post_meta($post->ID, 'ciudad', true);
    $sector     = get_post_meta($post->ID, 'sector', true);

    $direccion  = get_post_meta($post->ID, 'direccion', true);    // <-- lo que te falta
    $telefono   = get_post_meta($post->ID, 'telefono', true);     // <-- lo que te falta
    $anio       = get_post_meta($post->ID, 'anio_lectivo', true); // Año lectivo
    $logo_url   = get_post_meta($post->ID, 'logo_url', true);     // Logo opcional
    $has_escuela = get_post_meta($post->ID, 'has_escuela', true);
    $has_colegio = get_post_meta($post->ID, 'has_colegio', true);


  ?>
    <p>
      <label><strong>Niveles disponibles</strong></label><br>
      <label style="margin-right:16px;">
        <input type="checkbox" name="has_escuela" value="1" <?php checked($has_escuela, '1'); ?>>
        Tiene Escuela / Básica (1ero–7mo)
      </label>

      <label>
        <input type="checkbox" name="has_colegio" value="1" <?php checked($has_colegio, '1'); ?>>
        Tiene Colegio / BGU (8vo–3BGU)
      </label>
    </p>

    <p>
      <label><strong>Tipo</strong></label><br>
      <select name="tipo" style="width:100%">
        <option value="particular" <?php selected($tipo, 'particular'); ?>>Particular</option>
        <option value="fiscal" <?php selected($tipo, 'fiscal'); ?>>Fiscal</option>
        <option value="fiscomisional" <?php selected($tipo, 'fiscomisional'); ?>>Fiscomisional</option>
      </select>
    </p>

    <p style="display:flex; gap:12px;">
      <span style="flex:1">
        <label><strong>Ciudad</strong></label><br>
        <input type="text" name="ciudad" value="<?php echo esc_attr($ciudad); ?>" style="width:100%" placeholder="Quito / Loja...">
      </span>
      <span style="flex:1">
        <label><strong>Sector</strong></label><br>
        <input type="text" name="sector" value="<?php echo esc_attr($sector); ?>" style="width:100%" placeholder="Sector Norte / Centro...">
      </span>
    </p>

    <p>
      <label><strong>Dirección</strong></label><br>
      <input type="text" name="direccion" value="<?php echo esc_attr($direccion); ?>" style="width:100%" placeholder="Av. Ejemplo y Calle Principal, Quito">
    </p>

    <p style="display:flex; gap:12px;">
      <span style="flex:1">
        <label><strong>Teléfono de soporte</strong></label><br>
        <input type="text" name="telefono" value="<?php echo esc_attr($telefono); ?>" style="width:100%" placeholder="099 999 9999">
      </span>
      <span style="flex:1">
        <label><strong>Año lectivo</strong></label><br>
        <input type="text" name="anio_lectivo" value="<?php echo esc_attr($anio); ?>" style="width:100%" placeholder="2025-2026">
      </span>
    </p>

    <p>
      <label><strong>Logo URL (opcional)</strong></label><br>
      <input type="text" name="logo_url" value="<?php echo esc_attr($logo_url); ?>" style="width:100%" placeholder="https://...">
      <small>Si no pones logo, se usa el ícono por defecto.</small>
    </p>
  <?php
  }
}

add_action('save_post_gsi_institucion', function ($post_id) {
  update_post_meta($post_id, 'has_escuela', isset($_POST['has_escuela']) ? '1' : '0');
  update_post_meta($post_id, 'has_colegio', isset($_POST['has_colegio']) ? '1' : '0');

  if (!isset($_POST['gsi_inst_nonce']) || !wp_verify_nonce($_POST['gsi_inst_nonce'], 'gsi_inst_save')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  $fields = ['tipo', 'ciudad', 'sector', 'direccion', 'telefono', 'anio_lectivo', 'logo_url'];

  foreach ($fields as $f) {
    $val = isset($_POST[$f]) ? wp_unslash($_POST[$f]) : '';
    // Teléfono y dirección pueden llevar caracteres especiales, igual los guardamos como texto normal
    update_post_meta($post_id, $f, sanitize_text_field($val));
  }
});
////////////////////////mantener /mi-cuenta/ pero redirigir el logout a /login/
// Redirigir SIEMPRE después del logout de WooCommerce
add_filter('woocommerce_logout_redirect', function ($redirect) {
  return home_url('/login/');
});

add_action('template_redirect', function () {
  if (function_exists('is_account_page') && is_account_page() && !is_user_logged_in()) {
    // Evita interferir con el proceso de logout
    if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('customer-logout')) return;

    wp_safe_redirect(home_url('/login/'));
    exit;
  }
});


// Oculta el título de la página (Astra) SOLO en checkout
add_filter('astra_the_title_enabled', function ($enabled) {
  if (function_exists('is_checkout') && is_checkout() && !is_order_received_page()) return false;
  return $enabled;
});


// Tailwind classes para campos WooCommerce en checkout
add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
  if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return $args;

  $args['class'] = array_merge((array)($args['class'] ?? []), ['mb-4']);
  $args['label_class'] = array_merge((array)($args['label_class'] ?? []), [
    'block',
    'text-[11px]',
    'font-black',
    'uppercase',
    'tracking-widest',
    'text-gray-500',
    'mb-2'
  ]);

  $args['input_class'] = array_merge((array)($args['input_class'] ?? []), [
    'w-full',
    'rounded-2xl',
    'border',
    'border-gray-200',
    'bg-white',
    'px-4',
    'py-3',
    'text-gray-900',
    'placeholder-gray-400',
    'focus:ring-2',
    'focus:ring-blue-500',
    'outline-none'
  ]);

  return $args;
}, 10, 3);

// Botón principal checkout (Realizar pedido)
add_filter('woocommerce_order_button_html', function () {
  if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return '';
  return '<button type="submit" name="woocommerce_checkout_place_order" id="place_order"
    class="w-full py-4 rounded-2xl bg-yellow-500 text-blue-900 font-black hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20">
      Realizar el pedido
    </button>';
});

// Checkout Mega Santiago (Tailwind)

// 1) Quitar el title de Astra SOLO en checkout
add_filter('astra_the_title_enabled', function ($enabled) {
  if (function_exists('is_checkout') && is_checkout() && !is_order_received_page()) return false;
  return $enabled;
});

add_action('wp', function () {
  if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return;

  while (($p = has_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form')) !== false) {
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', $p);
  }
}, 999);

// 3) Tailwind para campos (wrapper + label + input)
add_filter('woocommerce_form_field_args', function ($args, $key, $value) {
  if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return $args;

  // Wrapper <p>
  $args['class'] = array_merge((array)($args['class'] ?? []), [
    'mb-4',
    '!float-none',
    '!w-full',
    'md:col-span-1'
  ]);

  // Label
  $args['label_class'] = array_merge((array)($args['label_class'] ?? []), [
    'block',
    'text-[11px]',
    'font-black',
    'uppercase',
    'tracking-widest',
    'text-gray-500',
    'mb-2'
  ]);

  // Input / select / textarea
  $args['input_class'] = array_merge((array)($args['input_class'] ?? []), [
    'w-full',
    'rounded-2xl',
    'border',
    'border-gray-200',
    'bg-white',
    'px-4',
    'py-3',
    'text-gray-900',
    'placeholder-gray-400',
    'focus:ring-2',
    'focus:ring-blue-500',
    'outline-none'
  ]);

  return $args;
}, 10, 3);

// 4) Botón principal (amarillo marca)
add_filter('woocommerce_order_button_html', function () {
  if (!function_exists('is_checkout') || !is_checkout() || is_order_received_page()) return '';
  return '<button type="submit" name="woocommerce_checkout_place_order" id="place_order"
    class="w-full py-4 rounded-2xl bg-yellow-500 text-blue-900 font-black hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20">
      Realizar el pedido
    </button>';
});


add_filter('body_class', function ($classes) {
  if (is_page('login')) $classes[] = 'gs-login-page';
  return $classes;
});

add_action('login_enqueue_scripts', function () {
  wp_enqueue_style(
    'gs-wp-login-mega',
    get_stylesheet_directory_uri() . '/assets/css/wp-login-mega.css',
    [],
    '1.0.0'
  );

  // Cambia el logo del login (opcional)
  $logo = get_stylesheet_directory_uri() . '/assets/img/logo-santiago.png'; // ajusta ruta
  echo '<style>.login h1 a{background-image:url(' . esc_url($logo) . ') !important;}</style>';
});

add_filter('login_headerurl', function () {
  return home_url('/');
});
add_filter('login_headertext', function () {
  return get_bloginfo('name');
});






/**
 * (Opcional) Si todavía NO existe tu CPT, lo registramos de forma mínima.
 * Si ya los tienes registrados en otro lado, esto NO hará nada.
 */


add_action('init', function () {

  if (!post_type_exists('gsi_institucion')) {
    register_post_type('gsi_institucion', [
      'labels' => [
        'name' => 'Instituciones',
        'singular_name' => 'Institución',
      ],
      'public' => true,
      'show_ui' => true,
      'menu_icon' => 'dashicons-welcome-learn-more',
      'supports' => ['title', 'thumbnail'],
    ]);
  }

  if (!post_type_exists('gsi_lista_utiles')) {
    register_post_type('gsi_lista_utiles', [
      'labels' => [
        'name' => 'Listas de Útiles',
        'singular_name' => 'Lista de Útiles',
      ],
      'public' => false,
      'show_ui' => true,
      'menu_icon' => 'dashicons-clipboard',
      'supports' => ['title', 'author'],
    ]);
  }
}, 5);


/**
 * Helpers (usuario/institución/permisos)
 */
if (!function_exists('gsi_get_user_institucion_id')) {
  function gsi_get_user_institucion_id($user_id = 0) {
    $user_id = $user_id ?: get_current_user_id();
    return (int) get_user_meta($user_id, 'gsi_institucion_id', true);
  }
}

if (!function_exists('gsi_user_can_manage_listas')) {
  function gsi_user_can_manage_listas($user_id = 0) {
    $user_id = $user_id ?: get_current_user_id();
    return user_can($user_id, 'edit_gsi_listas') || user_can($user_id, 'manage_options');
  }
}


/**
 * Dar permisos al rol "mayorista"
 */
add_action('init', function () {
  $role = get_role('mayorista');
  if (!$role) return;

  $caps = [
    'read',
    'edit_gsi_listas',
  ];

  foreach ($caps as $cap) {
    $role->add_cap($cap, true);
  }
}, 20);


/**
 * Campo en perfil de usuario (solo admin) para asignar institución
 */
add_action('show_user_profile', 'gsi_user_institucion_profile_field');
add_action('edit_user_profile', 'gsi_user_institucion_profile_field');
function gsi_user_institucion_profile_field($user) {
  if (!current_user_can('manage_options')) return;

  $assigned = (int) get_user_meta($user->ID, 'gsi_institucion_id', true);

  $insts = get_posts([
    'post_type'      => 'gsi_institucion',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
  ]);
  ?>
  <h2>Institución asignada (Mayorista)</h2>
  <table class="form-table" role="presentation">
    <tr>
      <th><label for="gsi_institucion_id">Institución</label></th>
      <td>
        <select name="gsi_institucion_id" id="gsi_institucion_id">
          <option value="0">— Sin institución (solo precios mayorista) —</option>
          <?php foreach ($insts as $p): ?>
            <option value="<?php echo (int)$p->ID; ?>" <?php selected($assigned, (int)$p->ID); ?>>
              <?php echo esc_html($p->post_title); ?> (ID: <?php echo (int)$p->ID; ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <p class="description">
          Si asignas una institución, este usuario podrá crear/editar listas de esa institución en su panel.
        </p>
      </td>
    </tr>
  </table>
  <?php
}

add_action('personal_options_update', 'gsi_user_institucion_profile_save');
add_action('edit_user_profile_update', 'gsi_user_institucion_profile_save');
function gsi_user_institucion_profile_save($user_id) {
  if (!current_user_can('manage_options')) return;
  $val = isset($_POST['gsi_institucion_id']) ? (int) $_POST['gsi_institucion_id'] : 0;
  update_user_meta($user_id, 'gsi_institucion_id', $val);
}


/**
 * Parse items_raw: "SKU;QTY" por línea
 * Devuelve: ['items'=>[['sku','qty']], ...]
 */
if (!function_exists('gsi_parse_items_raw_simple')) {
  function gsi_parse_items_raw_simple($items_raw) {
    $items_raw = (string)$items_raw;
    $lines = preg_split('/\r\n|\r|\n/', trim($items_raw));
    $lines = array_filter(array_map('trim', $lines));

    $items = [];
    foreach ($lines as $line) {
      $parts = array_map('trim', explode(';', $line));
      $sku = $parts[0] ?? '';
      $qty = isset($parts[1]) ? max(1, (int)$parts[1]) : 1;
      if ($sku === '') continue;
      $items[] = ['sku' => $sku, 'qty' => $qty];
    }
    return ['items' => $items];
  }
}


/**
 * ENDPOINT: /mi-cuenta/mayorista/
 */
add_action('init', function () {
  add_rewrite_endpoint('mayorista', EP_ROOT | EP_PAGES);
}, 20);

add_filter('woocommerce_get_query_vars', function ($vars) {
  $vars['mayorista'] = 'mayorista';
  return $vars;
});

add_filter('woocommerce_account_menu_items', function ($items) {
  if (!is_user_logged_in()) return $items;
  if (!gsi_user_can_manage_listas()) return $items;

  $new = [];
  foreach ($items as $k => $v) {
    $new[$k] = $v;
    if ($k === 'dashboard') {
      $new['mayorista'] = 'Panel mayorista';
    }
  }
  if (!isset($new['mayorista'])) $new['mayorista'] = 'Panel mayorista';
  return $new;
}, 40);


/**
 * Render endpoint
 */
add_action('woocommerce_account_mayorista_endpoint', function () {

  if (!is_user_logged_in()) {
    echo '<div class="bg-white p-6 rounded-2xl border">Debes iniciar sesión.</div>';
    return;
  }
  if (!gsi_user_can_manage_listas()) {
    echo '<div class="bg-white p-6 rounded-2xl border">No tienes permisos para gestionar listas.</div>';
    return;
  }

  $user_id = get_current_user_id();
  $inst_id = gsi_get_user_institucion_id($user_id);

  // Cursos fijos (SIN paralelos)
  $cursos_escuela = ["1ero de Básica","2do de Básica","3ero de Básica","4to de Básica","5to de Básica","6to de Básica","7mo de Básica"];
  $cursos_colegio = ["8vo EGB","9no EGB","10mo EGB","1BGU","2BGU","3BGU"];

  $view_segmento = isset($_GET['segmento']) ? sanitize_text_field(wp_unslash($_GET['segmento'])) : '';
  $view_curso    = isset($_GET['curso']) ? sanitize_text_field(wp_unslash($_GET['curso'])) : '';
  $is_edit       = in_array($view_segmento, ['escuela','colegio'], true) && $view_curso !== '';

  echo '<section class="w-full gsi-mayorista-panel">';
  echo '<div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 md:p-8">';
  echo '<h2 class="text-2xl md:text-3xl font-extrabold text-blue-900">Panel mayorista</h2>';
  echo '<p class="text-sm text-blue-900/70 mt-1">Crea y edita listas por curso (1ero–7mo y 8vo–3BGU).</p>';

  // Si no tiene institución asignada
  if (!$inst_id) {
    echo '<div class="mt-6 bg-yellow-50 border border-yellow-200 text-yellow-900 p-4 rounded-2xl">';
    echo '<p class="font-bold">Este usuario no tiene institución asignada.</p>';
    echo '<p class="text-sm mt-1">Puedes usarlo solo como mayorista (precios reducidos), o pedir al administrador que lo asigne a una institución para poder publicar listas.</p>';
    echo '</div>';
    echo '</div></section>';
    return;
  }

  $inst_title = get_the_title($inst_id) ?: 'Institución';

  echo '<div class="mt-6 bg-blue-50 border border-blue-100 text-blue-900 p-4 rounded-2xl">';
  echo '<p class="font-bold">Institución asignada:</p>';
  echo '<p class="text-sm mt-1">'.esc_html($inst_title).' (ID: '.(int)$inst_id.')</p>';
  echo '</div>';

  // Buscar lista por curso
  $get_list_id = function($segmento, $curso) use ($inst_id) {
    $q = new WP_Query([
      'post_type'      => 'gsi_lista_utiles',
      'post_status'    => ['publish','draft','pending'],
      'posts_per_page' => 1,
      'meta_query'     => [
        ['key' => 'institucion_id', 'value' => $inst_id],
        ['key' => 'segmento', 'value' => $segmento],
        ['key' => 'curso', 'value' => $curso],
      ],
      'orderby' => 'date',
      'order'   => 'DESC',
    ]);
    if ($q->have_posts()) return (int) $q->posts[0]->ID;
    return 0;
  };

  // ==========================
  // VISTA: EDITAR LISTA
  // ==========================
  if ($is_edit) {

    $list_id = $get_list_id($view_segmento, $view_curso);

    // Crear si no existe (draft)
    if (!$list_id) {
      $title = $inst_title . ' - ' . ucfirst($view_segmento) . ' - ' . $view_curso;

      $list_id = wp_insert_post([
        'post_type'   => 'gsi_lista_utiles',
        'post_status' => 'draft',
        'post_title'  => $title,
        'post_author' => $user_id,
      ]);

      if ($list_id && !is_wp_error($list_id)) {
        update_post_meta($list_id, 'institucion_id', $inst_id);
        update_post_meta($list_id, 'segmento', $view_segmento);
        update_post_meta($list_id, 'curso', $view_curso);
        update_post_meta($list_id, 'items_raw', "");
      }
    }

    if (!$list_id || is_wp_error($list_id)) {
      echo '<div class="mt-6 bg-red-50 border border-red-100 text-red-700 p-4 rounded-2xl">No se pudo crear/abrir la lista.</div>';
      echo '</div></section>';
      return;
    }

    // Seguridad: validar institución
    $list_inst = (int) get_post_meta($list_id, 'institucion_id', true);
    if ($list_inst !== (int)$inst_id && !current_user_can('manage_options')) {
      echo '<div class="mt-6 bg-red-50 border border-red-100 text-red-700 p-4 rounded-2xl">No puedes editar una lista que no es de tu institución.</div>';
      echo '</div></section>';
      return;
    }

    $items_raw = (string) get_post_meta($list_id, 'items_raw', true);
    $parsed = gsi_parse_items_raw_simple($items_raw);

    // Pre-cargar para JS (traer nombre/precio desde Woo)
    $items_for_js = [];
    foreach (($parsed['items'] ?? []) as $it) {
      $sku = (string)($it['sku'] ?? '');
      $qty = (int)($it['qty'] ?? 1);

      $pid = $sku ? wc_get_product_id_by_sku($sku) : 0;
      $p   = $pid ? wc_get_product($pid) : null;

      $items_for_js[] = [
        'sku'   => $sku,
        'name'  => $p ? $p->get_name() : $sku,
        'qty'   => $qty,
        'price' => $p ? (float)$p->get_price() : 0,
      ];
    }

    $nonce = wp_create_nonce('gsi_mayorista_nonce');

    echo '<div class="mt-8">';
    echo '<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">';
    echo '<div>';
    echo '<h3 class="text-xl font-extrabold text-gray-900">Editar lista</h3>';
    echo '<p class="text-sm text-gray-500">'.esc_html(ucfirst($view_segmento)).' • '.esc_html($view_curso).'</p>';
    echo '</div>';
    echo '<a class="inline-flex items-center px-4 py-2 rounded-2xl border border-blue-900 text-blue-900 font-bold hover:bg-blue-900 hover:text-white transition" href="'.esc_url(wc_get_account_endpoint_url('mayorista')).'">← Volver</a>';
    echo '</div>';
    ?>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
      <div class="lg:col-span-5 bg-gray-50 border border-gray-100 rounded-3xl p-5">
        <p class="font-extrabold text-gray-900 mb-2">Agregar productos</p>

        <div class="relative">
          <input id="gsiSearch" type="text"
            class="w-full px-4 py-3 rounded-2xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-200"
            placeholder="Busca por SKU o nombre (ej: 027872 / cuaderno / lápiz...)">
          <div id="gsiResults" class="hidden absolute z-20 mt-2 w-full bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden"></div>
        </div>

        <p class="text-xs text-gray-500 mt-3">
          Tip: tus listas usan <b>SKU</b>. Si un producto no tiene SKU, no se podrá agregar.
        </p>

        <div class="mt-5 flex gap-2">
          <button id="gsiSave" class="flex-1 px-4 py-3 rounded-2xl bg-blue-900 text-white font-extrabold hover:opacity-95 transition">
            Guardar
          </button>
          <button id="gsiPublish" class="flex-1 px-4 py-3 rounded-2xl bg-yellow-400 text-blue-900 font-extrabold hover:brightness-95 transition">
            Guardar y publicar
          </button>
        </div>

        <div id="gsiMsg" class="mt-4 text-sm"></div>
      </div>

      <div class="lg:col-span-7 bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
          <p class="font-extrabold text-gray-900">Items de la lista</p>
          <p class="text-sm font-bold text-gray-600">Lista ID: <?php echo (int)$list_id; ?></p>
        </div>

        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
              <tr>
                <th class="text-left px-5 py-3 font-semibold">Producto</th>
                <th class="text-left px-5 py-3 font-semibold">SKU</th>
                <th class="text-center px-5 py-3 font-semibold">Cant.</th>
                <th class="text-right px-5 py-3 font-semibold">Subtotal</th>
                <th class="text-right px-5 py-3 font-semibold">Acción</th>
              </tr>
            </thead>
            <tbody id="gsiTbody" class="divide-y divide-gray-100"></tbody>
          </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
          <span class="text-sm text-gray-500">Total estimado</span>
          <span id="gsiTotal" class="text-base font-extrabold text-gray-900">$0.00</span>
        </div>
      </div>
    </div>

    <script>
    (function(){
      const ajaxUrl = "<?php echo esc_js(admin_url('admin-ajax.php')); ?>";
      const nonce   = "<?php echo esc_js($nonce); ?>";
      const listId  = <?php echo (int)$list_id; ?>;

      let items = <?php echo wp_json_encode($items_for_js); ?>;

      const $search  = document.getElementById('gsiSearch');
      const $results = document.getElementById('gsiResults');
      const $tbody   = document.getElementById('gsiTbody');
      const $total   = document.getElementById('gsiTotal');
      const $msg     = document.getElementById('gsiMsg');

      function money(n){
        const v = Number(n || 0);
        return v.toLocaleString('es-EC', { style:'currency', currency:'USD' });
      }

      function escapeHtml(str){
        return String(str).replace(/[&<>"']/g, m => ({
          '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
        }[m]));
      }

      function render(){
        $tbody.innerHTML = '';
        let sum = 0;

        if (!items.length) {
          const tr = document.createElement('tr');
          tr.innerHTML = `<td class="px-5 py-6 text-gray-500" colspan="5">Lista vacía. Busca y agrega productos.</td>`;
          $tbody.appendChild(tr);
          $total.textContent = money(0);
          return;
        }

        items.forEach((it, idx) => {
          const subtotal = (Number(it.price||0) * Number(it.qty||0));
          sum += subtotal;

          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td class="px-5 py-4 text-gray-900 font-semibold">${escapeHtml(it.name || 'Producto')}</td>
            <td class="px-5 py-4 text-gray-600">${escapeHtml(it.sku || '')}</td>
            <td class="px-5 py-4 text-center">
              <input type="number" min="1" value="${Number(it.qty||1)}"
                class="w-20 text-center px-2 py-2 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-200"
                data-qty="${idx}">
            </td>
            <td class="px-5 py-4 text-right text-gray-900 font-bold">${money(subtotal)}</td>
            <td class="px-5 py-4 text-right">
              <button class="px-3 py-2 rounded-xl bg-red-50 text-red-700 font-bold hover:bg-red-100 transition" data-del="${idx}">
                Quitar
              </button>
            </td>
          `;
          $tbody.appendChild(tr);
        });

        $total.textContent = money(sum);

        $tbody.querySelectorAll('[data-qty]').forEach(inp => {
          inp.addEventListener('input', (e) => {
            const i = Number(e.target.getAttribute('data-qty'));
            const v = Math.max(1, Number(e.target.value||1));
            items[i].qty = v;
            render();
          });
        });

        $tbody.querySelectorAll('[data-del]').forEach(btn => {
          btn.addEventListener('click', () => {
            const i = Number(btn.getAttribute('data-del'));
            items.splice(i, 1);
            render();
          });
        });
      }

      async function searchProducts(q){
        const fd = new FormData();
        fd.append('action', 'gsi_mayorista_product_search');
        fd.append('nonce', nonce);
        fd.append('q', q);

        const res = await fetch(ajaxUrl, { method:'POST', body: fd });
        return await res.json();
      }

      let t = null;
      $search.addEventListener('input', () => {
        const q = ($search.value||'').trim();
        if (t) clearTimeout(t);
        if (q.length < 2) { $results.classList.add('hidden'); $results.innerHTML=''; return; }

        t = setTimeout(async () => {
          const data = await searchProducts(q);
          if (!data.success) { $results.classList.add('hidden'); return; }

          const rows = data.data || [];
          if (!rows.length) { $results.classList.add('hidden'); return; }

          $results.innerHTML = rows.map(r => `
            <button type="button"
              class="w-full text-left px-4 py-3 hover:bg-gray-50 transition flex items-center justify-between gap-3"
              data-add="${escapeHtml(r.sku)}"
              data-name="${escapeHtml(r.name)}"
              data-price="${Number(r.price||0)}">
              <span class="font-bold text-gray-900">${escapeHtml(r.name)}</span>
              <span class="text-xs font-semibold text-gray-500">${escapeHtml(r.sku)} • ${r.price_html}</span>
            </button>
          `).join('');
          $results.classList.remove('hidden');

          $results.querySelectorAll('[data-add]').forEach(btn => {
            btn.addEventListener('click', () => {
              const sku = btn.getAttribute('data-add');
              const name = btn.getAttribute('data-name');
              const price = Number(btn.getAttribute('data-price')||0);

              const ex = items.find(x => x.sku === sku);
              if (ex) ex.qty = Number(ex.qty||1) + 1;
              else items.push({ sku, name, qty: 1, price });

              $results.classList.add('hidden');
              $search.value = '';
              render();
            });
          });
        }, 250);
      });

      async function save(publish){
        $msg.className = "mt-4 text-sm text-gray-600";
        $msg.textContent = "Guardando...";

        const fd = new FormData();
        fd.append('action', 'gsi_mayorista_save_list');
        fd.append('nonce', nonce);
        fd.append('list_id', String(listId));
        fd.append('publish', publish ? '1' : '0');
        fd.append('items_json', JSON.stringify(items.map(x => ({ sku: x.sku, qty: Number(x.qty||1) }))));

        const res = await fetch(ajaxUrl, { method:'POST', body: fd });
        const data = await res.json();

        if (!data.success) {
          $msg.className = "mt-4 text-sm text-red-700";
          $msg.textContent = data.data?.message || "No se pudo guardar.";
          return;
        }

        $msg.className = "mt-4 text-sm text-green-700";
        $msg.textContent = publish ? "Guardado y publicado ✅" : "Guardado ✅";
      }

      document.getElementById('gsiSave').addEventListener('click', () => save(false));
      document.getElementById('gsiPublish').addEventListener('click', () => save(true));

      document.addEventListener('click', (e) => {
        if (!$results.contains(e.target) && e.target !== $search) $results.classList.add('hidden');
      });

      render();
    })();
    </script>

    <?php
    echo '</div>'; // mt-8
    echo '</div></section>';
    return;
  }

  // ==========================
  // VISTA: LISTADO DE CURSOS
  // ==========================
  $has_escuela = get_post_meta($inst_id, 'has_escuela', true) === '1';
  $has_colegio = get_post_meta($inst_id, 'has_colegio', true) === '1';
  if (!$has_escuela && !$has_colegio) { $has_escuela = $has_colegio = true; }

  $render_grid = function($segmento, $cursos) use ($get_list_id) {
    echo '<div class="mt-8">';
    echo '<h3 class="text-lg font-extrabold text-gray-900">'.esc_html(ucfirst($segmento)).'</h3>';
    echo '<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">';

    foreach ($cursos as $curso) {
      $list_id = $get_list_id($segmento, $curso);
      $status = $list_id ? get_post_status($list_id) : 'none';

      $items_raw = $list_id ? (string) get_post_meta($list_id, 'items_raw', true) : '';
      $count = 0;
      if ($items_raw) {
        $lines = preg_split('/\r\n|\r|\n/', trim($items_raw));
        $lines = array_filter(array_map('trim', $lines));
        $count = count($lines);
      }

      $badge = ($status === 'publish') ? 'Publicado' : (($status === 'draft') ? 'Borrador' : 'Sin lista');
      $badgeClass = ($status === 'publish')
        ? 'bg-green-100 text-green-700'
        : (($status === 'draft') ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600');

      $edit_url = add_query_arg([
        'segmento' => $segmento,
        'curso'    => $curso,
      ], wc_get_account_endpoint_url('mayorista'));

      echo '<div class="bg-white border border-gray-100 rounded-3xl p-5 shadow-sm hover:shadow-lg transition">';
      echo '<div class="flex items-start justify-between gap-3">';
      echo '<div>';
      echo '<p class="text-sm font-extrabold text-gray-900">'.esc_html($curso).'</p>';
      echo '<p class="text-xs text-gray-500 mt-1">'.($count ? esc_html($count.' items') : '0 items').'</p>';
      echo '</div>';
      echo '<span class="text-[11px] font-extrabold px-3 py-1 rounded-full '.$badgeClass.'">'.$badge.'</span>';
      echo '</div>';

      echo '<a href="'.esc_url($edit_url).'"
        class="mt-4 inline-flex w-full items-center justify-center px-4 py-3 rounded-2xl bg-blue-900 text-white font-extrabold hover:opacity-95 transition">
        Editar lista →
      </a>';
      echo '</div>';
    }

    echo '</div></div>';
  };

  if ($has_escuela) $render_grid('escuela', $cursos_escuela);
  if ($has_colegio) $render_grid('colegio', $cursos_colegio);

  echo '</div></section>';
});


/**
 * AJAX: Buscar productos (SKU/nombre)
 */
add_action('wp_ajax_gsi_mayorista_product_search', function () {
  if (!is_user_logged_in()) wp_send_json_error(['message' => 'No auth']);
  if (!gsi_user_can_manage_listas()) wp_send_json_error(['message' => 'Sin permisos']);

  check_ajax_referer('gsi_mayorista_nonce', 'nonce');

  $q = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
  if (strlen($q) < 2) wp_send_json_success([]);

  $results = [];

  // SKU LIKE
  $sku_ids = get_posts([
    'post_type'      => ['product', 'product_variation'],
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'fields'         => 'ids',
    'meta_query'     => [[
      'key'     => '_sku',
      'value'   => $q,
      'compare' => 'LIKE',
    ]],
  ]);

  // Título
  $name_ids = get_posts([
    'post_type'      => ['product', 'product_variation'],
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'fields'         => 'ids',
    's'              => $q,
  ]);

  $ids = array_values(array_unique(array_merge($sku_ids, $name_ids)));
  $ids = array_slice($ids, 0, 10);

  foreach ($ids as $pid) {
    $p = wc_get_product($pid);
    if (!$p) continue;

    $sku = $p->get_sku();
    if (!$sku) continue;

    $price = (float) $p->get_price();

    $results[] = [
      'id'         => $p->get_id(),
      'name'       => $p->get_name(),
      'sku'        => $sku,
      'price'      => $price,
      'price_html' => wp_kses_post(wc_price($price)),
    ];
  }

  wp_send_json_success($results);
});


/**
 * AJAX: Guardar lista (items_raw)
 */
add_action('wp_ajax_gsi_mayorista_save_list', function () {
  if (!is_user_logged_in()) wp_send_json_error(['message' => 'No auth']);
  if (!gsi_user_can_manage_listas()) wp_send_json_error(['message' => 'Sin permisos']);

  check_ajax_referer('gsi_mayorista_nonce', 'nonce');

  $list_id = isset($_POST['list_id']) ? absint($_POST['list_id']) : 0;
  $publish = !empty($_POST['publish']) && $_POST['publish'] === '1';

  $items_json = isset($_POST['items_json']) ? wp_unslash($_POST['items_json']) : '[]';
  $items = json_decode($items_json, true);
  if (!is_array($items)) $items = [];

  if (!$list_id || get_post_type($list_id) !== 'gsi_lista_utiles') {
    wp_send_json_error(['message' => 'Lista inválida']);
  }

  // Seguridad: solo su institución
  $user_id  = get_current_user_id();
  $my_inst  = gsi_get_user_institucion_id($user_id);
  $list_inst = (int) get_post_meta($list_id, 'institucion_id', true);

  if (!current_user_can('manage_options')) {
    if (!$my_inst || $my_inst !== $list_inst) {
      wp_send_json_error(['message' => 'No puedes editar listas de otra institución']);
    }
  }

  $lines = [];
  foreach ($items as $it) {
    $sku = isset($it['sku']) ? sanitize_text_field($it['sku']) : '';
    $qty = isset($it['qty']) ? max(1, (int) $it['qty']) : 1;

    if (!$sku) continue;

    $pid = wc_get_product_id_by_sku($sku);
    if (!$pid) continue;

    $lines[] = $sku . ';' . $qty;
  }

  update_post_meta($list_id, 'items_raw', implode("\n", $lines));

  if ($publish) {
    wp_update_post([
      'ID' => $list_id,
      'post_status' => 'publish',
    ]);
  }

  wp_send_json_success(['message' => 'OK']);
});


require_once get_stylesheet_directory() . '/inc/gs-home-logic.php';


// Enviar formulario de contacto (front + logged-in)
add_action('admin_post_nopriv_gs_contact_submit', 'gs_contact_submit');
add_action('admin_post_gs_contact_submit', 'gs_contact_submit');

function gs_contact_submit() {
  $ref = wp_get_referer() ?: home_url('/contactenos/');

  // Nonce
  if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'gs_contact_submit')) {
    wp_safe_redirect(add_query_arg('sent', '0', $ref));
    exit;
  }

  // Honeypot (anti-bots)
  if (!empty($_POST['website'])) {
    // Silencioso: simulamos éxito para bots
    wp_safe_redirect(add_query_arg('sent', '1', $ref));
    exit;
  }

  // Anti-bot: si envían demasiado rápido
  $ts = isset($_POST['ts']) ? (int) $_POST['ts'] : 0;
  if ($ts && (time() - $ts) < 3) {
    wp_safe_redirect(add_query_arg('sent', '0', $ref));
    exit;
  }

  $name    = sanitize_text_field($_POST['name'] ?? '');
  $email   = sanitize_email($_POST['email'] ?? '');
  $area    = sanitize_text_field($_POST['area'] ?? 'general');
  $message = sanitize_textarea_field($_POST['message'] ?? '');

  if (!$name || !is_email($email) || !$message) {
    wp_safe_redirect(add_query_arg('sent', '0', $ref));
    exit;
  }

  // Destinos por área
  $recipients = [
    'ventas'     => 'ventas@santiagopapeleria.com',
    'servicios'  => 'servicios@santiagopapeleria.com',
    'facturas'   => 'facturas@santiagopapeleria.com',
    'general'    => get_option('admin_email'),
  ];

  $to = $recipients[$area] ?? $recipients['general'];

  $subject = sprintf('[Contacto Web] %s - %s', ucfirst($area), $name);

  $body  = "Has recibido un nuevo mensaje desde la web:\n\n";
  $body .= "Nombre: {$name}\n";
  $body .= "Correo: {$email}\n";
  $body .= "Área: {$area}\n\n";
  $body .= "Mensaje:\n{$message}\n";

  $headers = [
    'Content-Type: text/plain; charset=UTF-8',
    'Reply-To: ' . $name . ' <' . $email . '>',
  ];

  $ok = wp_mail($to, $subject, $body, $headers);

  wp_safe_redirect(add_query_arg('sent', $ok ? '1' : '0', $ref));
  exit;
}


// 1) Cambiar texto y clase de disponibilidad
add_filter('woocommerce_get_availability_text', function ($text, $product) {
  if (!$product) return $text;

  if (!$product->is_in_stock()) {
    return 'Agotado por el momento';
  }
  return $text;
}, 10, 2);

add_filter('woocommerce_get_availability_class', function ($class, $product) {
  if ($product && !$product->is_in_stock()) {
    return 'gs-outofstock';
  }
  return $class;
}, 10, 2);


// 2) Badge "AGOTADO" en la grilla (tienda/categorías)
add_action('woocommerce_before_shop_loop_item_title', function () {
  global $product;
  if ($product && !$product->is_in_stock()) {
    echo '<span class="gs-badge-outofstock">Agotado</span>';
  }
}, 9);


// 3) Badge también en la ficha (single product), debajo del título
add_action('woocommerce_single_product_summary', function () {
  global $product;
  if ($product && !$product->is_in_stock()) {
    echo '<div class="gs-single-outofstock">Agotado por el momento</div>';
  }
}, 6);


// 4) En la grilla: reemplazar botón por "Sin stock" (deshabilitado) + link opcional a contactenos
add_filter('woocommerce_loop_add_to_cart_link', function ($html, $product) {
  if (!$product) return $html;

  if (!$product->is_in_stock()) {
    $contact_url = home_url('/contactenos/');
    return '<div class="gs-outofstock-actions">
              <span class="button gs-btn-disabled" aria-disabled="true">Sin stock</span>
              <a class="gs-link-notify" href="' . esc_url($contact_url) . '">Avísame cuando haya</a>
            </div>';
  }

  return $html;
}, 10, 2);


// Desactivar comentarios en todo el sitio (frontend + admin)
add_action('admin_init', function () {
  // Quitar soporte de comentarios en tipos de post comunes
  foreach (['post','page'] as $type) {
    if (post_type_supports($type, 'comments')) {
      remove_post_type_support($type, 'comments');
      remove_post_type_support($type, 'trackbacks');
    }
  }
});

// Cerrar comentarios y pings en frontend
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Ocultar comentarios existentes (no mostrar lista)
add_filter('comments_array', '__return_empty_array', 10, 2);

// Quitar menú de Comentarios del admin
add_action('admin_menu', function () {
  remove_menu_page('edit-comments.php');
});

// Quitar widget de “Comentarios recientes”
add_action('widgets_init', function () {
  unregister_widget('WP_Widget_Recent_Comments');
});
