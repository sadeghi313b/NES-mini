<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Http\Requests\DeliveryRequest;
use Inertia\Inertia;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the deliveries.
     */
    public function index()
    {
        $deliveries = Delivery::with(['product', 'deliveryOrders.order', 'createdBy'])
            ->orderBy('date', 'desc')
            ->paginate(15);

        return Inertia::render('Deliveries/Index', [
            'deliveries' => $deliveries
        ]);
    }

    /**
     * Show the form for creating a new delivery.
     */
    public function create()
    {
        return Inertia::render('Deliveries/Create');
    }

    /**
     * Store a newly created delivery in storage.
     */
    public function store(DeliveryRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $delivery = Delivery::create($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery created successfully.');
    }

    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load(['product', 'deliveryOrders.order', 'orders', 'createdBy']);

        return Inertia::render('Deliveries/Show', [
            'delivery' => $delivery
        ]);
    }

    /**
     * Show the form for editing the specified delivery.
     */
    public function edit(Delivery $delivery)
    {
        return Inertia::render('Deliveries/Edit', [
            'delivery' => $delivery
        ]);
    }

    /**
     * Update the specified delivery in storage.
     */
    public function update(DeliveryRequest $request, Delivery $delivery)
    {
        $validated = $request->validated();
        $delivery->update($validated);

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery updated successfully.');
    }

    /**
     * Remove the specified delivery from storage.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }
}