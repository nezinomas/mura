<x-app-layout>
        <x-slot name="header">
        <div class="text-center w-full">
            Change Password
        </div>
    </x-slot>

    <div class="flex items-center justify-center mt-12 pb-12">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                @if (session('status') === 'password-updated')
                    <div class="alert alert-success mb-6 text-sm rounded-none border-l-4 border-success">
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