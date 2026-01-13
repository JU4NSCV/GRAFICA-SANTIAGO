<?php
defined('ABSPATH') || exit;

/**
 * Helpers base
 */
if (!function_exists('gs_wc_active')) {
	function gs_wc_active(): bool {
		return function_exists('wc_get_products');
	}
}

if (!function_exists('gs_shop_link')) {
	function gs_shop_link(): string {
		return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/tienda');
	}
}

if (!function_exists('gs_home_fallback_img')) {
	function gs_home_fallback_img(): string {
		return get_stylesheet_directory_uri() . '/assets/img/servicios1.jpg';
	}
}

/**
 * PROMOS
 */
if (!function_exists('gs_home_promos_context')) {
	function gs_home_promos_context(): array {
		$enabled  = (bool) get_theme_mod('gs_promos_enabled', true);
		$mode     = (string) get_theme_mod('gs_promos_mode', 'manual');
		$active   = max(1, (int) get_theme_mod('gs_promos_active', 1));
		$interval = max(1500, (int) get_theme_mod('gs_promos_interval', 4500));

		$items = [];
		for ($i = 1; $i <= 5; $i++) {
			$id = (int) get_theme_mod("gs_promo_img_$i", 0);
			if (!$id) continue;

			$url = wp_get_attachment_image_url($id, 'full');
			if (!$url) continue;

			$alt  = (string) get_post_meta($id, '_wp_attachment_image_alt', true);
			$link = (string) get_theme_mod("gs_promo_link_$i", '');

			$items[] = [
				'id'   => $id,
				'url'  => $url,
				'alt'  => $alt ?: 'Promoción',
				'link' => $link,
			];
		}

		// Índice activo seguro (corrige el bug del modo manual)
		$active_index = 0;
		if (!empty($items)) {
			$active_index = min(max(0, $active - 1), count($items) - 1);
		}

		return [
			'enabled'      => $enabled,
			'mode'         => $mode,
			'active'       => $active,
			'active_index' => $active_index,
			'interval'     => $interval,
			'items'        => $items,
		];
	}
}

/**
 * DESTACADOS + RECOMENDADOS (featured y fallback a más vendidos)
 */
if (!function_exists('gs_home_featured_recommended')) {
	function gs_home_featured_recommended(int $limit = 3): array {
		if (!gs_wc_active()) {
			return ['items' => [], 'main' => null, 'side' => []];
		}

		$items = wc_get_products([
			'status'   => 'publish',
			'limit'    => $limit,
			'featured' => true,
			'orderby'  => 'date',
			'order'    => 'DESC',
		]);

		$ids = array_map(fn($p) => (int) $p->get_id(), $items);

		if (count($items) < $limit) {
			$need = $limit - count($items);
			$more = wc_get_products([
				'status'   => 'publish',
				'limit'    => $need,
				'orderby'  => 'meta_value_num',
				'meta_key' => 'total_sales',
				'order'    => 'DESC',
				'exclude'  => $ids,
			]);
			$items = array_merge($items, $more);
		}

		$main = $items[0] ?? null;
		$side = array_slice($items, 1, 2);

		return ['items' => $items, 'main' => $main, 'side' => $side];
	}
}

if (!function_exists('gs_home_product_img')) {
	function gs_home_product_img($product, string $size = 'large'): string {
		$fallback = gs_home_fallback_img();
		$img_id = $product ? (int) $product->get_image_id() : 0;
		$url = $img_id ? (string) wp_get_attachment_image_url($img_id, $size) : '';
		return $url ?: $fallback;
	}
}

if (!function_exists('gs_home_product_short')) {
	function gs_home_product_short($product, int $words = 18): string {
		$txt = wp_strip_all_tags((string) $product->get_short_description());
		if (!$txt) $txt = wp_strip_all_tags((string) $product->get_description());
		return $txt ? (string) wp_trim_words($txt, $words) : 'Producto seleccionado para ti.';
	}
}

if (!function_exists('gs_home_discount_label')) {
	function gs_home_discount_label($product): string {
		if (!$product || !$product->is_on_sale()) return '';
		$reg  = (float) $product->get_regular_price();
		$sale = (float) $product->get_sale_price();

		if ($reg > 0 && $sale > 0 && $sale < $reg) {
			$pct = (int) round((($reg - $sale) / $reg) * 100);
			return "-{$pct}%";
		}
		return 'Oferta';
	}
}

/**
 * OFERTAS DESTACADAS
 */
if (!function_exists('gs_home_offers')) {
	function gs_home_offers(int $limit = 4): array {
		if (!gs_wc_active()) return [];

		return wc_get_products([
			'status'  => 'publish',
			'limit'   => $limit,
			'on_sale' => true,
			'orderby' => 'date',
			'order'   => 'DESC',
		]);
	}
}

if (!function_exists('gs_home_offer_prices')) {
	function gs_home_offer_prices($p): array {
		// [price, reg, sale]
		if ($p->is_type('variable')) {
			$price = (float) $p->get_variation_price('min', true);
			$reg   = (float) $p->get_variation_regular_price('min', true);
			$sale  = (float) $p->get_variation_sale_price('min', true);
			return [$price, $reg, $sale];
		}

		$price = (float) $p->get_price();
		$reg   = (float) $p->get_regular_price();
		$sale  = (float) $p->get_sale_price();
		return [$price, $reg, $sale];
	}
}

