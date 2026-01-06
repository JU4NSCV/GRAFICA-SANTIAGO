<?php
/**
 * Plugin Name: GS Rol Mayorista
 * Description: Crea el rol "mayorista" (basado en customer).
 */

if (!defined('ABSPATH')) exit;

register_activation_hook(__FILE__, function () {
  if (get_role('mayorista')) return;

  // Tomamos capacidades del "customer" (WooCommerce)
  $customer = get_role('customer');
  $caps = $customer ? $customer->capabilities : ['read' => true];

  // Capacidad propia (opcional, útil para validaciones)
  $caps['gs_mayorista'] = true;

  add_role('mayorista', 'Mayorista', $caps);
});

// (Opcional) borrar rol al desactivar el plugin
// register_deactivation_hook(__FILE__, function () {
//   remove_role('mayorista');
// });
