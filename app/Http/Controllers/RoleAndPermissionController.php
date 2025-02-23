<?php

namespace App\Http\Controllers;

use App\Http\Requests\Roles\{StoreRoleRequest, UpdateRoleRequest};
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RoleAndPermissionController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:role & permission view', only: ['index', 'show']),
            new Middleware('permission:role & permission create', only: ['create', 'store']),
            new Middleware('permission:role & permission edit', only: ['edit', 'store']),
            new Middleware('permission:role & permission delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            $users = Role::query();

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('created_at', fn($row) => $row->created_at->format('Y-m-d H:i:s'))
                ->addColumn('updated_at', fn($row) => $row->updated_at->format('Y-m-d H:i:s'))
                ->addColumn('action', 'roles.include.action')
                ->toJson();
        }

        return view('roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('roles.create');
    }
    /**
     * Simpan role baru ke dalam database.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        return to_route('roles.index')->with('success', __('Role berhasil dibuat.'));
    }

    /**
     * Tampilkan detail role.
     */
    public function show(int $id): View
    {
        $role = Role::with('permissions')->findOrFail($id);

        return view('roles.show', compact('role'));
    }

    /**
     * Tampilkan form edit role.
     */
    public function edit(int $id): View
    {
        $role = Role::with('permissions')->findOrFail($id);

        return view('roles.edit', compact('role'));
    }

    /**
     * Perbarui data role di database.
     */
    public function update(UpdateRoleRequest $request, string $id): RedirectResponse
    {
        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->permissions);

        return to_route('roles.index')->with('success', __('Role berhasil diperbarui.'));
    }

    /**
     * Hapus role dari database.
     */
    public function destroy(string $id): RedirectResponse
    {
        $role = Role::withCount('users')->findOrFail($id);

        if ($role->users_count < 1) {
            $role->delete();

            return to_route('roles.index')->with('success', __('Role berhasil dihapus.'));
        }

        return to_route('roles.index')->with('error', __('Role tidak dapat dihapus.'));
    }
}
