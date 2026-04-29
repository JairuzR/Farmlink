<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_added_to_cart(): void
    {
        $response = $this->post(route('cart.store'), [
            'product' => 'fresh-tomatoes',
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('cart'));
        $this->assertSame(['fresh-tomatoes' => 2], session('cart'));

        $this->get(route('cart'))
            ->assertOk()
            ->assertSee('Fresh Tomatoes')
            ->assertSee('290.00');
    }

    public function test_checkout_moves_cart_items_to_pending_orders(): void
    {
        $this->withSession(['cart' => ['fresh-tomatoes' => 2]])
            ->post(route('checkout'))
            ->assertRedirect(route('orders.pending'));

        $this->assertSame([], session('cart', []));
        $this->assertCount(1, session('orders.pending'));
    }

    public function test_authenticated_farmer_can_publish_a_product(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('products.store'), [
            'name' => 'Sweet Corn',
            'farm' => 'Test Farm',
            'price' => 65,
            'unit' => 'kg',
            'category' => 'Vegetables',
            'stock' => 40,
            'minimum' => 1,
            'pickup' => 'Valencia City',
            'image' => '',
            'description' => 'Fresh sweet corn harvested this morning.',
        ]);

        $response->assertRedirect();
        $this->assertCount(1, session('farmer_products'));
        $this->get(route('marketplace'))->assertSee('Sweet Corn');
    }
}
