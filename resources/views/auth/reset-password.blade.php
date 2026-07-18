<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-200">
        <div class="mura-card">
            <div class="mura-card-body">
                
                <h2 class="mura-card-title mb-8">mura.</h2>

                <form method="POST" action="{{ route('password.store') }}" novalidate="">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <x-floating-input id="email" name="email" type="email" label="Email" required="true" />

                    <x-floating-input id="password" name="password" type="password" label="New Password" required="true" />

                    <x-floating-input id="password_confirmation" name="password_confirmation" type="password" label="Confirm Password" required="true" />

                    <div class="form-control mt-4">
                        <x-button type="submit" class="w-full">
                            Reset Password
                        </x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>