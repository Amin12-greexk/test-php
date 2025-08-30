<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Helpers\NumberHelper;

class SalesTargetController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);
        $sales = null;
        if ($request->has('sales_id')) {
            $sales = Sale::with('user')->find($request->sales_id);
        }

        $query = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->leftJoin('sales_targets', function ($join) use ($year) {
                $join->on('sales.id', '=', 'sales_targets.sales_id')
                    // Menggunakan active_date
                    ->where(DB::raw('YEAR(sales_targets.active_date)'), '=', $year);
            })
            ->leftJoin('sales_orders', function ($join) use ($year) {
                $join->on('sales.id', '=', 'sales_orders.sales_id')
                    ->where(DB::raw('YEAR(sales_orders.created_at)'), '=', $year);
            })
            ->leftJoin('sales_order_items', 'sales_orders.id', '=', 'sales_order_items.order_id')
            ->select(
                DB::raw('IFNULL(MONTH(sales_targets.active_date), MONTH(sales_orders.created_at)) as month_num'),

                DB::raw('SUM(DISTINCT sales_targets.amount) as total_target'),
                DB::raw('SUM(sales_order_items.selling_price * sales_order_items.quantity) as total_revenue'),
                DB::raw('SUM((sales_order_items.selling_price - sales_order_items.production_price) * sales_order_items.quantity) as total_income')
            )
            ->where(function ($q) use ($year) {

                $q->where(DB::raw('YEAR(sales_targets.active_date)'), '=', $year)
                    ->orWhere(DB::raw('YEAR(sales_orders.created_at)'), '=', $year);
            })
            ->groupBy('month_num');

        if ($request->has('sales_id')) {
            $query->where('sales.id', $request->sales_id);
        }

        $results = $query->get()->keyBy('month_num');

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $series = [
            ['name' => 'Target', 'data' => []],
            ['name' => 'Revenue', 'data' => []],
            ['name' => 'Income', 'data' => []],
        ];

        for ($i = 1; $i <= 12; $i++) {
            $monthResult = $results->get($i);
            $series[0]['data'][] = ['x' => $monthNames[$i - 1], 'y' => number_format($monthResult->total_target ?? 0, 2, '.', '')];
            $series[1]['data'][] = ['x' => $monthNames[$i - 1], 'y' => number_format($monthResult->total_revenue ?? 0, 2, '.', '')];
            $series[2]['data'][] = ['x' => $monthNames[$i - 1], 'y' => number_format($monthResult->total_income ?? 0, 2, '.', '')];
        }

        return response()->json([
            'sales' => $sales ? $sales->user->name : null,
            'year' => $year,
            'items' => $series,
        ]);
    }

    public function performance(Request $request)
    {
        $month = $request->has('month') ? Carbon::parse($request->month) : Carbon::now();

        $query = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->leftJoin('sales_targets', function ($join) use ($month) {
                $join->on('sales.id', '=', 'sales_targets.sales_id')

                    ->whereYear('sales_targets.active_date', '=', $month->year)
                    ->whereMonth('sales_targets.active_date', '=', $month->month);
            })
            ->select(
                'users.name as sales_name',

                DB::raw('IFNULL(sales_targets.amount, 0) as target'),
                DB::raw('(SELECT SUM(soi.selling_price * soi.quantity)
                          FROM sales_orders so
                          JOIN sales_order_items soi ON so.id = soi.order_id
                          WHERE so.sales_id = sales.id AND YEAR(so.created_at) = ' . $month->year . ' AND MONTH(so.created_at) = ' . $month->month . ') as revenue')
            );

        if ($request->has('is_underperform')) {
            $isUnderperform = filter_var($request->is_underperform, FILTER_VALIDATE_BOOLEAN);
            if ($isUnderperform) {
                $query->havingRaw('revenue < target');
            } else {
                $query->havingRaw('revenue >= target');
            }
        }

        $performance = $query->get();

        $formattedItems = $performance->map(function ($item) {
            $revenue = (float) $item->revenue;
            $target = (float) $item->target;
            $percentage = ($target > 0) ? ($revenue / $target) * 100 : 0;

            return [
                'sales' => $item->sales_name,
                'revenue' => [
                    'amount' => number_format($revenue, 2, '.', ''),
                    'abbreviation' => NumberHelper::abbreviate($revenue),
                ],
                'target' => [
                    'amount' => number_format($target, 2, '.', ''),
                    'abbreviation' => NumberHelper::abbreviate($target),
                ],
                'percentage' => number_format($percentage, 2, '.', ''),
            ];
        });

        return response()->json([
            'is_underperform' => $request->has('is_underperform') ? filter_var($request->is_underperform, FILTER_VALIDATE_BOOLEAN) : null,
            'month' => $month->format('F Y'),
            'items' => $formattedItems,
        ]);
    }
}