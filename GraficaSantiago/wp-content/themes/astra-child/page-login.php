<?php
/* Template Name: Login */

$login_errors = function_exists('gs_handle_login') ? gs_handle_login() : null;

// ==== Recuperar contraseña (form interno) ====
$reset_feedback = null;

if (!empty($_POST['gs_reset_password']) && !empty($_POST['gs_reset_nonce']) && wp_verify_nonce($_POST['gs_reset_nonce'], 'gs_reset_action')) {
    $reset_login = isset($_POST['reset_user_login']) ? sanitize_text_field($_POST['reset_user_login']) : '';

    if ($reset_login === '') {
        $reset_feedback = new WP_Error('empty_reset', 'Por favor ingresa tu usuario o correo.');
    } else {
        // retrieve_password() usa $_POST['user_login'] internamente
        $old_user_login = $_POST['user_login'] ?? null;
        $_POST['user_login'] = $reset_login;

        $result = retrieve_password();

        // Restaurar por seguridad
        if ($old_user_login === null) {
            unset($_POST['user_login']);
        } else {
            $_POST['user_login'] = $old_user_login;
        }

        if (is_wp_error($result)) {
            $reset_feedback = $result;
        } else {
            $reset_feedback = 'Si el usuario/correo existe, te enviamos un enlace para restablecer la contraseña. Revisa tu bandeja de entrada y spam.';
        }
    }
}

if (is_user_logged_in()) {
    $u = wp_get_current_user();
    if (in_array('mayorista', (array)$u->roles, true) && function_exists('wc_get_account_endpoint_url')) {
        wp_safe_redirect(wc_get_account_endpoint_url('mayorista'));
    } else if (function_exists('wc_get_page_permalink')) {
        wp_safe_redirect(wc_get_page_permalink('myaccount'));
    } else {
        wp_safe_redirect(home_url('/mi-cuenta/'));
    }
    exit;
}

get_header();
?>

