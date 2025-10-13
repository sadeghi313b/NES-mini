<?php

namespace App\Http\Controllers;
use App\Enums\OrderStatus;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

use App\Models\Order;
use App\Traits\ControllerCommonMethods;
use App\Traits\CookieHelper;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class OrderController extends Controller  implements HasMiddleware
{
    use CookieHelper;
    use ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $modelClass = \App\Models\Order::class;
    protected $requestClass = \App\Http\Requests\OrderRequest::class;
    protected $resourceClass = \App\Http\Resources\OrderResource::class;

    protected $basePath = [
        'routes' => 'dashboard.orders.',
        'pages' => 'dashboard/Orders/',
    ];

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


    
}