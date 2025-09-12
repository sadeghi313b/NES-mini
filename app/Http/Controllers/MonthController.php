<?php

namespace App\Http\Controllers;

use App\Models\Month;
use App\Http\Requests\MonthRequest;
use Inertia\Inertia;

class MonthController extends Controller
{
    /**
     * Display a listing of the months.
     */
    public function index()
    {
        $months = Month::with('createdBy')
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('Months/Index', [
            'months' => $months
        ]);
    }

    /**
     * Show the form for creating a new month.
     */
    public function create()
    {
        return Inertia::render('Months/Create');
    }

    /**
     * Store a newly created month in storage.
     */
    public function store(MonthRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $month = Month::create($validated);

        return redirect()->route('months.index')
            ->with('success', 'Month created successfully.');
    }

    /**
     * Display the specified month.
     */
    public function show(Month $month)
    {
        $month->load(['createdBy', 'orders']);

        return Inertia::render('Months/Show', [
            'month' => $month
        ]);
    }

    /**
     * Show the form for editing the specified month.
     */
    public function edit(Month $month)
    {
        return Inertia::render('Months/Edit', [
            'month' => $month
        ]);
    }

    /**
     * Update the specified month in storage.
     */
    public function update(MonthRequest $request, Month $month)
    {
        $validated = $request->validated();
        $month->update($validated);

        return redirect()->route('months.index')
            ->with('success', 'Month updated successfully.');
    }

    /**
     * Remove the specified month from storage.
     */
    public function destroy(Month $month)
    {
        $month->delete();

        return redirect()->route('months.index')
            ->with('success', 'Month deleted successfully.');
    }
}