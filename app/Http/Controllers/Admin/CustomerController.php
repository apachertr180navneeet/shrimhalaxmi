<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:customer-list', ['only' => ['index']]);
        $this->middleware('permission:customer-create', ['only' => ['create','store']]);
        $this->middleware('permission:customer-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:customer-delete', ['only' => ['destroy']]);
    }
    /**
     * LIST PAGE
     */
    public function index()
    {
        return view('admin.customers.index');
    }

    /**
     * DATATABLE LIST
     */
    public function getAll(Request $request)
    {
        $query = Customer::query()->latest();

        // 🔍 Name Search
        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // 🔍 Location Search
        if (!empty($request->location)) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // 🔍 Global Search
        if (!empty($request->search_value)) {
            $search = $request->search_value;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('phone', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('city', 'like', "%$search%");
            });
        }

        return DataTables::of($query)
            ->addIndexColumn()

            ->addColumn('location', function ($row) {
                return $row->location ?? '-';
            })

            ->addColumn('status', function ($row) {
                $checked = $row->status === 'active' ? 'checked' : '';

                return '
                    <label class="switch">
                        <input type="checkbox" class="statusToggle" data-id="'.$row->id.'" '.$checked.'>
                        <span class="slider round"></span>
                    </label>
                ';
            })

            ->addColumn('action', function ($row) {
                return '
                    <a href="'.route('admin.customers.edit',$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                ';
            })

            ->rawColumns(['status','action'])
            ->make(true);
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name'  => 'required|string|max:100',
                'phone' => 'required|digits:10|unique:customers,phone',
                'email' => 'nullable|email|unique:customers,email',
                'address_2' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Customer::create($request->only([
                'name','abbr','phone','email','location','address_2','firm_name','gst_no','state'
            ]));

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer added successfully');

        } catch (\Exception $e) {

            return back()->with('error', 'Something went wrong')->withInput();
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        try {

            $customer = Customer::findOrFail($id);

            return view('admin.customers.edit', compact('customer'));

        } catch (\Exception $e) {

            \Log::error('Customer Edit Error: '.$e->getMessage());

            return redirect()->route('admin.customers.index')
                ->with('error', 'Record not found');
        }
    }

    /**
     * UPDATE
     */
    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',

                'phone' => [
                    'required',
                    'digits:10',
                    Rule::unique('customers')->ignore($id),
                ],

                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('customers')->ignore($id),
                ],
                'address_2' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Customer::findOrFail($id)->update($request->only([
                'name','abbr','phone','email','location','address_2','firm_name','gst_no','state'
            ]));

            return redirect()->route('admin.customers.index')
                ->with('success', 'Updated successfully');

        } catch (\Exception $e) {

            \Log::error('Customer Update Error: '.$e->getMessage());

            return back()->with('error', 'Something went wrong')->withInput();
        }
    }

    /**
     * STATUS TOGGLE
     */
    public function changeStatus(Request $request)
    {
        try {

            $customer = Customer::findOrFail($request->id);

            $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
            $customer->save();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            \Log::error('Customer Status Error: '.$e->getMessage());

            return response()->json(['success' => false]);
        }
    }

    /**
     * DELETE (SOFT DELETE)
     */
    public function delete($id)
    {
        try {

            Customer::findOrFail($id)->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            \Log::error('Customer Delete Error: '.$e->getMessage());

            return response()->json(['success' => false]);
        }
    }
}
