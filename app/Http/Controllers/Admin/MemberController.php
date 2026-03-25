<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:members-list', ['only' => ['index']]);
        $this->middleware('permission:members-create', ['only' => ['create','store']]);
        $this->middleware('permission:members-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:members-delete', ['only' => ['destroy']]);
    }

    /**
     * Display member list
     */
    public function index()
    {
        try {
            return view('admin.members.index');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get all members (DataTable)
     */
    public function getAll(Request $request)
    {
        try {
            $query = User::query();

            if ($request->member_name) {
                $query->where('name', 'like', '%' . $request->member_name . '%');
            }

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('role', function ($row) {
                    return $row->getRoleNames()->first() ?? '-';
                })

                ->addColumn('status', function ($row) {
                    $checked = $row->status === 'active' ? 'checked' : '';

                    return '<label class="switch">
                        <input type="checkbox" class="statusToggle" data-id="'.$row->id.'" '.$checked.'>
                        <span class="slider"></span>
                    </label>';
                })

                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('admin.members.edit', $row->id).'" 
                        class="btn btn-sm btn-info">
                        Edit
                        </a>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })

                ->rawColumns(['status','action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create page
     */
    public function create()
    {
        try {
            $roles = Role::all();
            return view('admin.members.create', compact('roles'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Store member
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|digits_between:10,15',
                'password' => 'required|min:6|confirmed',
                'role' => 'required'
            ]);

            $user = User::create([
                'full_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->mobile,
                'password' => Hash::make($request->password)
            ]);

            $user->assignRole($request->role);

            return redirect()->route('admin.members.index')
                ->with('success', 'Member created successfully');

        } catch (\Exception $e) {
            dd($e);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Edit member
     */
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);

            $roles = Role::all();

            return view('admin.members.edit', compact('user','roles'));

        } catch (\Exception $e) {

            \Log::error('User Edit Error: ' . $e->getMessage());

            return redirect()->route('admin.members.index')
                ->with('error', 'User not found');
        }
    }

    /**
     * Update member
     */
    public function update(Request $request, $id)
    {
        try {
            $member = User::findOrFail($id);

            $request->validate([
                'name'   => 'required|string|max:50',
                'email'  => 'required|email|unique:users,email,' . $id,
                'mobile' => 'required|digits_between:10,15',
                'role'   => 'required',
            ]);

            $member->update([
                'full_name'   => $request->name,
                'email'  => $request->email,
                'phone' => $request->mobile,
            ]);

            // ✅ update role properly
            $member->syncRoles([$request->role]);

           return redirect()->route('admin.members.index')
                ->with('success', 'Member Update successfully');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete member
     */
    public function destroy($id)
    {
        try {
            User::findOrFail($id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Member deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change status
     */
    public function changeStatus(Request $request)
    {
        try {
            $member = User::findOrFail($request->id);

            // Toggle ENUM values
            $member->status = $member->status === 'active' ? 'inactive' : 'active';
            $member->save();

            return response()->json([
                'status' => true,
                'message' => 'Status updated',
                'data' => [
                    'label' => $member->status
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}