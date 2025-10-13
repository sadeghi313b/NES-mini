<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Traits\ControllerCommonMethods;
use App\Traits\CookieHelper;

use App\Http\Resources\PlugResource;
use Illuminate\Http\Request;
use App\Models\Plug;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

use Inertia\Inertia;

class PlugController extends Controller  implements HasMiddleware
{
    use CookieHelper;
    use ControllerCommonMethods;

    //. -------------------------------------------------------------------------- */
    //.                                  variables                                 */
    //. -------------------------------------------------------------------------- */

    protected $modelClass = \App\Models\Plug::class;
    protected $requestClass = \App\Http\Requests\PlugRequest::class;
    protected $resourceClass = \App\Http\Resources\PlugResource::class;

    protected $basePath = [
        'routes' => 'dashboard.plugs.',
        'pages' => 'dashboard/Plugs/',
    ];

    protected $title = [
        'icons' => ['power', 'electrical_services'],
        'texts' => ['Plugs'],
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
                'type' => (string) $request->input('criteria.keywords.type'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        //. ----------------------- $filterables->options ----------------------------- 
        if (! $request->header('X-Inertia-Partial-Data')) {
            $filterables =  [
                'type' => [
                    'multiple' => true,
                    'options' => Plug::query()
                        ->select('type')
                        ->distinct()
                        ->orderBy('type')
                        ->pluck('type')
                        ->map(fn($name, $index) => ['label' => $name, 'value' => $index + 1])
                        ->toArray()
                ],
                'tag' => [
                    'multiple' => true,
                    'options' => Plug::query()
                        ->select('tag')
                        ->distinct()
                        ->orderBy('tag')
                        ->pluck('tag')
                        ->map(fn($name, $index) => ['label' => $name, 'value' => $index + 1])
                        ->toArray()
                ],
            ];
            $response['filterables'] = $filterables;
        }

        //. ---------------------------------- query --------------------------------- 
        $query = Plug::orderBy('id', 'asc');

        /* --------------------------- query search texts -------------------------- */
        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description', '%' . $input . '%');
        }

        $input = $request->input('criteria.keywords.type');
        if (!empty($input)) {
            $query->whereLike('type', '%' . $input . '%');
        }

        /* ------------------------ query filter selections ------------------------- */
        $input = $request->input('criteria.selections.type');
        if (!empty($input)) {
            $query->whereIn('type', $input);
        }

        $input = $request->input('criteria.selections.tag');
        if (!empty($input)) {
            $query->whereIn('tag', $input);
        }


        //. --------------------------------- return --------------------------------- */

        $response = array_merge($response, [
            'columns' => PlugResource::setColumns(),
            'records' => $this->paginator($request, $query),
            'criteria' => $criteria,
            'title' => $this->title,
        ]);
        // mydump($response);


        return Inertia::render('dashboard/Index', $response);
    }


    
}
