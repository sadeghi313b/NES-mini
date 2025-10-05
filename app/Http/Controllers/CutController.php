<?php

namespace App\Http\Controllers;

use App\Http\Requests\CutRequest;
use App\Http\Resources\CutResource;
use App\Models\Cut;
use App\Models\Month;
use App\Models\Order;
use App\Traits\CookieHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CutController extends Controller
{
    use CookieHelper;
    protected $icon = 'content_cut';
    protected $title = 'Cuts';

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
            ],
            'selections' => $request->input('criteria.selections') ?: (object) [],
        ];

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
        $cuts = $query->paginate($perPage);
        $resourcedData = CutResource::collection($cuts); // [/]

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

        //. --------------------------------- return --------------------------------- */

        $response = array_merge($response, [
            'columns' => CutResource::setColumns(),
            'records' => $resourcedData,
            'criteria' => $criteria,
            'icon' => $this -> icon,
            'title' => $this -> title,
        ]);
        // mydump($response);


        return Inertia::render('dashboard/Index', $response);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    form                                    */
    //. -------------------------------------------------------------------------- */
    protected function form()
    {
        // Gate::authorize('form', Cut::class);

        $orders = Order::select('id', 'product_id')->get();
        return [
            'orders' => $orders,
            'icon' => $this -> icon,
            'title' => $this -> title,
        ];
    }

    //. --------------------------------- create --------------------------------- 
    public function create()
    {
        $responses = $this->form();
        $responses = array_merge($responses, []);

        return Inertia::render('dashboard/Cuts/Form', $responses);
    }

    //. ---------------------------------- store --------------------------------- 
    public function store(CutRequest $request)
    {
        $cutData = $request->validated();

        Cut::create($cutData);

        return redirect()->route('dashboard.cuts.index')->with('message', 'Cut created successfully.');
    }

    //. ---------------------------------- show ---------------------------------- 
    public function show(Cut $cut)
    {
        $responses = $this->form();

        $cut->load(['createdBy']);
        $responses = array_merge($responses, [
            'cut' => $cut,
        ]);

        return Inertia::render('dashboard/Cuts/Form', $responses);
    }

    //. ---------------------------------- edit ---------------------------------- 
    public function edit(Cut $cut)
    {
        $responses = $this->form();

        $cut->load(['order', 'createdBy']);
        $responses = array_merge($responses, [
            'cut' => $cut,
        ]);
        return Inertia::render('dashboard/Cuts/Form', $responses);
    }

    //. --------------------------------- update --------------------------------- 
    public function update(CutRequest $request, Cut $cut)
    {
        

        $cutData = $request->validated();

        $cut->update($cutData);

        return redirect()->route('dashboard.cuts.index')->with('message', 'Cut updated successfully.');
    }

    //. --------------------------------- destroy -------------------------------- 
    public function destroy(Cut $cut)
    {
        $cut->delete();

        return redirect()->route('dashboard.cuts.index')
            ->with('success', 'Cut deleted successfully.');
    }

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

        Cut::whereIn('id', $ids)->delete();

        return redirect()->route($thisIndexRoute, [
            'page' => $request->page,
            'perPage' => $request->perPage,
        ])->with('success', 'Selected cuts deleted successfully.');
    }
}
