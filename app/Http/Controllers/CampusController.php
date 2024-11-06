<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $campusList = Campus::latest()->paginate($perPage);

        return view('pages.campus.index', [
            'campusList' => $campusList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('pages.campus.form', [
            'save_route' => route('campus.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:campuses,name', // Change from exists to unique
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required' => 'Sila isi nama kampus',
            'name.unique' => 'Nama kampus telah wujud atau masih dalam rekod dipadam',
            'publish_status.required' => 'Sila isi status kampus',
        ]);
    
        $campus = new Campus();
    
        $campus->fill($request->all());
        $campus->save();
    
        return redirect()->route('campus')->with('success', 'Maklumat berjaya disimpan');
    }
    

    public function show($id)
    {
        $campus = Campus::findOrFail($id);

        return view('pages.campus.view', [
            'campus' => $campus,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('pages.campus.form', [
            'save_route' => route('campus.update', $id),
            'str_mode' => 'Kemas Kini',
            'campus' => Campus::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:campuses,name,' . $id, // Ignore the current record's ID
            'publish_status' => 'required|in:1,0',
        ], [
            'name.required' => 'Sila isi nama kampus',
            'name.unique' => 'Nama kampus telah wujud atau masih dalam rekod dipadam',
            'publish_status.required' => 'Sila isi status kampus',
        ]);
    
        $campus = Campus::findOrFail($id);
    
        $campus->fill($request->all());
        $campus->save();
    
        return redirect()->route('campus')->with('success', 'Maklumat berjaya dikemaskini');
    }
    
    
    public function search(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        if ($search) {
            $campusList = Campus::where('name', 'LIKE', "%$search%")
                ->latest()
                ->paginate($perPage);
        } else {
            $campusList = Campus::latest()->paginate($perPage);
        }

        return view('pages.campus.index', [
            'campusList' => $campusList,
            'perPage' => $perPage,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $campus = Campus::findOrFail($id);

        $campus->delete();

        return redirect()->route('campus')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = Campus::onlyTrashed()->latest()->paginate(10);

        return view('pages.campus.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        Campus::withTrashed()->where('id', $id)->restore();
    
        return redirect()->route('campus')->with('success', 'Maklumat berjaya dikembalikan');
    }

    public function forceDelete($id)
    {
        $campus = Campus::withTrashed()->findOrFail($id);

        $campus->forceDelete();

        return redirect()->route('campus.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
