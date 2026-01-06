<?php
/* Template Name: Register */

$register_errors = function_exists('gs_handle_register') ? gs_handle_register() : null;

if (is_user_logged_in()) {
    if (function_exists('wc_get_page_permalink')) {
        wp_safe_redirect(wc_get_page_permalink('myaccount'));
    } else {
        wp_safe_redirect(home_url('/mi-cuenta/'));
    }
    exit;
}

get_header();
?>
<main class="w-full"> 
<section class="gs-auth relative min-h-[calc(100vh-120px)] py-12 px-4 md:px-6">
    <!-- FIX DE ASTRA/WOO (NO BORRAR) -->
    <style>
        /* Fuerza padding para que el texto NO se meta debajo del icono */
        .gs-auth .gs-input {
            padding-left: 3.25rem !important;
            /* ~52px */
            padding-right: 1rem !important;
            line-height: 1.2 !important;
        }

        /* Para inputs con botón a la derecha (ojo de contraseña) */
        .gs-auth .gs-input--right {
            padding-right: 3.25rem !important;
            /* ~52px */
        }

        /* Centrado perfecto del icono */
        .gs-auth .gs-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: rgba(107, 114, 128, 1);
        }

        /* Botón del ojo centrado */
        .gs-auth .gs-toggle {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            height: 2.5rem;
            width: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: .9rem;
        }
    </style>

    <!-- Fondo -->
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-blue-900 via-blue-700 to-blue-200"></div>
    <div class="absolute inset-0 -z-10 opacity-20"
        style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.35) 1px, transparent 0); background-size: 24px 24px;"></div>

    <div class="max-w-6xl mx-auto w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch">

            <!-- Izquierda -->
            <div class="rounded-3xl p-8 md:p-10 bg-white/10 border border-white/20 backdrop-blur-xl text-white shadow-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21a7 7 0 10-14 0" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold leading-tight">Crea tu cuenta</h1>
                        <p class="text-white/80 text-sm">Compra más rápido y guarda tus favoritos.</p>
                    </div>
                </div>

                <div class="mt-8 space-y-4 text-sm">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-yellow-300"></span>
                        <p class="text-white/90">Wishlist, carrito y compras en un solo lugar.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-yellow-300"></span>
                        <p class="text-white/90">Historial de pedidos y direcciones guardadas.</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-yellow-300"></span>
                        <p class="text-white/90">Promos y recomendaciones pensadas para ti.</p>
                    </div>
                </div>

                <div class="mt-10">
                    <p class="text-white/70 text-xs">¿Ya tienes cuenta?</p>
                    <a href="<?php echo esc_url(home_url('/login/')); ?>"
                        class="inline-flex items-center mt-2 px-4 py-2 rounded-2xl bg-yellow-400 text-blue-900 font-bold hover:opacity-90 transition">
                        Iniciar sesión
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Derecha -->
            <div class="rounded-3xl bg-white shadow-2xl border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-extrabold text-gray-900">Registro Cliente</h2>
                    <p class="text-sm text-gray-500 mt-1">Completa tus datos para crear tu cuenta.</p>

                    <?php if (is_wp_error($register_errors) && $register_errors->has_errors()) : ?>
                        <div class="mt-5 bg-red-50 border border-red-100 text-red-700 text-sm p-4 rounded-2xl">
                            <p class="font-bold mb-1">No se pudo registrar</p>
                            <?php foreach ($register_errors->get_error_messages() as $message) : ?>
                                <p>• <?php echo esc_html($message); ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="mt-6 space-y-4">
                        <?php wp_nonce_field('gs_register_action', 'gs_register_nonce'); ?>
                        <input type="hidden" name="gs_register" value="1">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nombres -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Nombres</label>
                                <div class="relative">
                                    <span class="gs-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-4 0-6 2-6 4h12c0-2-2-4-6-4z" />
                                        </svg>
                                    </span>
                                    <input type="text" name="first_name" required
                                        placeholder="Nombres"
                                        class="gs-input w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="<?php echo isset($_POST['first_name']) ? esc_attr($_POST['first_name']) : ''; ?>">
                                </div>
                            </div>

                            <!-- Apellidos -->
                            <div>
                                <label class="block text-sm font-bold text-gray-800 mb-1">Apellidos</label>
                                <div class="relative">
                                    <span class="gs-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-4 0-6 2-6 4h12c0-2-2-4-6-4z" />
                                        </svg>
                                    </span>
                                    <input type="text" name="last_name" required
                                        placeholder="Apellidos"
                                        class="gs-input w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                        value="<?php echo isset($_POST['last_name']) ? esc_attr($_POST['last_name']) : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Correo -->
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Correo</label>
                            <div class="relative">
                                <span class="gs-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2z" />
                                    </svg>
                                </span>
                                <input type="email" name="email" required
                                    placeholder="correo@ejemplo.com"
                                    class="gs-input w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"
                                    value="<?php echo isset($_POST['email']) ? esc_attr($_POST['email']) : ''; ?>">
                            </div>
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Contraseña</label>
                            <div class="relative">
                                <span class="gs-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 11v8h14v-8" />
                                    </svg>
                                </span>

                                <input id="gs_reg_pass" type="password" name="password" required
                                    placeholder="Contraseña"
                                    class="gs-input gs-input--right w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200">

                                <button type="button" id="gs_reg_toggle"
                                    class="gs-toggle text-gray-500 hover:text-blue-700"
                                    aria-label="Mostrar contraseña">
                                    <svg id="gs_reg_eye_open" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <svg id="gs_reg_eye_closed" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 hidden"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58A2 2 0 0 0 12 14a2 2 0 0 0 1.42-.58" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 5.09A9.98 9.98 0 0 1 12 5c6.5 0 10 7 10 7a18.46 18.46 0 0 1-4.2 5.16" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.61 6.61A18.6 18.6 0 0 0 2 12s3.5 7 10 7a9.74 9.74 0 0 0 3.39-.6" />
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-2">
                                <div class="h-2 w-full bg-gray-100 rounded-full overflow-hidden">
                                    <div id="gs_pass_bar" class="h-2 w-1/4 bg-red-400 rounded-full transition-all"></div>
                                </div>
                                <p id="gs_pass_text" class="text-xs text-gray-500 mt-1">Usa 8+ caracteres, números y símbolos.</p>
                            </div>
                        </div>

                        <!-- Confirmar -->
                        <div>
                            <label class="block text-sm font-bold text-gray-800 mb-1">Confirmar contraseña</label>
                            <div class="relative">
                                <span class="gs-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <input id="gs_reg_pass2" type="password" name="password2" required
                                    placeholder="Confirmar contraseña"
                                    class="gs-input w-full border border-gray-200 rounded-2xl py-3 focus:outline-none focus:ring-2 focus:ring-blue-200">
                            </div>
                            <p id="gs_match" class="text-xs mt-2 hidden"></p>
                        </div>

                        <button class="w-full bg-blue-700 text-white py-3 rounded-2xl font-extrabold hover:opacity-95 active:scale-[0.99] transition">
                            Registrarse
                        </button>

                        <div class="text-sm text-gray-600 text-center">
                            ¿Ya tienes cuenta?
                            <a href="<?php echo esc_url(home_url('/login/')); ?>" class="text-blue-700 font-bold hover:underline">Iniciar sesión</a>
                        </div>
                    </form>
                </div>

                <div class="px-6 md:px-8 py-5 bg-gray-50 border-t border-gray-100 text-xs text-gray-500 flex items-center justify-between">
                    <span>Registro seguro</span>
                    <span class="font-semibold text-gray-600">Mega Santiago</span>
                </div>
            </div>

        </div>
    </div>
