@props([
    'phrase',
    'sentence' => null,
    'context' => null,
    'translation' => null,
    'locale' => 'Source text',
    'translationLocale' => 'Translation',
])

<section {{ $attributes->merge(['class' => 'mizan3g-context-card']) }} aria-label="Source context comparison">
    <div class="mizan3g-context-card__heading">
        <p class="mizan3g-eyebrow">Cultural term</p>
        <h3>“{{ $phrase }}”</h3>
    </div>

    <div class="mizan3g-context-card__comparison">
        <article class="mizan3g-context-card__panel">
            <p class="mizan3g-eyebrow">{{ $locale }}</p>
            @if ($sentence)
                <blockquote>{{ $sentence }}</blockquote>
            @endif
            @if ($context)
                <p class="mizan3g-context-card__context">{{ $context }}</p>
            @endif
        </article>

        @if ($translation)
            <article class="mizan3g-context-card__panel mizan3g-context-card__panel--translation">
                <p class="mizan3g-eyebrow">{{ $translationLocale }}</p>
                <blockquote>{{ $translation }}</blockquote>
            </article>
        @endif
    </div>
</section>
