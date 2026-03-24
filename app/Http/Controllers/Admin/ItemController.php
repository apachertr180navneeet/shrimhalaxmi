<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function index()
    {
        return view('admin.items.index');
    }

    public function getAll(Request $request)
    {
        try {
            $query = Item::query()->latest();

            if ($request->filled('item_name')) {
                $query->where('item_name', 'like', '%' . $request->item_name . '%');
            }

            if ($request->filled('search_value')) {
                $search = $request->search_value;

                $query->where(function ($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                        ->orWhere('abbr', 'like', "%{$search}%")
                        ->orWhere('remark', 'like', "%{$search}%");
                });
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->orderColumn('DT_RowIndex', function ($query, $order) {
                    return;
                })
                ->addColumn('short_code', function ($row) {
                    return $row->abbr ?: '-';
                })
                ->addColumn('qty_balance', function ($row) {
                    return '0';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status === 'active' ? 'checked' : '';

                    return '
                        <label class="switch">
                            <input type="checkbox" class="statusToggle" data-id="' . $row->id . '" ' . $checked . '>
                            <span class="slider round"></span>
                        </label>
                    ';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('admin.items.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $row->id . '">Delete</button>
                    ';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        } catch (\Exception $e) {
            \Log::error('Item DataTable Error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Something went wrong!',
            ]);
        }
    }

    public function create()
    {
        return view('admin.items.create');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_name' => 'required|string|max:100|unique:items,item_name',
                'abbr' => 'nullable|string|max:20|unique:items,abbr',
                'remark' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Item::create($request->only([
                'item_name',
                'abbr',
                'remark',
            ]));

            return redirect()->route('admin.items.index')
                ->with('success', 'Item added successfully');
        } catch (\Exception $e) {
            \Log::error('Item Store Error: ' . $e->getMessage());

            return back()->with('error', 'Something went wrong')->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $item = Item::findOrFail($id);

            return view('admin.items.edit', compact('item'));
        } catch (\Exception $e) {
            \Log::error('Item Edit Error: ' . $e->getMessage());

            return redirect()->route('admin.items.index')
                ->with('error', 'Record not found');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_name' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('items', 'item_name')->ignore($id),
                ],
                'abbr' => [
                    'nullable',
                    'string',
                    'max:20',
                    Rule::unique('items', 'abbr')->ignore($id),
                ],
                'remark' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            Item::findOrFail($id)->update($request->only([
                'item_name',
                'abbr',
                'remark',
            ]));

            return redirect()->route('admin.items.index')
                ->with('success', 'Item updated successfully');
        } catch (\Exception $e) {
            \Log::error('Item Update Error: ' . $e->getMessage());

            return back()->with('error', 'Something went wrong')->withInput();
        }
    }

    public function changeStatus(Request $request)
    {
        try {
            $item = Item::findOrFail($request->id);

            $item->status = $item->status === 'active' ? 'inactive' : 'active';
            $item->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Item Status Error: ' . $e->getMessage());

            return response()->json(['success' => false]);
        }
    }

    public function delete($id)
    {
        try {
            Item::findOrFail($id)->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Item Delete Error: ' . $e->getMessage());

            return response()->json(['success' => false]);
        }
    }
}
