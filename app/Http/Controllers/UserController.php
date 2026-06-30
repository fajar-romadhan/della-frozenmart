<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('pengguna.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:pengguna'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:admin,manager,owner'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status_aktif'] = true;

        $user = User::create($validated);
        \App\Services\LogActivity::log('create', 'Tambah Pengguna Baru', "Menambahkan pengguna baru '{$user->name}' dengan role '{$user->role}'.");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $pengguna)
    {
        return view('pengguna.show', compact('pengguna'));
    }

    public function edit(User $pengguna)
    {
        return view('pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, User $pengguna)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('pengguna')->ignore($pengguna->id)],
            'role' => ['required', 'in:admin,manager,owner'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $pengguna->update($validated);
        \App\Services\LogActivity::log('update', 'Perbarui Data Pengguna', "Memperbarui data pengguna '{$pengguna->name}' dengan role '{$pengguna->role}'.");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return redirect()->route('pengguna.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $pengguna->name;
        $userEmail = $pengguna->email;
        $pengguna->delete();
        \App\Services\LogActivity::log('delete', 'Hapus Pengguna', "Menghapus akun pengguna '{$userName}' ({$userEmail}).");

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('pengguna.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['status_aktif' => !$user->status_aktif]);

        $status = $user->status_aktif ? 'diaktifkan' : 'dinonaktifkan';
        \App\Services\LogActivity::log('update', 'Ubah Status Pengguna', "Mengubah status akun pengguna '{$user->name}' menjadi {$status}.");
        return redirect()->route('pengguna.index')
            ->with('success', "Pengguna '{$user->name}' berhasil {$status}.");
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('password')]);
        \App\Services\LogActivity::log('update', 'Reset Password Pengguna', "Mereset password pengguna '{$user->name}' ke default.");

        return redirect()->route('pengguna.index')
            ->with('success', "Password pengguna '{$user->name}' berhasil direset ke 'password'.");
    }
}
