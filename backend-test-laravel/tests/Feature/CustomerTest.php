<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dapat_membuat_customer_baru(): void
    {
        $customerData = [
            'name' => 'John',
            'address' => '123 Main St',
            'phone' => '+14155552671',
        ];

        $this->postJson('/api/customers', $customerData)
            ->assertStatus(201)
            ->assertJsonFragment($customerData);
    }

    public function test_dapat_mengupdate_data_customer(): void
    {
        $customer = Customer::factory()->create();
        $updateData = [
            'name' => 'Jane Doe',
            'phone' => '+14155552672',
        ];

        $this->putJson("/api/customers/{$customer->id}", $updateData)
            ->assertStatus(200)
            ->assertJsonFragment($updateData);
    }
}