<section class="gs-auth w-screen relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw] min-h-[calc(100vh-120px)] py-12 px-4 md:px-6">
    <!-- FIX DE ASTRA/WOO (NO BORRAR) -->
    <style>
        .gs-auth .gs-input{
            padding-left: 3.25rem !important; /* espacio para icono */
            padding-right: 1rem !important;
            line-height: 1.2 !important;
        }
        .gs-auth .gs-input--right{
            padding-right: 3.25rem !important; /* espacio para botón ojo */
        }
        .gs-auth .gs-icon{
            position:absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events:none;
            color: rgba(107,114,128,1);
        }
        .gs-auth .gs-toggle{
            position:absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            height: 2.5rem;
            width: 2.5rem;
            display:flex;
            align-items:center;
            justify-content:center;
            border-radius: .9rem;
        }
    </style>

    <!-- Fondo bonito -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-blue-900 via-blue-700 to-blue-200"></div>
    <div class="absolute inset-0 -z-10 opacity-20"
         style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.35) 1px, transparent 0); background-size: 24px 24px;"></div>

    <div class="max-w-6xl mx-auto w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

            <!-- Columna izquierda: branding -->
            <div class="rounded-3xl p-8 md:p-10 bg-white/10 border border-white/20 backdrop-blur-xl text-white shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21a7 7 0 10-14 0"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold leading-tight">Bienvenido a Mega Santiago</h1>
                        <p class="text-white/80 text-sm">Accede a tu cuenta para comprar rápido y guardar favoritos.</p>
                    </div>
                </div>

                <div class="mt-8 space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-yellow-300"></span>
                        <p class="text-white/90">Guarda productos en tu wishlist y retómalos cuando quieras.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-yellow-300"></span>
                        <p class="text-white/90">Revisa tus pedidos y direcciones en un solo lugar.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-yellow-300"></span>
                        <p class="text-white/90">Si eres mayorista, accede a tu área especial automáticamente.</p>
                    </div>
                </div>

                <div class="mt-10">
                    <p class="text-white/70 text-xs">¿No tienes cuenta?</p>
                    <a href="<?php echo esc_url(home_url('/register/')); ?>"
                       class="inline-flex items-center mt-2 px-4 py-2 rounded-2xl bg-yellow-400 text-blue-900 font-bold hover:opacity-90 transition">
                        Crear cuenta
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Columna derecha: card form -->
            <div class="rounded-3xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
                <!-- Tabs -->
                <div class="p-4 md:p-5 bg-gray-50 border-b border-gray-100">
                    <div class="flex gap-2 bg-white rounded-2xl p-1 border border-gray-100 w-fit">
                        <button type="button" data-tab="login"
                            class="gs-tab px-4 py-2 rounded-2xl text-sm font-bold text-blue-900 bg-blue-50">
                            Iniciar sesión
                        </button>
                        <button type="button" data-tab="reset"
                            class="gs-tab px-4 py-2 rounded-2xl text-sm font-bold text-gray-600 hover:text-blue-900">
                            Recuperar contraseña
                        </button>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    <!-- ==== LOGIN TAB ==== -->
                    <div id="tab-login">
                        <h2 class="text-2xl font-extrabold text-gray-900">Accede a tu cuenta</h2>
                        <p class="text-sm text-gray-500 mt-1">Ingresa con tu usuario o correo y tu contraseña.</p>

                        <?php if (is_wp_error($login_errors) && $login_errors->has_errors()) : ?>
                            <div class="mt-5 bg-red-50 border border-red-100 text-red-700 text-sm p-4 rounded-2xl">
                                <p class="font-bold mb-1">No se pudo iniciar sesión</p>
                                <?php foreach ($login_errors->get_error_messages() as $message) : ?>
                                    <p>• <?php echo esc_html($message); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="mt-6 space-y-4">
                            <?php wp_nonce_field('gs_login_action', 'gs_login_nonce'); ?>
                            <input type="hidden" name="gs_login" value="1">
                            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($_GET['redirect_to'] ?? ''); ?>">

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Usuario o correo</label>
                                <div class="relative">
                                    <span class="gs-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-4 0-6 2-6 4h12c0-2-2-4-6-4z"/>
                                        </svg>
                                    </span>

                                    <input type="text" name="user_login"
                                           class="gs-input w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                           placeholder="Usuario o correo"
                                           required
                                           value="<?php echo isset($_POST['user_login']) ? esc_attr($_POST['user_login']) : ''; ?>">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Contraseña</label>

                                <div class="relative">
                                    <span class="gs-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 11v8h14v-8"/>
                                        </svg>
                                    </span>

                                    <input id="gs_password" type="password" name="user_pass"
                                           class="gs-input gs-input--right w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                           placeholder="Contraseña"
                                           required>

                                    <button type="button" id="gs_toggle_password"
                                            class="gs-toggle text-gray-500 hover:text-blue-700"
                                            aria-label="Mostrar contraseña">
                                        <svg id="gs_eye_open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        <svg id="gs_eye_closed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.98 9.98 0 0 1 12 5c6.5 0 10 7 10 7a18.46 18.46 0 0 1-4.2 5.16"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.61 6.61A18.6 18.6 0 0 0 2 12s3.5 7 10 7a9.74 9.74 0 0 0 3.39-.6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-3">
                                <label class="inline-flex items-center text-sm text-gray-700">
                                    <input type="checkbox" name="rememberme" class="mr-2 rounded border-gray-300">
                                    Recordarme
                                </label>

                                <button type="button" data-go-reset
                                    class="text-sm font-semibold text-blue-700 hover:underline">
                                    ¿Olvidaste tu contraseña?
                                </button>
                            </div>

                            <button class="w-full bg-blue-700 text-white py-3 rounded-2xl font-extrabold hover:opacity-95 active:scale-[0.99] transition">
                                Iniciar sesión
                            </button>

                            <p class="text-xs text-gray-500 text-center">
                                Al ingresar aceptas nuestras políticas. Si aún no tienes cuenta,
                                <a href="<?php echo esc_url(home_url('/register/')); ?>" class="text-blue-700 font-bold hover:underline">regístrate aquí</a>.
                            </p>
                        </form>
                    </div>

                    <!-- ==== RESET TAB ==== -->
                    <div id="tab-reset" class="hidden">
                        <h2 class="text-2xl font-extrabold text-gray-900">Recuperar contraseña</h2>
                        <p class="text-sm text-gray-500 mt-1">Te enviaremos un enlace para restablecerla.</p>

                        <div class="mt-5">
                            <?php if ($reset_feedback instanceof WP_Error) : ?>
                                <div class="bg-red-50 border border-red-100 text-red-700 text-sm p-4 rounded-2xl">
                                    <p class="font-bold mb-1">No se pudo enviar el enlace</p>
                                    <?php foreach ($reset_feedback->get_error_messages() as $message) : ?>
                                        <p>• <?php echo esc_html($message); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif (is_string($reset_feedback) && $reset_feedback) : ?>
                                <div class="bg-green-50 border border-green-100 text-green-700 text-sm p-4 rounded-2xl">
                                    <p class="font-bold mb-1">Listo</p>
                                    <p><?php echo esc_html($reset_feedback); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <form method="post" class="mt-6 space-y-4">
                            <?php wp_nonce_field('gs_reset_action', 'gs_reset_nonce'); ?>
                            <input type="hidden" name="gs_reset_password" value="1">

                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Usuario o correo</label>
                                <div class="relative">
                                    <span class="gs-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-4 0-6 2-6 4h12c0-2-2-4-6-4z"/>
                                        </svg>
                                    </span>

                                    <input type="text" name="reset_user_login"
                                           class="gs-input w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                           placeholder="Usuario o correo"
                                           required>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">
                                    Tip: si estás en local y no llegan correos, configura SMTP o prueba en tu hosting.
                                </p>
                            </div>

                            <button class="w-full bg-gray-900 text-white py-3 rounded-2xl font-extrabold hover:opacity-95 active:scale-[0.99] transition">
                                Enviar enlace
                            </button>

                            <div class="text-center">
                                <button type="button" data-go-login class="text-sm font-semibold text-blue-700 hover:underline">
                                    Volver a iniciar sesión
                                </button>
                                <span class="text-gray-300 mx-2">|</span>
                                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="text-sm font-semibold text-gray-600 hover:underline">
                                    Abrir recuperador clásico
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- footer card -->
                <div class="px-6 md:px-8 py-5 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 flex items-center justify-between">
                    <span>Soporte: compras rápidas y seguras</span>
                    <span class="font-semibold text-gray-600">Mega Santiago</span>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
