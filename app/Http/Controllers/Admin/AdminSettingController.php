<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN]), 403, 'You do not have permission to manage settings.');

        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN]), 403, 'You do not have permission to manage settings.');

        foreach (Setting::all() as $setting) {
            if ($setting->type === 'boolean') {
                Setting::set($setting->key, $request->boolean("settings.{$setting->key}"), $setting->group, $setting->type, $setting->is_public);
            } elseif ($request->has("settings.{$setting->key}")) {
                $value = $request->input("settings.{$setting->key}");

                if ($setting->type === 'number') {
                    $value = $value === '' ? null : (float) $value;
                } elseif ($setting->type === 'integer') {
                    $value = $value === '' ? null : (int) $value;
                }

                Setting::set($setting->key, $value, $setting->group, $setting->type, $setting->is_public);
            }
        }

        $new = $request->validate([
            'new_setting.key' => ['nullable', 'string', 'max:255'],
            'new_setting.value' => ['nullable', 'string'],
            'new_setting.group' => ['nullable', 'string', 'max:120'],
            'new_setting.type' => ['nullable', 'in:string,number,integer,boolean,json'],
        ]);

        if (! empty($new['new_setting']['key'])) {
            Setting::set(
                $new['new_setting']['key'],
                $new['new_setting']['value'] ?? '',
                $new['new_setting']['group'] ?: 'general',
                $new['new_setting']['type'] ?: 'string'
            );
        }

        if ($request->filled('delete_key')) {
            Setting::where('key', $request->input('delete_key'))->delete();
        }

        return back()->with('success', 'Settings saved.');
    }
}