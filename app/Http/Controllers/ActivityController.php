<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivityRequest;
use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Traits\CookieHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;

class ActivityController extends Controller implements HasMiddleware
{
    use CookieHelper;
    use \App\Traits\ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $modelClass = \App\Models\Activity::class;
    protected $requestClass = \App\Http\Requests\ActivityRequest::class;
    protected $resourceClass = \App\Http\Resources\ActivityResource::class;
    protected $basePath = [
        'routes' => 'dashboard.activities.',
        'pages' => 'dashboard/Activities/',
    ];

    protected $title = [
        'icons' => ['category', 'add'],
        'texts' => ['Activities'],
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
    //.                                    index                                    */
    //. -------------------------------------------------------------------------- */
    public function index(Request $request)
    {
        $response = [];

        //. --------------------------------- initial -------------------------------- 
        $criteria = [
            'keywords' => [
                'name' => (string) $request->input('criteria.keywords.name'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ----------------------- $filterables->options ----------------------------- 
        if (! $request->header('X-Inertia-Partial-Data')) {
            $filterables =  [
                'status' => [
                    'multiple' => true,
                    'options' => [
                        ['label' => 'Active', 'value' => 1],
                        ['label' => 'Inactive', 'value' => 0],
                    ],
                ],
                'zone' => [
                    'multiple' => true,
                    'options' => Activity::query()
                        ->select('zone')
                        ->distinct()
                        ->orderBy('zone')
                        ->pluck('zone')
                        ->map(fn($zone) => ['label' => $zone, 'value' => $zone])
                        ->toArray()
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query --------------------------------- 
        $query = Activity::query()->orderBy('id', 'asc');

        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.name');
        if (!empty($input)) {
            $query->whereLike('name', '%' . $input . '%');
        }

        $input = $request->input('criteria.keywords.alias');
        if (!empty($input)) {
            $query->whereLike('alias', '%' . $input . '%');
        }

        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }

        $input = $request->input('criteria.selections.zone');
        if (!empty($input)) {
            $query->whereIn('zone', $input);
        }

        //. --------------------------------- return --------------------------------- 
        $response = array_merge($response, [
            'columns' => ActivityResource::setColumns(),
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
        return [
            'title' => $this->title,
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                    create                                  */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $responses = $this->form();
        return Inertia::render('dashboard/Activities/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(ActivityRequest $request)
    {
        $data = $request->validated();
        // $data['created_by'] = auth()->id();
        // dd($data);
        Activity::create($data);

        return redirect()->route('dashboard.activities.index')
            ->with('message', 'Activity created successfully.');
    }

    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit(Activity $activity)
    {
        $responses = $this->form();

        $responses = array_merge($responses, [
            'record' => $activity,
        ]);

        return Inertia::render('dashboard/Activities/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update(ActivityRequest $request, Activity $activity)
    {
        $data = $request->validated();

        $activity->update($data);

        return redirect()->route('dashboard.activities.index')
            ->with('message', 'Activity updated successfully.');
    }
}
