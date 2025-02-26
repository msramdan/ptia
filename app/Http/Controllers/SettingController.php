<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\Settings\{UpdateSettingRequest};
use Illuminate\Contracts\View\View;
use App\Generators\Services\ImageService;
use Carbon\Carbon;
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

        if ($request->has('jam_mulai')) {
            $validated['jam_mulai'] = Carbon::parse($validated['jam_mulai'])->format('H:i');
        }
        if ($request->has('jam_selesai')) {
            $validated['jam_selesai'] = Carbon::parse($validated['jam_selesai'])->format('H:i');
        }

        // Konversi hari_jalan_cron ke array integer sebelum disimpan
        if ($request->has('hari_jalan_cron')) {
            $validated['hari_jalan_cron'] = array_map('intval', $validated['hari_jalan_cron']);
        }

        $setting->update($validated);

        return to_route('setting.index')->with('success', __('Pengaturan berhasil diperbarui.'));
    }

}
