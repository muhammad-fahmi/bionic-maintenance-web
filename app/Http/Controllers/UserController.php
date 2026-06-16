<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * Class UserController
 *
 * Controller untuk mengelola data master pengguna (CRUD) oleh Admin.
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna dengan paginasi.
     */
    public function index(): View
    {
        $users = User::paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Menyimpan pengguna baru ke dalam database.
     *
     * @param  Request  $request  Data dari form pembuatan pengguna.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,reporter,receiver,executor'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created successfully!');
    }

    /**
     * Menampilkan form untuk mengedit pengguna yang sudah ada.
     *
     * @param  User  $user  Model pengguna yang akan diedit.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Memperbarui data pengguna di dalam database.
     *
     * @param  Request  $request  Data yang diperbarui dari form.
     * @param  User  $user  Model pengguna yang sedang diedit.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:admin,reporter,receiver,executor'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully!');
    }

    /**
     * Menghapus pengguna dari database.
     *
     * @param  User  $user  Model pengguna yang akan dihapus.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted successfully!');
    }
}
