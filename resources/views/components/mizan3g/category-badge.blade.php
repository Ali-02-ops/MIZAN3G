@props(['category' => null, 'subcategory' => null])

@php
    $categoryName = is_object($category) ? $category->name : $category;
    $categoryCode = is_object($category) ? $category->code : null;
    $subcategoryName = is_object($subcategory) ? $subcategory->name : $subcategory;
@endphp

<span {{ $attributes->merge(['class' => 'mizan3g-category-badge']) }}>
    @if ($categoryCode)
        <span class="mizan3g-category-badge__code">{{ $categoryCode }}</span>
    @endif
    <span>{{ $categoryName ?: 'Unclassified' }}</span>
    @if ($subcategoryName)
        <span class="mizan3g-category-badge__separator" aria-hidden="true">/</span>
        <span>{{ $subcategoryName }}</span>
    @endif
</span>
