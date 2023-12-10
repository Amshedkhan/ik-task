<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        $existingOrder = Order::where('order_id', $data['order_id'])->first();

        if (!$existingOrder) {
            $merchant = Merchant::firstOrCreate(['domain' => $data['merchant_domain']]);
            $affiliate = Affiliate::firstOrCreate(['email' => $data['customer_email']], [
                'user_id' => null, 
                'merchant_id' => $merchant->id,
                'commission_rate' => 0.0,
            ]);

            $order = Order::create([
                'merchant_id' => $merchant->id,
                'affiliate_id' => $affiliate->id,
                'subtotal' => $data['subtotal_price'],
                'commission_owed' => 0.0,
                'payout_status' => Order::STATUS_UNPAID,
                'customer_email' => $data['customer_email'],
            ]);
        }
    }
}
