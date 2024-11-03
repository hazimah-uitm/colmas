<?php

namespace App\Http\Controllers;

use App\Models\WorkChecklist;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class WorkChecklistController extends Controller
{
    use SoftDeletes;

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
    
        $workChecklists = WorkChecklist::latest()->paginate($perPage);
    
        return view('pages.work-checklist.index', [
            'workChecklists' => $workChecklists,
            'perPage' => $perPage, 
        ]);
    }

    public function create()
    {
        return view('pages.work-checklist.form', [
            'save_route' => route('work-checklist.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:work_checklists,title',
            'publish_status' => 'required|in:1,0'
        ],[
            'title.unique'     => 'Nama proses kerja telah wujud atau masih dalam rekod dipadam',
            'title.required'     => 'Sila isi nama proses kerja',
            'publish_status.required'     => 'Sila pilih status',
        ]);

        $workChecklist = new WorkChecklist();

        $workChecklist->fill($request->all());
        $workChecklist->save();

        return redirect()->route('work-checklist')
            ->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $workChecklist = WorkChecklist::findOrFail($id);

        return view('pages.work-checklist.view', [
            'workChecklist' => $workChecklist,
        ]);
    }
    
    public function edit(Request $request, $id)
    {
        return view('pages.work-checklist.form', [
            'save_route' => route('work-checklist.update', $id),
            'str_mode' => 'Kemas Kini',
            'workChecklist' => WorkChecklist::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:work_checklists,title,' . $id,
            'publish_status' => 'required|in:1,0'
        ],[
            'title.unique'       => 'Nama proses kerja telah wujud atau masih dalam rekod dipadam',
            'title.required'     => 'Sila isi nama proses kerja',
            'publish_status.required'     => 'Sila pilih status',
        ]);

        $workChecklist = WorkChecklist::findOrFail($id);

        $workChecklist->fill($request->all());
        $workChecklist->save();

        return redirect()->route('work-checklist')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
    
        if ($search) {
            $workChecklists = WorkChecklist::where('title', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $workChecklists = WorkChecklist::latest()->paginate(10);
        }
    
        return view('pages.work-checklist.index', [
            'workChecklists' => $workChecklists,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $workChecklist = WorkChecklist::findOrFail($id);

        $workChecklist->delete();

        return redirect()->route('work-checklist')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = WorkChecklist::onlyTrashed()->latest()->paginate(10);

        return view('pages.work-checklist.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        WorkChecklist::withTrashed()->where('id', $id)->restore();

        return redirect()->route('work-checklist')->with('success', 'Maklumat berjaya dikembalikan');
    }

    public function forceDelete($id)
    {
        // Permanently delete the record
        WorkChecklist::withTrashed()->where('id', $id)->forceDelete();

        return redirect()->route('work-checklist.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
