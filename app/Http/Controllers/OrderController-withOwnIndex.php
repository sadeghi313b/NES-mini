<?php
//filename: OrderController.php
namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\DeadlineRequest;
use App\Http\Requests\OrderRequest;

use App\Http\Resources\OrderResource;
use App\Models\Deadline;
use App\Models\Month;
use App\Models\Order;
use App\Models\Product;
use App\Traits\CookieHelper;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class OrderController extends Controller
{
    use CookieHelper;
    
    //. -------------------------------- construct ------------------------------- */
    protected $statusOptions;
    public function __construct()
    {
        $this->statusOptions = collect(OrderStatus::cases())->sort()
            ->map(fn($status) => ['label' => $status->label(), 'value' => $status->value])
            ->values();
    }

    //. ---------------------------------- index --------------------------------- */
    public function index(Request $request)
    {
        $responseData = [];
        $showDD = $request->input('showDD');
        if ($showDD) dd($request->input('criteria'));
        $responseData[] = ['showDD' => $showDD];

        // [Initial Definitions]
        $related = ['product', 'month', 'createdBy'];

        $criteria = [
            'keywords' => [ //keywords is searches texts
                'product' => (string) $request->input('criteria.keywords.product'),
                'description' => (string) $request->input('criteria.keywords.description'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];
        // [/]

        // [Query]
        $query = Order::with($related)->orderBy('id', 'asc');
        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description',  '%' . $input . '%');
        }

        $input = $request->input('criteria.keywords.product');
        if (!empty($input)) {
            $query->whereHas('product', function ($q) use ($input) {
                $q->whereLike('product_id', '%' . $input . '%');
            });
        }

        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.product');
        if (!empty($input)) {
            $query->whereIn('product_id', $input);
        }

        $input = $request->input('criteria.selections.month');
        if (!empty($input)) {
            $query->whereIn('month_id', $input);
        }

        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }

        $input = $request->input('criteria.selections.seen');
        if (!empty($input)) {
            // dd(($input));
            $query->where('seen', strtolower($input) === 'seen');
        }
        // [/]

        // [Pagination]
        $paginationCookie = $this->getPaginationCookie($request);
        $responseData['paginationCookie'] = $paginationCookie;

        $rawPerPage = $paginationCookie['perPage'] ?: $request->input('perPage', 5);
        $perPage = is_numeric($rawPerPage) ? (int) $rawPerPage : 5;
        if ($perPage === 0) {
            $perPage = $query->count() ?: 1;
        }

        $orders = $query->paginate($perPage); //$request->perPage ?? 2
        $resourcedData = OrderResource::collection($orders); // [/]

        // [$filterables->options]
        // define keys of 'selections' and lable of 'filters' as: 'product','month','status','seen';
        $filterables = [];

        $productOptions = $resourcedData->pluck('product')
            ->unique()
            ->sort()
            ->map(fn($p) => ['label' => $p['id'], 'value' => $p['id']])
            ->values()                                       // reset keys
        ;
        $filterables['product'] = ['multiple' => true, 'options' => $productOptions];


        $monthOptions = $resourcedData->pluck('month')
            ->unique()
            ->sort()
            ->map(fn($m) => ['label' => $m['name'], 'value' => $m['id']])
            ->values()                                       // reset keys
        ;
        $filterables['month'] = ['multiple' => true, 'options' => $monthOptions];


        $filterables['status'] = ['multiple' => true, 'options' => $this->statusOptions];


        $seenOptions = ['Seen', 'unSeen'];
        $filterables['seen'] = ['multiple' => false, 'options' => $seenOptions];

        // [/]

        // [Return]
        $responseData = array_merge([
            'records' => $resourcedData,

            'criteria' => $criteria,
            'filterables' => $filterables,
        ], $responseData);

        $response = Inertia::render('dashboard/Orders/Index', $responseData);
        // dd(Route::currentRouteName());
        // dd_inertia($response);
        return $response;
        // // [/]
    }

    //. ---------------------------------- form ---------------------------------- */
    protected function form() {
        Gate::authorize('form', Order::class);

        $products = Product::select('id', 'name')->get();
        $months = Month::select('id', 'name')->get();
        return [
            'statusOptions' => $this->statusOptions,
            'months' => $months, q
            'products' => $products,
        ];
    }
    //. --------------------------------- create --------------------------------- */
    public function create()
    {
        $responses = $this->form();
        $responses = array_merge($responses,[
            'titles' => ['Order', 'Create'],
        ]);

        return Inertia::render('dashboard/Orders/Form', $responses);
    }

    //. ---------------------------------- store --------------------------------- */
    public function store(OrderRequest $orderRequest)
    {
        /* ---------------------------- Order Validation ---------------------------- */
        $orderData = $orderRequest->validated();
        unset($orderData['deadlines']);

        /* -------------------------- Deadlines Validation -------------------------- */
        //todo:delete below
        $deadlines = $orderRequest['deadlines'] ?? [];
        // $deadlines = $orderRequest->input('deadlines', []);
        $orderRequest->validate(DeadlineRequest::deadlinesRules());

        /* ---------------------------- Order write  --------------------------------- */
        $order = Order::create($orderData);

        /* --------------------------- Deadlines write ------------------------------- */
        foreach ($deadlines as $deadline) {
            Deadline::create([
                'order_id' => $order->id,
                'part_quantity' => $deadline['part_quantity'],
                'due_date' => $deadline['due_date'],
                'description' => $deadline['description'] ?? null,
            ]);
        }

        return redirect()->route('dashboard.orders.index')->with('message', 'Order created successfully.');
    }
    
    //. ---------------------------------- show ---------------------------------- */
    public function show(Order $order)
    {
        $responses = $this->form();

        $order->load(['deadlines', 'createdBy']);
        $responses = array_merge($responses,[
            'order' => $order,
            'titles' => ['Order', 'show'],
        ]);


        return Inertia::render('dashboard/Orders/Form', $responses);
    }

    //. ---------------------------------- edit ---------------------------------- */
    public function edit(Order $order)
    {
        $responses = $this->form();

        $order->load(['deadlines', 'createdBy']);
        $responses = array_merge($responses,[
            'order' => $order,
            'titles' => ['Order', 'Edit'],
        ]);
        return Inertia::render('dashboard/Orders/Form', $responses);
    }

    //. --------------------------------- update --------------------------------- */
    public function update(OrderRequest $request, Order $order)
    {
        Gate::authorize('edit', $order);

        /* ---------------------------- Order Validation ---------------------------- */
        $orderData = $request->validated();
        $deadlines = $request['deadlines'] ?? [];
        unset($orderData['deadlines']);

        /* -------------------------- Deadlines Validation -------------------------- */
        $request->validate(DeadlineRequest::deadlinesRules());
        
        // به‌روزرسانی سفارش
        $order->update($orderData);

        // حذف ددلاین‌های قبلی
        $order->deadlines()->delete();

        // ایجاد ددلاین‌های جدید
        foreach ($deadlines as $deadline) {
            Deadline::create([
                'order_id' => $order->id,
                'part_quantity' => $deadline['part_quantity'],
                'due_date' => $deadline['due_date'],
                'description' => $deadline['description'] ?? null,
                // 'status' => $deadline['status'] ?? true,
            ]);
        }

        return redirect()->route('dashboard.orders.index')->with('message', 'Order updated successfully.');
    }

    //. --------------------------------- destroy -------------------------------- */
    public function destroy(Request $request)
    {
        dd('order@destroy');
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $parts = explode('.', $request->route()->getName());
        array_pop($parts);
        $thisIndexRoute = implode('.', $parts) . '.index';
        // dd($thisIndexRoute);
        $thisIndexUri = ucfirst(explode('.', $request->route()->getName())[0]) . '/Index';
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            // return redirect()->back()->with('error', 'لطفا یک یا چند رکورد را انتخاب کنید.');
            return Inertia::render($thisIndexUri, [
                'error' => 'please select one or more rows',
                'page' => $request->page,
                'perPage' => $request->perPage,
            ]);
        }

        $noDeletable = [];
        if (array_intersect($noDeletable, $ids)) {
            abort(
                403,
                "You can not delete records: "
                    .
                    implode(', ', array_intersect($noDeletable, $ids))
            );
        }


        Order::whereIn('id', $ids)->delete();

        return redirect()->route($thisIndexRoute, [
            'page' => $request->page,
            'perPage' => $request->perPage,
        ])->with('success', 'Selected users deleted successfully.');

    }
}




