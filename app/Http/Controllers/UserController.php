<?php

namespace App\Http\Controllers;
use App\Helpers\QColumnsHelper;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // [Query]
        $query = User::query();
        $relateds = [
            'phones',
            'roles',
            'employee:id,user_id'
        ];
        $query->with($relateds); // [/]

        // [Filters]
        if ($request->filled('search')) {
            // filled(): // Check if search parameter exists and is not empty to avoid errors
            $query->where(
                'first_name',
                'like',
                '%' . $request->search . '%'
            )->orWhere(
                'last_name',
                'like',
                '%' . $request->search . '%'
            )->orWhere(
                'email',
                'like',
                '%' . $request->search . '%'
            )
            ;
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } // [/]
        
        // [Pagination]
        $paginator = $query->paginate(25);
        $records = $paginator->getCollection(); // [/]

        // [Quasar Columns]
        $fields = [
            'id',
            'gender',
            'first_name',
            'last_name',
            'actions',
            'status',
            'email',
            'phones.phone_no as phone',
            'roles.name as roles',
            'employee.id as is_employee',
            'description',
            'created_by',
            'created_at',
            'updated_at',
            'deleted_at',
        ];
        $columns = array_values(QColumnsHelper::QColumns($fields)); // [/]

        // [Quasar Rows]
        $rows = [];
        foreach ($records as $record) {
            $record = $record->toArray();
            $row = [];

            foreach ($fields as $field) {
                $fieldInBackend = subString($field,' as ',0);
                $fieldInFront = subString($field,' as ',1);
                $field = $fieldInBackend;
                if (str_contains($field, '.')) {
                    [$mainField, $nestedField] = array_pad(explode('.', $field, 2), 2, null);
                    $cellValue =  collect($record[$mainField])->pluck($nestedField)
                        ->filter() // Remove null/empty values
                        ->implode(', '); //return string
                    $row[$fieldInFront] = $cellValue;
                } else {
                    $row[$field] = $record[$field] ?? null;
                }
            }
            $rows[] = $row;
        }  // [/]

        // [Return]
        return Inertia::render('Users/Index', [
            'users' => $paginator,
            'rows' => $rows,
            'filters' => $request->only('search', 'status'),
            'columns' => $columns,
            'pagination' => [
                'current_page' =>  $paginator->currentPage(),
                'last_page' =>  $paginator->lastPage(),
                'per_page' =>  $paginator->perPage(),
                'total' =>  $paginator->total(),
            ],
        ]); // [/]
    }

    public function create()
    {
        return inertia('Users/Create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        User::create($data);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function activate(User $user)
    {
        $user->status = 'active'; // Set user status to active
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User activated successfully.');
    }

    public function deactivate(User $user)
    {
        $user->status = 'inactive'; // Set user status to hold
        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User deactivated successfully.');
    }
}

/*
// [fields]
$fields = [];
$fields = array_map(
    function ($key) {
        return Str::afterLast($key, '.');
    },
    array_keys($flat->toArray())
);
$fields = array_unique($fields);
$columns = array_values(QColumnsHelper::QColumns($fields)); // [/]

$a = array_merge($record->getAttributes(), $record->getRelations());
----------------------------------------------------------------------- 
$value = data_get ($record, $field);
if ( !$value ) {
    $rowObj -> {$field} = null;
    $row[$field] = null;
    continue;
}
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 
----------------------------------------------------------------------- 


*/