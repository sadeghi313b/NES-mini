<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlugRequest;
use App\Http\Resources\PlugResource;
use App\Models\Plug;
use App\Traits\CookieHelper;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class PlugController extends Controller  implements HasMiddleware
{
    use CookieHelper;
    protected $icon = ['power', 'electrical_services'];
    protected $title = ['Plugs'];

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


        //. ------------------------------- pagination ------------------------------- 
        // [Pagination]
        $paginationCookie = $this->getPaginationCookie($request);
        $response['paginationCookie'] = $paginationCookie;

        $rawPerPage = $paginationCookie['perPage'] ?: $request->input('perPage', 5);
        $perPage = is_numeric($rawPerPage) ? (int) $rawPerPage : 5;
        if ($perPage === 0) {
            $perPage = $query->count() ?: 1;
        }

        $response['getQuery'] = $query->get()->toArray();
        $plugs = $query->paginate($perPage);
        $resourcedData = PlugResource::collection($plugs); // [/]

        //. --------------------------------- return --------------------------------- */

        $response = array_merge($response, [
            'columns' => PlugResource::setColumns(),
            'records' => $resourcedData,
            'criteria' => $criteria,
            'icon' => $this->icon,
            'title' => $this->title,
        ]);
        // mydump($response);


        return Inertia::render('dashboard/Index', $response);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    form                                    */
    //. -------------------------------------------------------------------------- */
    protected function form()
    {
        // Gate::authorize('form', Plug::class);

        return [
            'icon' => $this->icon,
            'title' => $this->title,
        ];
    }


    //. -------------------------------------------------------------------------- */
    //.                                   create                                   */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $responses = $this->form();
        $responses = array_merge($responses, []);

        return Inertia::render('dashboard/Plugs/Form', $responses);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(PlugRequest $request)
    {
        $plugData = $request->validated();

        Plug::create($plugData);

        return redirect()->route('dashboard.plugs.index')->with('message', 'Plug created successfully.');
    }

    //. -------------------------------------------------------------------------- */
    //.                                    show                                    */
    //. -------------------------------------------------------------------------- */
    public function show(Plug $plug)
    {
        $responses = $this->form();

        if ($plug && $plug->relationLoaded('createdBy')) {
            $plug->load(['createdBy']);
        }
        //or/ $plug?->load($plug->relationLoaded('createdBy') ? ['createdBy'] : []);
        $responses = array_merge($responses, [
            'plug' => $plug,
        ]);

        return Inertia::render('dashboard/Plugs/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit(Plug $plug)
    {
        $responses = $this->form();

        if ($plug && $plug->relationLoaded('createdBy')) {
            $plug->load(['createdBy']);
        }
        $responses = array_merge($responses, [
            'plug' => $plug,
        ]);
        return Inertia::render('dashboard/Plugs/Form', $responses);
    }

    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update(PlugRequest $request, Plug $plug)
    {


        $plugData = $request->validated();

        $plug->update($plugData);

        return redirect()->route('dashboard.plugs.index')->with('message', 'Plug updated successfully.');
    }

    //. -------------------------------------------------------------------------- */
    //.                                   destroy                                  */
    //. -------------------------------------------------------------------------- */
    public function destroy(Plug $plug)
    {
        $plug->delete();

        return redirect()->route('dashboard.plugs.index')
            ->with('success', 'Plug deleted successfully.');
    }


    //. -------------------------------------------------------------------------- */
    //.                                 bulkDestroy                                */
    //. -------------------------------------------------------------------------- */
    public function bulkDestroy(Request $request)
    {
        $parts = explode('.', $request->route()->getName());
        array_pop($parts);
        $thisIndexRoute = implode('.', $parts) . '.index';
        $thisIndexUri = ucfirst(explode('.', $request->route()->getName())[0]) . '/Index';
        $ids = $request->input('ids', []);
        if (empty($ids)) {
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

        Plug::whereIn('id', $ids)->delete();

        return redirect()->route($thisIndexRoute, [
            'page' => $request->page,
            'perPage' => $request->perPage,
        ])->with('success', 'Selected plugs deleted successfully.');
    }
}
