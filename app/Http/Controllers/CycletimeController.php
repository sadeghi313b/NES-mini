<?php

namespace App\Http\Controllers;

use App\Models\Cycletime;
use App\Http\Requests\CycletimeRequest;
use Inertia\Inertia;

class CycletimeController extends Controller
{
    /**
     * Display a listing of the cycletimes.
     */
    public function index()
    {
        $cycletimes = Cycletime::with(['product', 'activity', 'createdBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Cycletimes/Index', [
            'cycletimes' => $cycletimes
        ]);
    }

    /**
     * Show the form for creating a new cycletime.
     */
    public function create()
    {
        return Inertia::render('Cycletimes/Create');
    }

    /**
     * Store a newly created cycletime in storage.
     */
    public function store(CycletimeRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $cycletime = Cycletime::create($validated);

        return redirect()->route('cycletimes.index')
            ->with('success', 'Cycletime created successfully.');
    }

    /**
     * Display the specified cycletime.
     */
    public function show(Cycletime $cycletime)
    {
        $cycletime->load(['product', 'activity', 'createdBy']);

        return Inertia::render('Cycletimes/Show', [
            'cycletime' => $cycletime
        ]);
    }

    /**
     * Show the form for editing the specified cycletime.
     */
    public function edit(Cycletime $cycletime)
    {
        return Inertia::render('Cycletimes/Edit', [
            'cycletime' => $cycletime
        ]);
    }

    /**
     * Update the specified cycletime in storage.
     */
    public function update(CycletimeRequest $request, Cycletime $cycletime)
    {
        $validated = $request->validated();
        $cycletime->update($validated);

        return redirect()->route('cycletimes.index')
            ->with('success', 'Cycletime updated successfully.');
    }

    /**
     * Remove the specified cycletime from storage.
     */
    public function destroy(Cycletime $cycletime)
    {
        $cycletime->delete();

        return redirect()->route('cycletimes.index')
            ->with('success', 'Cycletime deleted successfully.');
    }
}