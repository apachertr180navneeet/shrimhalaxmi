<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    /**
     * Display vendor listing page
     */
    public function index()
    {
        return view('admin.vendors.index');
    }

    /**
     * DataTable AJAX - Fetch vendors with filters
     */
    public function getAll(Request $request)
    {
        try {

            /**
             * Base query (IMPORTANT: do NOT use ->get())
             * Using query builder for performance (server-side)
             */
            $query = Vendor::query()->latest();

            /**
             * 🔍 Filter: Vendor Name (specific search)
             */
            if ($request->filled('vendor_name')) {
                $query->where('vendor_name', 'like', '%' . $request->vendor_name . '%');
            }

            /**
             * 🔍 Global Search (multiple columns)
             */
            if ($request->filled('search_value')) {
                $search = $request->search_value;

                $query->where(function ($q) use ($search) {
                    $q->where('vendor_name', 'like', "%{$search}%")
                        ->orWhere('firm_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('state', 'like', "%{$search}%");
                });
            }

            /**
             * DataTables response
             */
            return DataTables::of($query)
                ->addIndexColumn()

                // Prevent sorting error on index column
                ->orderColumn('DT_RowIndex', function ($query, $order) {
                    return;
                })

                /**
                 * Status toggle column (ENUM safe)
                 */
                ->addColumn('status', function ($row) {
                    $checked = $row->status === 'active' ? 'checked' : '';

                    return '
                        <label class="switch">
                            <input type="checkbox" class="statusToggle" data-id="'.$row->id.'" '.$checked.'>
                            <span class="slider round"></span>
                        </label>
                    ';
                })

                /**
                 * Action buttons column
                 */
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('admin.vendors.edit',$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })

                ->rawColumns(['status','action'])
                ->make(true);

        } catch (\Exception $e) {

            // Log error (important for debugging)
            \Log::error('Vendor DataTable Error: '.$e->getMessage());

            return response()->json([
                'error' => 'Something went wrong!'
            ]);
        }
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.vendors.create');
    }

    /**
     * Store new vendor
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [

                'vendor_name' => 'required|string|max:100',
                'firm_name'   => 'nullable|string|max:150',
                'abbr'        => 'nullable|string|max:10',

                'phone'       => 'required|digits:10|unique:vendors,phone',
                'email'       => 'nullable|email|max:100|unique:vendors,email',

                'address'     => 'nullable|string',
                'gst_no'      => 'nullable|string|max:20',
                'city'        => 'nullable|string|max:100',
                'state'       => 'nullable|string|max:100',
                'pincode'     => 'nullable|digits:6',

            ],[
                'phone.unique' => 'This phone number already exists.',
                'email.unique' => 'This email already exists.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            /**
             * 🔐 Secure: only allowed fields (NO $request->all())
             */
            Vendor::create($request->only([
                'vendor_name','firm_name','abbr','phone','email',
                'address','gst_no','city','state','pincode'
            ]));

            return redirect()->route('admin.vendors.index')
                ->with('success', 'Vendor added successfully');

        } catch (\Exception $e) {

            \Log::error('Vendor Store Error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong!')
                ->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            return view('admin.vendors.edit', compact('vendor'));
        } catch (\Exception $e) {

            \Log::error('Vendor Edit Error: '.$e->getMessage());

            return redirect()->route('admin.vendors.index')
                ->with('error', 'Vendor not found');
        }
    }

    /**
     * Update vendor
     */
    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [

                'vendor_name' => 'required|string|max:100',

                'phone' => [
                    'required',
                    'digits:10',
                    Rule::unique('vendors')->ignore($id),
                ],

                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('vendors')->ignore($id),
                ],

            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Vendor::findOrFail($id)->update($request->only([
                'vendor_name','firm_name','abbr','phone','email',
                'address','gst_no','city','state','pincode'
            ]));

            return redirect()->route('admin.vendors.index')
                ->with('success', 'Vendor updated successfully');

        } catch (\Exception $e) {

            \Log::error('Vendor Update Error: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Something went wrong!')
                ->withInput();
        }
    }

    /**
     * Toggle vendor status (active/inactive)
     */
    public function changeStatus(Request $request)
    {
        try {

            $vendor = Vendor::findOrFail($request->id);

            $vendor->status = $vendor->status === 'active' ? 'inactive' : 'active';
            $vendor->save();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            \Log::error('Vendor Status Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating status'
            ]);
        }
    }

    /**
     * Soft delete vendor
     */
    public function delete($id)
    {
        try {

            Vendor::findOrFail($id)->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            \Log::error('Vendor Delete Error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting vendor'
            ]);
        }
    }
}