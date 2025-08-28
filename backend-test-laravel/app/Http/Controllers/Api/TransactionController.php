<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Sale;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $customer = null;
        if ($request->has('customer_id')) {
            $customer = Customer::find($request->customer_id);
        }

        $sales = null;
        if ($request->has('sales_id')) {
            $sales = Sale::with('user')->find($request->sales_id);
        }

        $query = DB::table('sales_orders')
            ->join('sales_order_items', 'sales_orders.id', '=', 'sales_order_items.order_id')
            ->select(
                DB::raw('YEAR(sales_orders.created_at) as year'),
                DB::raw('MONTH(sales_orders.created_at) as month_num'),
                DB::raw('SUM(sales_order_items.selling_price * sales_order_items.quantity) as total_nominal')
            )
            ->where('sales_orders.created_at', '>=', Carbon::now()->subYears(3))
            ->groupBy('year', 'month_num')
            ->orderBy('year', 'desc')
            ->orderBy('month_num', 'asc');

        if ($request->has('customer_id')) {
            $query->where('sales_orders.customer_id', $request->customer_id);
        }

        if ($request->has('sales_id')) {
            $query->where('sales_orders.sales_id', $request->sales_id);
        }

        $transactions = $query->get();

        // Format data
        $formattedData = [];
        $groupedByYear = $transactions->groupBy('year');
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        foreach ($groupedByYear as $year => $items) {
            $yearData = [
                'name' => $year,
                'data' => [],
            ];

            $monthData = [];
            foreach ($items as $item) {
                $monthData[$item->month_num] = $item->total_nominal;
            }

            for ($i = 1; $i <= 12; $i++) {
                $yearData['data'][] = [
                    'x' => $monthNames[$i - 1],
                    'y' => number_format($monthData[$i] ?? 0, 2, '.', '')
                ];
            }
            $formattedData[] = $yearData;
        }

        return response()->json([
            'customer' => $customer ? $customer->name : null,
            'sales' => $sales ? $sales->user->name : null,
            'items' => $formattedData,
        ]);
    }
}