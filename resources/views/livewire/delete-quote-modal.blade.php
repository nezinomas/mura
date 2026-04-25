<div>
    <div x-data="{ show: $wire.entangle('showModal') }"
         class="modal"
         :class="{ 'modal-open': show }"
         x-cloak>

        <div class="modal-box bg-slate-50 border-slate-200 rounded-none shadow-sm border p-8 text-left max-w-lg w-full"
             @click.outside="show = false"
             @keydown.escape.window="show = false">
            
            <h3 class="font-bold text-base-content text-lg mb-4 capitalize">Confirm Deletion</h3>

            <div class="text-base-content/80 leading-relaxed mb-8 text-base">
                @if($isGrabbed)
                    This thought will remain visible on the global feed forever. Are you sure to disown it?
                @else
                    Are you sure you want to delete this thought?
                @endif
            </div>

            <div class="modal-action flex justify-end gap-4 mt-0">
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