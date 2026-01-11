<?php

/**
 * Plugin Name: GS Catalog Sync
 * Description: Sincroniza productos WooCommerce desde una API externa (ngrok) por SKU, incluye categorías, precio mayorista e imágenes.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

class GS_Catalog_Sync
{
    const OPT = 'gs_cs_settings';
    const GROUP = 'gs-catalog-sync';
    const ACTION_HOOK = 'gs_cs_run_batch';
    const DAILY_HOOK  = 'gs_cs_daily_sync';

    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'admin_menu']);
        add_action('admin_init', [__CLASS__, 'register_settings']);

        // Acción manual (botón)
        add_action('admin_post_gs_cs_sync_now', [__CLASS__, 'handle_sync_now']);

        // Batch runner (Action Scheduler)
        add_action(self::ACTION_HOOK, [__CLASS__, 'run_batch'], 10, 2);

        // Daily scheduler trigger
        add_action(self::DAILY_HOOK, [__CLASS__, 'enqueue_daily_sync']);

        // Precio mayorista por rol
        add_filter('woocommerce_product_get_price', [__CLASS__, 'apply_wholesale_price'], 9999, 2);
        add_filter('woocommerce_product_get_regular_price', [__CLASS__, 'apply_wholesale_price'], 9999, 2);

        // Activación / desactivación
        register_activation_hook(__FILE__, [__CLASS__, 'activate']);
        register_deactivation_hook(__FILE__, [__CLASS__, 'deactivate']);
    }

    public static function defaults()
    {
        return [
            'api_base' => '',                // ej: https://xxxx.ngrok-free.app
            'api_token' => '',
            'batch_size' => 200,
            'include_images' => 1,
            'sync_hour' => 3,                // 03:00
            'last_updated_after' => '1970-01-01T00:00:00Z',
            'last_offset' => 0,
            'is_running' => 0,
            'last_run' => '',
            'last_log' => '',
        ];
    }

    public static function get_settings()
    {
        $s = get_option(self::OPT, []);
        return array_merge(self::defaults(), is_array($s) ? $s : []);
    }

    public static function save_settings($s)
    {
        update_option(self::OPT, $s, false);
    }

    public static function admin_menu()
    {
        add_submenu_page(
            'woocommerce',
            'GS Catalog Sync',
            'GS Catalog Sync',
            'manage_woocommerce',
            'gs-catalog-sync',
            [__CLASS__, 'render_admin']
        );
    }

    public static function register_settings()
    {
        register_setting('gs_cs_settings_group', self::OPT, [
            'type' => 'array',
            'sanitize_callback' => [__CLASS__, 'sanitize_settings'],
            'default' => self::defaults(),
        ]);
    }

    public static function sanitize_settings($input)
    {
        $d = self::defaults();
        $out = array_merge($d, is_array($input) ? $input : []);

        $out['api_base'] = trim((string)$out['api_base']);
        $out['api_token'] = trim((string)$out['api_token']);
        $out['batch_size'] = max(50, min(500, (int)$out['batch_size']));
        $out['include_images'] = !empty($out['include_images']) ? 1 : 0;
        $out['sync_hour'] = max(0, min(23, (int)$out['sync_hour']));

        return $out;
    }

    public static function render_admin()
    {
        if (!current_user_can('manage_woocommerce')) return;

        $s = self::get_settings();

?>
        <div class="wrap">
            <h1>GS Catalog Sync</h1>

            <form method="post" action="options.php">
                <?php settings_fields('gs_cs_settings_group'); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">API Base (ngrok)</th>
                        <td>
                            <input type="text" name="<?php echo esc_attr(self::OPT); ?>[api_base]"
                                value="<?php echo esc_attr($s['api_base']); ?>"
                                class="regular-text" placeholder="https://xxxx.ngrok-free.app" />
                            <p class="description">Debe ser HTTPS. Ej: https://abcd-1234.ngrok-free.app</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">API Token</th>
                        <td>
                            <input type="password" name="<?php echo esc_attr(self::OPT); ?>[api_token]"
                                value="<?php echo esc_attr($s['api_token']); ?>"
                                class="regular-text" />
                            <p class="description">Bearer token que te pasó tu compañero.</p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Batch size</th>
                        <td>
                            <input type="number" name="<?php echo esc_attr(self::OPT); ?>[batch_size]"
                                value="<?php echo esc_attr($s['batch_size']); ?>"
                                min="50" max="500" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Incluir imágenes</th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo esc_attr(self::OPT); ?>[include_images]" value="1"
                                    <?php checked($s['include_images'], 1); ?> />
                                Descargar/actualizar imágenes desde URLs públicas
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Sync diario (hora)</th>
                        <td>
                            <input type="number" name="<?php echo esc_attr(self::OPT); ?>[sync_hour]"
                                value="<?php echo esc_attr($s['sync_hour']); ?>" min="0" max="23" />
                            <p class="description">Hora local WP (0–23). Recomendado: 3</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button('Guardar'); ?>
            </form>

            <hr />

            <h2>Acciones</h2>

            <p><strong>Estado:</strong> <?php echo $s['is_running'] ? '🟡 Ejecutando' : '🟢 Inactivo'; ?></p>
            <p><strong>Última ejecución:</strong> <?php echo esc_html($s['last_run'] ?: '—'); ?></p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('gs_cs_sync_now'); ?>
                <input type="hidden" name="action" value="gs_cs_sync_now" />
                <?php submit_button('Sincronizar ahora', 'primary'); ?>
            </form>

            <?php if (!empty($s['last_log'])): ?>
                <h3>Log</h3>
                <pre style="background:#111;color:#0f0;padding:12px;border-radius:8px;max-height:300px;overflow:auto;"><?php
                                                                                                                        echo esc_html($s['last_log']);
                                                                                                                        ?></pre>
            <?php endif; ?>
        </div>
<?php
    }

    // -------------------------
    // Scheduling
    // -------------------------
    public static function activate()
    {
        // Programar evento diario
        self::schedule_daily();
    }

    public static function deactivate()
    {
        // Desprogramar eventos
        if (function_exists('as_unschedule_all_actions')) {
            as_unschedule_all_actions(self::DAILY_HOOK, [], self::GROUP);
            as_unschedule_all_actions(self::ACTION_HOOK, [], self::GROUP);
        }
    }

    private static function schedule_daily()
    {
        if (!function_exists('as_schedule_recurring_action')) return;

        $s = self::get_settings();
        $hour = (int)$s['sync_hour'];

        $timestamp = self::next_local_timestamp($hour, 0);
        // Cada día
        as_schedule_recurring_action($timestamp, DAY_IN_SECONDS, self::DAILY_HOOK, [], self::GROUP);
    }

    private static function next_local_timestamp($hour, $minute)
    {
        $tz = wp_timezone();
        $now = new DateTime('now', $tz);
        $next = new DateTime('now', $tz);
        $next->setTime($hour, $minute, 0);

        if ($next <= $now) $next->modify('+1 day');
        return $next->getTimestamp();
    }

    public static function enqueue_daily_sync()
    {
        // Encola una sync completa diaria (sin bloquear admin)
        self::enqueue_sync(true);
    }

    // -------------------------
    // Manual Sync
    // -------------------------
    public static function handle_sync_now()
    {
        if (!current_user_can('manage_woocommerce')) wp_die('No autorizado');
        check_admin_referer('gs_cs_sync_now');

        self::enqueue_sync(true);

        wp_safe_redirect(admin_url('admin.php?page=gs-catalog-sync'));
        exit;
    }

    private static function enqueue_sync($reset_cursor = false)
    {
        if (!function_exists('as_enqueue_async_action')) return;

        $s = self::get_settings();

        if ($reset_cursor) {
            $s['last_updated_after'] = '1970-01-01T00:00:00Z';
            $s['last_offset'] = 0;
            $s['last_log'] = '';
        }

        // Marcar running
        $s['is_running'] = 1;
        $s['last_run'] = current_time('Y-m-d H:i:s');
        self::save_settings($s);

        // Encolar primer batch
        as_enqueue_async_action(self::ACTION_HOOK, [
            'updated_after' => $s['last_updated_after'],
            'offset' => (int)$s['last_offset'],
        ], self::GROUP);
    }

    // -------------------------
    // Batch runner
    // -------------------------
    public static function run_batch($updated_after, $offset)
    {
        if (!class_exists('WooCommerce')) return;

        $s = self::get_settings();
        $api_base = rtrim($s['api_base'], '/');
        $token = $s['api_token'];

        if (!$api_base || !$token) {
            self::append_log("❌ Falta API_BASE o TOKEN.");
            $s['is_running'] = 0;
            self::save_settings($s);
            return;
        }

        $limit = (int)$s['batch_size'];
        $include_images = !empty($s['include_images']) ? 1 : 0;

        $url = $api_base . '/api/products?updated_after=' . rawurlencode($updated_after)
            . '&limit=' . $limit
            . '&offset=' . (int)$offset
            . '&include_images=' . $include_images;

        $resp = wp_remote_get($url, [
            'timeout' => 60,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'ngrok-skip-browser-warning' => 'true',
            ],
        ]);

        if (is_wp_error($resp)) {
            self::append_log("❌ Error HTTP: " . $resp->get_error_message());
            $s['is_running'] = 0;
            self::save_settings($s);
            return;
        }

        $code = wp_remote_retrieve_response_code($resp);
        $body = wp_remote_retrieve_body($resp);

        if ($code < 200 || $code >= 300) {
            self::append_log("❌ HTTP $code: " . substr($body, 0, 300));
            $s['is_running'] = 0;
            self::save_settings($s);
            return;
        }

        $json = json_decode($body, true);

        if (!is_array($json)) {
            self::append_log("❌ Respuesta no es JSON. Primeros 200 chars: " . substr($body, 0, 200));
            $s['is_running'] = 0;
            self::save_settings($s);
            return;
        }

        $data = $json['data'] ?? [];
        $meta = $json['meta'] ?? null;

        // Si no hay data, terminamos
        if (empty($data)) {
            self::append_log("✅ Sin datos. Fin.");
            $s['is_running'] = 0;
            self::save_settings($s);
            return;
        }

        // -------------------------
        // ✅ Paginación compatible
        // -------------------------
        // next_offset: de meta si existe, sino offset + cantidad recibida
        $next_offset = $offset + count($data);

        // has_more por defecto: si recibimos exactamente $limit, probablemente hay más
        $has_more = (count($data) === $limit);

        // Si viene meta, úsalo
        if (is_array($meta)) {
            if (isset($meta['next_offset'])) $next_offset = (int)$meta['next_offset'];
            if (isset($meta['has_more'])) $has_more = (bool)$meta['has_more'];
        }

        // Si tu API manda next_offset en raíz (json['next_offset'])
        // - si es null => no hay más
        // - si tiene número => sí hay más, y ese es el next_offset real
        if (array_key_exists('next_offset', $json)) {
            if ($json['next_offset'] === null) {
                $has_more = false;
            } else {
                $has_more = true;
                $next_offset = (int)$json['next_offset'];
            }
        }

        // -------------------------
        // Procesar batch
        // -------------------------
        $created = 0;
        $updated = 0;
        $img_updated = 0;

        foreach ($data as $p) {
            $result = self::upsert_product($p, $include_images);
            if ($result === 'created') $created++;
            if ($result === 'updated') $updated++;
            if ($result === 'img_updated') $img_updated++;
        }

        self::append_log("✅ Batch offset=$offset -> next=$next_offset | +created=$created +updated=$updated +img=$img_updated | has_more=" . ($has_more ? '1' : '0'));

        // Guardar cursor
        $s = self::get_settings();
        $s['last_updated_after'] = $updated_after;
        $s['last_offset'] = (int)$next_offset;
        self::save_settings($s);

        // Encolar siguiente batch si hay más
        if ($has_more && function_exists('as_enqueue_async_action')) {
            as_enqueue_async_action(self::ACTION_HOOK, [
                'updated_after' => $updated_after,
                'offset' => (int)$next_offset,
            ], self::GROUP);
            return;
        }

        // Fin
        $s = self::get_settings();
        $s['is_running'] = 0;
        self::save_settings($s);
        self::append_log("🏁 Sync finalizada.");
    }


    private static function upsert_product($p, $include_images)
    {
        $sku = isset($p['sku']) ? (string)$p['sku'] : '';
        if ($sku === '') return 'skip';

        $name = (string)($p['name'] ?? $sku);
        $stock = (int)($p['stock_qty'] ?? 0);
        $price_unit = $p['price_unit'];
        $price_wholesale = $p['price_wholesale'];

        // 1) Buscar producto por SKU
        $product_id = wc_get_product_id_by_sku($sku);
        $is_new = false;

        if (!$product_id) {
            $wc_product = new WC_Product_Simple();
            $wc_product->set_sku($sku);
            $is_new = true;
        } else {
            $wc_product = wc_get_product($product_id);
            if (!$wc_product) {
                $wc_product = new WC_Product_Simple();
                $wc_product->set_sku($sku);
                $is_new = true;
            }
        }

        // 2) Mapear campos
        $wc_product->set_name($name);
        $wc_product->set_manage_stock(true);
        $wc_product->set_stock_quantity($stock);
        $wc_product->set_stock_status($stock > 0 ? 'instock' : 'outofstock');

        // Precio normal (PVP)
        if ($price_unit !== null && $price_unit !== '' && is_numeric($price_unit)) {
            $wc_product->set_regular_price((string)$price_unit);
            $wc_product->set_price((string)$price_unit);
        }

        // Guardar precio mayorista en meta (no toca precio normal)
        if ($price_wholesale !== null && $price_wholesale !== '' && is_numeric($price_wholesale)) {
            $wc_product->update_meta_data('_gs_wholesale_price', (string)$price_wholesale);
        }

        // 3) Categorías por slug
        $cat_slug = $p['category_slug'] ?? null;
        $sub_slug = $p['subcategory_slug'] ?? null;
        self::assign_categories($wc_product, $cat_slug, $sub_slug);

        $saved_id = $wc_product->save();

        // 4) Imágenes
        $img_changed = false;
        if ($include_images && !empty($p['images']) && is_array($p['images'])) {
            $img_changed = self::sync_images($saved_id, $p['images']);
        }

        if ($is_new) return $img_changed ? 'img_updated' : 'created';
        return $img_changed ? 'img_updated' : 'updated';
    }

    private static function slug_to_name($slug)
    {
        $slug = (string)$slug;
        $slug = str_replace('-', ' ', $slug);
        return ucwords($slug);
    }

    private static function assign_categories($wc_product, $cat_slug, $sub_slug)
    {
        if (!taxonomy_exists('product_cat')) return;

        $term_ids = [];

        $parent_id = 0;
        if ($cat_slug) {
            $parent = term_exists($cat_slug, 'product_cat');
            if (!$parent) {
                $created = wp_insert_term(self::slug_to_name($cat_slug), 'product_cat', ['slug' => $cat_slug]);
                if (!is_wp_error($created)) $parent_id = (int)$created['term_id'];
            } else {
                $parent_id = is_array($parent) ? (int)$parent['term_id'] : (int)$parent;
            }
        }

        if ($sub_slug) {
            $child = term_exists($sub_slug, 'product_cat');
            if (!$child) {
                $created = wp_insert_term(self::slug_to_name($sub_slug), 'product_cat', [
                    'slug' => $sub_slug,
                    'parent' => $parent_id,
                ]);
                if (!is_wp_error($created)) {
                    $term_ids[] = (int)$created['term_id'];
                }
            } else {
                $term_ids[] = is_array($child) ? (int)$child['term_id'] : (int)$child;
            }
        } elseif ($parent_id) {
            $term_ids[] = $parent_id;
        }

        if (!empty($term_ids)) {
            $wc_product->set_category_ids($term_ids);
        }
    }

    private static function sync_images($product_id, $images)
    {
        // Ordenar por position
        usort($images, function ($a, $b) {
            return ((int)($a['position'] ?? 0)) <=> ((int)($b['position'] ?? 0));
        });

        $urls = [];
        foreach ($images as $img) {
            if (!empty($img['url'])) $urls[] = (string)$img['url'];
        }
        if (empty($urls)) return false;

        $hash = md5(implode('|', $urls));
        $prev = get_post_meta($product_id, '_gs_image_urls_hash', true);
        if ($prev === $hash) return false;

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_ids = [];

        foreach ($urls as $u) {
            $tmp = download_url($u, 30);
            if (is_wp_error($tmp)) continue;

            $file = [
                'name'     => basename(parse_url($u, PHP_URL_PATH) ?: 'image.jpg'),
                'type'     => 'image/jpeg',
                'tmp_name' => $tmp,
                'error'    => 0,
                'size'     => filesize($tmp),
            ];

            $id = media_handle_sideload($file, $product_id);
            if (is_wp_error($id)) {
                @unlink($tmp);
                continue;
            }
            $attachment_ids[] = (int)$id;
        }

        if (empty($attachment_ids)) return false;

        // Featured = primera imagen
        set_post_thumbnail($product_id, $attachment_ids[0]);

        // Galería = resto
        $gallery = array_slice($attachment_ids, 1);
        update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery));

        update_post_meta($product_id, '_gs_image_urls_hash', $hash);
        return true;
    }

    public static function apply_wholesale_price($price, $product)
    {
        if (!is_user_logged_in()) return $price;

        $user = wp_get_current_user();
        if (!in_array('mayorista', (array)$user->roles, true)) return $price;

        $wholesale = $product->get_meta('_gs_wholesale_price', true);
        if ($wholesale === '' || $wholesale === null) return $price;

        $wholesale = (float)$wholesale;
        if ($wholesale <= 0) return $price;

        return (string)$wholesale;
    }

    private static function append_log($line)
    {
        $s = self::get_settings();
        $s['last_log'] = trim((string)$s['last_log']) . "\n" . '[' . current_time('Y-m-d H:i:s') . '] ' . $line;
        self::save_settings($s);
    }
}

GS_Catalog_Sync::init();
