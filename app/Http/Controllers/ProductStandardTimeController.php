<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStandardTimeRequest;
use App\Http\Resources\ProductStandardTimeResource;
use App\Models\Activity;
use App\Models\Product;
use App\Models\ProductStandardTime;
use App\Traits\CookieHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProductStandardTimeController extends Controller implements HasMiddleware
{
    use CookieHelper;
    use \App\Traits\ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */
    protected $modelClass = \App\Models\ProductStandardTime::class;
    protected $requestClass = \App\Http\Requests\ProductStandardTimeRequest::class;
    protected $resourceClass = \App\Http\Resources\ProductStandardTimeResource::class;

    protected $title = [
        'icons' => ['timer', 'schedule'],
        'texts' => ['Product Standard Times'],
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
    //.                                    index                                   */
    //. -------------------------------------------------------------------------- */
    public function index(Request $request)
    {
        $response = [];

        //. -------------------------------- criteria ------------------------------- 
        $criteria = [
            'keywords' => [
                'product' => (string) $request->input('criteria.keywords.product'),
                'activity' => (string) $request->input('criteria.keywords.activity'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ----------------------- filterables options ----------------------------- 
        if (!$request->header('X-Inertia-Partial-Data')) {
            $filterables = [
                'status' => [
                    'multiple' => true,
                    'options' => [
                        ['label' => 'Active', 'value' => true],
                        ['label' => 'Inactive', 'value' => false],
                    ],
                ],
                'product' => [
                    'multiple' => true,
                    'options' => Product::query()
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->map(fn($p) => ['label' => $p->name, 'value' => $p->id])
                        ->toArray(),
                ],
                'activity' => [
                    'multiple' => true,
                    'options' => Activity::query()
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->map(fn($a) => ['label' => $a->name, 'value' => $a->id])
                        ->toArray(),
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query --------------------------------- 
        $related = ['product', 'activity', 'creator'];
        $query = ProductStandardTime::with($related)->orderBy('id', 'asc');

        //. --------------------------- text search ---------------------------------- 
        if ($input = $request->input('criteria.keywords.product')) {
            $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$input}%"));
        }

        if ($input = $request->input('criteria.keywords.activity')) {
            $query->whereHas('activity', fn($q) => $q->where('name', 'like', "%{$input}%"));
        }

        //. ------------------------ filter selections ------------------------------ 
        if ($input = $request->input('criteria.selections.status')) {
            $query->whereIn('status', $input);
        }

        if ($input = $request->input('criteria.selections.product')) {
            $query->whereIn('product_id', $input);
        }

        if ($input = $request->input('criteria.selections.activity')) {
            $query->whereIn('activity_id', $input);
        }

        //. -------------------------------- return --------------------------------- 
        $response = array_merge($response, [
            'columns'  => ProductStandardTimeResource::setColumns(),
            'records'  => $this->paginator($request, $query),
            'criteria' => $criteria,
            'title'    => $this->title,
            'temp' => $this->title,
        ]);

        return Inertia::render('dashboard/Index', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    form                                    */
    //. -------------------------------------------------------------------------- */
    protected function form()
    {
        $products = Product::select('id', 'name')->orderBy('id')->get();
        $activities = Activity::select('id', 'name', 'zone')->orderBy('id')->get();

        return [
            'title' => $this->title,
            'productOptions' => $products->map(fn($p) => ['value' => $p->id, 'label' => $p->id]),
            'activityOptions' => $activities->map(fn($a) => ['value' => $a->id, 'label' => $a->name]),
            'activities' => $activities->map(fn($i) => [
                'id' => $i->id,
                'zone' => $i->zone,
                'name' => $i->name,
            ])->values(),
            'uniquedZones' => $activities->pluck('zone')
                ->unique()
                ->values()
                ->map(fn($zone, $index) => [
                    'value' => $zone,
                    'label' => $zone,
                ]),
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                   create                                   */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        return Inertia::render('dashboard/ProductStandardTimes/Form', $this->form());
    }

    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(ProductStandardTimeRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        ProductStandardTime::create($data);

        return redirect()->route('dashboard.product_standard_times.index')
            ->with('message', 'Standard Time created successfully.');
    }

    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit(ProductStandardTime $ProductStandardTime)
    {
        $responses = $this->form();
        $ProductStandardTime->load(['product', 'activity', 'creator']);

        $responses['record'] = $ProductStandardTime;

        return Inertia::render('dashboard/ProductStandardTimes/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update(ProductStandardTimeRequest $request, ProductStandardTime $ProductStandardTime)
    {
        Gate::authorize('edit', $ProductStandardTime);

        $data = $request->validated();
        $ProductStandardTime->update($data);

        return redirect()->route('dashboard.product_standard_times.index')
            ->with('message', 'Standard Time updated successfully.');
    }

    //. -------------------------------------------------------------------------- */
    //.                                    show                                    */
    //. -------------------------------------------------------------------------- */
    public function show(ProductStandardTime $ProductStandardTime)
    {
        $responses = $this->form();
        $ProductStandardTime->load(['product', 'activity', 'creator']);
        $responses['record'] = $ProductStandardTime;

        return Inertia::render($this->basePath['pages'] . 'Form', $responses);
    }
}
