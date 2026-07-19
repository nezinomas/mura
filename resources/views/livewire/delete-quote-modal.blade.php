<div>
    <div x-data="{ show: $wire.entangle('showModal') }"
         class="modal"
         :class="{ 'modal-open': show }"
         x-cloak>

        <div class="mura-modal-box max-w-lg w-full"
             @click.outside="show = false"
             @keydown.escape.window="show = false">
            
            <h3 class="mura-modal-title">Confirm Deletion</h3>

            <div class="mura-modal-body">
                @if($isGrabbed)
                    This thought will remain visible on the global feed forever. Are you sure to disown it?
                @else
                    Are you sure you want to delete this thought?
                @endif
            </div>

            <div class="mura-modal-action">
                <x-button @click="show = false" type="button" class="cursor-pointer">
                    Cancel
                </x-button>
                
                <x-button wire:click="destroy" variant="danger">
                    Delete
                </x-button>
            </div>
        </div>
    </div>
</div>