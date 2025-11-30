<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'administrator');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email|ends_with:@polban.ac.id',
            'password'     => 'required|min:8|confirmed',
            'role'         => 'required|in:tu,dosen,koordinator',
        ], [
            'email.ends_with' => 'Email harus menggunakan domain @polban.ac.id',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        User::create([
            'nama_lengkap' => $validated['nama_lengkap'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'role'         => $validated['role'],
            'status'       => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil dibuat!');
    }

    public function edit($id)
    {
        $user = User::where('id_user', $id)
            ->where('role', '!=', 'administrator')
            ->firstOrFail();

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id_user', $id)
            ->where('role', '!=', 'administrator')
            ->firstOrFail();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email'        => 'required|email|ends_with:@polban.ac.id|unique:users,email,' . $id . ',id_user',
            'password'     => 'nullable|min:8|confirmed',
            'role'         => 'required|in:tu,dosen,koordinator',
            'status'       => 'required|boolean',
        ]);

        $user->nama_lengkap = $validated['nama_lengkap'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::where('id_user', $id)
            ->where('role', '!=', 'administrator')
            ->firstOrFail();

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun berhasil dihapus!');
    }
}
