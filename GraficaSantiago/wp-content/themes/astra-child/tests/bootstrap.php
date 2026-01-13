<?php
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/../');
}

require __DIR__ . '/../vendor/autoload.php';

// Cargar tu lógica (ahora sí, con ABSPATH definido)
require __DIR__ . '/../inc/gs-home-logic.php';

// Stub wc_get_products para que gs_wc_active() sea true en tests
if (!function_exists('wc_get_products')) {
    function wc_get_products(array $args = [])
    {
        $handler = $GLOBALS['__wc_get_products_handler'] ?? null;
        if (is_callable($handler)) return $handler($args);
        return [];
    }
}
