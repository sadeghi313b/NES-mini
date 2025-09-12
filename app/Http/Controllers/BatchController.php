<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Http\Requests\BatchRequest;
use Inertia\Inertia;

class BatchController extends Controller
{
    /**
     * Display a listing of the batches.
     */
    public function index()
    {
        $batches = Batch::with(['cut.order.product', 'cut.order.month', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Batches/Index', [
            'batches' => $batches
        ]);
    }

    /**
     * Show the form for creating a new batch.
     */
    public function create()
    {
        return Inertia::render('Batches/Create');
    }

    /**
     * Store a newly created batch in storage.
     */
    public function store(BatchRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $batch = Batch::create($validated);

        return redirect()->route('batches.index')
            ->with('success', 'Batch created successfully.');
    }

    /**
     * Display the specified batch.
     */
    public function show(Batch $batch)
    {
        $batch->load(['cut.order.product', 'cut.order.month', 'timesheets', 'createdBy']);

        return Inertia::render('Batches/Show', [
            'batch' => $batch
        ]);
    }

    /**
     * Show the form for editing the specified batch.
     */
    public function edit(Batch $batch)
    {
        return Inertia::render('Batches/Edit', [
            'batch' => $batch
        ]);
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(BatchRequest $request, Batch $batch)
    {
        $validated = $request->validated();
        $batch->update($validated);

        return redirect()->route('batches.index')
            ->with('success', 'Batch updated successfully.');
    }

    /**
     * Remove the specified batch from storage.
     */
    public function destroy(Batch $batch)
    {
        $batch->delete();

        return redirect()->route('batches.index')
            ->with('success', 'Batch deleted successfully.');
    }
}