<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $announcementList = Announcement::latest()->paginate($perPage);

        return view('pages.announcement.index', [
            'announcementList' => $announcementList,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        return view('pages.announcement.form', [
            'save_route' => route('announcement.store'),
            'str_mode' => 'Tambah',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'string',
            'publish_status' => 'required|in:1,0',
        ],[
            'title.required'     => 'Sila isi tajuk',
            'publish_status.required' => 'Sila isi status',
        ]);

        $announcement = new Announcement();

        $announcement->fill($request->all());
        $announcement->save();

        return redirect()->route('announcement')->with('success', 'Maklumat berjaya disimpan');
    }

    public function show($id)
    {
        $announcement = Announcement::findOrFail($id);

        return view('pages.announcement.view', [
            'announcement' => $announcement,
        ]);
    }

    public function edit(Request $request, $id)
    {
        return view('pages.announcement.form', [
            'save_route' => route('announcement.update', $id),
            'str_mode' => 'Kemas Kini',
            'announcement' => Announcement::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'desc' => 'string',
            'publish_status' => 'required|in:1,0',
        ],[
            'title.required'     => 'Sila isi tajuk',
            'publish_status.required' => 'Sila isi status',
        ]);

        $announcement = Announcement::findOrFail($id);

        $announcement->fill($request->all());
        $announcement->save();

        return redirect()->route('announcement')->with('success', 'Maklumat berjaya dikemaskini');
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        if ($search) {
            $announcementList = Announcement::where('title', 'LIKE', "%$search%")
                ->latest()
                ->paginate(10);
        } else {
            $announcementList = Announcement::latest()->paginate(10);
        }

        return view('pages.announcement.index', [
            'announcementList' => $announcementList,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $announcement->delete();

        return redirect()->route('announcement')->with('success', 'Maklumat berjaya dihapuskan');
    }

    public function trashList()
    {
        $trashList = Announcement::onlyTrashed()->latest()->paginate(10);

        return view('pages.announcement.trash', [
            'trashList' => $trashList,
        ]);
    }

    public function restore($id)
    {
        Announcement::withTrashed()->where('id', $id)->restore();

        return redirect()->route('announcement')->with('success', 'Maklumat berjaya dikembalikan');
    }


    public function forceDelete($id)
    {
        $announcement = Announcement::withTrashed()->findOrFail($id);

        $announcement->forceDelete();

        return redirect()->route('announcement.trash')->with('success', 'Maklumat berjaya dihapuskan sepenuhnya');
    }
}
