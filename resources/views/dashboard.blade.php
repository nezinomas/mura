<x-app-layout>
    <x-slot name="header">
        <div class="text-center text-ui-label w-full">
            Your Feed
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto my-12 px-4 pb-24"> {{-- Added pb-24 to ensure content doesn't hide behind the button --}}
        <div class="space-y-8">
            @forelse ($quotes as $post)
                @php($isMine = $post->isMine())
                @php($isGrabbedBy = $post->isGrabbedBy(auth()->user()))

                <div class="card w-full shadow-xl border {{ $isMine ? 'bg-slate-50 border-slate-200 mura-grab-card' : 'bg-base-100 border-base-300' }}">
                    <div class="card-body p-8">

                        <div class="flex justify-between items-start mb-6 text-ui-label text-sm text-base-content/60">
                            <div>
                                <span class="font-bold text-base-content tracking-wide">{{ $post->author_display }}</span>
                                <span class="italic ml-2">
                                    @if($isMine && $post->is_private) — Private @else — Public @endif
                                </span>
                            </div>
                            <span class="opacity-70">{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="prose max-w-none leading-relaxed mb-4 text-base-content">
                            {!! $post->content_html !!} 
                        </div>

                        <div class="flex justify-end gap-4 mt-4 pt-4 border-t border-base-300/50 text-ui-label text-sm">
                            @if($isGrabbedBy)
                                <x-button variant="text-danger">Ungrab</x-button>
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
                                            @if(!$post->is_private && $post->grabbedBy()->exists())
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
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-24 border border-dashed border-base-300 bg-slate-50/30">
                    <p class="text-ui-label text-base-content/50 italic mb-6">
                        The paper is blank. No thoughts have been carved yet.
                    </p>
                    
                    <x-button as="a" href="{{ route('quotes.create') }}" class="px-10">
                        Write your first thought
                    </x-button>
                </div>
            @endforelse
        </div>
    </div>

    <a href="{{ route('quotes.create') }}" 
       class="fixed bottom-8 right-8 btn btn-circle btn-lg shadow-2xl border-slate-200 bg-slate-50 hover:bg-slate-100 transition-all duration-300 group">
        <span class="text-ui-label text-2xl group-hover:scale-110 transition-transform">+</span>
    </a>

</x-app-layout>