<?php

namespace App\Traits;

use Illuminate\Http\Request;
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
    
    // protected function getRequestClass()
    // {
    //     $controllerName = class_basename($this);
    //     $requestClassName = Str::replaceLast('Controller', 'Request', $controllerName);
    //     return "App\\Http\\Requests\\{$requestClassName}";
    // }

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

        $data = $query->orderBy('id', 'asc') -> paginate($perPage);
        $resourcedData = $resourceClass::collection($data);

        return $resourcedData; //$this->paginator($request, $query, PlugResource::class);
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
        // dd($modelClass);
        $modelClass::whereKey($ids)->delete();

        return redirect()->route($this->basePath['routes'] . 'index', [
            'page' => $request->page,
            'perPage' => $request->perPage,
        ])->with('success', 'Selected records deleted successfully.');
    }
}
