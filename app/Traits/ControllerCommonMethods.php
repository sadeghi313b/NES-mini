<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

trait ControllerCommonMethods
{
    protected function getModelClass()
    {
        $controllerName = class_basename($this);
        $modelName = Str::replaceLast('Controller', '', $controllerName);
        return "App\\Models\\{$modelName}";
    }

    //. -------------------------------------------------------------------------- */
    //.                                 pagination                                 */
    //. -------------------------------------------------------------------------- */
    protected function paginator(Request $request, $query, $resourceClass)
    {
        $paginationCookie = $this->getPaginationCookie($request);

        $rawPerPage = $paginationCookie['perPage'] ?: $request->input('perPage', 5);
        $perPage = is_numeric($rawPerPage) ? (int) $rawPerPage : 5;
        if ($perPage === 0) {
            $perPage = $query->count() ?: 1;
        }

        $data = $query->paginate($perPage);
        $resourcedData = $resourceClass::collection($data);

        return $resourcedData; //$this->paginator($request, $query, PlugResource::class);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    form                                    */
    //. -------------------------------------------------------------------------- */
    protected function form()
    {
        return [
            'title' => $this->title,
        ];
    }


    //. -------------------------------------------------------------------------- */
    //.                                   create                                   */
    //. -------------------------------------------------------------------------- */
    public function create()
    {
        $response = $this->form();
        $response = array_merge($response, []);

        return Inertia::render($this->basePath['pages'] . 'Form', $response);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    store                                   */
    //. -------------------------------------------------------------------------- */
    public function store(Request $request)
    {
        $data = $request->validated();

        $modelClass = $this->modelClass ?? $this->getModelClass();
        $modelClass::create($data);

        $successMessage = Str::singular(class_basename($modelClass)) . ' created successfully';
        return redirect()->route($this->basePath['routes'] . 'index')->with('success', $successMessage);
    }


    //. -------------------------------------------------------------------------- */
    //.                                 bulkDestroy                                */
    //. -------------------------------------------------------------------------- */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);

        /* ---------------------------------- error --------------------------------- */
        if (empty($ids)) {
            return Inertia::render('dashboard/Index', [
                'error' => 'please select one or more rows',
                'page' => $request->page,
                'perPage' => $request->perPage,
            ]);
        }

        $noDeletable = $this->noDeletable ?? [];
        if (array_intersect($noDeletable, $ids)) {
            abort(
                403,
                "You can not delete records: "
                    .
                    implode(', ', array_intersect($noDeletable, $ids))
            );
        }

        /* --------------------------------- success -------------------------------- */
        $modelClass = $this->modelClass ?? $this->getModelClass();
        $modelClass::whereIn('id', $ids)->delete();

        return redirect()->route($this->basePath['routes'] . 'index', [
            'page' => $request->page,
            'perPage' => $request->perPage,
        ])->with('success', 'Selected records deleted successfully.');
    }
}
