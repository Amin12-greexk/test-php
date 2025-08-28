<?php

use App\Models\Customer;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

it('dapat membuat customer baru', function () {
    $customerData = [
        'name' => 'John Doe',
        'address' => '123 Main St',
        'phone' => '+14155552671', // Nomor valid untuk testing
    ];

    postJson('/api/customers', $customerData)
        ->assertStatus(201)
        ->assertJsonFragment($customerData);
});

it('dapat mengupdate data customer', function () {
    $customer = Customer::factory()->create();
    $updateData = [
        'name' => 'Jane Doe',
        'phone' => '+14155552672', // Nomor valid untuk testing
    ];

    putJson("/api/customers/{$customer->id}", $updateData)
        ->assertStatus(200)
        ->assertJsonFragment($updateData);
});