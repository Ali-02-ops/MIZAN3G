<x-layouts.auth title="Sign in">
    <div>
        <p class="text-sm font-semibold tracking-wide text-teal-700">RESEARCHER ACCESS</p>
        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">Welcome back</h2>
        <p class="mt-3 text-base leading-7 text-slate-600">Sign in to continue your cultural translation audit work.</p>
    </div>

    @if (session('status'))
        <div class="mt-8 rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-900" role="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-8 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert" aria-live="assertive">
            <p class="font-semibold">We could not sign you in.</p>
            <p class="mt-1">Check your email address and password, then try again.</p>
        </div>
    @endif

    <form class="mt-8 space-y-5" method="POST" action="{{ $action ?? url('/api/v1/auth/login') }}">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-semibold text-slate-800">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus inputmode="email" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-700 focus:ring-3 focus:ring-teal-100" placeholder="you@organisation.org">
            @error('email')
                <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="mb-2 flex items-center justify-between gap-4">
                <label for="password" class="block text-sm font-semibold text-slate-800">Password</label>
                <a href="{{ $forgotPasswordUrl ?? '#' }}" class="text-sm font-semibold text-teal-700 underline decoration-teal-300 underline-offset-4 hover:text-teal-900 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700">Forgot password?</a>
            </div>
            <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-base text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-700 focus:ring-3 focus:ring-teal-100" placeholder="Enter your password">
            @error('password')
                <p class="mt-2 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="flex w-full items-center justify-center rounded-xl bg-teal-700 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-700 active:bg-teal-900">Sign in securely</button>
    </form>

    <p class="mt-8 border-t border-slate-200 pt-6 text-center text-sm leading-6 text-slate-500">Access is provided by your research organisation. Contact your organisation administrator if you need an account.</p>
</x-layouts.auth>
