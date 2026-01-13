<?php

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Brain\Monkey\Functions;

final class HomeLogicTest extends TestCase
{
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
		$GLOBALS['__wc_get_products_handler'] = null;
	}

	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	public function test_promos_disabled_returns_enabled_false(): void
	{
		Functions\when('get_theme_mod')->alias(function($key, $default = null) {
			if ($key === 'gs_promos_enabled') return false;
			return $default;
		});

		$ctx = gs_home_promos_context();

		$this->assertFalse($ctx['enabled']);
		$this->assertSame([], $ctx['items']);
	}

	public function test_promos_builds_items_and_active_index_is_safe(): void
	{
		Functions\when('get_theme_mod')->alias(function($key, $default = null) {
			$map = [
				'gs_promos_enabled' => true,
				'gs_promos_mode' => 'manual',
				'gs_promos_active' => 5,
				'gs_promos_interval' => 4500,
				'gs_promo_img_1' => 111,
				'gs_promo_img_2' => 0,
				'gs_promo_img_3' => 333,
				'gs_promo_link_1' => 'https://example.com/1',
				'gs_promo_link_3' => '',
			];
			return $map[$key] ?? $default;
		});

		Functions\when('wp_get_attachment_image_url')->alias(function($id, $size) {
			if ($id === 111) return 'https://img/111.jpg';
			if ($id === 333) return 'https://img/333.jpg';
			return '';
		});

		Functions\when('get_post_meta')->justReturn('ALT');

		$ctx = gs_home_promos_context();

		$this->assertTrue($ctx['enabled']);
		$this->assertCount(2, $ctx['items']);
		$this->assertSame(1, $ctx['active_index']);
	}

	public function test_discount_label_returns_percentage(): void
	{
		$p = $this->getMockBuilder(stdClass::class)
			->addMethods(['is_on_sale','get_regular_price','get_sale_price'])
			->getMock();

		$p->method('is_on_sale')->willReturn(true);
		$p->method('get_regular_price')->willReturn('100');
		$p->method('get_sale_price')->willReturn('80');

		$this->assertSame('-20%', gs_home_discount_label($p));
	}

	public function test_cta_simple_in_stock_goes_to_add_to_cart(): void
	{
		$p = $this->getMockBuilder(stdClass::class)
			->addMethods(['is_type','is_purchasable','is_in_stock','add_to_cart_url','add_to_cart_text','get_permalink'])
			->getMock();

		$p->method('is_type')->willReturnCallback(fn($t) => $t === 'simple');
		$p->method('is_purchasable')->willReturn(true);
		$p->method('is_in_stock')->willReturn(true);
		$p->method('add_to_cart_url')->willReturn('/add-to-cart');
		$p->method('add_to_cart_text')->willReturn('Añadir al carrito');
		$p->method('get_permalink')->willReturn('/producto');

		$cta = gs_home_cta($p);

		$this->assertSame('/add-to-cart', $cta['url']);
		$this->assertSame('Añadir al carrito', $cta['text']);
		$this->assertStringContainsString('ajax_add_to_cart', $cta['class']);
	}
}
