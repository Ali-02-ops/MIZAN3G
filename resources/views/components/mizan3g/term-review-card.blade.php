@props(['term', 'categories' => [], 'subcategories' => [], 'showTranslation' => false])

@php
    $termId = data_get($term, 'id', 'new');
    $selected = (bool) data_get($term, 'selected_for_audit');
@endphp

<article {{ $attributes->merge(['class' => 'mizan3g-term-review-card']) }}>
    <header class="mizan3g-term-review-card__header">
        <div>
            <p class="mizan3g-eyebrow">Term review</p>
            <h2>{{ data_get($term, 'source_phrase') }}</h2>
        </div>
        <x-mizan3g.status-badge :status="$selected ? 'selected' : 'needs review'" />
    </header>

    <x-mizan3g.source-context-card
        :phrase="data_get($term, 'source_phrase')"
        :sentence="data_get($term, 'source_sentence')"
        :context="data_get($term, 'source_context')"
        :translation="$showTranslation ? data_get($term, 'translation') : null"
    />

    <div class="mizan3g-term-review-card__fields">
        <label for="term-{{ $termId }}-category">Category
            <select id="term-{{ $termId }}-category" name="category_id">
                <option value="">Choose a category</option>
                @foreach ($categories as $category)
                    <option value="{{ data_get($category, 'id') }}" @selected(data_get($term, 'category_id') == data_get($category, 'id'))>{{ data_get($category, 'name') }}</option>
                @endforeach
            </select>
        </label>
        <label for="term-{{ $termId }}-subcategory">Subcategory
            <select id="term-{{ $termId }}-subcategory" name="subcategory_id">
                <option value="">Choose a subcategory</option>
                @foreach ($subcategories as $subcategory)
                    <option value="{{ data_get($subcategory, 'id') }}" @selected(data_get($term, 'subcategory_id') == data_get($subcategory, 'id'))>{{ data_get($subcategory, 'name') }}</option>
                @endforeach
            </select>
        </label>
        <label for="term-{{ $termId }}-significance">Cultural significance
            <textarea id="term-{{ $termId }}-significance" name="cultural_significance" rows="3">{{ data_get($term, 'cultural_significance') }}</textarea>
        </label>
    </div>

    <label class="mizan3g-term-review-card__select" for="term-{{ $termId }}-select">
        <input id="term-{{ $termId }}-select" name="selected_for_audit" type="checkbox" value="1" @checked($selected)>
        Include this term in the audit
    </label>
</article>
