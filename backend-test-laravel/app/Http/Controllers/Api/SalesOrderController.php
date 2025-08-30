<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSalesOrderRequest;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use Illuminate\Support\Str;

class SalesOrderController extends Controller
{

    public function store(StoreSalesOrderRequest $request)
    {
        $validated = $request->validated();

        $salesOrder = SalesOrder::create([
            'reference_no' => 'INV' . Str::random(17),
            'sales_id' => $validated['sales_id'],
            'customer_id' => $validated['customer_id'],
        ]);

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            SalesOrderItem::create([
                'order_id' => $salesOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'production_price' => $product->production_price,
                'selling_price' => $product->selling_price,
            ]);
        }

        return response()->json($salesOrder->load('items'), 201);
    }
}
