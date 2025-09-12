<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Http\Requests\TimesheetRequest;
use Inertia\Inertia;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the timesheets.
     */
    public function index()
    {
        $timesheets = Timesheet::with(['employee.user', 'batch.cut.order.product', 'activity', 'createdBy'])
            ->orderBy('date', 'desc')
            ->paginate(15);

        return Inertia::render('Timesheets/Index', [
            'timesheets' => $timesheets
        ]);
    }

    /**
     * Show the form for creating a new timesheet.
     */
    public function create()
    {
        return Inertia::render('Timesheets/Create');
    }

    /**
     * Store a newly created timesheet in storage.
     */
    public function store(TimesheetRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $timesheet = Timesheet::create($validated);

        return redirect()->route('timesheets.index')
            ->with('success', 'Timesheet created successfully.');
    }

    /**
     * Display the specified timesheet.
     */
    public function show(Timesheet $timesheet)
    {
        $timesheet->load(['employee.user', 'batch.cut.order.product', 'activity', 'createdBy']);

        return Inertia::render('Timesheets/Show', [
            'timesheet' => $timesheet
        ]);
    }

    /**
     * Show the form for editing the specified timesheet.
     */
    public function edit(Timesheet $timesheet)
    {
        return Inertia::render('Timesheets/Edit', [
            'timesheet' => $timesheet
        ]);
    }

    /**
     * Update the specified timesheet in storage.
     */
    public function update(TimesheetRequest $request, Timesheet $timesheet)
    {
        $validated = $request->validated();
        $timesheet->update($validated);

        return redirect()->route('timesheets.index')
            ->with('success', 'Timesheet updated successfully.');
    }

    /**
     * Remove the specified timesheet from storage.
     */
    public function destroy(Timesheet $timesheet)
    {
        $timesheet->delete();

        return redirect()->route('timesheets.index')
            ->with('success', 'Timesheet deleted successfully.');
    }
}