@props(['id'])

<input type="checkbox" id="{{ $id }}" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="modal-box bg-slate-50 border-slate-200 rounded-none shadow-sm border p-8 text-left">
        @if (isset($title))
            <h3 class="text-ui-label font-bold text-base-content text-lg mb-4 capitalize">{{ $title }}</h3>
        @endif
        
        <div class="text-base-content/80 leading-relaxed mb-8 text-base">
            {{ $slot }}
        </div>
        
        @if (isset($actions))
            <div class="modal-action flex justify-end gap-4 mt-0">
                {{ $actions }}
            </div>
        @endif
    </div>
    <label class="modal-backdrop cursor-pointer" for="{{ $id }}">Close</label>
</div>