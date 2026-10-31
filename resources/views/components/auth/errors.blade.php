@props(['errors', 'class' => ''])

@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'rounded-md bg-rose-50 p-4 ring-1 ring-rose-500/30 '.$class]) }}>
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-rose-800">There were problems with your submission</h3>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-rose-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif