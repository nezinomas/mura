<x-app-layout>
    <div class="min-h-screen flex items-center justify-center bg-base-200">
        <div class="card w-96 bg-base-100 shadow-xl border border-base-300">
            <div class="card-body p-8">
                
                <h2 class="card-title text-2xl font-bold mb-6 justify-center">mura.</h2>

                <div class="mb-6 text-sm text-base-content/70">
                    Please verify your email address by clicking the link we just sent you.
                </div>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success mb-6 text-sm rounded-none border-l-4 border-success">
                        A new verification link has been sent.
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between">
                    <form method="POST" action="{{ route('verification.send') }}" novalidate="">
                        @csrf
                        <x-button type="submit">
                            Resend Email
                        </x-button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" novalidate="">
                        @csrf
                        <x-button type="submit" variant="text">
                            Log Out
                        </x-button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>