if (!function_exists('gs_home_offer_badge')) {
	function gs_home_offer_badge($p): string {
		if (!$p->is_on_sale()) return '';
		[$price, $reg] = gs_home_offer_prices($p);

		if ($reg > 0 && $price > 0 && $price < $reg) {
			$pct = (int) round((($reg - $price) / $reg) * 100);
			return "-{$pct}%";
		}
		return 'Oferta';
	}
}

if (!function_exists('gs_home_cta')) {
	function gs_home_cta($p): array {
		$is_simple_cart = $p->is_type('simple') && $p->is_purchasable() && $p->is_in_stock();
		return [
			'url'   => $is_simple_cart ? $p->add_to_cart_url() : $p->get_permalink(),
			'text'  => $is_simple_cart ? $p->add_to_cart_text() : 'Ver opciones',
			'class' => $is_simple_cart ? 'ajax_add_to_cart add_to_cart_button' : '',
		];
	}
}

/**
 * RENDER reutilizable (tu gs_render_products_section) -> lo movemos aquí
 */
if (!function_exists('gs_render_products_section')) {
	function gs_render_products_section(string $title, array $args = [], string $more_url = ''): void {
		if (!gs_wc_active()) {
			echo '<p class="text-sm text-red-500">WooCommerce no está activo.</p>';
			return;
		}

		$defaults = [
			'status'  => 'publish',
			'limit'   => 8,
			'orderby' => 'popularity',
		];

		$products = wc_get_products(array_merge($defaults, $args));
		$fallback_img = gs_home_fallback_img();
		?>
		<section class="mt-16 px-4 md:px-6 max-w-7xl mx-auto">
			<div class="flex items-end justify-between gap-4 mb-6">
				<div>
					<h2 class="text-2xl md:text-3xl font-extrabold text-blue-900"><?php echo esc_html($title); ?></h2>
					<p class="text-sm text-blue-900/70 mt-1">Productos seleccionados para ti.</p>
				</div>

				<?php if (!empty($more_url)) : ?>
					<a href="<?php echo esc_url($more_url); ?>"
						class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-blue-900 text-blue-900 font-semibold
						hover:bg-blue-900 hover:text-white transition">
						Ver más <span aria-hidden="true">→</span>
					</a>
				<?php endif; ?>
			</div>

			<?php if (!empty($products)) : ?>
				<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
					<?php foreach ($products as $product) :
						$product_id = $product->get_id();
						$permalink  = get_permalink($product_id);
						$thumb_url  = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
						$img_url    = $thumb_url ? $thumb_url : $fallback_img;

						$is_simple_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
						$btn_url   = $is_simple_cart ? $product->add_to_cart_url() : $permalink;
						$btn_text  = $is_simple_cart ? $product->add_to_cart_text() : 'Ver opciones';
						$btn_class = $is_simple_cart ? 'ajax_add_to_cart add_to_cart_button' : '';
						?>
						<article class="group border border-gray-200 rounded-2xl p-3 shadow-sm bg-white flex flex-col
							hover:shadow-lg hover:-translate-y-0.5 transition">

							<a href="<?php echo esc_url($permalink); ?>" class="block mb-2">
								<div class="relative w-full aspect-square rounded-2xl overflow-hidden bg-white border border-gray-100">
									<img src="<?php echo esc_url($img_url); ?>"
										alt="<?php echo esc_attr($product->get_name()); ?>"
										class="w-full h-full object-contain p-2 transition duration-300 group-hover:scale-[1.03]"
										loading="lazy">

									<?php if ($product->is_on_sale()) : ?>
										<span class="absolute top-2 left-2 px-2 py-1 rounded-full text-[11px] font-extrabold bg-yellow-400 text-blue-900 shadow">
											Oferta
										</span>
									<?php endif; ?>
								</div>
							</a>

							<h3 class="text-sm font-semibold text-gray-800 mb-1 line-clamp-2">
								<a href="<?php echo esc_url($permalink); ?>">
									<?php echo esc_html($product->get_name()); ?>
								</a>
							</h3>

							<p class="text-sm font-extrabold text-blue-900 mb-3">
								<?php echo wp_kses_post($product->get_price_html()); ?>
							</p>

							<a href="<?php echo esc_url($btn_url); ?>"
								class="mt-auto inline-flex items-center justify-center w-full text-xs font-semibold
								border border-blue-900 rounded-2xl px-3 py-2
								hover:bg-blue-900 hover:text-white transition <?php echo esc_attr($btn_class); ?>"
								data-product_id="<?php echo esc_attr($product_id); ?>"
								data-quantity="1"
								rel="nofollow">
								<?php echo esc_html($btn_text); ?>
							</a>
						</article>
					<?php endforeach; ?>
				</div>

			<?php else : ?>
				<p class="text-sm text-gray-500">No hay productos disponibles por el momento.</p>
			<?php endif; ?>

			<?php if (!empty($more_url)) : ?>
				<div class="mt-6 flex justify-center sm:hidden">
					<a href="<?php echo esc_url($more_url); ?>"
						class="px-5 py-3 bg-yellow-400 text-blue-900 font-extrabold shadow-md rounded-2xl
						hover:shadow-lg hover:bg-blue-700 hover:text-white transition tracking-[0.10em]">
						Ver más
					</a>
				</div>
			<?php endif; ?>
		</section>
		<?php
	}
}
