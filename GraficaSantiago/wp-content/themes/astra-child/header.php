<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
?>
<!DOCTYPE html>
<?php astra_html_before(); ?>
<html <?php language_attributes(); ?>>

<head>
	<?php astra_head_top(); ?>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<?php astra_head_bottom(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<a
		class="skip-link screen-reader-text"
		href="#content">
		<?php echo esc_html(astra_default_strings('string-header-skip-link', false)); ?>
	</a>

	<div
		<?php
		echo wp_kses_post(
			astra_attr(
				'site',
				array(
					'id'    => 'page',
					'class' => 'hfeed site',
				)
			)
		);
		?>>

		<!-- =========================
	     CABECERA PERSONALIZADA
	     ========================= -->
		<header class="w-full bg-gradient-to-r from-blue-800 to-blue-600 text-white" id="site-header">
			<div>
				<div class="p-4 md:p-6">
					<?php
					$show_search = (bool) get_theme_mod('gs_header_search_enabled', true);

					// Columnas según si hay buscador o no (para que se acomode bonito)
					$logo_col    = $show_search ? 'col-span-12 md:col-span-3' : 'col-span-7 md:col-span-4';
					$search_col  = $show_search ? 'col-span-12 md:col-span-6' : 'hidden';
					$actions_col = $show_search ? 'col-span-12 md:col-span-3' : 'col-span-5 md:col-span-8';
					?>

					<?php
					$show_search  = (bool) get_theme_mod('gs_header_search_enabled', true);
					$actions_push = $show_search ? '' : 'md:ml-auto';
					?>

					<!-- =========================
     CABECERA PERSONALIZADA
     ========================= -->
					<header class="w-full bg-gradient-to-r from-blue-800 to-blue-600 text-white" id="site-header">
						<?php
						// Detectar mayorista sin romper si el plugin no está activo
						$is_mayorista = (function_exists('gs_is_mayorista') && gs_is_mayorista());

						// URLs base
						$shop_url      = home_url('/productos/');
						$cart_url      = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/carrito/');
						$myaccount_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/mi-cuenta/');

						// URL login (tu página /login/ o /Login/)
						$login_page = get_page_by_path('login') ?: get_page_by_path('Login');
						$login_url  = $login_page ? get_permalink($login_page) : wp_login_url();

						// Cuenta
						if (is_user_logged_in()) {
							$account_url = ($is_mayorista && function_exists('wc_get_account_endpoint_url'))
								? wc_get_account_endpoint_url('mayorista')
								: $myaccount_url;
						} else {
							$account_url = $login_url;
						}

						// Catálogo
						$catalog_url = ($is_mayorista && function_exists('wc_get_account_endpoint_url'))
							? wc_get_account_endpoint_url('catalogo-mayorista')
							: $shop_url;

						// Mostrar/ocultar buscador desde Personalizar
						$show_search = (bool) get_theme_mod('gs_header_search_enabled', true);
						?>

						<!-- FULL WIDTH real -->
						<div class="w-full px-4 md:px-10 2xl:px-16 py-4 md:py-5">
							<div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-6">

								<!-- LOGO -->
								<div class="shrink-0">
									<a href="<?php echo esc_url(home_url('/')); ?>"
										class="gs-logo group inline-flex items-center rounded-2xl bg-white/95 backdrop-blur
          px-5 py-3 shadow-lg ring-2 ring-yellow-400/70
          hover:ring-yellow-400 hover:shadow-xl transition
          max-w-[260px] md:max-w-[320px]">
										<img
											src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/img/logoGS.png'); ?>"
											alt="Gráfica Santiago"
											class="!h-10 md:!h-12 !w-auto object-contain" />
									</a>

								</div>

								<!-- BUSCADOR (solo desktop dentro de la fila) -->
								<?php if ($show_search): ?>
									<div class="hidden md:block flex-1 min-w-[320px]">
										<?php get_template_part('template-parts/header/search'); ?>
									</div>
								<?php endif; ?>

								<!-- ACCIONES: Wishlist / Cuenta / Carrito -->
								<div class="shrink-0 md:ml-auto">
									<div class="flex items-center justify-end gap-3 md:gap-4">

										<?php
										$wishlist_url   = site_url('/wishlist/');
										$wishlist_count = function_exists('gs_wishlist_get_ids') ? count(gs_wishlist_get_ids()) : 0;
										?>

										<!-- Wishlist -->
										<a href="<?php echo esc_url($wishlist_url); ?>"
											class="relative inline-flex items-center justify-center w-14 h-14 rounded-full
                    bg-white text-blue-900 border-2 border-blue-900 font-semibold
                    hover:bg-yellow-400 hover:border-yellow-400 hover:text-blue-900 hover:shadow-md transition"
											aria-label="Wishlist">

											<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
												width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
												stroke-linecap="round" stroke-linejoin="round">
												<path d="M20.8 4.6c-1.5-1.7-3.9-2-5.7-.8-.6.4-1.1 1-1.4 1.6-.3-.6-.8-1.2-1.4-1.6-1.8-1.2-4.2-.9-5.7.8-1.7 2-1.5 4.9.4 6.7l6.7 6.1 6.7-6.1c1.9-1.8 2.1-4.7.4-6.7z" />
											</svg>

											<span class="js-wishlist-count <?php echo ($wishlist_count > 0 ? '' : 'hidden'); ?>
                         absolute -top-2 -right-2 w-6 h-6 rounded-full text-[11px] font-bold
                         bg-yellow-400 text-blue-900 border-2 border-white flex items-center justify-center">
												<?php echo esc_html($wishlist_count); ?>
											</span>
										</a>

										<!-- Cuenta -->
										<a href="<?php echo esc_url($account_url); ?>"
											class="inline-flex items-center justify-center w-14 h-14 rounded-full
                    bg-white text-blue-900 border-2 border-blue-900 font-semibold
                    hover:bg-yellow-400 hover:border-yellow-400 hover:text-blue-900 hover:shadow-md transition"
											aria-label="Cuenta">

											<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
												stroke-width="2" stroke="currentColor" class="w-8 h-8">
												<path stroke-linecap="round" stroke-linejoin="round"
													d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118
                    a7.5 7.5 0 0 1 15 0A18.001 18.001 0 0 1 12 21.75
                    c-2.676 0-5.216-.584-7.5-1.632Z" />
											</svg>
										</a>

										<!-- Carrito -->
										<a href="<?php echo esc_url($cart_url); ?>"
											class="relative inline-flex items-center justify-center px-4 h-14 rounded-2xl
                    bg-yellow-400 text-blue-900 border-2 border-yellow-400 font-semibold
                    hover:bg-blue-700 hover:text-white hover:border-blue-700 hover:shadow-md transition"
											aria-label="Carrito">

											<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
												stroke-width="1.8" stroke="currentColor" class="w-7 h-7">
												<path stroke-linecap="round" stroke-linejoin="round"
													d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25h9.75m-9.75 0
                    a3 3 0 0 0-3 3h15.75a3 3 0 0 0-3-3M7.5 14.25
                    L5.106 5.272A.75.75 0 0 1 5.83 4.5h14.34a.75.75 0 0 1 .724.953l-1.5 5.25
                    a.75.75 0 0 1-.724.547H7.5zM9 20.25a.75.75 0 1 1-1.5 0
                    .75.75 0 0 1 1.5 0zm9 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0z" />
											</svg>

											<?php if (function_exists('WC') && WC()->cart && WC()->cart->get_cart_contents_count() > 0) : ?>
												<span class="absolute -top-2 -right-2 min-w-[20px] h-5 px-1 rounded-full
                           bg-white text-blue-900 text-[11px] font-bold
                           flex items-center justify-center border border-blue-900">
													<?php echo esc_html(WC()->cart->get_cart_contents_count()); ?>
												</span>
											<?php endif; ?>

										</a>

									</div>
								</div>

							</div>

							<!-- BUSCADOR EN MÓVIL (debajo, solo si está activado) -->
							<?php if ($show_search): ?>
								<div class="mt-4 md:hidden">
									<?php get_template_part('template-parts/header/search'); ?>
								</div>
							<?php endif; ?>

						</div>
					</header>



				</div>
			</div>
		</header>

		<nav class="h-auto border-t border-blue-900 sticky top-0 z-50 shadow-md bg-white">
			<ul class="py-5 flex flex-wrap items-center justify-center gap-8 sm:gap-12 text-sm tracking-[0.2em] uppercase text-blue-900">
				<li class="hover:scale-105 transition">
					<a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-yellow-400 hover:underline">
						Inicio
					</a>
				</li>

				<li class="relative group hover:scale-105 transition">
					<a href="<?php echo esc_url($catalog_url); ?>"
						class="hover:text-yellow-400 hover:underline">
						Catálogo
					</a>
				</li>

				<li class="hover:scale-105 transition">
					<a href="<?php echo esc_url(home_url('/blog')); ?>" class="hover:text-yellow-400 hover:underline">
						Blog
					</a>
				</li>

				<li class="hover:scale-105 transition">
					<a href="<?php echo esc_url(home_url('/instituciones')); ?>" class="hover:text-yellow-400 hover:underline">
						Instituciones
					</a>
				</li>
			</ul>
		</nav>
		<!-- CONTENEDOR PRINCIPAL -->
		<div id="content" class="site-content w-full">
			<div class="ast-container w-full !max-w-none !px-0">