<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Requests\ActivityRequest;
use Inertia\Inertia;

class ActivityController extends Controller
{
    /**
     * Display a listing of the activities.
     */
    public function index()
    {
        $activities = Activity::with('createdBy')
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('Activities/Index', [
            'activities' => $activities
        ]);
    }

    /**
     * Show the form for creating a new activity.
     */
    public function create()
    {
        return Inertia::render('Activities/Create');
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(ActivityRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = auth()->id();

        $activity = Activity::create($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Activity created successfully.');
    }

    /**
     * Display the specified activity.
     */
    public function show(Activity $activity)
    {
        $activity->load(['cycleTimes.product', 'timesheets', 'createdBy']);

        return Inertia::render('Activities/Show', [
            'activity' => $activity
        ]);
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit(Activity $activity)
    {
        return Inertia::render('Activities/Edit', [
            'activity' => $activity
        ]);
    }

    /**
     * Update the specified activity in storage.
     */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $validated = $request->validated();
        $activity->update($validated);

        return redirect()->route('activities.index')
            ->with('success', 'Activity updated successfully.');
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Activity deleted successfully.');
    }
}