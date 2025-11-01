<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkRequest;
use App\Http\Resources\WorkResource;
use App\Models\Work;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Illuminate\Support\Facades\Gate;
use App\Traits\CookieHelper;
use App\Traits\ControllerCommonMethods;

class WorkController extends Controller implements HasMiddleware
{
    use CookieHelper;
    use ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */
    protected $modelClass = \App\Models\Work::class;
    protected $requestClass = \App\Http\Requests\WorkRequest::class;
    protected $resourceClass = \App\Http\Resources\WorkResource::class;

    protected $title = [
        'icons' => ['engineering', 'work'],
        'texts' => ['Works'],
    ];

    //. -------------------------------------------------------------------------- */
    //.                                 middleware                                 */
    //. -------------------------------------------------------------------------- */
    public static function middleware(): array
    {
        return [
            'check-master-roles',
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                   index                                    */
    //. -------------------------------------------------------------------------- */
    public function index(Request $request)
    {
        $response = [];

        $criteria = [
            'keywords' => [
                'employee' => (string) $request->input('criteria.keywords.employee'),
                'description' => (string) $request->input('criteria.keywords.description'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ------------------------ $filterables->options -------------------------
        if (! $request->header('X-Inertia-Partial-Data')) {
            $filterables = [
                'work_type' => [
                    'multiple' => true,
                    'options' => collect(['Batched', 'nonBatched', 'Extra', 'noWork', 'Vacation'])
                        ->map(fn($type) => ['label' => $type, 'value' => $type])
                        ->toArray(),
                ],
                'status' => [
                    'multiple' => true,
                    'options' => [
                        ['label' => 'Active', 'value' => true],
                        ['label' => 'Inactive', 'value' => false],
                    ],
                ],
                'employee' => [
                    'multiple' => true,
                    'options' => Employee::query()
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->map(fn($e) => ['label' => $e->name, 'value' => $e->id])
                        ->toArray(),
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ------------------------------- query ----------------------------------
        $query = Work::with(['employee', 'createdBy'])
            ->orderBy('date', 'desc');

        // 🔍 search by employee name
        $input = $request->input('criteria.keywords.employee');
        if (!empty($input)) {
            $query->whereHas('employee', function ($q) use ($input) {
                $q->where('name', 'like', '%' . $input . '%');
            });
        }

        // 🔍 search by description
        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->where('description', 'like', '%' . $input . '%');
        }

        // 🔘 filter work_type
        $input = $request->input('criteria.selections.work_type');
        if (!empty($input)) {
            $query->whereIn('work_type', $input);
        }

        // 🔘 filter status
        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }

        // 🔘 filter employee
        $input = $request->input('criteria.selections.employee');
        if (!empty($input)) {
            $query->whereIn('employee_id', $input);
        }

        //. -------------------------------- return -------------------------------
        $response = array_merge($response, [
            'columns' => WorkResource::setColumns(),
            'records' => $this->paginator($request, $query),
            'criteria' => $criteria,
            'title' => $this->title,
        ]);

        return Inertia::render('dashboard/Index', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    form                                   */
    //. -------------------------------------------------------------------------- */
    protected function form()
    {
        $employees = Employee::select('id', 'user_id')->get();

        $workTypes = collect(['Batched', 'nonBatched', 'Extra', 'noWork', 'Vacation'])
            ->map(fn($t) => ['label' => $t, 'value' => $t])
            ->toArray();

        return [
            'title' => $this->title,
            'employees' => $employees,
            'workTypes' => $workTypes,
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                    show                                   */
    //. -------------------------------------------------------------------------- */
    public function show(Work $work)
    {
        $response = $this->form();
        $work->load(['employee', 'createdBy']);

        $response = array_merge($response, [
            'record' => new WorkResource($work),
        ]);

        return Inertia::render('dashboard/Works/Form', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   create                                  */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $responses = $this->form();
        return Inertia::render('dashboard/Works/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    store                                  */
    //. -------------------------------------------------------------------------- */
    public function store(WorkRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        Work::create($data);

        return redirect()->route('dashboard.works.index')->with('message', 'Work created successfully.');
    }

    //. -------------------------------------------------------------------------- */
    //.                                    edit                                   */
    //. -------------------------------------------------------------------------- */
    public function edit(Work $work)
    {
        $responses = $this->form();
        $work->load(['employee', 'createdBy']);
        $responses = array_merge($responses, [
            'record' => new WorkResource($work),
        ]);

        return Inertia::render('dashboard/Works/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   update                                  */
    //. -------------------------------------------------------------------------- */
    public function update(WorkRequest $request, Work $work)
    {
        Gate::authorize('edit', $work);

        $data = $request->validated();
        $work->update($data);

        return redirect()->route('dashboard.works.index')->with('message', 'Work updated successfully.');
    }
}
