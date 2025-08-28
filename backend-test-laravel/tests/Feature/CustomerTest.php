<?php

use App\Models\Customer;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('dapat membuat customer', function () {
    $customerData = [
        'name' => 'John Doe',
        'address' => '123 Main St',
        'phone' => '+14155552671',
    ];

    postJson('/api/customers', $customerData)
        ->assertStatus(201)
        ->assertJsonFragment($customerData);
});

it('dapat update customer', function () {
    $customer = Customer::factory()->create();
    $updateData = [
        'name' => 'Jane Doe',
        'phone' => '+14155552672', // Valid US Number
    ];

    putJson("/api/customers/{$customer->id}", $updateData)
        ->assertStatus(200)
        ->assertJsonFragment($updateData);
});