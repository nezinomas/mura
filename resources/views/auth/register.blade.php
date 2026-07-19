<x-app-layout>
    <div class="mura-auth-wrap">
        <div class="mura-card">
            <div class="mura-card-body">
                
                <h1 class="mura-card-title">Join {{ config('app.name', 'mura.') }}</h1>

                <form method="POST" action="{{ route('register') }}" novalidate="">
                    @csrf

                    <x-floating-input id="name" name="name" label="Name" autocomplete="name" required="true" />
                    
                    <x-floating-input id="email" name="email" type="email" label="Email" autocomplete="username" required="true" />

                    <x-floating-input id="password" name="password" type="password" label="Password" autocomplete="new-password" required="true" />

                    <x-floating-input id="password_confirmation" name="password_confirmation" type="password" label="Confirm Password" autocomplete="new-password" required="true" />

                    <div class="mura-form-action">
                        <x-button type="submit" class="w-full">Register</x-button>
                    </div>

                    <div class="mura-card-footer">
                        <a href="{{ route('login') }}" class="mura-auth-link">Already registered?</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>