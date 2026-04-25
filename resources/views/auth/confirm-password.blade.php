<x-app-layout>
    <div class="flex flex-col items-center justify-center mt-12 pb-24">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-6  justify-center">mura.</h2>

                <div class="mb-6 text-sm text-base-content/70">
                    Secure area. Please confirm your password to continue.
                </div>

                <form method="POST" action="{{ route('password.confirm') }}" novalidate="">
                    @csrf

                    <x-floating-input id="password" name="password" type="password" label="Password" required="true" />

                    <div class="form-control mt-4">
                        <x-button type="submit" class="w-full">
                            Confirm
                        </x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>