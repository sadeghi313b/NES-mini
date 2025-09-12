<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Http\Requests\DeliveryOrderRequest;
use Inertia\Inertia;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the delivery orders.
     */
    public function index()
    {
        $deliveryOrders = DeliveryOrder::with(['delivery', 'order', 'createdBy'])
            ->paginate(15);

        return Inertia::render('DeliveryOrders/Index', [
            'deliveryOrders' => $deliveryOrders
        ]);
    }

    /**
     * Show the form for creating a new delivery order.
     */
    public function create()
    {
        return Inertia::render('DeliveryOrders/Create');
    }

    /**
     * Store a newly created delivery order in storage.
     */
    public function store(DeliveryOrderRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $deliveryOrder = DeliveryOrder::create($validated);

        return redirect()->route('delivery-orders.index')
            ->with('success', 'Delivery order created successfully.');
    }

    /**
     * Display the specified delivery order.
     */
    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['delivery', 'order', 'createdBy']);

        return Inertia::render('DeliveryOrders/Show', [
            'deliveryOrder' => $deliveryOrder
        ]);
    }

    /**
     * Show the form for editing the specified delivery order.
     */
    public function edit(DeliveryOrder $deliveryOrder)
    {
        return Inertia::render('DeliveryOrders/Edit', [
            'deliveryOrder' => $deliveryOrder
        ]);
    }

    /**
     * Update the specified delivery order in storage.
     */
    public function update(DeliveryOrderRequest $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validated();
        $deliveryOrder->update($validated);

        return redirect()->route('delivery-orders.index')
            ->with('success', 'Delivery order updated successfully.');
    }

    /**
     * Remove the specified delivery order from storage.
     */
    public function destroy(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->delete();

        return redirect()->route('delivery-orders.index')
            ->with('success', 'Delivery order deleted successfully.');
    }
}