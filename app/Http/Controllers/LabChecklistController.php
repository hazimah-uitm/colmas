<?php

namespace App\Http\Controllers;

use App\Models\LabChecklist;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class LabChecklistController extends Controller
{
    use SoftDeletes;

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
    
        $labChecklistList = LabChecklist::latest()->paginate($perPage);
    
        return view('pages.lab-checklist.index', [
            'labChecklistList' => $labChecklistList,
            'perPage' => $perPage, 
        ]);
    }

    public function create()
    {
        return view('pages.lab-checklist.form', [
            'save_route' => route('lab-checklist.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:lab_checklists,title',
            'publish_status' => 'required|in:1,0'
        ],[
            'title.required'     => 'Sila isi perkara',
            'title.unique'     => 'Tajuk telah wujud atau masih dalam rekod yang dipadam',
            'publish_status.required'     => 'Sila pilih status',
        ]);

        $labChecklist = new LabChecklist();

        $labChecklist->fill($request->all());
        $labChecklist->save();

        return redirect()->route('lab-checklist')
            ->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $labChecklist = LabChecklist::findOrFail($id);

        return view('pages.lab-checklist.view', [
            'labChecklist' => $labChecklist,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('pages.lab-checklist.form', [
            'save_route' => route('lab-checklist.update', $id),
            'str_mode' => 'Kemas Kini',
            'labChecklist' => LabChecklist::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:lab_checklists,title,' . $id,
            'publish_status' => 'required|in:1,0'
        ],[
            'title.required'     => 'Sila isi perkara',
            'title.unique'     => 'Tajuk telah wujud atau masih dalam rekod yang dipadam',
            'publish_status.required'     => 'Sila pilih status',
        ]);

        $labChecklist = LabChecklist::findOrFail($id);

        $labChecklist->fill($request->all());
        $labChecklist->save();

        return redirect()->route('lab-checklist')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
    
        if ($search) {
            $labChecklistList = LabChecklist::where('title', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $labChecklistList = LabChecklist::latest()->paginate(10);
        }
    
        return view('pages.lab-checklist.index', [
            'labChecklistList' => $labChecklistList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $labChecklist = LabChecklist::findOrFail($id);

        $labChecklist->delete();

        return redirect()->route('lab-checklist')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = LabChecklist::onlyTrashed()->latest()->paginate(10);

        return view('pages.lab-checklist.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        LabChecklist::withTrashed()->where('id', $id)->restore();

        return redirect()->route('lab-checklist')->with('success', 'Maklumat berjaya dikembalikan');
    }

    public function forceDelete($id)
    {
        // Permanently delete the record
        LabChecklist::withTrashed()->where('id', $id)->forceDelete();

        return redirect()->route('lab-checklist.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
