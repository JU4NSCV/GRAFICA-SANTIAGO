<?php
defined('ABSPATH') || exit;
?>

<div class="woocommerce-billing-fields">

  <?php
  // Título solo aquí (para evitar duplicados)
  ?>
  <div class="mb-5">
    <h2 class="text-xl font-black text-gray-900">Detalles de facturación</h2>
    <p class="text-sm text-gray-500 mt-1">Completa la información para facturación y entrega.</p>
  </div>

  <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

  <div class="woocommerce-billing-fields__field-wrapper grid grid-cols-1 md:grid-cols-2 gap-4">

    <?php
    // Campos que queremos a ancho completo dentro del grid
    $full_width = [
      'billing_company',
      'billing_address_1',
      'billing_address_2',
      'billing_email',
      'billing_phone',
    ];

    $fields = $checkout->get_checkout_fields('billing');

    foreach ($fields as $key => $field) {
      if (in_array($key, $full_width, true)) {
        $field['class'] = array_merge((array) ($field['class'] ?? []), ['md:col-span-2']);
      } else {
        $field['class'] = array_merge((array) ($field['class'] ?? []), ['md:col-span-1']);
      }

      woocommerce_form_field($key, $field, $checkout->get_value($key));
    }
    ?>

  </div>

  <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>

</div>

<?php if (!is_user_logged_in() && $checkout->is_registration_enabled()) : ?>
  <div class="woocommerce-account-fields mt-8">
    <?php if (!$checkout->is_registration_required()) : ?>
      <p class="form-row form-row-wide create-account">
        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox inline-flex items-center gap-2">
          <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount"
                 <?php checked((bool) $checkout->get_value('createaccount'), true); ?> type="checkbox" name="createaccount" value="1" />
          <span class="font-semibold text-gray-700">Crear una cuenta para esta compra</span>
        </label>
      </p>
    <?php endif; ?>

    <?php do_action('woocommerce_before_checkout_registration_form', $checkout); ?>

    <?php if ($checkout->get_checkout_fields('account')) : ?>
      <div class="create-account">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
          <?php foreach ($checkout->get_checkout_fields('account') as $key => $field) : ?>
            <?php
              $field['class'] = array_merge((array) ($field['class'] ?? []), ['md:col-span-1']);
              woocommerce_form_field($key, $field, $checkout->get_value($key));
            ?>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <?php do_action('woocommerce_after_checkout_registration_form', $checkout); ?>
  </div>
<?php endif; ?>
