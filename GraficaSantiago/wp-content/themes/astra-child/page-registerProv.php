<?php
/* Template Name: RegisterProv */
get_header();
?>

    <div class="h-60 bg-slate-50 justify-center flex items-center">
        <div>
            Logo
        </div>
    </div>

    <div class="flex justify-center my-48">
        <form class="bg-white shadow p-6 rounded">
            <h2 class="text-2xl font-bold text-center mb-4">Registro Proveedor</h2>
            <div class="flex gap-4  mb-4">
                <div class="flex-1">
                    <p class="font-bold">Nombre de la empresa</p>
                    <input type="text" placeholder="Nombres" class="w-full border p-2 rounded">
                </div>
                <div class="flex-1">
                    <p class="font-bold">RUC</p>
                    <input type="number" placeholder="RUC" class="w-full border p-2 rounded">
                </div>
            </div>
            <p class="font-bold">Correo</p>
            <input type="email" placeholder="Email" class="w-full border p-2 rounded mb-4">
            <p class="font-bold">Contraseña</p>
            <input type="password" placeholder="Contraseña" class="w-full border p-2 rounded mb-4">
            <input type="password" placeholder="Confirmar contraseña" class="w-full border p-2 rounded mb-4">

            <button class="w-full bg-blue-600 text-white py-2 rounded hover:scale-95 transition">
                Registrarse
            </button>
            <p class="mt-4 text-center">
                ¿Ya tienes cuenta?
                <a href="page-login.php" class="text-blue-600">Iniciar sesion</a>
            </p>
        </form>
    </div>

<?php
get_footer();
?>