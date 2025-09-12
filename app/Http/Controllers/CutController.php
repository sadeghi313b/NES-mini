<?php

namespace App\Http\Controllers;

use App\Models\Cut;
use App\Http\Requests\CutRequest;
use Inertia\Inertia;

class CutController extends Controller
{
    /**
     * Display a listing of the cuts.
     */
    public function index()
    {
        $cuts = Cut::with(['order.product', 'order.month', 'batches', 'createdBy'])
            ->orderBy('cutting_date', 'desc')
            ->paginate(15);

        return Inertia::render('Cuts/Index', [
            'cuts' => $cuts
        ]);
    }

    /**
     * Show the form for creating a new cut.
     */
    public function create()
    {
        return Inertia::render('Cuts/Create');
    }

    /**
     * Store a newly created cut in storage.
     */
    public function store(CutRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $cut = Cut::create($validated);

        return redirect()->route('cuts.index')
            ->with('success', 'Cut created successfully.');
    }

    /**
     * Display the specified cut.
     */
    public function show(Cut $cut)
    {
        $cut->load(['order.product', 'order.month', 'batches', 'createdBy']);

        return Inertia::render('Cuts/Show', [
            'cut' => $cut
        ]);
    }

    /**
     * Show the form for editing the specified cut.
     */
    public function edit(Cut $cut)
    {
        return Inertia::render('Cuts/Edit', [
            'cut' => $cut
        ]);
    }

    /**
     * Update the specified cut in storage.
     */
    public function update(CutRequest $request, Cut $cut)
    {
        $validated = $request->validated();
        $cut->update($validated);

        return redirect()->route('cuts.index')
            ->with('success', 'Cut updated successfully.');
    }

    /**
     * Remove the specified cut from storage.
     */
    public function destroy(Cut $cut)
    {
        $cut->delete();

        return redirect()->route('cuts.index')
            ->with('success', 'Cut deleted successfully.');
    }
}