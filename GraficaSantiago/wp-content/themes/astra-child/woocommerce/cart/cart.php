<?php
defined('ABSPATH') || exit;

do_action('woocommerce_before_cart');

if (function_exists('WC') && WC()->cart && WC()->cart->is_empty()) : ?>
	<main class="w-full">
		<section class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 pt-10 pb-16 rounded-b-[3rem] relative overflow-hidden">
			<div class="absolute -top-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
			<div class="absolute -bottom-24 -left-24 w-[28rem] h-[28rem] bg-white/10 rounded-full blur-2xl"></div>

			<div class="max-w-7xl mx-auto px-6 relative z-10">
				<h1 class="text-3xl md:text-5xl font-black tracking-tight text-white">Carrito</h1>
				<p class="text-blue-200 text-base md:text-lg mt-2">Tu carrito está vacío por ahora.</p>
			</div>
		</section>

		<section class="max-w-7xl mx-auto px-4 sm:px-6 -mt-10 pb-20 relative z-10">
			<?php wc_get_template('cart/cart-empty.php'); ?>
		</section>
	</main>

	<?php do_action('woocommerce_after_cart'); ?>
	<?php return; ?>
<?php endif; ?>

<main class="w-full">
	<!-- HERO -->
	<section class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 pt-10 pb-16 relative overflow-hidden">
		<div class="absolute -top-20 -right-20 w-80 h-80 bg-white/10 rounded-full blur-2xl"></div>
		<div class="absolute -bottom-24 -left-24 w-[28rem] h-[28rem] bg-white/10 rounded-full blur-2xl"></div>

		<div class="max-w-7xl mx-auto px-6 relative z-10">
			<h1 class="text-3xl md:text-5xl font-black tracking-tight text-white">
				<?php echo esc_html__('Carrito', 'woocommerce'); ?>
			</h1>
			<p class="text-blue-200 text-base md:text-lg mt-2">
				<?php echo esc_html__('Revisa tus productos antes de finalizar la compra.', 'woocommerce'); ?>
			</p>

			<!-- mini pasos -->
			<div class="mt-6 flex flex-wrap items-center gap-2 text-[11px] font-black tracking-wide">
				<span class="px-3 py-1 rounded-full bg-white/15 text-white">1. Carrito</span>
				<span class="text-white/60">→</span>
				<span class="px-3 py-1 rounded-full bg-white/10 text-white/80">2. Finalizar compra</span>
				<span class="text-white/60">→</span>
				<span class="px-3 py-1 rounded-full bg-white/10 text-white/80">3. Confirmación</span>
			</div>
		</div>
	</section>

	<section class="max-w-7xl mx-auto px-4 sm:px-6 -mt-10 pb-20 relative z-10">
		<?php do_action('woocommerce_before_cart_table'); ?>

		<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

			<!-- IZQUIERDA: ITEMS (FORM SOLO AQUÍ) -->
			<section class="lg:col-span-2 space-y-6">

				<form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">

					<div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
						<div class="p-5 sm:p-6 border-b border-gray-100">
							<div class="flex items-center justify-between gap-3">
								<p class="text-sm text-gray-600">
									<?php echo esc_html__('Productos agregados', 'woocommerce'); ?>
								</p>
								<span class="text-[10px] font-black px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 uppercase tracking-wider">
									<?php echo esc_html__('Mega Santiago', 'woocommerce'); ?>
								</span>
							</div>
						</div>

						<div class="p-5 sm:p-6 space-y-4">
							<?php do_action('woocommerce_before_cart_contents'); ?>

							<?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
								$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
								$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

								if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0) continue;
								if (!apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) continue;

								$product_permalink = apply_filters(
									'woocommerce_cart_item_permalink',
									$_product->is_visible() ? $_product->get_permalink($cart_item) : '',
									$cart_item,
									$cart_item_key
								);
							?>

								<article class="rounded-3xl border border-gray-100 bg-white p-4 sm:p-5 shadow-sm hover:shadow-md transition">
									<div class="flex gap-4">

										<!-- Thumb -->
										<div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden flex items-center justify-center">
											<?php
											$thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail'), $cart_item, $cart_item_key);
											if ($product_permalink) {
												echo '<a class="block w-full h-full flex items-center justify-center" href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>';
											} else {
												echo $thumbnail;
											}
											?>
										</div>

										<!-- Info -->
										<div class="flex-1 min-w-0">
											<div class="flex items-start justify-between gap-3">
												<div class="min-w-0">
													<h2 class="text-sm sm:text-base font-black text-gray-900 leading-snug">
														<?php
														$name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
														if ($product_permalink) {
															echo '<a class="hover:text-blue-900 hover:underline transition" href="' . esc_url($product_permalink) . '">' . wp_kses_post($name) . '</a>';
														} else {
															echo wp_kses_post($name);
														}
														?>
													</h2>

													<?php $meta = wc_get_formatted_cart_item_data($cart_item); ?>
													<?php if (!empty($meta)) : ?>
														<div class="mt-1 text-xs text-gray-500">
															<?php echo $meta; ?>
														</div>
													<?php endif; ?>

													<div class="mt-2 text-sm font-extrabold text-blue-900">
														<?php echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key); ?>
													</div>
												</div>

												<!-- Remove -->
												<div class="flex-shrink-0">
													<?php
													echo apply_filters(
														'woocommerce_cart_item_remove_link',
														sprintf(
															'<a href="%s" class="inline-flex items-center justify-center w-10 h-10 rounded-2xl border border-gray-100 text-gray-500 hover:bg-yellow-50 hover:text-blue-900 transition" aria-label="%s" data-product_id="%s" data-product_sku="%s">✕</a>',
															esc_url(wc_get_cart_remove_url($cart_item_key)),
															esc_attr__('Eliminar este producto', 'woocommerce'),
															esc_attr($product_id),
															esc_attr($_product->get_sku())
														),
														$cart_item_key
													);
													?>
												</div>
											</div>

											<div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
												<!-- Quantity -->
												<div class="flex items-center gap-3">
													<span class="text-xs text-gray-500 font-semibold"><?php echo esc_html__('Cantidad', 'woocommerce'); ?></span>

													<div class="woocommerce-cart-form__quantity">
														<?php
														if ($_product->is_sold_individually()) {
															$min_quantity = 1;
															$max_quantity = 1;
														} else {
															$min_quantity = 1;
															$max_quantity = $_product->get_max_purchase_quantity();
														}

														echo woocommerce_quantity_input(
															array(
																'input_name'   => "cart[{$cart_item_key}][qty]",
																'input_value'  => $cart_item['quantity'],
																'min_value'    => $min_quantity,
																'max_value'    => $max_quantity,
																'product_name' => $_product->get_name(),
																'input_id'     => "quantity_{$cart_item_key}",
																'input_class'  => array(
																	'input-text',
																	'qty',
																	'text',
																	'w-20',
																	'h-10',
																	'rounded-2xl',
																	'border',
																	'border-gray-200',
																	'text-center',
																	'font-bold',
																),
															),
															$_product,
															false
														);
														?>
													</div>
												</div>

												<!-- Subtotal -->
												<div class="text-sm font-black text-gray-900">
													<span class="text-xs font-semibold text-gray-500 mr-2"><?php echo esc_html__('Subtotal', 'woocommerce'); ?></span>
													<?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); ?>
												</div>
											</div>
										</div>
									</div>
								</article>

							<?php endforeach; ?>

							<?php do_action('woocommerce_cart_contents'); ?>
							<?php do_action('woocommerce_after_cart_contents'); ?>
						</div>

						<!-- ACCIONES -->
						<div class="p-5 sm:p-6 border-t border-gray-100 bg-gray-50">
							<div class="flex flex-col gap-4">

								<?php if (wc_coupons_enabled()) : ?>
									<details class="group bg-white rounded-3xl border border-gray-100 p-4">
										<summary class="cursor-pointer list-none flex items-center justify-between gap-3">
											<span class="font-black text-blue-900 text-sm">¿Tienes un cupón?</span>
											<span class="text-[11px] font-black px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 uppercase tracking-wider">
												Aplicar
											</span>
										</summary>

										<div class="mt-4 flex flex-col sm:flex-row gap-2 sm:items-center">
											<input type="text" name="coupon_code"
												class="w-full sm:w-72 h-11 rounded-2xl border border-gray-200 px-3 outline-none focus:ring-2 focus:ring-yellow-300/60"
												id="coupon_code" value="" placeholder="<?php echo esc_attr__('Código de cupón', 'woocommerce'); ?>" />

											<button type="submit" name="apply_coupon"
												class="h-11 px-5 rounded-2xl font-black bg-white text-blue-900 border-2 border-blue-900 hover:bg-yellow-400 hover:border-yellow-400 transition">
												<?php echo esc_html__('Aplicar', 'woocommerce'); ?>
											</button>
										</div>
									</details>
								<?php endif; ?>

								<div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-end">
									<button
										type="submit"
										name="update_cart"
										value="<?php echo esc_attr__('Actualizar carrito', 'woocommerce'); ?>"
										class="button h-11 px-5 rounded-2xl font-black bg-blue-900 text-white border-2 border-blue-900 hover:bg-blue-700 hover:border-blue-700 transition">
										<?php echo esc_html__('Actualizar carrito', 'woocommerce'); ?>
									</button>
								</div>

							</div>

							<?php do_action('woocommerce_cart_actions'); ?>
							<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
						</div>
					</div>

				</form>
			</section>

			<!-- DERECHA: TOTALES (FUERA DEL FORM) -->
			<aside class="lg:col-span-1">
				<div class="lg:sticky lg:top-24 space-y-4">
					<?php woocommerce_cart_totals(); ?>

					<div class="grid grid-cols-2 gap-3">
						<div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
							<div class="text-2xl">✅</div>
							<div class="font-black text-gray-900 text-sm mt-1">Compra segura</div>
							<div class="text-xs text-gray-500">Protegemos tus datos</div>
						</div>
						<div class="bg-white rounded-2xl border border-gray-100 p-4 text-center shadow-sm">
							<div class="text-2xl">🚚</div>
							<div class="font-black text-gray-900 text-sm mt-1">Envíos</div>
							<div class="text-xs text-gray-500">Rápidos y confiables</div>
						</div>
					</div>
				</div>
			</aside>

		</div>

		<script>
			// Opcional: forzar habilitar el botón si tu tema lo deja en disabled
			document.addEventListener('DOMContentLoaded', () => {
				const form = document.querySelector('form.woocommerce-cart-form');
				if (!form) return;
				const btn = form.querySelector('button[name="update_cart"]');
				if (!btn) return;

				form.addEventListener('input', (e) => {
					if (e.target && e.target.matches('input.qty')) {
						btn.disabled = false;
						btn.classList.remove('disabled');
						btn.removeAttribute('aria-disabled');
					}
				});
			});
		</script>

		<?php do_action('woocommerce_after_cart_table'); ?>
		<?php do_action('woocommerce_after_cart'); ?>

	</section>
</main>