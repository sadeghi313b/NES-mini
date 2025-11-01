<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionRequest;
use App\Http\Resources\ProductionResource;
use App\Models\Product;
use App\Models\Production;
use App\Traits\CookieHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ProductionController extends Controller implements HasMiddleware
{
    use CookieHelper;
    use \App\Traits\ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $modelClass = \App\Models\Production::class;
    protected $requestClass = \App\Http\Requests\ProductionRequest::class;
    protected $resourceClass = \App\Http\Resources\ProductionResource::class;

    protected $title = [
        'icons' => ['factory', 'build'],
        'texts' => ['Productions'],
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

        //. ----------------------------- criteria setup ---------------------------- */
        $criteria = [
            'keywords' => [
                'id' => (string) $request->input('criteria.keywords.id'),
                'product' => (string) $request->input('criteria.keywords.product'),
                'description' => (string) $request->input('criteria.keywords.description'),
                'order' => (string) $request->input('criteria.keywords.order'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ---------------------- setup filterables (first load) ------------------- */
        if (!$request->header('X-Inertia-Partial-Data')) {
            $filterables = [
                'product' => [
                    'multiple' => true,
                    'options' => Product::query()
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->map(fn($p) => ['label' => $p->name, 'value' => $p->id])
                        ->toArray(),
                ],
                'status' => [
                    'multiple' => true,
                    'options' => [
                        ['label' => 'Active', 'value' => 1],
                        ['label' => 'Inactive', 'value' => 0],
                    ],
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query -------------------------------- */
        $related = ['product', 'creator', 'orders'];
        $query = Production::with($related)->orderBy('date', 'desc');

        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.id');
        if (!empty($input)) {
            $query->whereLike('id',  "%{$input}%");
        }

        $input = $request->input('criteria.keywords.product');
        if (!empty($input)) {
            $query->whereLike('product_id', "%{$input}%");
        }

        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description', "%{$input}%");
        }

        $input = $request->input('criteria.keywords.order');
        if (!empty($input)) {
            $query->whereHas('orders', function ($q) use ($input) {
                $q->where('orders.id', (int) $input);
            });
        }


        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.product');
        if (!empty($input)) {
            $query->whereIn('product_id', $input);
        }

        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }

        //. --------------------------------- return -------------------------------- */
        $response = array_merge($response, [
            'columns' => ProductionResource::setColumns(),
            'records' => $this->paginator($request, $query),
            'criteria' => $criteria,
            'title' => $this->title,
        ]);

        return Inertia::render('dashboard/Index', $response);
    }


    //. -------------------------------------------------------------------------- */
    //.                                 validation                                 */
    //. -------------------------------------------------------------------------- */
    protected function validateProductionRelations(array $data): array
    {
        $errors = [];

        // 1) Ensure sum of orders.quantity equals form.quantity
        $sumOrdersQty = collect($data['orders'])->sum('quantity');
        if ($sumOrdersQty != $data['quantity']) {
            $errors['orders'] = 'Sum of all order quantities must equal total production quantity.';
        }

        // 2) Ensure all (month_name, order_id, product_id) combinations exist in orders table
        foreach ($data['orders'] as $index => $order) {
            $exists = \App\Models\Order::where('id', $order['order_id'])
                ->where('product_id', $order['product_id'])
                ->whereHas('month', function ($q) use ($order) {
                    $q->where('name', $order['month_name']);
                })
                ->exists();

            if (!$exists) {
                $errors["orders.$index"] = "No matching order found for month '{$order['month_name']}', product ID {$order['product_id']}, and order ID {$order['order_id']}.";
            }
        }

        return $errors;
    }


    /**
     * Check if total allocated quantity across all productions for the same product/order
     * exceeds the available order quantity.
     * This does not block saving, only warns the user.
     */
    protected function checkProductionOverAllocation(int $productId, array $orders): ?string
    {
        foreach ($orders as $order) {
            // Get total already assigned quantity for this product/order
            $allocated = \DB::table('order_production')
                ->join('productions', 'productions.id', '=', 'order_production.production_id')
                ->where('productions.product_id', $productId)
                ->where('order_production.order_id', $order['order_id'])
                ->sum('order_production.quantity');

            // Get total available from orders table
            $orderTotal = \App\Models\Order::where('id', $order['order_id'])->value('quantity');

            if ($allocated > $orderTotal) {
                return "Warning: Total allocated quantity for Order ID {$order['order_id']} and Product ID {$productId} exceeds the available order quantity.";
            }
        }

        return null;
    }


    //. -------------------------------------------------------------------------- */
    //.                                    form                                    */
    //. -------------------------------------------------------------------------- */
    protected function form()
    {
        $products = Product::select('id')->get();
        $orders = \App\Models\Order::with('month:id,name') // eager load relation
            ->select('id', 'month_id', 'product_id', 'quantity')
            ->orderBy('id')
            ->get()
            ->makeHidden(['month_id']);

        return [
            'title' => $this->title,
            'products' => $products,
            'orders' => $orders,
        ];
    }

    //. -------------------------------------------------------------------------- */
    //.                                   dataOf                                   */
    //. -------------------------------------------------------------------------- */
    protected function dataOf(Production $production)
    {
        // Select only certain attributes from Production itself
        $data = $production->only([
            'id',
            'date',
            'quantity',
            'product_id',
            'description',
            'status'
        ]);

        // Attach relations manually (filtered)
        $data['orders'] = $production->orders->map(function ($order) {
            return [
                'id' => $order->id,
                'month_name' => $order->month?->name,
                'product_id' => $order->product_id,
                'quantity' => $order->pivot->quantity,
            ];
        });

        // If you need total count of related orders
        $data['orders_count'] = $production->orders->count();

        return $data;
    }

    //. -------------------------------------------------------------------------- */
    //.                                    show                                    */
    //. -------------------------------------------------------------------------- */
    public function show(Production $production)
    {
        $response = array_merge($this->form(), [
            'record' => $this->dataOf($production),
        ]);

        return Inertia::render('dashboard/Productions/Form', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   create                                   */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $response = $this->form();
        return Inertia::render('dashboard/Productions/Form', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(Request $request)
    {
        // -------------------------------------------
        // 1) Basic validation of main Production fields
        // -------------------------------------------
        $data = $request->validate([
            'date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'tags' => 'nullable|array',
            'orders' => 'required|array|min:1',
            'orders.*.order_id' => 'required|exists:orders,id',
            'orders.*.quantity' => 'required|numeric|min:1',
            'orders.*.month_name' => 'required|string',
            'orders.*.product_id' => 'required|exists:products,id',
        ]);

        // -------------------------------------------
        // 2) Run local custom validations
        // -------------------------------------------
        // $errors = $this->validateProductionRelations($data);

        if (!empty($errors)) {
            // Return back to the same page with validation errors
            return back()->withErrors($errors)->withInput();
        }

        // -------------------------------------------
        // 3) Store production main record
        // -------------------------------------------
        $production = new Production();
        $production->date = $data['date'];
        $production->product_id = $data['product_id'];
        $production->quantity = $data['quantity'];
        $production->description = $data['description'] ?? null;
        $production->status = $data['status'] ?? true;
        $production->tags = $data['tags'] ?? [];
        $production->created_by = auth()->id();
        $production->save();

        // -------------------------------------------
        // 4) Attach related orders in pivot table
        // -------------------------------------------
        foreach ($data['orders'] as $order) {
            \DB::table('order_production')->insert([
                'production_id' => $production->id,
                'order_id' => $order['order_id'],
                'quantity' => $order['quantity'],
            ]);
        }

        // -------------------------------------------
        // 5) Post-save check (non-blocking warning)
        // -------------------------------------------
        // $warning = $this->checkProductionOverAllocation($data['product_id'], $data['orders']);

        // Redirect with optional warning message
        $redirect = redirect()->route('dashboard.productions.index');
        if ($warning) {
            $redirect->with('warning', $warning);
        }

        return $redirect->with('success', 'Production created successfully.');
    }


    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit(Production $production)
    {
        $response = array_merge($this->form(), [
            'record' => $this->dataOf($production),
        ]);

        return Inertia::render('dashboard/Productions/Form', $response);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update(ProductionRequest $request, Production $production)
    {
        $data = $request->validated();

        // --------------------------------------
        // Update main production record
        // --------------------------------------
        $production->update($data);

        // --------------------------------------
        // Update order_production pivot table
        // --------------------------------------
        if (isset($data['orders']) && is_array($data['orders'])) {
            foreach ($data['orders'] as $order) {
                // Update existing pivot record if pivot_id exists
                if (!empty($order['pivot_id'])) {
                    \DB::table('order_production')
                        ->where('id', $order['pivot_id'])
                        ->update([
                            'production_id' => $production->id,
                            'order_id'      => $order['id'],
                            'quantity'      => $order['quantity'],
                            'updated_at'    => now(),
                        ]);
                } else {
                    // Otherwise insert a new pivot record
                    \DB::table('order_production')->insert([
                        'production_id' => $production->id,
                        'order_id'      => $order['id'],
                        'quantity'      => $order['quantity'],
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                }
            }

            // Optional cleanup: remove pivot records no longer present
            $currentPivotIds = collect($data['orders'])->pluck('pivot_id')->filter()->toArray();

            \DB::table('order_production')
                ->where('production_id', $production->id)
                ->whereNotIn('id', $currentPivotIds)
                ->delete();
        }

        return redirect()->route('dashboard.productions.index')
            ->with('message', 'Production updated successfully.');
    }
}
