<?php
defined('ABSPATH') || exit;

$items = wc_get_account_menu_items();

$icons = [
  'dashboard'       => '🏠',
  'orders'          => '📦',
  'historial'       => '🕘',
  'edit-address'    => '📍',
  'edit-account'    => '👤',
  'seguridad'       => '🔒',
  'customer-logout' => '🚪',
  'downloads'       => '⬇️',
];
?>

<nav class="woocommerce-MyAccount-navigation">
  <ul class="space-y-2">
    <?php foreach ($items as $endpoint => $label) :
      $classes = wc_get_account_menu_item_classes($endpoint);
      $is_active = (strpos($classes, 'is-active') !== false);
      $icon = $icons[$endpoint] ?? '•';
    ?>
      <li class="<?php echo esc_attr($classes); ?>">
        <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>"
          class="<?php echo $is_active
            ? 'bg-blue-900 text-white'
            : 'bg-blue-50/40 text-blue-900 hover:bg-blue-900 hover:text-white'; ?>
            flex items-center justify-between gap-3 px-4 py-3 rounded-2xl font-extrabold transition">

          <span class="flex items-center gap-3">
            <span><?php echo esc_html($icon); ?></span>
            <span class="text-sm"><?php echo esc_html($label); ?></span>
          </span>

          <span class="<?php echo $is_active ? 'text-white/80' : 'text-blue-900/40'; ?>">→</span>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</nav>
