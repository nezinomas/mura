<div class="inline m-0">
    @if($isGrabbed)
        <x-button wire:click="toggle" variant="text-danger" class="cursor-pointer">
            Ungrab
        </x-button>
    @else
        <x-button wire:click="toggle" variant="text" class="cursor-pointer">
            Grab
        </x-button>
    @endif
</div>