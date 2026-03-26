<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            <div class="mb-3">Your Feed</div>

            <div class="flex items-center justify-center gap-6 text-sm text-base-content/60 text-ui-label">
                <a href="/dashboard" class="hover:text-base-content transition-colors {{ request('filter') === null ? 'text-base-content underline underline-offset-4' : '' }}">All</a>
                <a href="/dashboard?filter=public" class="hover:text-base-content transition-colors {{ request('filter') === 'public' ? 'text-base-content underline underline-offset-4' : '' }}">Public</a>
                <a href="/dashboard?filter=private" class="hover:text-base-content transition-colors {{ request('filter') === 'private' ? 'text-base-content underline underline-offset-4' : '' }}">Private</a>
                <a href="/dashboard?filter=grabbed" class="hover:text-base-content transition-colors {{ request('filter') === 'grabbed' ? 'text-base-content underline underline-offset-4' : '' }}">Grabbed</a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4"> {{-- Added pb-24 to ensure content doesn't hide behind the button --}}
        <div class="space-y-8">
            @forelse ($quotes as $post)
                @php($isMine = $post->isMine())
                @php($isGrabbedBy = $post->isGrabbedBy(auth()->user()))

                <x-quote-card :post="$post" :isMine="$isMine">
                    <x-slot name="meta">
                        <span class="italic ml-2">
                            @if($isMine && $post->is_private) — Private @else — Public @endif
                        </span>
                    </x-slot>

                    <x-slot name="actions">
                        <x-button as="a" href="{{ route('quotes.show', $post) }}" variant="text" class="mr-auto">Permalink</x-button>

                        @if($isGrabbedBy)
                            <form method="POST" action="{{ route('quotes.ungrab', $post) }}" class="inline m-0">
                                @csrf
                                @method('DELETE')
                                <x-button type="submit" variant="text-danger">Ungrab</x-button>
                            </form>
                        @else
                            @can('update', $post)
                                @if($post->isEditable())
                                    <x-button as="a" href="{{ route('quotes.edit', $post) }}" variant="text">Edit</x-button>
                                @endif
                            @endcan

@can('delete', $post)
    <x-button as="button" 
              x-data
              @click="$dispatch('confirmDelete', { quoteId: {{ $post->id }} })" 
              variant="text-danger" 
              class="cursor-pointer">
        Delete
    </x-button>
@endcan
                        @endif
                    </x-slot>
                </x-quote-card>
            @empty
                <div class="flex flex-col items-center justify-center py-24 border border-dashed border-base-300 bg-slate-50/30">
                    <p class="text-base-content/50 italic mb-6">
                        The paper is blank. No thoughts have been carved yet.
                    </p>

                    <x-button as="a" href="{{ route('quotes.create') }}" class="px-10">
                        Write your first thought
                    </x-button>
                </div>
            @endforelse
        </div>

        @if($quotes->hasPages())
            <div class="mt-8 mb-24">
                {{ $quotes->links('layouts.pagination') }}
            </div>
        @endif

    </div>

    <livewire:delete-quote-modal />

    <a href="{{ route('quotes.create') }}" 
       class="fixed bottom-8 right-8 btn btn-circle btn-lg shadow-2xl border-slate-200 bg-slate-50 hover:bg-slate-100 transition-all duration-300 group">
        <span class="text-2xl group-hover:scale-110 transition-transform">+</span>
    </a>

</x-app-layout>