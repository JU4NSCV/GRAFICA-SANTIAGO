<?php

/**
 * Plugin Name: GS Admin Dashboard (Front)
 * Description: Dashboard front-end con CRUD de Productos (WooCommerce) y Usuarios.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) exit;

class GS_Dashboard_Front
{
    private static $enqueue = false;

    public static function init()
    {
        add_shortcode('gs_admin_dashboard', [__CLASS__, 'shortcode']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);

        // AJAX Products
        add_action('wp_ajax_gsd_list_products', [__CLASS__, 'list_products']);
        add_action('wp_ajax_gsd_get_product',  [__CLASS__, 'get_product']);
        add_action('wp_ajax_gsd_save_product', [__CLASS__, 'save_product']);
        add_action('wp_ajax_gsd_delete_product', [__CLASS__, 'delete_product']);

        // AJAX Users
        add_action('wp_ajax_gsd_list_users', [__CLASS__, 'list_users']);
        add_action('wp_ajax_gsd_get_user',  [__CLASS__, 'get_user']);
        add_action('wp_ajax_gsd_save_user', [__CLASS__, 'save_user']);
        add_action('wp_ajax_gsd_delete_user', [__CLASS__, 'delete_user']);
    }

    private static function can_manage_products()
    {
        return current_user_can('manage_woocommerce') || current_user_can('manage_options');
    }
    private static function can_manage_users()
    {
        return current_user_can('list_users') || current_user_can('manage_options');
    }

    public static function enqueue_assets()
    {
        global $post;

        if (! $post instanceof WP_Post) return;
        if (! has_shortcode($post->post_content, 'gs_admin_dashboard')) return;

        wp_enqueue_script('tailwind-cdn', 'https://cdn.tailwindcss.com', [], null, false);

        wp_enqueue_script(
            'gs-dashboard-js',
            plugins_url('../../themes/astra-child/assets/js/gs-dashboard.js', __FILE__),
            [],
            '1.0.1',
            true
        );

        wp_localize_script('gs-dashboard-js', 'GSD', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('gsd_nonce'),
        ]);
    }


    public static function shortcode()
    {
        self::$enqueue = true;

        if (!is_user_logged_in()) {
            return '<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-2xl border">
        <p class="text-sm text-slate-700">Debes iniciar sesión para ver el dashboard.</p>
      </div>';
        }

        if (!self::can_manage_products() && !self::can_manage_users()) {
            return '<div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded-2xl border">
        <p class="text-sm text-slate-700">No tienes permisos para acceder a este dashboard.</p>
      </div>';
        }

        ob_start(); ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="flex items-start justify-between gap-6 flex-col sm:flex-row">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-blue-900">Dashboard Administrador</h1>
                    <p class="mt-1 text-sm text-slate-600">Gestión moderna de productos y usuarios (WooCommerce + WordPress).</p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>"
                        class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                    border border-blue-900 text-blue-900 hover:bg-blue-900 hover:text-white transition
                    focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                        Cerrar sesión
                    </a>
                </div>
            </div>

            <div class="mt-8 bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                <!-- Tabs -->
                <div class="flex items-center gap-2 p-3 border-b border-slate-200 bg-slate-50">
                    <button type="button" id="tab-products"
                        class="gsd-tab inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-semibold
                   bg-yellow-400 text-blue-900 border-2 border-yellow-400 hover:shadow-md transition
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900"
                        aria-controls="panel-products" aria-selected="true">
                        Productos
                    </button>

                    <button type="button" id="tab-users"
                        class="gsd-tab inline-flex items-center gap-2 rounded-2xl px-4 py-2 text-sm font-semibold
                   bg-white text-blue-900 border-2 border-blue-900 hover:bg-yellow-400 hover:border-yellow-400 transition
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900"
                        aria-controls="panel-users" aria-selected="false">
                        Usuarios
                    </button>

                    <div class="ml-auto text-xs text-slate-500 pr-2" aria-live="polite" id="gsd-status"></div>
                </div>

                <!-- Panels -->
                <div class="p-5">
                    <!-- Productos -->
                    <section id="panel-products" class="gsd-panel" role="region" aria-label="Panel productos">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-3 justify-between">
                            <div class="flex-1 max-w-xl">
                                <label class="block text-sm font-semibold text-slate-700" for="gsd-product-search">Buscar producto</label>
                                <input id="gsd-product-search" type="search" placeholder="Nombre, SKU…"
                                    class="mt-1 w-full rounded-2xl border border-slate-300 px-4 py-2 text-sm
                         focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900" />
                            </div>

                            <button type="button" id="gsd-add-product"
                                class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                       bg-blue-900 text-white hover:opacity-90 transition
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                                + Nuevo producto
                            </button>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-slate-600">
                                    <tr class="border-b">
                                        <th class="py-3 pr-4">
                                            <button type="button"
                                                class="gsd-sort inline-flex items-center gap-2 font-semibold hover:text-slate-900"
                                                data-sort="name" aria-sort="none">
                                                Producto <span class="gsd-sort-icon" aria-hidden="true"></span>
                                            </button>
                                        </th>

                                        <th class="py-3 pr-4">SKU</th>

                                        <th class="py-3 pr-4">
                                            <button type="button"
                                                class="gsd-sort inline-flex items-center gap-2 font-semibold hover:text-slate-900"
                                                data-sort="price" aria-sort="none">
                                                Precio <span class="gsd-sort-icon" aria-hidden="true"></span>
                                            </button>
                                        </th>

                                        <th class="py-3 pr-4">
                                            <button type="button"
                                                class="gsd-sort inline-flex items-center gap-2 font-semibold hover:text-slate-900"
                                                data-sort="stock" aria-sort="none">
                                                Stock <span class="gsd-sort-icon" aria-hidden="true"></span>
                                            </button>
                                        </th>

                                        <th class="py-3 pr-4 text-right">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody id="gsd-products-tbody" class="divide-y"></tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <button type="button" id="gsd-prev-products"
                                class="rounded-2xl px-3 py-2 border text-sm hover:bg-slate-50
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                                ← Anterior
                            </button>
                            <div class="text-xs text-slate-500" id="gsd-products-page"></div>
                            <button type="button" id="gsd-next-products"
                                class="rounded-2xl px-3 py-2 border text-sm hover:bg-slate-50
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                                Siguiente →
                            </button>
                        </div>
                    </section>

                    <!-- Usuarios -->
                    <section id="panel-users" class="gsd-panel hidden" role="region" aria-label="Panel usuarios">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-3 justify-between">
                            <div class="flex-1 max-w-xl">
                                <label class="block text-sm font-semibold text-slate-700" for="gsd-user-search">Buscar usuario</label>
                                <input id="gsd-user-search" type="search" placeholder="Nombre, email, usuario…"
                                    class="mt-1 w-full rounded-2xl border border-slate-300 px-4 py-2 text-sm
                         focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-blue-900" />
                            </div>

                            <button type="button" id="gsd-add-user"
                                class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                       bg-blue-900 text-white hover:opacity-90 transition
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                                + Nuevo usuario
                            </button>
                        </div>

                        <div class="mt-5 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-slate-600">
                                    <tr class="border-b">
                                        <th class="py-3 pr-4">Usuario</th>
                                        <th class="py-3 pr-4">Email</th>
                                        <th class="py-3 pr-4">Rol</th>
                                        <th class="py-3 pr-4 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="gsd-users-tbody" class="divide-y"></tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <button type="button" id="gsd-prev-users"
                                class="rounded-2xl px-3 py-2 border text-sm hover:bg-slate-50
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                                ← Anterior
                            </button>
                            <div class="text-xs text-slate-500" id="gsd-users-page"></div>
                            <button type="button" id="gsd-next-users"
                                class="rounded-2xl px-3 py-2 border text-sm hover:bg-slate-50
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                                Siguiente →
                            </button>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Drawer / Modal accesible -->
            <div id="gsd-drawer" class="fixed inset-0 hidden" aria-hidden="true">
                <div class="absolute inset-0 bg-black/40" id="gsd-backdrop"></div>

                <div class="absolute right-0 top-0 h-full w-full max-w-xl bg-white shadow-2xl
                    border-l border-slate-200 p-6 overflow-y-auto"
                    role="dialog" aria-modal="true" aria-labelledby="gsd-drawer-title">
                    <div class="flex items-start justify-between gap-4">
                        <h2 id="gsd-drawer-title" class="text-xl font-bold text-blue-900">Editar</h2>
                        <button type="button" id="gsd-close"
                            class="rounded-2xl px-3 py-2 border hover:bg-slate-50
                     focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900"
                            aria-label="Cerrar">
                            ✕
                        </button>
                    </div>

                    <div id="gsd-drawer-body" class="mt-4"></div>

                    <div class="mt-6 flex items-center gap-3">
                        <button type="button" id="gsd-save"
                            class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                     bg-yellow-400 text-blue-900 border-2 border-yellow-400 hover:shadow-md transition
                     focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                            Guardar
                        </button>

                        <button type="button" id="gsd-cancel"
                            class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                     bg-white text-blue-900 border-2 border-blue-900 hover:bg-slate-50 transition
                     focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-900">
                            Cancelar
                        </button>

                        <div class="ml-auto text-xs text-slate-500" id="gsd-form-status" aria-live="polite"></div>
                    </div>
                </div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }

    private static function verify()
    {
        check_ajax_referer('gsd_nonce', 'nonce');
        if (!is_user_logged_in()) wp_send_json_error(['message' => 'No autenticado'], 401);
    }

    /* -------------------- PRODUCTS -------------------- */
    public static function list_products()
    {
        self::verify();
        if (!self::can_manage_products()) wp_send_json_error(['message' => 'Sin permisos'], 403);
        if (!function_exists('wc_get_products')) wp_send_json_error(['message' => 'WooCommerce no disponible'], 400);

        $page  = max(1, intval($_POST['page'] ?? 1));
        $limit = 10;
        $search = sanitize_text_field($_POST['search'] ?? '');

        $args = [
            'status' => 'publish',
            'limit'  => $limit,
            'page'   => $page,
            'orderby' => 'date',
            'order'  => 'DESC',
            'return' => 'objects',
        ];
        if ($search) $args['search'] = $search;
        $orderby_req = sanitize_text_field($_POST['orderby'] ?? 'date');
        $order_req   = strtoupper(sanitize_text_field($_POST['order'] ?? 'DESC'));
        if (!in_array($order_req, ['ASC', 'DESC'], true)) $order_req = 'DESC';

        switch ($orderby_req) {
            case 'name':
                $args['orderby'] = 'title';
                $args['order']   = $order_req;
                break;

            case 'price':
                $args['orderby']  = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order']    = $order_req;
                break;

            case 'stock':
                $args['orderby']  = 'meta_value_num';
                $args['meta_key'] = '_stock';
                $args['order']    = $order_req;
                break;

            default:
                $args['orderby'] = 'date';
                $args['order']   = 'DESC';
                break;
        }

        $products = wc_get_products($args);

        $data = array_map(function ($p) {
            return [
                'id' => $p->get_id(),
                'name' => $p->get_name(),
                'sku' => $p->get_sku(),
                'price' => $p->get_regular_price(),
                'stock_status' => $p->get_stock_status(),
                'stock_quantity' => $p->get_stock_quantity(),
            ];
        }, $products);

        wp_send_json_success([
            'items' => $data,
            'page' => $page,
            'limit' => $limit,
            'hasMore' => count($data) === $limit
        ]);
    }

    public static function get_product()
    {
        self::verify();
        if (!self::can_manage_products()) wp_send_json_error(['message' => 'Sin permisos'], 403);

        $id = intval($_POST['id'] ?? 0);
        $product = $id ? wc_get_product($id) : null;
        if (!$product) wp_send_json_error(['message' => 'Producto no encontrado'], 404);

        wp_send_json_success([
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'sku' => $product->get_sku(),
            'price' => $product->get_regular_price(),
            'stock_quantity' => $product->get_stock_quantity(),
            'manage_stock' => (bool)$product->get_manage_stock(),
            'stock_status' => $product->get_stock_status(),
        ]);
    }

    public static function save_product()
    {
        self::verify();
        if (!self::can_manage_products()) wp_send_json_error(['message' => 'Sin permisos'], 403);

        $id = intval($_POST['id'] ?? 0);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $sku  = sanitize_text_field($_POST['sku'] ?? '');
        $price = wc_format_decimal($_POST['price'] ?? '0');
        $stock_qty = isset($_POST['stock_quantity']) ? intval($_POST['stock_quantity']) : null;
        $manage_stock = ($_POST['manage_stock'] ?? '0') === '1';

        if (!$name) wp_send_json_error(['message' => 'Nombre es obligatorio'], 422);

        if ($id) {
            $product = wc_get_product($id);
            if (!$product) wp_send_json_error(['message' => 'Producto no encontrado'], 404);
        } else {
            $post_id = wp_insert_post([
                'post_title'  => $name,
                'post_type'   => 'product',
                'post_status' => 'publish',
            ], true);
            if (is_wp_error($post_id)) wp_send_json_error(['message' => $post_id->get_error_message()], 500);
            $product = wc_get_product($post_id);
        }

        $product->set_name($name);
        $product->set_sku($sku);
        $product->set_regular_price($price);

        $product->set_manage_stock($manage_stock);
        if ($manage_stock && $stock_qty !== null) {
            $product->set_stock_quantity($stock_qty);
            $product->set_stock_status($stock_qty > 0 ? 'instock' : 'outofstock');
        }

        $product->save();

        wp_send_json_success(['message' => 'Guardado', 'id' => $product->get_id()]);
    }

    public static function delete_product()
    {
        self::verify();
        if (!self::can_manage_products()) wp_send_json_error(['message' => 'Sin permisos'], 403);

        $id = intval($_POST['id'] ?? 0);
        if (!$id) wp_send_json_error(['message' => 'ID inválido'], 422);

        wp_trash_post($id);
        wp_send_json_success(['message' => 'Eliminado']);
    }

    /* -------------------- USERS -------------------- */
    public static function list_users()
    {
        self::verify();
        if (!self::can_manage_users()) wp_send_json_error(['message' => 'Sin permisos'], 403);

        $page  = max(1, intval($_POST['page'] ?? 1));
        $limit = 10;
        $search = sanitize_text_field($_POST['search'] ?? '');

        $args = [
            'number' => $limit,
            'paged'  => $page,
            'orderby' => 'registered',
            'order'  => 'DESC',
        ];
        if ($search) $args['search'] = '*' . $search . '*';

        $users = get_users($args);

        $data = array_map(function ($u) {
            return [
                'id' => $u->ID,
                'login' => $u->user_login,
                'email' => $u->user_email,
                'name' => $u->display_name,
                'roles' => $u->roles,
            ];
        }, $users);

        wp_send_json_success([
            'items' => $data,
            'page' => $page,
            'limit' => $limit,
            'hasMore' => count($data) === $limit
        ]);
    }

    public static function get_user()
    {
        self::verify();
        if (!self::can_manage_users()) wp_send_json_error(['message' => 'Sin permisos'], 403);

        $id = intval($_POST['id'] ?? 0);
        $u = $id ? get_user_by('id', $id) : null;
        if (!$u) wp_send_json_error(['message' => 'Usuario no encontrado'], 404);

        wp_send_json_success([
            'id' => $u->ID,
            'login' => $u->user_login,
            'email' => $u->user_email,
            'name' => $u->display_name,
            'role' => $u->roles[0] ?? 'subscriber',
        ]);
    }

    public static function save_user()
    {
        self::verify();
        if (!self::can_manage_users()) wp_send_json_error(['message' => 'Sin permisos'], 403);

        $id = intval($_POST['id'] ?? 0);
        $login = sanitize_user($_POST['login'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $name  = sanitize_text_field($_POST['name'] ?? '');
        $role  = sanitize_text_field($_POST['role'] ?? 'subscriber');
        $pass  = $_POST['password'] ?? '';

        if (!$email) wp_send_json_error(['message' => 'Email es obligatorio'], 422);

        if ($id) {
            $userdata = [
                'ID' => $id,
                'user_email' => $email,
                'display_name' => $name ?: $email,
            ];
            if ($pass) $userdata['user_pass'] = $pass;

            $res = wp_update_user($userdata);
            if (is_wp_error($res)) wp_send_json_error(['message' => $res->get_error_message()], 500);

            $u = get_user_by('id', $id);
            if ($u && $role) $u->set_role($role);

            wp_send_json_success(['message' => 'Usuario actualizado', 'id' => $id]);
        } else {
            if (!$login) wp_send_json_error(['message' => 'Usuario (login) es obligatorio'], 422);
            if (!$pass)  wp_send_json_error(['message' => 'Contraseña es obligatoria al crear'], 422);

            $uid = wp_create_user($login, $pass, $email);
            if (is_wp_error($uid)) wp_send_json_error(['message' => $uid->get_error_message()], 500);

            $u = get_user_by('id', $uid);
            if ($u) {
                if ($name) wp_update_user(['ID' => $uid, 'display_name' => $name]);
                $u->set_role($role);
            }

            wp_send_json_success(['message' => 'Usuario creado', 'id' => $uid]);
        }
    }

    public static function delete_user()
    {
        self::verify();
        if (!self::can_manage_users()) wp_send_json_error(['message' => 'Sin permisos'], 403);

        $id = intval($_POST['id'] ?? 0);
        if (!$id) wp_send_json_error(['message' => 'ID inválido'], 422);

        require_once ABSPATH . 'wp-admin/includes/user.php';
        wp_delete_user($id);

        wp_send_json_success(['message' => 'Usuario eliminado']);
    }
}

GS_Dashboard_Front::init();

