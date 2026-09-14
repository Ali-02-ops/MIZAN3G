@props(['terms' => [], 'showSelection' => true, 'emptyMessage' => 'No cultural terms have been recorded yet.'])

<div {{ $attributes->merge(['class' => 'mizan3g-inventory-table-wrap']) }}>
    <table class="mizan3g-inventory-table">
        <caption class="sr-only">Cultural term inventory</caption>
        <thead>
            <tr>
                <th scope="col">Term and source context</th>
                <th scope="col">Ghazala classification</th>
                <th scope="col">Significance</th>
                @if ($showSelection)
                    <th scope="col">Audit status</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($terms as $term)
                <tr>
                    <td>
                        <strong class="mizan3g-inventory-table__term">{{ data_get($term, 'source_phrase') }}</strong>
                        @if (data_get($term, 'source_sentence'))
                            <p class="mizan3g-inventory-table__context">{{ data_get($term, 'source_sentence') }}</p>
                        @endif
                    </td>
                    <td>
                        <x-mizan3g.category-badge :category="data_get($term, 'category')" :subcategory="data_get($term, 'subcategory')" />
                    </td>
                    <td>{{ data_get($term, 'cultural_significance') ?: 'Not recorded' }}</td>
                    @if ($showSelection)
                        <td>
                            <x-mizan3g.status-badge :status="data_get($term, 'selected_for_audit') ? 'selected' : 'not selected'" />
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $showSelection ? 4 : 3 }}" class="mizan3g-inventory-table__empty">{{ $emptyMessage }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
