<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifikasi = Notifikasi::where('id_user', $user->id_user)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function read($id)
    {
        $notif = Notifikasi::where('id_user', Auth::id())->findOrFail($id);
        $notif->update(['dibaca' => true]);

        if ($notif->link) {
            return redirect($notif->link);
        }

        return back();
    }

    public function readAll()
    {
        Notifikasi::where('id_user', Auth::id())->update(['dibaca' => true]);
        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
