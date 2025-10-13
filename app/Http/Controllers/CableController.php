<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ControllerCommonMethods;
use App\Traits\CookieHelper;
use App\Http\Resources\CableResource;
use Illuminate\Http\Request;
use App\Models\Cable;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;

class CableController extends Controller implements HasMiddleware
{
    use CookieHelper;
    use ControllerCommonMethods;

    /* -------------------------------------------------------------------------- */
    /*                                 variables                                   */
    /* -------------------------------------------------------------------------- */
    protected $modelClass = \App\Models\Cable::class;
    protected $requestClass = \App\Http\Requests\CableRequest::class;
    protected $resourceClass = \App\Http\Resources\CableResource::class;
    protected $basePath = [
        'routes' => 'dashboard.cables.',
        'pages' => 'dashboard/Cables/',
    ];
    protected $title = [
        'icons' => ['cable', 'electrical_services'],
        'texts' => ['Cables'],
    ];

    /* -------------------------------------------------------------------------- */
    /*                               middleware                                    */
    /* -------------------------------------------------------------------------- */
    public static function middleware(): array
    {
        return [
            'check-master-roles',
        ];
    }

    /* -------------------------------------------------------------------------- */
    /*                                  index                                      */
    /* -------------------------------------------------------------------------- */
    public function index(Request $request)
    {
        $response = [];
        $showDD = $request->input('showDD');
        if ($showDD) dd($request->input('criteria'));
        $response[] = ['showDD' => $showDD];

        /* ----------------------------- initial criteria --------------------------- */
        $criteria = [
            'keywords' => [
                'description' => (string) $request->input('criteria.keywords.description'),
                'name' => (string) $request->input('criteria.keywords.name'),
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

        /* ----------------------- filterables options ----------------------------- */
        if (!$request->header('X-Inertia-Partial-Data')) {
            $filterables = [
                'color' => [
                    'multiple' => true,
                    'options' => Cable::query()
                        ->select('color')
                        ->distinct()
                        ->orderBy('color')
                        ->pluck('color')
                        ->map(fn($name, $index) => ['label' => $name, 'value' => $index + 1])
                        ->toArray()
                ],
            ];
            $response['filterables'] = $filterables;
        }

        /* ------------------------------- query ---------------------------------- */
        $query = Cable::orderBy('id', 'asc');

        // Search keywords
        $input = $request->input('criteria.keywords.description');
        if (!empty($input)) {
            $query->whereLike('description', '%' . $input . '%');
        }
        $input = $request->input('criteria.keywords.name');
        if (!empty($input)) {
            $query->whereLike('name', '%' . $input . '%');
        }

        // Filter selections
        $input = $request->input('criteria.selections.color');
        if (!empty($input)) {
            $query->whereIn('color', $input);
        }

        /* ---------------------------- prepare response --------------------------- */
        $response = array_merge($response, [
            'columns' => CableResource::setColumns(),
            'records' => $this->paginator($request, $query),
            'criteria' => $criteria,
            'title' => $this->title,
        ]);

        return Inertia::render('dashboard/Index', $response);
    }

    /* -------------------------------------------------------------------------- */
    /*                                override Form                               */
    /* -------------------------------------------------------------------------- */
    protected function form()
    {
        $response ['title'] = $this->title;
        $response['colors'] = \App\Models\Cable::COLORS; // اضافه کردن رنگ‌ها
        return $response;
    }
}
