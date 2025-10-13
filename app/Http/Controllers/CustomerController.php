<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Traits\ControllerCommonMethods;
use App\Traits\CookieHelper;

use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use App\Models\Customer;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use Inertia\Inertia;

class CustomerController extends Controller  implements HasMiddleware
{
    use CookieHelper;
    use ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */
    
    protected $modelClass = \App\Models\Customer::class;
    protected $requestClass = \App\Http\Requests\CustomerRequest::class;
    protected $resourceClass = \App\Http\Resources\CustomerResource::class;

    protected $basePath = [
        'routes' => 'dashboard.customers.',
        'pages' => 'dashboard/Customers/',
    ];

    protected $title = [
        'icons' => ['group'],
        'texts' => ['Customers'],
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
        $showDD = $request->input('showDD');
        if ($showDD) dd($request->input('criteria'));
        $response[] = ['showDD' => $showDD];

        //. --------------------------------- initial -------------------------------- 
        $criteria = [
            'keywords' => [ //keywords is searches texts
                'description' => (string) $request->input('criteria.keywords.description'),
                'name' => (string) $request->input('criteria.keywords.name'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ----------------------- $filterables->options ----------------------------- 
        if (! $request->header('X-Inertia-Partial-Data')) {
            $filterables =  [
                'status' => [
                    'multiple' => true,
                    'options' => Customer::query()
                        ->select('status')
                        ->distinct()
                        ->orderBy('status')
                        ->pluck('status')
                        ->map(fn($name, $index) => ['label' => $name, 'value' => $index + 1])
                        ->toArray()
                ],
            ];

            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query --------------------------------- 
        $query = Customer::orderBy('id', 'asc');

        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description', '%' . $input . '%');
        }

        $input = $request->input('criteria.keywords.name');
        if (!empty($input)) {
            $query->whereLike('name', '%' . $input . '%');
        }

        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.status');
        if (!empty($input)) {
            $query->whereIn('status', $input);
        }


        //. --------------------------------- return --------------------------------- */

        $response = array_merge($response, [
            'columns' => CustomerResource::setColumns(),
            'records' => $this->paginator($request, $query),
            'criteria' => $criteria,
            'title' => $this->title,
        ]);
        // mydump($response);


        return Inertia::render('dashboard/Index', $response);
    }


    
}