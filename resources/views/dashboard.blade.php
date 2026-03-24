<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            Your Feed
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
                                <x-button as="label" for="delete-modal-{{ $post->id }}" variant="text-danger" class="cursor-pointer">Delete</x-button>

                                <x-modal id="delete-modal-{{ $post->id }}">
                                    <x-slot name="title">Confirm Deletion</x-slot>
                                    
                                    <p>
                                        @if(!$post->is_private && $post->isGrabbedByAnyone())
                                            This thought will remain visible on the global feed forever.
                                        @else
                                            Are you sure you want to delete this thought?
                                        @endif
                                    </p>

                                    <x-slot name="actions">
                                        <x-button as="label" for="delete-modal-{{ $post->id }}" class="cursor-pointer">Cancel</x-button>
                                        <form method="POST" action="{{ route('quotes.destroy', $post) }}" class="inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <x-button type="submit" variant="danger">Delete</x-button>
                                        </form>
                                    </x-slot>
                                </x-modal>
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

    <a href="{{ route('quotes.create') }}" 
       class="fixed bottom-8 right-8 btn btn-circle btn-lg shadow-2xl border-slate-200 bg-slate-50 hover:bg-slate-100 transition-all duration-300 group">
        <span class="text-2xl group-hover:scale-110 transition-transform">+</span>
    </a>

</x-app-layout>