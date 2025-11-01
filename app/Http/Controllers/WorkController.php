<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkRequest;
use App\Http\Resources\WorkResource;
use App\Models\Employee;
use App\Traits\CookieHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class WorkController extends Controller  implements HasMiddleware
{
    use CookieHelper;
    use \App\Traits\ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $modelClass = \App\Models\Work::class;
    protected $requestClass = \App\Http\Requests\WorkRequest::class;
    protected $resourceClass = \App\Http\Resources\WorkResource::class;

    protected $title = [
        'icons' => ['work', 'engineering'],
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

    //. -------------------------------------------------------------------------- 
    //.                                    index                                   
    //. -------------------------------------------------------------------------- 
    public function index(Request $request)
    {
        $response = [];

        //. --------------------------------- initial -------------------------------- 
        $criteria = [
            'keywords' => [ //keywords is searches texts
                'description' => (string) $request->input('criteria.keywords.description'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ----------------------- $filterables->options ----------------------------- 
        if (! $request->header('X-Inertia-Partial-Data')) {
            $filterables =  [
                'status' => [
                    'multiple' => true,
                    'options' => [
                        ['label' => 'Active', 'value' => true],
                        ['label' => 'Inactive', 'value' => false],
                    ]
                ],
                'work_type' => [
                    'multiple' => true,
                    'options' => [
                        ['label' => 'Batched', 'value' => 'Batched'],
                        ['label' => 'Non-Batched', 'value' => 'nonBatched'],
                        ['label' => 'Extra', 'value' => 'Extra'],
                        ['label' => 'No Work', 'value' => 'noWork'],
                        ['label' => 'Vacation', 'value' => 'Vacation'],
                    ]
                ],
                'employee' => [
                    'multiple' => true,
                    'options' => \App\Models\Employee::query()
                        ->select('id', 'user_id')
                        ->orderBy('user_id')
                        ->pluck('user_id', 'id')
                        ->map(fn($name, $id) => ['label' => $name, 'value' => $id])
                        ->values()
                        ->toArray()
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query --------------------------------- 
        $related = ['employee', 'createdBy'];
        $query = \App\Models\Work::with($related)->orderBy('id', 'asc');

        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description', '%' . $input . '%');
        }

        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }

        $input = $request->input('criteria.selections.work_type');
        if (!empty($input)) {
            $query->whereIn('work_type', $input);
        }

        $input = $request->input('criteria.selections.employee');
        if (!empty($input)) {
            $query->whereIn('employee_id', $input);
        }


        //. --------------------------------- return --------------------------------- */

        $response = array_merge($response, [
            'columns' => WorkResource::setColumns(),
            'records' => $this->paginator($request, $query),
            'criteria' => $criteria,
            'title' => $this->title,
        ]);

        return Inertia::render('dashboard/Index', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    form                                    */
    //. -------------------------------------------------------------------------- */
    protected function form()
    {
        $activities = \App\Models\Activity::select('id', 'zone', 'name')->orderBy('id')->get();
        $batches = \App\Models\Batch::select('id', 'size')->orderBy('id')->get();

        $employees = Employee::with('user:id,first_name,last_name')
            ->select('id', 'user_id','employee_number')
            ->get();
        return [
            'title' => $this->title,
            'employees' => $employees,
            'activities' => $activities,
            'batches' => $batches,
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                    show                                    */
    //. -------------------------------------------------------------------------- */
    public function show(\App\Models\Work $work)
    {
        $response = $this->form();
        $work->load(['createdBy']);

        $response = array_merge($response, [
            'record' => $work,
        ]);

        return Inertia::render('dashboard/Works/Form', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   create                                   */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $responses = $this->form();

        return Inertia::render('dashboard/Works/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(WorkRequest $request)
    {
        $workData = $request->validated();

        \App\Models\Work::create($workData);

        return redirect()->route('dashboard.works.index')->with('message', 'Work created successfully.');
    }

    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit(\App\Models\Work $work)
    {
        $responses = $this->form();

        $work->load(['createdBy']);
        $responses = array_merge($responses, [
            'record' => $work,
        ]);
        return Inertia::render('dashboard/Works/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update(WorkRequest $request, \App\Models\Work $work)
    {
        Gate::authorize('edit', $work);

        $workData = $request->validated();

        $work->update($workData);

        return redirect()->route('dashboard.works.index')->with('message', 'Work updated successfully.');
    }
}
