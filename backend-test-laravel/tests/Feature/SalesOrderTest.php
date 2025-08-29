<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

class SalesOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_a_sales_order(): void
    {
        User::factory()->create();
        $sale = Sale::factory()->create();
        $customer = Customer::factory()->create();
        $products = Product::factory()->count(2)->create();

        $orderData = [
            'sales_id' => $sale->id,
            'customer_id' => $customer->id,
            'items' => [
                ['product_id' => $products[0]->id, 'quantity' => 2],
                ['product_id' => $products[1]->id, 'quantity' => 3],
            ],
        ];

        $this->postJson('/api/sales-orders', $orderData)
            ->assertStatus(201);
    }
}