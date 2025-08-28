<?php

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use function Pest\Laravel\postJson;

it('can create a sales order', function () {
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

    postJson('/api/sales-orders', $orderData)
        ->assertStatus(201);
});