(function () {
    const tabBtns = document.querySelectorAll('.gs-tab');
    const tabLogin = document.getElementById('tab-login');
    const tabReset = document.getElementById('tab-reset');

    function setTab(name) {
        const isLogin = name === 'login';
        tabLogin.classList.toggle('hidden', !isLogin);
        tabReset.classList.toggle('hidden', isLogin);

        tabBtns.forEach(btn => {
            const active = btn.dataset.tab === name;
            btn.classList.toggle('bg-blue-50', active);
            btn.classList.toggle('text-blue-900', active);
            btn.classList.toggle('text-gray-600', !active);
        });
    }

    tabBtns.forEach(btn => btn.addEventListener('click', () => setTab(btn.dataset.tab)));

    const goReset = document.querySelector('[data-go-reset]');
    const goLogin = document.querySelector('[data-go-login]');
    if (goReset) goReset.addEventListener('click', () => setTab('reset'));
    if (goLogin) goLogin.addEventListener('click', () => setTab('login'));

    // Toggle ver contraseña
    const input = document.getElementById('gs_password');
    const btn = document.getElementById('gs_toggle_password');
    const open = document.getElementById('gs_eye_open');
    const closed = document.getElementById('gs_eye_closed');

    if (input && btn) {
        btn.addEventListener('click', () => {
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            if (open) open.classList.toggle('hidden', !isPassword);
            if (closed) closed.classList.toggle('hidden', isPassword);
            btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    // Si viene feedback de reset, abre esa pestaña automáticamente
    <?php if ($reset_feedback) : ?>
        setTab('reset');
    <?php else : ?>
        setTab('login');
    <?php endif; ?>
})();
</script>

<?php get_footer(); ?>
