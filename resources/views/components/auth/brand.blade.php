@props(['compact' => false])

<div {{ $attributes->class(['flex items-center gap-3', 'gap-2.5' => $compact]) }}>
    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-teal-700 text-lg font-bold tracking-tight text-white shadow-sm ring-1 ring-teal-600" aria-hidden="true">M</span>
    <span>
        <span class="block text-lg font-semibold tracking-tight text-slate-950">MIZAN3G</span>
        @unless ($compact)
            <span class="block text-xs font-medium tracking-wide text-slate-500">Cultural translation audit</span>
        @endunless
    </span>
</div>
