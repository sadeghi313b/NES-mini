<?php

namespace App\Traits;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Inertia\Inertia;

trait ControllerCommonMethods
{
    protected function getModelClass()
    {
        $controllerName = class_basename($this);
        $modelName = Str::replaceLast('Controller', '', $controllerName);
        return "App\\Models\\{$modelName}";
    }
    
    protected function getRequestClass()
    {
        $controllerName = class_basename($this);
        $requestClassName = Str::replaceLast('Controller', 'Request', $controllerName);
        return "App\\Http\\Requests\\{$requestClassName}";
    }

    protected function getResourceClass()
    {
        $controllerName = class_basename($this);
        $resourceClassName = Str::replaceLast('Controller', 'Resource', $controllerName);
        return "App\\Http\\Resources\\{$resourceClassName}";
    }

    //. -------------------------------------------------------------------------- */
    //.                                 pagination                                 */
    //. -------------------------------------------------------------------------- */
    protected function paginator(Request $request, $query)
    {
        $resourceClass = $this->resourceClass ?? $this->getResourceClass();
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
    public function store()
    {
        $modelClass   = $this->modelClass ?? $this->getModelClass();
        $requestClass = $this->requestClass ?? $this->getRequestClass();

        // Dynamically instantiate and validate request
        /** @var \Illuminate\Foundation\Http\FormRequest $request */
        $request = App::make($requestClass);
        $request = $request->setContainer(app())->setRedirector(app('redirect'));

        $data = $request->validated();

        $modelClass::create($data);

        $successMessage = Str::singular(class_basename($modelClass)) . ' created successfully.';

        return redirect()
            ->route($this->basePath['routes'] . 'index')
            ->with('message', $successMessage);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    show                                    */
    //. -------------------------------------------------------------------------- */
    public function show($id)
    {
        $response = $this->form();

        // Load relation if needed
        // if ($id && $id?->relationLoaded('createdBy')) {
        //     $id->load(['createdBy']);
        // }


        $modelClass = $this->modelClass ?? $this->getModelClass();
        $response = array_merge($response, [
            'record' => $modelClass::findOrFail($id),
        ]);

        return Inertia::render($this->basePath['pages'] . 'Form', $response);
    }


    //. -------------------------------------------------------------------------- */
    //.                                    edit                                    */
    //. -------------------------------------------------------------------------- */
    public function edit($id)
    {
        $modelClass = $this->modelClass ?? $this->getModelClass();
        $record = $modelClass::findOrFail($id);

        $response = $this->form();

        if ($record && $record->relationLoaded('createdBy')) {
            $record->load(['createdBy']);
        }

        $response = array_merge($response, [
            'record' => $record,
        ]);

        return Inertia::render($this->basePath['pages'] . 'Form', $response);
    }


    //. -------------------------------------------------------------------------- */
    //.                                   update                                   */
    //. -------------------------------------------------------------------------- */
    public function update($id)
    {
        $modelClass   = $this->modelClass ?? $this->getModelClass();
        $requestClass = $this->requestClass ?? $this->getRequestClass(); //eg: \App\Http\Requests\PlugRequest

        // make an instance from related Request
        $request = App::make($requestClass);
        $request = $request->setContainer(app())
            ->setRedirector(app('redirect'));

        $data = $request->validated();

        $record = $modelClass::findOrFail($id);
        $record->update($data);

        return redirect()
            ->route($this->basePath['routes'] . 'index')
            ->with('message', class_basename($modelClass) . ' updated successfully.');
    }


    //. -------------------------------------------------------------------------- */
    //.                                   destroy                                  */
    //. -------------------------------------------------------------------------- */
    public function destroy($id)
    {
        $modelClass = $this->modelClass ?? $this->getModelClass();
        $record = $modelClass::findOrFail($id);

        $record->delete();

        return redirect()
            ->route($this->basePath['routes'] . 'index')
            ->with('message', class_basename($modelClass) . ' deleted successfully.');
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
        $modelClass::whereKey($ids)->delete();

        return redirect()->route($this->basePath['routes'] . 'index', [
            'page' => $request->page,
            'perPage' => $request->perPage,
        ])->with('success', 'Selected records deleted successfully.');
    }
}
