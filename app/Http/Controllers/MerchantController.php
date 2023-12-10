<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        $orderStats = $this->calculateOrderStats($fromDate, $toDate);

        return response()->json($orderStats);
    
    }

    public function calculateOrderStats(string $fromDate, string $toDate): array
    {
        $orderCount = Order::whereBetween('created_at', [$fromDate, $toDate])->count();
        $commissionOwed = Order::whereBetween('created_at', [$fromDate, $toDate])->sum('commission_owed');
        $revenue = Order::whereBetween('created_at', [$fromDate, $toDate])->sum('subtotal');

        return [
            'count' => $orderCount,
            'commission_owed' => $commissionOwed,
            'revenue' => $revenue,
        ];
    }

    
}
