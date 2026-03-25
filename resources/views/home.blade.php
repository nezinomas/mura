<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            Global Discover
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 pb-24">
        <div class="space-y-8">
            @if($dailyQuote)
                <div class="relative mt-4">
                    <div class="absolute -top-3 left-6 z-10">
                        <span class="bg-slate-50 border border-slate-200 px-3 py-1 text-xs uppercase tracking-widest text-slate-500 rounded-full shadow-sm">
                            Thought of the Day
                        </span>
                    </div>
                    @php($isDailyGrabbed = auth()->check() ? $dailyQuote->isGrabbedBy(auth()->user()) : false)
                    <x-quote-card :post="$dailyQuote">
                        <x-slot name="actions">
                            <x-button as="a" href="{{ route('quotes.show', $dailyQuote) }}" variant="text" class="mr-auto">Permalink</x-button>
                            @auth
                                @if($isDailyGrabbed)
                                    <form method="POST" action="{{ route('quotes.ungrab', $dailyQuote) }}" class="inline m-0">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="submit" variant="text-danger">Ungrab</x-button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('quotes.grab', $dailyQuote) }}" class="inline m-0">
                                        @csrf
                                        <x-button type="submit" variant="text">Grab</x-button>
                                    </form>
                                @endif
                            @endauth
                        </x-slot>
                    </x-quote-card>
                </div>
                
                @if($quotes->isNotEmpty())
                    <div class="flex items-center justify-center my-12">
                        <div class="h-px bg-slate-200 w-full max-w-xs"></div>
                        <span class="px-4 text-slate-400 italic text-sm text-center">Discover<br>more</span>
                        <div class="h-px bg-slate-200 w-full max-w-xs"></div>
                    </div>
                @endif
            @endif

            @forelse ($quotes as $post)
                @if($dailyQuote && $post->id === $dailyQuote->id)
                    @continue
                @endif
                @php($isGrabbed = auth()->check() ? $post->isGrabbedBy(auth()->user()) : false)
                <x-quote-card :post="$post">
                    <x-slot name="actions">
                        <x-button as="a" href="{{ route('quotes.show', $post) }}" variant="text" class="mr-auto">Permalink</x-button>
                        @auth
                            @if($isGrabbed)
                                <form method="POST" action="{{ route('quotes.ungrab', $post) }}" class="inline m-0">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="text-danger">Ungrab</x-button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('quotes.grab', $post) }}" class="inline m-0">
                                    @csrf
                                    <x-button type="submit" variant="text">Grab</x-button>
                                </form>
                            @endif
                        @endauth
                    </x-slot>
                </x-quote-card>
            @empty
                @if(!$dailyQuote)
                    <div class="flex flex-col items-center justify-center py-24 border border-dashed border-base-300 bg-slate-50/30">
                        <p class="text-base-content/50 italic">
                            The world is quiet. No thoughts have been shared yet.
                        </p>
                    </div>
                @endif
            @endforelse
        </div>
    </div>
</x-app-layout>