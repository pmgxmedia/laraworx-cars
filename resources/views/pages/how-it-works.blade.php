<x-layouts.app :title="$seo['title']" :description="$seo['description']">
    <section class="border-b border-royal-900/10 bg-royal-950 py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-widest text-gold-400">The WorxCars reward programme</p>
            <h1 class="mt-2 font-display text-3xl font-bold text-ivory-50">How WorxCars Works</h1>
            <p class="mt-2 max-w-2xl text-sm text-ivory-100/60">A clear, step-by-step guide to earning your WorxCars Buyer Reward — from browsing, to buying, to getting paid.</p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="space-y-0">
            @foreach ($steps as [$num, $title, $desc])
                <div class="relative flex gap-6 pb-10 last:pb-0">
                    @if (! $loop->last)
                        <span class="absolute left-7 top-14 bottom-0 w-px bg-gold-500/30"></span>
                    @endif
                    <div class="relative z-10 grid h-14 w-14 shrink-0 place-items-center rounded-full bg-royal-950 font-display text-lg font-black text-gold-400 ring-4 ring-ivory-50">{{ $num }}</div>
                    <div>
                        <h2 class="font-display text-xl font-bold text-royal-950">{{ $title }}</h2>
                        <p class="mt-2 text-sm leading-relaxed text-royal-900/65">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @auth
            <a href="{{ route('vehicles.index') }}" class="btn-gold mt-10">Start browsing now</a>
        @else
            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="btn-gold">Create free account</a>
                <a href="{{ route('vehicles.index') }}" class="btn-royal">Browse vehicles</a>
            </div>
        @endauth

        <div class="card mt-12 border-l-4 border-l-gold-500 p-6">
            <h2 class="font-display text-lg font-bold text-royal-950">Important — what does NOT earn a reward</h2>
            <p class="mt-2 text-sm leading-relaxed text-royal-900/70">
                Registering, viewing, enquiring or saving a vehicle never earns a reward. Rewards are payable <strong>only</strong> after you complete a qualifying purchase of a vehicle, the seller confirms the transaction, WorxCars verifies it, and the reward is approved and paid. Every claim is subject to eligibility rules and fraud checks.
            </p>
        </div>

        @if ($faqs->isNotEmpty())
            <div class="mt-14">
                <h2 class="font-display text-2xl font-bold text-royal-950">Reward FAQs</h2>
                <div class="mt-6 space-y-3">
                    @foreach ($faqs as $faq)
                        <div class="card overflow-hidden" x-data="{ open: false }">
                            <button type="button" @click="open = !open" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                                <span class="font-semibold text-royal-950">{{ $faq->question }}</span>
                                <svg class="h-5 w-5 shrink-0 text-gold-600 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                            </button>
                            <div x-show="open" x-collapse>
                                <div class="border-t border-royal-900/5 px-5 py-4 text-sm leading-relaxed text-royal-900/70">{!! nl2br(e($faq->answer)) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-layouts.app>