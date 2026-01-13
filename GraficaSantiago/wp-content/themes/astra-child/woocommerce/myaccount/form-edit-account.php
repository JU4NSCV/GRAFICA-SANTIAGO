<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_edit_account_form');
?>

<form class="woocommerce-EditAccountForm edit-account space-y-6" action="" method="post" <?php do_action('woocommerce_edit_account_form_tag'); ?>>

  <?php do_action('woocommerce_edit_account_form_start'); ?>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
      <label class="block text-sm font-extrabold text-blue-900 mb-1" for="account_first_name">
        Nombre <span class="text-red-500">*</span>
      </label>
      <input type="text"
             class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300"
             name="account_first_name"
             id="account_first_name"
             autocomplete="given-name"
             value="<?php echo esc_attr($user->first_name); ?>" />
    </p>

    <p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
      <label class="block text-sm font-extrabold text-blue-900 mb-1" for="account_last_name">
        Apellidos <span class="text-red-500">*</span>
      </label>
      <input type="text"
             class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300"
             name="account_last_name"
             id="account_last_name"
             autocomplete="family-name"
             value="<?php echo esc_attr($user->last_name); ?>" />
    </p>
  </div>

  <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label class="block text-sm font-extrabold text-blue-900 mb-1" for="account_display_name">
      Nombre visible <span class="text-red-500">*</span>
    </label>
    <input type="text"
           class="woocommerce-Input woocommerce-Input--text input-text w-full rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300"
           name="account_display_name"
           id="account_display_name"
           value="<?php echo esc_attr($user->display_name); ?>" />
    <span class="block text-xs text-blue-900/60 mt-1">
      Así se mostrará tu nombre en tu cuenta y valoraciones.
    </span>
  </p>

  <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
    <label class="block text-sm font-extrabold text-blue-900 mb-1" for="account_email">
      Dirección de correo electrónico <span class="text-red-500">*</span>
    </label>
    <input type="email"
           class="woocommerce-Input woocommerce-Input--email input-text w-full rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300"
           name="account_email"
           id="account_email"
           autocomplete="email"
           value="<?php echo esc_attr($user->user_email); ?>" />
  </p>

  <?php
  /**
   * Hook para campos extra (si algún plugin añade cosas).
   * OJO: aquí ya NO hay cambio de contraseña.
   */
  do_action('woocommerce_edit_account_form');
  ?>

  <p class="mt-4">
    <?php wp_nonce_field('save_account_details'); ?>
    <button type="submit"
            class="woocommerce-Button button w-full md:w-auto rounded-2xl bg-blue-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-yellow-400 hover:text-blue-900 transition"
            name="save_account_details"
            value="<?php esc_attr_e('Guardar los cambios', 'woocommerce'); ?>">
      <?php esc_html_e('Guardar los cambios', 'woocommerce'); ?>
    </button>
    <input type="hidden" name="action" value="save_account_details" />
  </p>

  <?php do_action('woocommerce_edit_account_form_end'); ?>

</form>

<?php do_action('woocommerce_after_edit_account_form'); ?>
