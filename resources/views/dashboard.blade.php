<x-app-layout>
    <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">

        <div class="space-y-6">
            @foreach ($feed as $post)
                @php($isGrab = $post->isGrab())

                <div class="card shadow-xl border {{ $isGrab ? 'bg-secondary text-secondary-content border-secondary mura-grab-card' : 'bg-base-100 border-base-300' }}">
                    <div class="card-body p-6">

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="font-bold font-mono">{{ $post->user->display_name ?? 'Anonymous' }}</span>

                                @if($isGrab)
                                    <span class="badge badge-outline badge-sm ml-2">Grabbed</span>
                                @else
                                    @if($post->is_private)
                                        <span class="badge badge-error badge-sm ml-2">Private</span>
                                    @else
                                        <span class="badge badge-success badge-sm ml-2">Public</span>
                                    @endif
                                @endif
                            </div>
                            <span class="text-xs opacity-70">{{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="prose max-w-none font-serif">
                            {!! $post->content_html !!} 
                        </div>

                        <div class="card-actions justify-end mt-4 pt-4 border-t border-base-300/20">
                            @if($isGrab)
                                <button class="btn btn-ghost btn-xs">Ungrab</button>
                            @else
                                @can('update', $post)
                                    <a href="#" class="btn btn-ghost btn-xs">Edit</a>
                                @endcan

                                @can('delete', $post)
                                    <button class="btn btn-ghost btn-xs text-error">Delete</button>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>