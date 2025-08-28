<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesTargetController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('sales_targets')
            ->join('sales', 'sales_targets.sales_id', '=', 'sales.id')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select(
                'sales_targets.month',
                'users.name as sales_name',
                'sales_targets.target',
                DB::raw('(SELECT SUM(soi.selling_price * soi.quantity)
                          FROM sales_orders so
                          JOIN sales_order_items soi ON so.id = soi.order_id
                          WHERE so.sales_id = sales.id AND YEAR(so.created_at) = YEAR(CURDATE()) AND MONTH(so.created_at) = sales_targets.month) as revenue'),
                DB::raw('(SELECT SUM((soi.selling_price - soi.production_price) * soi.quantity)
                          FROM sales_orders so
                          JOIN sales_order_items soi ON so.id = soi.order_id
                          WHERE so.sales_id = sales.id AND YEAR(so.created_at) = YEAR(CURDATE()) AND MONTH(so.created_at) = sales_targets.month) as income')
            )
            ->whereYear('sales_targets.month', '=', Carbon::now()->year);

        if ($request->has('sales_id')) {
            $query->where('sales.id', $request->sales_id);
        }

        $targets = $query->get();

        return response()->json($targets);
    }

    public function performance(Request $request)
    {
        $month = $request->has('month') ? Carbon::parse($request->month) : Carbon::now();

        $query = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->leftJoin('sales_targets', function ($join) use ($month) {
                $join->on('sales.id', '=', 'sales_targets.sales_id')
                    ->whereYear('sales_targets.month', '=', $month->year)
                    ->whereMonth('sales_targets.month', '=', $month->month);
            })
            ->select(
                'users.name as sales_name',
                DB::raw('IFNULL(sales_targets.target, 0) as target'),
                DB::raw('(SELECT SUM(soi.selling_price * soi.quantity)
                          FROM sales_orders so
                          JOIN sales_order_items soi ON so.id = soi.order_id
                          WHERE so.sales_id = sales.id AND YEAR(so.created_at) = ' . $month->year . ' AND MONTH(so.created_at) = ' . $month->month . ') as revenue')
            );

        if ($request->has('isUnderperform')) {
            if ($request->isUnderperform == 'true') {
                $query->havingRaw('revenue < target');
            } else {
                $query->havingRaw('revenue >= target');
            }
        }

        $performance = $query->get();

        return response()->json($performance);
    }
}