<?php

namespace App\Http\Controllers;

use App\Models\ComputerLab;
use App\Models\Software;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);

        $computerLabQuery = ComputerLab::with(['pemilik', 'campus'])
            ->where('publish_status', 1);

        $computerLabList = $computerLabQuery->latest()->paginate($perPage);

        foreach ($computerLabList as $computerLab) {
            // Check if the 'user_credentials' is a valid JSON string before decoding
            if (is_string($computerLab->user_credentials)) {
                $computerLab->user_credentials = json_decode($computerLab->user_credentials, true);
            }
        }

        return view('pages.schedule.index', [
            'computerLabList' => $computerLabList,
            'perPage' => $perPage,
        ]);
    }

    public function show($id)
    {
        $computerLab = ComputerLab::findOrFail($id);
        $softwareList = Software::where('publish_status', 1)->get();
        $userCredentials = is_string($computerLab->user_credentials)
            ? json_decode($computerLab->user_credentials, true)
            : $computerLab->user_credentials;


        return view('pages.schedule.view', [
            'computerLab' => $computerLab,
            'softwareList' => $softwareList,
            'userCredentials' => $userCredentials,
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('perPage', 10);

        $computerLabQuery = ComputerLab::with(['pemilik', 'campus'])
            ->where('publish_status', 1);

        if ($search) {
            $computerLabQuery->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('code', 'LIKE', "%$search%");
            });
        }

        $computerLabList = $computerLabQuery->latest()->paginate($perPage);

        foreach ($computerLabList as $computerLab) {
            // Check if the 'user_credentials' is a valid JSON string before decoding
            if (is_string($computerLab->user_credentials)) {
                $computerLab->user_credentials = json_decode($computerLab->user_credentials, true);
            }
        }

        return view('pages.schedule.index', [
            'computerLabList' => $computerLabList,
            'perPage' => $perPage,
        ]);
    }
}
