<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    private const MAX_HEADER_TILES = 4;

    public function edit()
    {
        $setting = Setting::getCached() ?? new Setting();
        $themes = [];
        try {
            $base = resource_path('views/themes');
            if (is_dir($base)) {
                foreach (scandir($base) as $d) {
                    if ($d === '.' || $d === '..') continue;
                    if (is_dir($base.DIRECTORY_SEPARATOR.$d)) $themes[] = $d;
                }
            }
        } catch (\Throwable $e) {}
        if (empty($themes)) { $themes = ['nimtech','aspirenoted']; }
        return view('admin.settings.edit', compact('setting','themes'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'currency_code' => ['required', 'string', 'max:10'],
            'currency_symbol' => ['required', 'string', 'max:8'],
            'currency_position' => ['required', 'in:left,right'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'favicon' => ['nullable', 'mimes:png,ico', 'max:2048'],
            'theme_color' => ['nullable', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
            'active_theme' => ['nullable','string','max:50'],
            'deals_under_threshold' => ['nullable', 'numeric', 'min:0'],
            'homepage_cta_heading' => ['nullable', 'string', 'max:255'],
            'homepage_cta_subtext' => ['nullable', 'string', 'max:255'],
            'shipping_flat_rate' => ['nullable', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'header_notice_text' => ['nullable', 'string', 'max:255'],
            'ga4_measurement_id' => ['nullable', 'string', 'max:50'],
            'google_ads_id' => ['nullable', 'string', 'max:50'],
            'meta_pixel_id' => ['nullable', 'string', 'max:50'],
            'tiktok_pixel_id' => ['nullable', 'string', 'max:50'],
            'custom_head_scripts' => ['nullable', 'string'],
            'custom_body_scripts' => ['nullable', 'string'],
            'special_tiles' => ['nullable', 'array'],
            'special_tiles.*.label' => ['nullable', 'string', 'max:80'],
            'special_tiles.*.url' => ['nullable', 'string', 'max:255'],
            'special_tiles.*.bg' => ['nullable', 'string', 'max:100'],
            'special_tile_images' => ['nullable', 'array'],
            'special_tile_images.*' => ['nullable', 'image', 'max:4096'],
        ]);

        $setting = Setting::query()->first() ?? new Setting();
        $setting->fill(collect($data)->except(['special_tiles', 'special_tile_images'])->all());
        $setting->enable_mpesa = $request->boolean('enable_mpesa', true);
        $setting->enable_cod = $request->boolean('enable_cod', true);

        $existingTiles = $this->headerTiles($setting);
        $incomingTiles = array_values((array) $request->input('special_tiles', []));
        $uploadedTileImages = (array) $request->file('special_tile_images', []);
        $tileCount = max(count($incomingTiles), count($existingTiles), count($uploadedTileImages), self::MAX_HEADER_TILES);
        $normalizedTiles = [];

        for ($i = 0; $i < $tileCount; $i++) {
            $incoming = (array) ($incomingTiles[$i] ?? []);
            $existing = (array) ($existingTiles[$i] ?? []);
            $tile = $this->normalizeHeaderTile($incoming, $existing, $uploadedTileImages[$i] ?? null);
            if ($tile === null) {
                continue;
            }
            $normalizedTiles[] = $tile;
        }

        $normalizedTiles = array_slice($normalizedTiles, 0, self::MAX_HEADER_TILES);
        $setting->header_special_tiles = json_encode($normalizedTiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $setting->save();

        // Handle uploads
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            // delete old if exists
            if ($setting->logo_path) { \Illuminate\Support\Facades\Storage::disk('public')->delete($setting->logo_path); }
            $setting->logo_path = $path;
        }
        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('branding', 'public');
            if ($setting->favicon_path) { \Illuminate\Support\Facades\Storage::disk('public')->delete($setting->favicon_path); }
            $setting->favicon_path = $path;
        }
        $setting->save();

        Setting::clearCache();

        return redirect()->route('admin.settings.edit')->with('success', 'Settings saved.');
    }

    public function updateHeaderTile(Request $request, int $tile)
    {
        abort_unless($tile >= 0 && $tile < self::MAX_HEADER_TILES, 404);

        $request->validate([
            "special_tiles.$tile.label" => ['nullable', 'string', 'max:80'],
            "special_tiles.$tile.url" => ['nullable', 'string', 'max:255'],
            "special_tiles.$tile.bg" => ['nullable', 'string', 'max:100'],
            "special_tile_images.$tile" => ['nullable', 'image', 'max:4096'],
        ]);

        $setting = Setting::query()->first() ?? new Setting();
        $tiles = $this->headerTiles($setting);
        $incoming = (array) $request->input("special_tiles.$tile", []);
        $existing = (array) ($tiles[$tile] ?? []);
        $uploadedImage = $request->file("special_tile_images.$tile");
        $normalized = $this->normalizeHeaderTile($incoming, $existing, $uploadedImage);

        if ($normalized === null) {
            $this->deleteHeaderTileImage($existing['image'] ?? null);
            unset($tiles[$tile]);
        } else {
            $tiles[$tile] = $normalized;
        }

        $this->saveHeaderTiles($setting, $tiles);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Tile '.($tile + 1).' saved.');
    }

    public function destroyHeaderTile(int $tile)
    {
        abort_unless($tile >= 0 && $tile < self::MAX_HEADER_TILES, 404);

        $setting = Setting::query()->first() ?? new Setting();
        $tiles = $this->headerTiles($setting);

        if (isset($tiles[$tile])) {
            $this->deleteHeaderTileImage($tiles[$tile]['image'] ?? null);
            unset($tiles[$tile]);
            $this->saveHeaderTiles($setting, $tiles);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Tile '.($tile + 1).' deleted.');
    }

    private function headerTiles(Setting $setting): array
    {
        $rawTiles = $setting->header_special_tiles;
        $tiles = json_decode((string) ($rawTiles ?? '[]'), true);
        if (($rawTiles === null || trim((string) $rawTiles) === '') && (!is_array($tiles) || empty($tiles))) {
            $tiles = config('header.special_tiles', []);
        }

        return is_array($tiles) ? array_values($tiles) : [];
    }

    private function normalizeHeaderTile(array $incoming, array $existing, mixed $newImage = null): ?array
    {
        $label = trim((string) ($incoming['label'] ?? ''));
        $url = trim((string) ($incoming['url'] ?? ''));
        $bg = trim((string) ($incoming['bg'] ?? ''));
        $imagePath = trim((string) ($existing['image'] ?? ''));

        if ($newImage) {
            $this->deleteHeaderTileImage($imagePath);
            $imagePath = $newImage->store('header-tiles', 'public');
        }

        if ($label === '' && $url === '' && $bg === '' && $imagePath === '') {
            return null;
        }

        return [
            'label' => $label !== '' ? $label : ((string) ($existing['label'] ?? 'Special')),
            'url' => $url !== '' ? $url : ((string) ($existing['url'] ?? '#')),
            'bg' => $bg !== '' ? $bg : ((string) ($existing['bg'] ?? 'bg-gray-900 text-white')),
            'image' => $imagePath !== '' ? $imagePath : null,
        ];
    }

    private function saveHeaderTiles(Setting $setting, array $tiles): void
    {
        $tiles = array_values(array_filter(array_slice($tiles, 0, self::MAX_HEADER_TILES)));
        $setting->header_special_tiles = json_encode($tiles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $setting->save();
        Setting::clearCache();
    }

    private function deleteHeaderTileImage(?string $imagePath): void
    {
        $imagePath = trim((string) $imagePath);
        if ($imagePath !== '' && !Str::startsWith($imagePath, ['http://', 'https://', 'images/', 'storage/'])) {
            Storage::disk('public')->delete($imagePath);
        }
    }
}
