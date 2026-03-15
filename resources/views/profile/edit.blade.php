<x-app-layout>
    <x-slot name="header">
        <div class="text-center text-typewriter w-full">
            Profile Settings
        </div>
    </x-slot>

    <div class="flex flex-col items-center justify-center mt-12 space-y-8 pb-12">
        
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                <h2 class="card-title text-xl font-bold mb-2">Profile Information</h2>
                <div class="mb-6 text-sm text-base-content/70">
                    Update your account's profile information and email address.
                </div>
                
                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')
                    
                    <x-floating-input id="name" name="name" type="text" label="Name" :value="Auth::user()->name" required="true" />
                    <x-floating-input id="display_name" name="display_name" type="text" label="Display name" :value="Auth::user()->display_name" required="true" />
                    <x-floating-input id="email" name="email" type="email" label="Email" :value="Auth::user()->email" required="true" />
                    
                    <button type="submit" class="btn btn-neutral w-full rounded-none text-typewriter mt-2">
                        Save
                    </button>

                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-success text-center mt-4">Saved.</p>
                    @endif
                </form>
            </div>
        </div>

        <div class="card w-96 bg-base-100 shadow-xl border border-error/50">
            <div class="card-body p-8">
                <h2 class="card-title text-xl font-bold mb-2 text-error">Delete Account</h2>
                <div class="mb-6 text-sm text-base-content/70">
                    <p class="mb-5">When your account is deleted, your <strong>private thoughts</strong> will be permanently erased.</p>
                        
                    <p class="bg-warning/10 border border-warning/50 rounded-md p-4 text-base-content">
                        However, your <strong>public thoughts</strong> will remain visible on the global feed forever.
                    </p>
                </div>
                
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <x-floating-input id="password" name="password" type="password" label="Confirm Password" required="true" />
                    
                    <button type="submit" class="btn btn-error w-full rounded-none text-typewriter mt-2">
                        Delete Account
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>