/*
$orderRequest->validate([
    'deadlines' => 'array',
    'deadlines.*.part_quantity' => 'nuulable|integer|min:1|max:10000',
    'deadlines.*.due_date'      => 'nuulable|date',
    'deadlines.*.description'   => 'nullable|string',
    'deadlines.*.status'        => 'nullable|boolean',
]);
$request->validate([
    'deadlines' => 'array',
    'deadlines' => 'array',
    'deadlines.*.part_quantity' => 'nullable|integer|min:1|max:10000',
    'deadlines.*.due_date'      => 'nullable|date',
    'deadlines.*.description'   => 'nullable|string',
    // 'deadlines.*.status'        => 'nullable|boolean',
]);



$searchableAttributes = ['description'];
$searchableRelations = ['product' => 'product_id'];
foreach ($searchableAttributes as $att) {
    $searchTxt = $searchables[$att];
    if (!empty($searchTxt)) {
        $query->whereLike($att,  '%' . $searchTxt . '%');
    }
}

foreach ($searchableRelations as $relation => $targetColumn) {
    $searchTxt = $searchables[$relation];
    if (!empty($searchTxt)) {
        $query->whereHas($relation, function ($q) use ($searchTxt, $targetColumn) {
            $q->whereLike($targetColumn, '%' . $searchTxt . '%');
        });
    }
}


foreach ($deadlines as $index => $deadline) {
    $rules = [
        'part_quantity' => 'required|integer|min:1',
        'due_date' => 'required|date',
        'description' => 'nullable|string',
    ];

    $validator = Validator::make($deadline, $rules);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }
}



$cookiePagination = json_decode($request?->cookie('pagination'), true);
$cookiePage = $cookiePagination['page'] ?? 1;
$cookiePerPage = $cookiePagination['perPage'] ?? 10;

$cookieCriteria = json_decode($request?->cookie('criteria'), true);
$criteria = [];
if ($cookieCriteria) {
    $decoded = json_decode($cookieCriteria, true);

    // unti XSS: strip_tags روی تمام مقادیر
    array_walk_recursive($decoded, function (&$val) {
        $val = strip_tags((string) $val);
    });

    $criteria = $decoded;
}


foreach ($deadlines as $index => $deadline) {
    $deadlineRequest = new DeadlineRequest(); //new instance
    $deadlineRequest->merge($deadline); // put data into the request (only this deadline)
    $rules = $deadlineRequest->rules();
    unset($rules['order_id']);
    $validator = \Validator::make($deadline, $rules);
    if ($validator->fails()) {
        throw ValidationException::withMessages([
            "deadlines.{$index}" => $validator->errors()->all()
        ]);
    }
}
*/