</section>
</main>
<script>
    (function() {
        const pass = document.getElementById('gs_reg_pass');
        const pass2 = document.getElementById('gs_reg_pass2');
        const btn = document.getElementById('gs_reg_toggle');
        const open = document.getElementById('gs_reg_eye_open');
        const closed = document.getElementById('gs_reg_eye_closed');

        if (btn && pass) {
            btn.addEventListener('click', () => {
                const isPassword = pass.type === 'password';
                pass.type = isPassword ? 'text' : 'password';
                if (pass2) pass2.type = isPassword ? 'text' : 'password';
                if (open) open.classList.toggle('hidden', !isPassword);
                if (closed) closed.classList.toggle('hidden', isPassword);
                btn.setAttribute('aria-label', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        }

        const bar = document.getElementById('gs_pass_bar');
        const text = document.getElementById('gs_pass_text');

        function scorePassword(p) {
            let score = 0;
            if (!p) return 0;
            if (p.length >= 8) score++;
            if (/[A-Z]/.test(p)) score++;
            if (/[0-9]/.test(p)) score++;
            if (/[^A-Za-z0-9]/.test(p)) score++;
            if (p.length >= 12) score++;
            return score;
        }

        function updateStrength() {
            const p = pass ? pass.value : '';
            const s = scorePassword(p);

            const widths = ['0%', '20%', '40%', '60%', '80%', '100%'];
            if (bar) bar.style.width = widths[s];

            if (!text) return;
            if (s <= 1) text.textContent = 'Contraseña débil: agrega números y símbolos.';
            else if (s === 2) text.textContent = 'Contraseña aceptable: agrega mayúsculas y símbolos.';
            else if (s === 3) text.textContent = 'Buena: casi listo.';
            else text.textContent = 'Excelente: contraseña fuerte.';
        }

        if (pass) pass.addEventListener('input', updateStrength);

        const match = document.getElementById('gs_match');

        function updateMatch() {
            if (!pass || !pass2 || !match) return;
            if (!pass2.value) {
                match.className = 'text-xs mt-2 hidden';
                match.textContent = '';
                return;
            }
            const ok = pass.value === pass2.value;
            match.className = 'text-xs mt-2 ' + (ok ? 'text-green-600' : 'text-red-600');
            match.textContent = ok ? '✓ Las contraseñas coinciden' : '✕ Las contraseñas no coinciden';
        }
        if (pass2) pass2.addEventListener('input', updateMatch);
        if (pass) pass.addEventListener('input', updateMatch);
    })();
</script>

<?php get_footer(); ?>