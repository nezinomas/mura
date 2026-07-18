<x-app-layout>
    <div class="mura-auth-wrap">
        <div class="mura-card">
            <div class="mura-card-body">
                
                <h2 class="mura-card-title mb-6">mura.</h2>

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