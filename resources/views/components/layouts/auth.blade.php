<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light">
        <title>{{ $title ?? 'Sign in' }} · MIZAN3G</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-stone-50 font-sans text-slate-900 antialiased">
        <main class="grid min-h-screen lg:grid-cols-[1.08fr_0.92fr]">
            <section class="relative hidden overflow-hidden bg-slate-950 px-12 py-10 text-white lg:flex lg:flex-col">
                <div class="absolute inset-0 opacity-40" aria-hidden="true" style="background-image: radial-gradient(circle at 14% 18%, #0f766e 0, transparent 28rem), radial-gradient(circle at 90% 86%, #b45309 0, transparent 26rem);"></div>
                <div class="relative"><x-auth.brand /></div>

                <div class="relative my-auto max-w-xl">
                    <p class="mb-6 inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-semibold tracking-widest text-teal-200">REPRODUCIBLE RESEARCH</p>
                    <h1 class="text-5xl font-semibold leading-tight tracking-tight">Make cultural translation decisions visible.</h1>
                    <p class="mt-6 max-w-lg text-lg leading-8 text-slate-300">MIZAN3G preserves the evidence behind every audit—source versions, cultural terms, prompts, model settings, outputs, and researcher judgements.</p>
                </div>

                <p class="relative text-sm text-slate-400">A controlled workspace for translation researchers and review teams.</p>
            </section>

            <section class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10">
                <div class="w-full max-w-md">
                    <div class="mb-12 lg:hidden"><x-auth.brand /></div>
                    {{ $slot }}
                </div>
            </section>
        </main>
    </body>
</html>
