@php
    $sliderLimit = max(1, min(12, (int) ($TopicBlockContents->records_count ?? 5)));
    $rawCategoryIds = $TopicBlockContents->category_ids ?? [];
    $categoryIds = collect(is_array($rawCategoryIds) ? $rawCategoryIds : explode(',', (string) $rawCategoryIds))
        ->filter()
        ->map(fn ($id) => (int) $id)
        ->all();

    $SliderTopics = \App\Models\Topic::query()
        ->where('webmaster_id', (int) ($TopicBlockContents->module_id ?? 3))
        ->where('status', 1)
        ->where('featured', 1)
        ->where(function ($query) {
            $query->whereNull('expire_date')->orWhere('expire_date', '>=', now()->toDateString());
        })
        ->when($categoryIds, function ($query) use ($categoryIds) {
            $query->whereHas('categories', fn ($categories) => $categories->whereIn('section_id', $categoryIds));
        })
        ->whereNotNull('photo_file')
        ->where('photo_file', '<>', '')
        ->orderByDesc('date')
        ->orderByDesc('id')
        ->limit($sliderLimit)
        ->get();
@endphp

@include('frontEnd.layouts.slider', ['SliderTopics' => $SliderTopics])
