<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Controllers\Controller;


class DashboardController extends Controller implements HasMiddleware
{
    //. -------------------------------------------------------------------------- */
    //.                                 middleware                                 */
    //. -------------------------------------------------------------------------- */
    public static function middleware(): array
    {
        return [
            'check-master-roles',
        ];
    }


    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $title = [
        'icons' => ['shopping_cart', 'add_shopping_cart'],
        'texts' => ['Orders'],
    ];

    
    //. -------------------------------------------------------------------------- */
    //.                                    index                                   */
    //. -------------------------------------------------------------------------- */
    public function index()
    {
        // Load products with orders, months, cuts, and productions (through pivot)
        $products = Product::with([
            'orders' => function ($q) {
                $q->with(['month:id,name', 'cuts', 'productions']);
            }
        ])->get();

        // Aggregate data
        $aggregatedOrders = $products->flatMap(function ($product) {
            return $product->orders->map(function ($order) use ($product) {
                return [
                    'month_name' => $order->month?->name,
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'order_quantity' => $order->quantity,
                    'total_cuts' => $order->cuts->sum('quantity'),
                    'total_productions' => $order->productions->sum('quantity'),
                ];
            });
        }) ->sortByDesc('month_name')->values();

        return Inertia::render('Dashboard', [
            'aggregatedOrders' => $aggregatedOrders,
            'title' => $this->title,
        ]);
    }
}
