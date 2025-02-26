<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\Settings\{UpdateSettingRequest};
use Illuminate\Contracts\View\View;
use App\Generators\Services\ImageService;
use Illuminate\Http\{RedirectResponse};
use Illuminate\Routing\Controllers\{HasMiddleware, Middleware};

class SettingController extends Controller implements HasMiddleware
{
    public function __construct(public ImageService $imageService, public string $logoPath = '', public string $logoLoginPath = '', public string $faviconPath = '')
    {
        $this->logoPath = storage_path('app/public/uploads/logos/');
        $this->logoLoginPath = storage_path('app/public/uploads/logo-logins/');
        $this->faviconPath = storage_path('app/public/uploads/favicons/');
    }

    public static function middleware(): array
    {
        return [
            'auth',
            new Middleware('permission:setting view', only: ['index']),
            new Middleware('permission:setting edit', only: ['update']),
        ];
    }

    public function index(): View
    {
        $setting = Setting::findOrFail(1)->first();
        return view('setting.edit', compact('setting'));
    }

    public function show(Setting $setting): View
    {
        return view('setting.show', compact('setting'));
    }

    public function edit(Setting $setting): View
    {
        return view('setting.edit', compact('setting'));
    }


    public function update(UpdateSettingRequest $request, Setting $setting): RedirectResponse
    {
        $validated = $request->validated();

        $validated['logo'] = $this->imageService->upload(name: 'logo', path: $this->logoPath, defaultImage: $setting?->logo);
        $validated['logo_login'] = $this->imageService->upload(name: 'logo_login', path: $this->logoLoginPath, defaultImage: $setting?->logo_login);
        $validated['favicon'] = $this->imageService->upload(name: 'favicon', path: $this->faviconPath, defaultImage: $setting?->favicon);

        $setting->update($validated);

        // Simpan status pengumuman di session jika aktif
        if ($setting->is_aktif_pengumuman === 'Yes') {
            session(['show_announcement' => true]);
        } else {
            session()->forget('show_announcement');
        }

        return to_route('setting.index')->with('success', __('Pengaturan berhasil diperbarui.'));
    }
}
