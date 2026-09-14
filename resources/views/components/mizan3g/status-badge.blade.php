@props(['status' => 'draft'])

@php
    $value = strtolower(str_replace(['_', '-'], ' ', (string) $status));
    $tone = match ($value) {
        'selected', 'confirmed', 'complete', 'ready to generate', 'approved' => 'positive',
        'needs review', 'pending', 'draft' => 'caution',
        'rejected', 'archived', 'failed' => 'negative',
        default => 'neutral',
    };
@endphp

<span {{ $attributes->merge(['class' => "mizan3g-status-badge mizan3g-status-badge--{$tone}"]) }}>
    {{ str($value)->headline() }}
</span>
