<?php
// LÓGICA DE REGISTRO
function gs_handle_register() {
    if ( $_SERVER['REQUEST_METHOD'] !== 'POST' || ! isset($_POST['gs_register']) ) {
        return null; // No se envió el formulario
    }

    $errors = new WP_Error();

    // Seguridad
    if ( ! isset($_POST['gs_register_nonce']) || 
         ! wp_verify_nonce( $_POST['gs_register_nonce'], 'gs_register_action' ) ) {
        $errors->add( 'nonce', 'Error de seguridad, por favor recarga la página e inténtalo de nuevo.' );
        return $errors;
    }

    // Obtener y sanitizar campos
    $first_name = sanitize_text_field( $_POST['first_name'] ?? '' );
    $last_name  = sanitize_text_field( $_POST['last_name'] ?? '' );
    $email      = sanitize_email( $_POST['email'] ?? '' );
    $password   = $_POST['password'] ?? '';
    $password2  = $_POST['password2'] ?? '';

    // Validación básica
    if ( empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($password2) ) {
        $errors->add( 'required', 'Todos los campos son obligatorios.' );
    }

    if ( ! is_email( $email ) ) {
        $errors->add( 'email', 'El correo electrónico no es válido.' );
    }

    if ( email_exists( $email ) ) {
        $errors->add( 'email_exists', 'Ya existe una cuenta registrada con este correo.' );
    }

    if ( $password !== $password2 ) {
        $errors->add( 'password_mismatch', 'Las contraseñas no coinciden.' );
    }

    if ( strlen( $password ) < 6 ) {
        $errors->add( 'password_length', 'La contraseña debe tener al menos 6 caracteres.' );
    }

    if ( $errors->has_errors() ) {
        return $errors;
    }

    // Generar nombre de usuario (puedes usar el correo directamente si quieres)
    $username = sanitize_user( current( explode( '@', $email ) ), true );
    if ( username_exists( $username ) ) {
        $username = $email;
    }

    // Crear usuario
    $user_id = wp_create_user( $username, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        return $user_id;
    }

    // Guardar nombres
    wp_update_user( array(
        'ID'         => $user_id,
        'first_name' => $first_name,
        'last_name'  => $last_name,
    ) );

    // Asignar rol de cliente (WooCommerce)
    $user = new WP_User( $user_id );
    $user->set_role( 'customer' );

    // Loguear automáticamente
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id );

    // Redirigir (puedes cambiar la URL a donde quieras)
    wp_safe_redirect( home_url( '/mi-cuenta/' ) );
    exit;
}

// LÓGICA DE LOGIN
function gs_handle_login() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['gs_login'])) {
        return null;
    }

    $errors = new WP_Error();

    if (!isset($_POST['gs_login_nonce']) || !wp_verify_nonce($_POST['gs_login_nonce'], 'gs_login_action')) {
        $errors->add('nonce', 'Error de seguridad, por favor recarga la página e inténtalo de nuevo.');
        return $errors;
    }

    $user_login = sanitize_text_field($_POST['user_login'] ?? '');
    $password   = $_POST['user_pass'] ?? '';
    $remember   = !empty($_POST['rememberme']);

    if (empty($user_login) || empty($password)) {
        $errors->add('required', 'Debes ingresar usuario/correo y contraseña.');
        return $errors;
    }

    $creds = array(
        'user_login'    => $user_login,
        'user_password' => $password,
        'remember'      => $remember,
    );

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $errors->add('login_failed', 'Usuario/correo o contraseña incorrectos.');
        return $errors;
    }

    // ---- REDIRECCIÓN POR ROL ----
    $roles = (array) $user->roles;

    // URLs base
    $myaccount = function_exists('wc_get_page_permalink')
        ? wc_get_page_permalink('myaccount')
        : home_url('/mi-cuenta/');

    $mayorista_url = function_exists('wc_get_account_endpoint_url')
        ? wc_get_account_endpoint_url('mayorista')
        : home_url('/mi-cuenta/mayorista/');

    // Si viene un redirect_to desde otra página, lo aceptamos SOLO si es interno
    $requested = $_POST['redirect_to'] ?? $_GET['redirect_to'] ?? '';
    $requested = $requested ? wp_validate_redirect($requested, '') : '';

    if (in_array('administrator', $roles, true)) {
        wp_safe_redirect(admin_url());
        exit;
    }

    if (in_array('mayorista', $roles, true)) {
        wp_safe_redirect($mayorista_url);
        exit;
    }

    // cliente normal: si hay redirect_to interno, úsalo; si no, mi cuenta
    wp_safe_redirect($requested ?: $myaccount);
    exit;
}
