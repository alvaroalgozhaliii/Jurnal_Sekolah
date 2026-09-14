<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AksesController extends Controller
{
    public function showPilihAkses()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $accesses = $user->getAvailableAccesses();

        // Jika hanya memiliki 1 akses atau tidak ada tugas tambahan, langsung ke dashboard akses tersebut
        if (count($accesses) <= 1) {
            $singleAccess = array_key_first($accesses) ?? 'guru';
            session(['active_access' => $singleAccess]);
            $route = $accesses[$singleAccess]['route'] ?? AuthController::getDashboardRouteName($user->role);
            return redirect()->route($route);
        }

        return view('auth.pilih-akses', compact('user', 'accesses'));
    }

    public function prosesPilihAkses(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $accesses = $user->getAvailableAccesses();

        $request->validate([
            'akses' => 'required|string|in:' . implode(',', array_keys($accesses)),
        ], [
            'akses.in' => 'Akses yang dipilih tidak sah untuk akun Anda.',
        ]);

        $chosenAccess = $request->akses;
        session(['active_access' => $chosenAccess]);

        $targetRoute = $accesses[$chosenAccess]['route'] ?? 'guru.dashboard';

        $title = $accesses[$chosenAccess]['title'] ?? ucfirst($chosenAccess);

        return redirect()->route($targetRoute)->with('success', "Selamat datang! Anda masuk dengan akses {$title}.");
    }
}
