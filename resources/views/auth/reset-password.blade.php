<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-200">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-8 justify-center">mura.</h2>

                <form method="POST" action="{{ route('password.store') }}" novalidate="">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <x-floating-input id="email" name="email" type="email" label="Email" required="true" />

                    <x-floating-input id="password" name="password" type="password" label="New Password" required="true" />

                    <x-floating-input id="password_confirmation" name="password_confirmation" type="password" label="Confirm Password" required="true" />

                    <div class="form-control mt-4">
                        <button type="submit" class="btn btn-neutral w-full rounded-none text-typewriter">
                            Reset Password
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-guest-layout>