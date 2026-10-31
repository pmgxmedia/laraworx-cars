@extends('dealer.layouts.dealer')

@section('title', 'Add Vehicle')

@section('header')
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-gold-600">New listing</p>
        <h2 class="mt-1 section-title">Add a Vehicle</h2>
        <p class="mt-2 text-sm text-royal-900/60">Complete the form below. You can save a draft and finish later, or submit for approval right away.</p>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('dealer.vehicles.store') }}" enctype="multipart/form-data" class="card p-6">
        @csrf

        @include('dealer.vehicles._form')

        <section class="mt-8">
            <div class="flex items-center gap-3">
                <h3 class="font-display text-lg font-bold text-royal-950">Images</h3>
                <div class="divider-gold"></div>
            </div>
            <p class="mt-2 text-xs text-royal-900/50">Upload up to {{ config('worxcars.images_per_vehicle_max', 20) }} photos. The first image becomes the main photo (you can reorder later).</p>

            <div class="mt-4" x-data="{
                files: [],
                previews: [],
                dragOver: false,
                pick() { this.$refs.input.click(); },
                handleFiles() {
                    this.files = [...this.$refs.input.files];
                    this.previews = this.files.map(f => URL.createObjectURL(f));
                }
            }">
                <div class="rounded-lg border-2 border-dashed p-8 text-center transition"
                     :class="dragOver ? 'border-gold-500 bg-gold-500/10' : 'border-royal-900/20 bg-ivory-50'"
                     @click="pick()"
                     @dragover.prevent="dragOver = true"
                     @dragleave.prevent="dragOver = false"
                     @drop.prevent="dragOver = false; $refs.input.files = $event.dataTransfer.files; handleFiles()">
                    <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" x-ref="input" @change="handleFiles()" class="hidden">
                    <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-royal-950">
                        <svg class="h-6 w-6 text-gold-400" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    </div>
                    <p class="mt-3 text-sm font-semibold text-royal-950">Drag &amp; drop photos here, or click to browse</p>
                    <p class="mt-1 text-xs text-royal-900/50">JPG, PNG or WebP · up to 5 MB each</p>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4" x-show="previews.length">
                    <template x-for="(src, i) in previews" :key="i">
                        <div class="relative aspect-[4/3] overflow-hidden rounded-md bg-royal-900">
                            <img :src="src" :alt="'Image ' + (i + 1)" class="h-full w-full object-cover">
                            <span class="absolute left-2 top-2 rounded-full bg-royal-950/80 px-2 py-0.5 text-[10px] font-bold text-gold-300" x-text="i === 0 ? 'Main' : (i + 1)"></span>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <div class="mt-8 flex flex-wrap items-center justify-end gap-3 border-t border-royal-900/10 pt-5">
            <button type="submit" name="action" value="save" class="btn-ghost">Save draft</button>
            <button type="submit" name="action" value="submit" class="btn-gold">Save &amp; submit for approval</button>
        </div>
    </form>
@endsection