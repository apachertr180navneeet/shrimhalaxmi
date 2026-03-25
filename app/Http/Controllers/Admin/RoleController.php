<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:role-list')->only(['index']);
        $this->middleware('permission:role-create')->only(['create','store']);
        $this->middleware('permission:role-edit')->only(['edit','update']);
        $this->middleware('permission:role-delete')->only(['destroy']);
    }

    // ✅ List Roles
    public function index()
    {
        $roles = Role::with('permissions')->latest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    // ✅ Create Form
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.roles.create', compact('permissions'));
    }

    // ✅ Store Role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    // ✅ Edit Form
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    // ✅ Update Role
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:roles,name,' . $id
        ]);

        $role->update(['name' => $request->name]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    // ✅ Delete Role
    public function destroy($id)
    {
        Role::findOrFail($id)->delete();

        return back()->with('success', 'Role deleted successfully');
    }

    public function getAll(Request $request)
    {
        $query = Role::with('permissions')->latest();

        if ($request->filled('search_value')) {
            $query->where('name', 'like', '%' . $request->search_value . '%');
        }

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('permissions', function ($role) {
                return $role->permissions->map(function ($perm) {
                    return '<span class="badge bg-info me-1">' . $perm->name . '</span>';
                })->implode('');
            })

            ->addColumn('action', function ($role) {
                $edit = '<a href="'.route('admin.roles.edit',$role->id).'" class="btn btn-sm btn-warning">Edit</a>';

                $delete = '<button class="btn btn-sm btn-danger deleteBtn" data-id="'.$role->id.'">Delete</button>';

                return $edit . ' ' . $delete;
            })

            ->rawColumns(['permissions','action'])
            ->make(true);
    }
}