<?php
defined('ABSPATH') || exit;

if (!is_user_logged_in()) {
  echo '<div class="p-4 rounded-2xl bg-yellow-50 border border-yellow-200 text-yellow-900">
          Debes iniciar sesión para cambiar tu contraseña.
        </div>';
  return;
}

wc_print_notices();
?>

<div class="space-y-6">
  <div>
    <h2 class="text-2xl font-extrabold text-blue-900">Seguridad</h2>
    <p class="text-sm text-blue-900/60 mt-1">Actualiza tu contraseña para mantener tu cuenta protegida.</p>
  </div>

  <form class="space-y-4" action="" method="post">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-extrabold text-blue-900 mb-1">Contraseña actual</label>
        <input type="password" name="password_current" autocomplete="current-password"
               class="w-full rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300"
               required>
      </div>

      <div>
        <label class="block text-sm font-extrabold text-blue-900 mb-1">Nueva contraseña</label>
        <input type="password" name="password_1" autocomplete="new-password"
               class="w-full rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300"
               required>
      </div>

      <div>
        <label class="block text-sm font-extrabold text-blue-900 mb-1">Confirmar nueva contraseña</label>
        <input type="password" name="password_2" autocomplete="new-password"
               class="w-full rounded-2xl border border-blue-900/10 bg-blue-50/30 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-yellow-300"
               required>
      </div>
    </div>

    <div class="flex items-center justify-between gap-3 pt-2">
      <p class="text-xs text-blue-900/60">
        Tip: usa al menos 10 caracteres, mayúsculas, números y un símbolo.
      </p>

      <?php wp_nonce_field('save_account_details'); ?>
      <button type="submit" name="save_account_details" value="1"
              class="inline-flex items-center justify-center rounded-2xl bg-blue-900 px-6 py-3 text-sm font-extrabold text-white hover:bg-yellow-400 hover:text-blue-900 transition">
        Guardar cambios
      </button>
    </div>
  </form>
</div>
