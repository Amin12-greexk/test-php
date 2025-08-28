<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Support\Facades\Http;

class CustomerController extends Controller
{
    public function store(StoreCustomerRequest $request)
    {
        $validated = $request->validated();

        // Phone validation
        $response = Http::get('https://phonevalidation.abstractapi.com/v1/', [
            'api_key' => env('ABSTRACT_API_KEY'),
            'phone' => $validated['phone']
        ]);

        if ($response->failed() || !$response->json()['valid']) {
            return response()->json(['error' => 'Invalid phone number'], 422);
        }

        $customer = Customer::create($validated);

        return response()->json($customer, 201);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $validated = $request->validated();

        if (isset($validated['phone'])) {
            // Phone validation
            $response = Http::get('https://phonevalidation.abstractapi.com/v1/', [
                'api_key' => env('ABSTRACT_API_KEY'),
                'phone' => $validated['phone']
            ]);

            if ($response->failed() || !$response->json()['valid']) {
                return response()->json(['error' => 'Invalid phone number'], 422);
            }
        }

        $customer->update($validated);

        return response()->json($customer);
    }
}