<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;

use App\Traits\CookieHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

use App\Enums\OrderStatus;
use App\Models\Deadline;
use App\Http\Requests\DeadlineRequest;
use App\Models\Order;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Month;
use App\Models\Product;




class OrderController extends Controller  implements HasMiddleware
{
    use CookieHelper;
    use \App\Traits\ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $modelClass = \App\Models\Order::class;
    protected $requestClass = \App\Http\Requests\OrderRequest::class;
    protected $resourceClass = \App\Http\Resources\OrderResource::class;

    protected $title = [
        'icons' => ['shopping_cart', 'add_shopping_cart'],
        'texts' => ['Orders'],
    ];

    protected $statusOptions; //will get from Enum Class in constructor


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
    public function __construct()
    {
        $this->statusOptions = collect(OrderStatus::cases())->sort()
            ->map(fn($status) => ['label' => $status->label(), 'value' => $status->value])
            ->values();
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
                'product' => (string) $request->input('criteria.keywords.name'),
                'description' => (string) $request->input('criteria.keywords.description'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ----------------------- $filterables->options ----------------------------- 
        if (! $request->header('X-Inertia-Partial-Data')) {
            $filterables =  [
                'status' => [
                    'multiple' => true,
                    'options' => $this->statusOptions
                ],
                'product' => [
                    'multiple' => true,
                    'options' => \App\Models\Product::query()
                        ->select('id')
                        ->orderBy('id')
                        ->pluck('id')
                        ->map(fn($id) => ['label' => $id, 'value' => $id])
                        ->toArray()
                ],
                'month' => [
                    'multiple' => true,
                    'options' => \App\Models\Month::query()
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->map(fn($name, $id) => ['label' => $name, 'value' => $id])
                        ->values()
                        ->toArray()
                ],
                'seen' => [
                    'multiple' => false,
                    'options' => ['Seen', 'unSeen'],
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query --------------------------------- 
        $related = ['product', 'month', 'createdBy'];
        $query = Order::with($related)->orderBy('id', 'asc');

        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.product');
        if (!empty($input)) {
            $query->whereHas('product', function ($q) use ($input) {
                $q->whereLike('product_id', '%' . $input . '%');
            });
        }

        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description', '%' . $input . '%');
        }

        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }

        $input = $request->input('criteria.selections.product');
        if (!empty($input)) {
            $query->whereIn('product_id', $input);
        }

        $input = $request->input('criteria.selections.month');
        if (!empty($input)) {
            $query->whereIn('month_id', $input);
        }

        $input = $request->input('criteria.selections.seen');
        if (!empty($input)) {
            $query->where('seen', strtolower($input) === 'seen');
        }


        //. --------------------------------- return --------------------------------- */

        $response = array_merge($response, [
            'columns' => OrderResource::setColumns(),
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
        $products = Product::select('id', 'name')->get();
        $productId_options = Product::query()
            ->orderBy('id') //->take(5)
            ->get(['id'])
            ->map(fn($p) => ['value' => $p->id, 'label' => (string) $p->id])
            ->toArray();
        $months = Month::select('id', 'name')->get();
        return [
            'title' => $this->title,
            'statusOptions' => $this->statusOptions,
            'months' => $months,
            'products' => $productId_options,
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                    show                                    */
    //. -------------------------------------------------------------------------- */
    public function show(Order $order)
    {
        $response = $this->form();
        $order->load(['createdBy', 'deadlines']);

        $response = array_merge($response, [
            'record' => $order,
        ]);

        return Inertia::render($this->basePath['pages'] . 'Form', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   create                                   */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $responses = $this->form();

        return Inertia::render('dashboard/Orders/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(OrderRequest $orderRequest)
    {
        /* ---------------------------- Order Validation ---------------------------- */
        // $orderData['created_by'] = auth()->id();
        $orderData = $orderRequest->validated();
        unset($orderData['deadlines']);

        /* -------------------------- Deadlines Validation -------------------------- */
        $deadlines = $orderRequest['deadlines'] ?? [];
        $orderRequest->validate(DeadlineRequest::deadlinesRules());

        /* ---------------------------- Order write  --------------------------------- */
        $order = Order::create($orderData);

        /* --------------------------- Deadlines write ------------------------------- */
        if (count($deadlines)) {
            foreach ($deadlines as $deadline) {
                if (!empty(array_filter($deadline))) {
                    Deadline::create([
                        'order_id' => $order->id,
                        'promised_quantity' => $deadline['promised_quantity'],
                        'promised_date' => $deadline['promised_date'],
                        'description' => $deadline['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard.orders.index')->with('message', 'Order created successfully.');
    }


    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit(Order $order)
    {
        $responses = $this->form();

        $order->load(['deadlines', 'createdBy']);
        $responses = array_merge($responses, [
            'record' => $order,
        ]);
        return Inertia::render('dashboard/Orders/Form', $responses);
    }


    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update(OrderRequest $request, Order $order)
    {
        Gate::authorize('edit', $order);

        $deadlines = $request['deadlines'] ?? [];

        /* ---------------------------- Order Validation ---------------------------- */
        $orderData = $request->validated();
        unset($orderData['deadlines']);

        /* -------------------------- Deadlines Validation -------------------------- */
        $request->validate(DeadlineRequest::deadlinesRules());

        /* ------------------------------ update order ------------------------------ */
        // dd($orderData);
        $order->update($orderData);

        /* ---------------------------- update deadlines ---------------------------- */
        // $order->deadlines()->delete();

        // ایجاد ددلاین‌های جدید
        if (count($deadlines)) {
            foreach ($deadlines as $deadline) {
                if (!empty(array_filter($deadline))) {
                    Deadline::create([
                        'order_id' => $order->id,
                        'promised_quantity' => $deadline['promised_quantity'],
                        'promised_date' => $deadline['promised_date'],
                        'description' => $deadline['description'] ?? null,
                    ]);
                }
            }
        }

        /* --------------------------------- return --------------------------------- */
        return redirect()->route('dashboard.orders.index')->with('message', 'Order updated successfully.');
    }
}
