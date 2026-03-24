<x-app-layout>
    <x-slot name="header">
        <div class="text-center w-full">
            {{ $user->display_name }}
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 pb-24">
        <div class="space-y-8">
            @forelse ($quotes as $post)
                <div class="card w-full shadow-xl border bg-base-100 border-base-300">
                    <div class="card-body p-8">

                        <div class="flex justify-between items-start mb-6 text-sm text-base-content/60">
                            <div>
                                @if($post->user)
                                    <a href="{{ route('users.show', $post->user) }}" class="font-bold text-base-content tracking-wide hover:underline">{{ $post->author_display }}</a>
                                @else
                                    <span class="font-bold text-base-content tracking-wide">{{ $post->author_display }}</span>
                                @endif
                            </div>
                            <span class="opacity-70">{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="prose max-w-none leading-relaxed mb-4 text-base-content">
                            {!! $post->content_html !!} 
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-24 border border-dashed border-base-300 bg-slate-50/30">
                    <p class="text-base-content/50 italic">
                        The world is quiet. No thoughts have been shared yet.
                    </p>
                </div>
            @endforelse
        </div>

        @if($quotes->hasPages())
            <div class="mt-8 mb-24">
                {{ $quotes->links('layouts.pagination') }}
            </div>
        @endif
    </div>
</x-app-layout>