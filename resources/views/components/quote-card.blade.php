@props(['post', 'isMine' => false])

<div class="card w-full shadow-xl border {{ $isMine ? 'bg-slate-50 border-slate-200 mura-grab-card' : 'bg-base-100 border-base-300' }}">
    <div class="card-body p-8">

        <div class="flex justify-between items-start pb-4 text-sm text-base-content/60 border-b border-base-300/50">
            <div>
                @if($post->user)
                    <a href="{{ route('users.show', $post->user) }}" class="font-bold text-base-content tracking-wide hover:underline">{{ $post->author_display }}</a>
                @else
                    <span class="font-bold text-base-content tracking-wide">{{ $post->author_display }}</span>
                @endif
                @if(isset($meta))
                    {{ $meta }}
                @endif
            </div>
            <span class="opacity-70">{{ $post->created_at->diffForHumans() }}</span>
        </div>

        <div class="prose max-w-none leading-relaxed mb-3 text-base-content whitespace-pre-wrap">
            {!! $post->content_html !!} 
        </div>

        @if(isset($actions))
            <div class="flex justify-end gap-4 mt-4 pt-4 border-t border-base-300/50 text-sm">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>