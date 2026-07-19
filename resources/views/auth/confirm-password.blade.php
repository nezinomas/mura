<x-app-layout>
    <div class="mura-auth-wrap">
        <div class="mura-card">
            <div class="mura-card-body">
                
                <h1 class="mura-card-title">{{ config('app.name', 'mura.') }}</h1>

                <div class="mura-card-note">
                    Secure area. Please confirm your password to continue.
                </div>

                <form method="POST" action="{{ route('password.confirm') }}" novalidate="">
                    @csrf

                    <x-floating-input id="password" name="password" type="password" label="Password" required="true" />

                    <div class="mura-form-action">
                        <x-button type="submit" class="w-full">
                            Confirm
                        </x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>