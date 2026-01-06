<?php
/**
 * Plugin Name: GS Dobra Simulator
 * Description: Simula consultas tipo Dobra (JSON por SKU) y aplica precio/stock por rol.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) exit;

define('GS_DOBRA_SIM_PATH', plugin_dir_path(__FILE__));
define('GS_DOBRA_SIM_DATA', GS_DOBRA_SIM_PATH . 'mock-data.json');

/** ---------------------------
 *  1) Rol Proveedor
 * --------------------------- */
add_action('init', function () {
    if (get_role('proveedor')) return;

    $customer = get_role('customer');
    $caps = $customer ? $customer->capabilities : ['read' => true];
    $caps['gs_wholesale'] = true;

    add_role('proveedor', 'Proveedor', $caps);
});

/** ---------------------------
 *  2) Cargar data mock (JSON)
 * --------------------------- */
function gs_dobra_sim_load_data(): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    if (!file_exists(GS_DOBRA_SIM_DATA)) return $cache = [];
    $raw = file_get_contents(GS_DOBRA_SIM_DATA);
    $json = json_decode($raw, true);

    return $cache = (is_array($json) ? $json : []);
}

function gs_dobra_sim_find_by_sku(string $sku): ?array {
    $data = gs_dobra_sim_load_data();
    foreach ($data as $row) {
        if (!empty($row['sku']) && (string)$row['sku'] === (string)$sku) {
            $row['timestamp'] = date('c');
            return $row;
        }
    }
    return null;
}

/** ---------------------------
 *  3) Endpoint REST local (simula Dobra)
 *  GET /wp-json/gs-dobra/v1/product/{sku}
 * --------------------------- */
add_action('rest_api_init', function () {
    register_rest_route('gs-dobra/v1', '/product/(?P<sku>[a-zA-Z0-9\-\_]+)', [
        'methods'  => 'GET',
        'callback' => function ($request) {
            $sku = (string)$request['sku'];
            $row = gs_dobra_sim_find_by_sku($sku);
            if (!$row) {
                return new WP_REST_Response(['error' => 'SKU no encontrado', 'sku' => $sku], 404);
            }
            return new WP_REST_Response($row, 200);
        },
        'permission_callback' => '__return_true', // simulación, sin credenciales
    ]);
});

/** ---------------------------
 *  4) “Cliente Dobra” (por ahora llama al endpoint local)
 * --------------------------- */
function gs_dobra_get_by_sku(string $sku): ?array {
    $url = home_url('/wp-json/gs-dobra/v1/product/' . rawurlencode($sku));
    $res = wp_remote_get($url, ['timeout' => 8]);

    if (is_wp_error($res)) return null;
    if ((int)wp_remote_retrieve_response_code($res) !== 200) return null;

    $body = wp_remote_retrieve_body($res);
    $json = json_decode($body, true);

    return is_array($json) ? $json : null;
}

/** ---------------------------
 *  5) Sincronizar un producto (guarda meta y stock Woo)
 * --------------------------- */
function gs_dobra_sync_product(int $product_id): bool {
    $sku = get_post_meta($product_id, '_sku', true);
    if (!$sku) return false;

    $data = gs_dobra_get_by_sku((string)$sku);
    if (!$data) return false;

    // Meta precios
    if (isset($data['precios']['pvp'])) {
        update_post_meta($product_id, '_dobra_pvp', (float)$data['precios']['pvp']);
    }
    if (isset($data['precios']['mayorista'])) {
        update_post_meta($product_id, '_dobra_mayorista', (float)$data['precios']['mayorista']);
    }

    // Stock
    if (isset($data['stock'])) {
        $product = wc_get_product($product_id);
        if ($product) {
            $product->set_manage_stock(true);
            $product->set_stock_quantity((int)$data['stock']);
            $product->save();
        }
        update_post_meta($product_id, '_dobra_stock', (int)$data['stock']);
    }

    update_post_meta($product_id, '_dobra_last_sync', time());
    return true;
}

/** ---------------------------
 *  6) Sync masivo manual (admin)
 *  /wp-admin/?gs_dobra_sync=1
 * --------------------------- */
add_action('admin_init', function () {
    if (!current_user_can('manage_options')) return;
    if (!isset($_GET['gs_dobra_sync'])) return;

    $q = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 200, // ajusta
        'fields'         => 'ids',
    ]);

    $ok = 0; $fail = 0;
    foreach ($q->posts as $pid) {
        gs_dobra_sync_product((int)$pid) ? $ok++ : $fail++;
    }

    wp_die("Sync finalizado. OK: {$ok} | FAIL: {$fail}");
});

/** ---------------------------
 *  7) Precio por rol (lee meta sincronizada)
 * --------------------------- */
function gs_has_role(string $role): bool {
    $user = wp_get_current_user();
    return $user && in_array($role, (array)$user->roles, true);
}

function gs_get_role_price_from_meta($product): ?float {
    $pid = $product->get_id();
    if (is_user_logged_in() && gs_has_role('mayorista')) {
        $m = get_post_meta($pid, '_dobra_mayorista', true);
        if ($m !== '') return (float)$m;
    }
    $p = get_post_meta($pid, '_dobra_pvp', true);
    if ($p !== '') return (float)$p;

    return null;
}

// Simple + variaciones
add_filter('woocommerce_product_get_price', function ($price, $product) {
    $rp = gs_get_role_price_from_meta($product);
    return ($rp !== null) ? $rp : $price;
}, 20, 2);

add_filter('woocommerce_product_get_regular_price', function ($price, $product) {
    $rp = gs_get_role_price_from_meta($product);
    return ($rp !== null) ? $rp : $price;
}, 20, 2);

add_filter('woocommerce_product_variation_get_price', function ($price, $product) {
    $rp = gs_get_role_price_from_meta($product);
    return ($rp !== null) ? $rp : $price;
}, 20, 2);

add_filter('woocommerce_product_variation_get_regular_price', function ($price, $product) {
    $rp = gs_get_role_price_from_meta($product);
    return ($rp !== null) ? $rp : $price;
}, 20, 2);
