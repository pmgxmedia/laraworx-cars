<x-admin.layouts.admin title="Settings" :breadcrumbs="[['label' => 'Settings']]">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="font-display text-3xl font-black text-royal-950">Platform Settings</h1>
            <p class="mt-1 text-sm text-royal-900/60">Global configuration stored persistently and cached by key.</p>
        </div>
    </div>
    <div class="mt-2 h-px w-16 bg-gradient-to-r from-gold-500 to-transparent"></div>

    @if ($errors->any())
        <div class="mb-4 mt-6 rounded-md bg-rose-50 px-4 py-3 text-sm text-rose-800 ring-1 ring-rose-500/30">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 grid gap-6 xl:grid-cols-3">
        @csrf
        @method('PUT')
        <div class="space-y-6 xl:col-span-2">
            @forelse ($settings as $group => $groupSettings)
                <div class="card overflow-hidden">
                    <div class="border-b border-royal-900/10 px-5 py-4">
                        <h2 class="font-display text-lg font-bold text-royal-950">{{ Str::headline($group) }}</h2>
                    </div>
                    <div class="divide-y divide-royal-900/5">
                        @foreach ($groupSettings as $setting)
                            <div class="px-5 py-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <label for="settings-{{ $setting->key }}" class="label !mb-0.5">{{ Str::headline(str_replace(['_', '.'], ' ', $setting->key)) }}</label>
                                        <p class="font-mono text-xs text-royal-900/40">{{ $setting->key }}{{ $setting->is_public ? ' · public' : '' }}</p>
                                        @if ($setting->description)
                                            <p class="mt-1 text-xs text-royal-900/55">{{ $setting->description }}</p>
                                        @endif
                                    </div>
                                    <div class="flex shrink-0 items-center gap-3">
                                        @if ($setting->type === 'boolean')
                                            <label class="flex items-center gap-2 text-sm">
                                                <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" @checked(\App\Models\Setting::get($setting->key)) class="h-4 w-4 rounded border-royal-900/20 text-gold-500 focus:ring-gold-500">
                                                Enabled
                                            </label>
                                        @elseif (in_array($setting->type, ['number', 'integer']))
                                            <input type="number" step="{{ $setting->type === 'integer' ? '1' : '0.01' }}" name="settings[{{ $setting->key }}]" class="input !w-40 !py-2" value="{{ \App\Models\Setting::get($setting->key) }}">
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}]" class="input !w-56 !py-2" value="{{ \App\Models\Setting::get($setting->key) }}">
                                        @endif
                                        <label class="flex items-center gap-1 text-xs text-rose-700" title="Mark to delete this setting when saving">
                                            <input type="radio" name="delete_key" value="{{ $setting->key }}" class="h-3.5 w-3.5 border-royal-900/20 text-rose-500 focus:ring-rose-400">
                                            Delete
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-sm text-royal-900/50">No settings defined yet — add one below.</div>
            @endforelse

            <div class="flex justify-end">
                <button type="submit" class="btn-gold btn-sm">Save all settings</button>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card p-5">
                <h2 class="section-title !text-xl">Add New Setting</h2>
                <div class="mt-4 space-y-4">
                    <div>
                        <label class="label">Key *</label>
                        <input type="text" name="new_setting[key]" class="input" placeholder="e.g. support_email" value="{{ old('new_setting.key') }}">
                    </div>
                    <div>
                        <label class="label">Value</label>
                        <input type="text" name="new_setting[value]" class="input" value="{{ old('new_setting.value') }}">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">Group</label>
                            <input type="text" name="new_setting[group]" class="input" placeholder="general" value="{{ old('new_setting.group') }}">
                        </div>
                        <div>
                            <label class="label">Type</label>
                            <select name="new_setting[type]" class="input">
                                <option value="string">String</option>
                                <option value="number">Number</option>
                                <option value="integer">Integer</option>
                                <option value="boolean">Boolean</option>
                                <option value="json">JSON</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-ghost btn-sm w-full">Add setting</button>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="section-title !text-xl">Notes</h3>
                <p class="mt-2 text-sm leading-relaxed text-royal-900/60">
                    Settings are read via <code class="rounded bg-ivory-100 px-1.5 py-0.5 text-xs">setting('key')</code> / <code class="rounded bg-ivory-100 px-1.5 py-0.5 text-xs">\App\Models\Setting::get('key')</code>.
                    The golden chevron values like commission defaults are safe to leave at the platform standard — they only apply when a vehicle is approved without an explicit configuration.
                </p>
            </div>
        </div>
    </form>
</x-admin.layouts.admin>