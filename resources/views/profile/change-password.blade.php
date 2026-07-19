<x-app-layout>
        <x-slot name="header">
        <h1 class="mura-page-heading">
            Change Password
        </h1>
    </x-slot>

    <div class="mura-auth-wrap">
        <div class="mura-card">
            <div class="mura-card-body">
                @if (session('status') === 'password-updated')
                    <div class="mura-alert-success">
                        Password updated successfully.
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" novalidate="">
                    @csrf
                    @method('put')

                    <x-floating-input id="update_password_current_password" name="current_password" type="password" label="Current Password" required="true" autocomplete="current-password" bag="updatePassword" />
                    
                    <x-floating-input id="update_password_password" name="password" type="password" label="New Password" required="true" autocomplete="new-password" bag="updatePassword" />
                    
                    <x-floating-input id="update_password_password_confirmation" name="password_confirmation" type="password" label="Confirm Password" required="true" autocomplete="new-password" bag="updatePassword" />

                    <x-button type="submit" class="w-full mt-2">
                        Save Password
                    </x-button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>