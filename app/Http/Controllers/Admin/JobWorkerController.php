<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobWorker;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JobWorkerController extends Controller
{
    /**
     * LIST PAGE
     */
    public function index()
    {
        return view('admin.job_workers.index'); // ✅ fixed
    }

    /**
     * DATATABLE LIST
     */
    public function getAll(Request $request)
    {
        try {

            // ✅ correct model
            $query = JobWorker::query()->latest();

            return DataTables::of($query)
                ->addIndexColumn()

                ->orderColumn('DT_RowIndex', function ($query, $order) {
                    return;
                })

                /**
                 * STATUS TOGGLE
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
                 * ACTION BUTTONS
                 */
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('admin.jobworkers.edit',$row->id).'" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger deleteBtn" data-id="'.$row->id.'">Delete</button>
                    ';
                })

                ->rawColumns(['status','action'])
                ->make(true);

        } catch (\Exception $e) {

            \Log::error('JobWorker DataTable Error: '.$e->getMessage());

            return response()->json(['error' => 'Something went wrong']);
        }
    }

    /**
     * CREATE PAGE
     */
    public function create()
    {
        return view('admin.job_workers.create'); // ✅ fixed
    }

    /**
     * STORE
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name'  => 'required|string|max:100',
                'phone' => 'required|digits:10|unique:job_workers,phone',
                'email' => 'nullable|email|unique:job_workers,email',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            JobWorker::create($request->only([
                'name','abbr','phone','email','address',
                'firm_name','city','state','pincode'
            ]));

            return redirect()->route('admin.jobworkers.index')
                ->with('success', 'Job Worker added successfully');

        } catch (\Exception $e) {

            \Log::error('JobWorker Store Error: '.$e->getMessage());

            return back()->with('error', 'Something went wrong')->withInput();
        }
    }

    /**
     * EDIT
     */
    public function edit($id)
    {
        try {
            $worker = JobWorker::findOrFail($id);
            return view('admin.job_workers.edit', compact('worker'));

        } catch (\Exception $e) {

            \Log::error('JobWorker Edit Error: '.$e->getMessage());

            return redirect()->route('admin.jobworkers.index')
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
                    Rule::unique('job_workers')->ignore($id),
                ],

                'email' => [
                    'nullable',
                    'email',
                    Rule::unique('job_workers')->ignore($id),
                ],
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            JobWorker::findOrFail($id)->update($request->only([
                'name','abbr','phone','email','address',
                'firm_name','city','state','pincode'
            ]));

            return redirect()->route('admin.jobworkers.index')
                ->with('success', 'Updated successfully');

        } catch (\Exception $e) {

            \Log::error('JobWorker Update Error: '.$e->getMessage());

            return back()->with('error', 'Something went wrong')->withInput();
        }
    }

    /**
     * STATUS TOGGLE
     */
    public function changeStatus(Request $request)
    {
        try {

            $worker = JobWorker::findOrFail($request->id);

            $worker->status = $worker->status === 'active' ? 'inactive' : 'active';
            $worker->save();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            \Log::error('JobWorker Status Error: '.$e->getMessage());

            return response()->json(['success' => false]);
        }
    }

    /**
     * DELETE (SOFT DELETE)
     */
    public function delete($id)
    {
        try {

            JobWorker::findOrFail($id)->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            \Log::error('JobWorker Delete Error: '.$e->getMessage());

            return response()->json(['success' => false]);
        }
    }
}