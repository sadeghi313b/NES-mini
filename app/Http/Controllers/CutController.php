<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// use Illuminate\Routing\Controller;

use App\Http\Requests\BatchRequest;
use App\Http\Requests\CutRequest;
use App\Http\Resources\CutResource;
use App\Models\Batch;
use App\Models\Cut;
use App\Models\Month;
use App\Models\Order;
use App\Traits\CookieHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;

class CutController extends Controller implements HasMiddleware
{
    use CookieHelper;
    use \App\Traits\ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $modelClass = \App\Models\Cut::class;
    // protected $requestClass = \App\Http\Requests\CutRequest::class;
    protected $resourceClass = \App\Http\Resources\CutResource::class;

    protected $basePath = [
        'routes' => 'dashboard.cuts.',
        'pages' => 'dashboard/Cuts/',
    ];

    protected $title = [
        'icons' => ['content_cut'],
        'texts' => ['Cuts'],
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
    //.                                  construct                                 */
    //. -------------------------------------------------------------------------- */
    // public function __construct()
    // {

    // }


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
                'month' => [
                    'multiple' => true,
                    'options' => Month::query()
                        ->select('name')
                        ->distinct()
                        ->orderBy('name')
                        ->pluck('name')
                        ->map(fn($name, $index) => ['label' => $name, 'value' => $index + 1])
                        ->toArray()
                ],
                'maximum_batch_size' => [
                    'multiple' => true,
                    'options' => Cut::query()
                        ->select('maximum_batch_size')
                        ->distinct()
                        ->orderBy('maximum_batch_size')
                        ->pluck('maximum_batch_size')
                        ->map(fn($name, $index) => ['label' => $name, 'value' => $index + 1])
                        ->toArray()
                ],
                'status' => [
                    'multiple' => false,
                    'options' => array_map(
                        fn($name, $index) => ['label' => $name, 'value' => $index + 1],
                        ['true', 'false'],
                        [0, 1]
                    ),
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query --------------------------------- 
        $related = ['order', 'batches', 'createdBy'];
        $query = Cut::with($related)->orderBy('id', 'asc');

        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description', '%' . $input . '%');
        }

        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.maximum_batch_size');
        if (!empty($input)) {
            $query->whereIn('maximum_batch_size', $input);
        }

        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }

        //. --------------------------------- return --------------------------------- */

        $response = array_merge($response, [
            'columns' => CutResource::setColumns(),
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
        $orders = Order::select('id', 'product_id')->get();
        return [
            'orders' => $orders,
            'title' => $this->title,
        ];
    }


    //. -------------------------------------------------------------------------- */
    //.                                    show                                    */
    //. -------------------------------------------------------------------------- */
    public function show(Cut $cut)
    {
        $responses = $this->form();

        $cut->load(['createdBy', 'batches']);

        $responses = array_merge($responses, [
            'record' => $cut,
        ]);

        return Inertia::render('dashboard/Cuts/Form', $responses);
    }


    //. -------------------------------------------------------------------------- */
    //.                                   create                                   */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $responses = $this->form();
        $responses = array_merge($responses, []);

        return Inertia::render('dashboard/Cuts/Form', $responses);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(CutRequest $request)
    {
        $data = $request->validated();
        $request->validate(BatchRequest::batchesRules());

        $cut = Cut::create($data);

        $batches = $request->input('creatingBatches', []);
        if (!empty($batches)) {
            foreach ($batches as $batch) {
                Batch::create([
                    'cut_id' => $cut->id, // Link batch to the created Cut
                    'size' => $batch['size'] ?? null,
                    'created_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('dashboard.cuts.index')->with('message', 'Cut created successfully.');
    }


    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit(Cut $cut)
    {
        $responses = $this->form();

        $cut->load(['order', 'createdBy', 'batches']);
        $responses = array_merge($responses, [
            'record' => $cut,
        ]);
        return Inertia::render('dashboard/Cuts/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update(CutRequest $request, Cut $cut)
    {
        $data = $request->validated();

        $cut->update($data);

        return redirect()->route('dashboard.cuts.index')->with('message', 'Cut updated successfully.');
    }
}
