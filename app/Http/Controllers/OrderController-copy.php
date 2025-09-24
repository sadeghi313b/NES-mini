<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class OrderController extends Controller
{

    public function index(Request $request)
    {

        $actionsRouteParams = [
            'show' => 'show params',
            'edit' => 'edit params',
        ];
        $related = ['product', 'month', 'createdBy'];

        // [Searchables]
        $searchables = ['product.product_id', 'description'];

        // dd($request->selections);

        if ($request->filled('selections.product')) {
            $query->whereIn('product_id', $request->input('selections.product'));
        }

        if ($request->filled('selections.seen')) {
            $query->whereIn('seen', $request->input('selections.seen'));
        }

        // [Query]
        $query = Order::with($related)->orderBy('id', 'asc');

        if ($request->filled('searchProduct')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->whereLike('product_id', '%' . $request->searchProduct . '%');
            });
        }

        if ($request->filled('searchDescription')) {
            $query->whereLike('description',  '%' . $request->searchDescription . '%');
        }

        // Apply column filters
        if ($request->filled('productFilter')) {
            $query->whereIn('product_id', $request->productFilter);
        }
        // clg(now());
        if ($request->filled('monthFilter')) {
            $query->whereIn('month_id', $request->monthFilter);
        }

        if ($request->filled('statusFilter')) {
            $query->whereIn('status', $request->statusFilter);
        }

        if ($request->filled('seenFilter')) {
            $query->whereIn('seen', $request->seenFilter);
        } // [/]

        // [Pagination]
        $rawPerPage = $request->input('perPage', 20);
        $perPage = is_numeric($rawPerPage) ? (int) $rawPerPage : 20;
        if ($perPage === 0) {
            $perPage = $query->count() ?: 1;
        }

        $orders = $query->paginate($perPage); //$request->perPage ?? 2
        $resourcedData = OrderResource::collection($orders); // [/]

        // [Filterables]
        $filterables = [];
        $productOptions = $resourcedData->pluck('product')
            ->unique()
            ->sort()
            ->map(fn($p) => ['label' => $p['id'], 'value' => $p['id']])
            ->values()                                       // reset keys
        ;
        $filterables['product'] = ['multiple' => true, 'options' => $productOptions];


        $monthOptions = $resourcedData->pluck('month')
            ->unique()
            ->sort()
            ->map(fn($m) => ['label' => $m['name'], 'value' => $m['id']])
            ->values()                                       // reset keys
        ;
        $filterables['month'] = ['multiple' => true, 'options' => $monthOptions];


        $statusOptions = collect(OrderStatus::cases())->sort()
            ->map(fn($status) => ['label' => $status->label(), 'value' => $status->value])
            ->values() //
        ;
        $filterables['status'] = ['multiple' => true, 'options' => $statusOptions];


        $seenOptions = ['Seen', 'unSeen'];
        $filterables['seen'] = ['multiple' => false, 'options' => $seenOptions];

        // dd($filterables);

        // [/]

        // [Return]
        $response = Inertia::render('Orders/Index', [
            'records' => $resourcedData,

            'productOptions' => $productOptions,
            'monthOptions' => $monthOptions,
            'statusOptions' => $statusOptions,
            'filterables' => $filterables,
            'selections' => $request->selections,

            'searchProduct' => $request->searchProduct,
            'searchDescription' => $request->searchDescription,

            'productFilter' => $request->productFilter,
            'monthFilter' => $request->monthFilter,
            'statusFilter' => $request->statusFilter,
            'seenFilter' => $request->seenFilter,

            'searchables' => $searchables,
            'routeName' => Route::currentRouteName(),
            'title' => 'Orders',
            'actionsRouteParams' => $actionsRouteParams,
        ]);
        // dd_inertia($response);
        return $response;
        // // [/]
    }

    public function create()
    {
        return Inertia::render('Orders/Create');
    }

    public function store(OrderRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $order = Order::create($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['product', 'month', 'deadlines', 'deliveryOrders', 'cuts', 'createdBy']);

        return Inertia::render('Orders/Show', [
            'order' => $order
        ]);
    }

    public function edit(Order $order)
    {
        return Inertia::render('Orders/Edit', [
            'order' => $order
        ]);
    }

    public function update(OrderRequest $request, Order $order)
    {
        $validated = $request->validated();
        $order->update($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}
