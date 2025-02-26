<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-settings')->only(['index', 'show']);
        $this->middleware('permission:edit-settings')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Rol listesi
     */
    public function index()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();

        return Inertia::render('Settings/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Yeni rol oluşturma formu
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy('module');

        return Inertia::render('Settings/Roles/Form', [
            'permissions' => $permissions,
        ]);
    }

    /**
     * Yeni rol kaydetme
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        $role->permissions()->sync($validated['permissions']);

        return redirect()->route('roles.index')
            ->with('success', 'Rol başarıyla oluşturuldu.');
    }

    /**
     * Rol detayı
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);

        return Inertia::render('Settings/Roles/Show', [
            'role' => $role,
        ]);
    }

    /**
     * Rol düzenleme formu
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all()->groupBy('module');

        return Inertia::render('Settings/Roles/Form', [
            'role' => $role,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Rol güncelleme
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        $role->permissions()->sync($validated['permissions']);

        return redirect()->route('roles.index')
            ->with('success', 'Rol başarıyla güncellendi.');
    }

    /**
     * Rol silme
     */
    public function destroy(Role $role)
    {
        if ($role->slug === 'admin') {
            return back()->with('error', 'Yönetici rolü silinemez.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Bu role sahip kullanıcılar var. Önce kullanıcıların rollerini değiştirin.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol başarıyla silindi.');
    }
} 