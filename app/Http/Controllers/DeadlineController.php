<?php

namespace App\Http\Controllers;

use App\Models\Deadline;
use App\Http\Requests\DeadlineRequest;
use Inertia\Inertia;

class DeadlineController extends Controller
{
    /**
     * Display a listing of the deadlines.
     */
    public function index()
    {
        $deadlines = Deadline::with(['order.product', 'order.month', 'createdBy'])
            ->orderBy('due_date')
            ->paginate(15);

        return Inertia::render('Deadlines/Index', [
            'deadlines' => $deadlines
        ]);
    }

    /**
     * Show the form for creating a new deadline.
     */
    public function create()
    {
        return Inertia::render('Deadlines/Create');
    }

    /**
     * Store a newly created deadline in storage.
     */
    public function store(DeadlineRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $deadline = Deadline::create($validated);

        return redirect()->route('deadlines.index')
            ->with('success', 'Deadline created successfully.');
    }

    /**
     * Display the specified deadline.
     */
    public function show(Deadline $deadline)
    {
        $deadline->load(['order.product', 'order.month', 'createdBy']);

        return Inertia::render('Deadlines/Show', [
            'deadline' => $deadline
        ]);
    }

    /**
     * Show the form for editing the specified deadline.
     */
    public function edit(Deadline $deadline)
    {
        return Inertia::render('Deadlines/Edit', [
            'deadline' => $deadline
        ]);
    }

    /**
     * Update the specified deadline in storage.
     */
    public function update(DeadlineRequest $request, Deadline $deadline)
    {
        $validated = $request->validated();
        $deadline->update($validated);

        return redirect()->route('deadlines.index')
            ->with('success', 'Deadline updated successfully.');
    }

    /**
     * Remove the specified deadline from storage.
     */
    public function destroy(Deadline $deadline)
    {
        $deadline->delete();

        return redirect()->route('deadlines.index')
            ->with('success', 'Deadline deleted successfully.');
    }
}