@props(['id'])

<input type="checkbox" id="{{ $id }}" class="modal-toggle" />
<div class="modal" role="dialog">
    <div class="mura-modal-box">
        @if (isset($title))
            <h3 class="mura-modal-title">{{ $title }}</h3>
        @endif

        <div class="mura-modal-body">
            {{ $slot }}
        </div>

        @if (isset($actions))
            <div class="mura-modal-action">
                {{ $actions }}
            </div>
        @endif
    </div>
    <label class="modal-backdrop cursor-pointer" for="{{ $id }}">Close</label>
</div>