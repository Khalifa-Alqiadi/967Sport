@php
    $tickerLang = @Helper::currentLanguage()->code;
    $tickerDefaultLang = config('smartend.default_language');
    $tickerTitleVar = 'title_'.$tickerLang;
    $tickerFallbackTitleVar = 'title_'.$tickerDefaultLang;
    $tickerDescVar = 'desc_'.$tickerLang;
    $tickerFallbackDescVar = 'desc_'.$tickerDefaultLang;
    $tickerLabel = @$TopicBlockContents->$tickerTitleVar ?: (@$TopicBlockContents->$tickerFallbackTitleVar ?: __('frontend.sportTickerNow'));
    $tickerBrand = @$TopicBlockContents->$tickerDescVar ?: (@$TopicBlockContents->$tickerFallbackDescVar ?: __('frontend.sportNewsroomBrand'));
    $tickerModuleId = (int) (@$TopicBlockContents->module_id ?: 0);
    $tickerLimit = max(1, min(50, (int) (@$TopicBlockContents->records_count ?: 5)));
    $tickerCategoryIds = collect(explode(',', (string) @$TopicBlockContents->category_ids))
        ->map(fn ($id) => (int) trim($id))
        ->filter()
        ->values();

    $tickerQuery = \App\Models\Topic::query()
        ->where('status', 1)
        ->where('featured', 1)
        ->where(function ($query) {
            $query->where(function ($expiryQuery) {
                $expiryQuery->whereNotNull('expire_date')->where('expire_date', '>=', now()->toDateString());
            })->orWhereNull('expire_date');
        });

    if ($tickerModuleId > 0) {
        $tickerQuery->where('webmaster_id', $tickerModuleId);
    }
    if ($tickerCategoryIds->isNotEmpty()) {
        $tickerQuery->whereHas('categories', function ($query) use ($tickerCategoryIds) {
            $query->whereIn('section_id', $tickerCategoryIds);
        });
    }

    switch ((int) @$TopicBlockContents->records_order) {
        case 3:
            $tickerQuery->oldest('date')->oldest('id');
            break;
        case 4:
            $tickerQuery->inRandomOrder();
            break;
        default:
            $tickerQuery->latest('date')->latest('id');
            break;
    }

    $featuredTickerTopics = $tickerQuery->limit($tickerLimit)->get();
@endphp

@if($featuredTickerTopics->isNotEmpty())
    <section id="landing-block-{{ @$TopicBlock->id }}"
             class="sport-featured-ticker {{ @$TopicBlock->css_classes }}"
             aria-label="{{ $tickerLabel }}">
        <div class="sport-ticker">
            <div class="container sport-ticker-inner">
                <div class="sport-ticker-label"><span></span> {{ $tickerLabel }}</div>
                <div class="sport-ticker-window">
                    <div class="sport-ticker-track">
                        @foreach([false, true] as $tickerDuplicate)
                            @foreach($featuredTickerTopics as $featuredTickerTopic)
                                @php
                                    $featuredTickerTitle = $featuredTickerTopic->$tickerTitleVar
                                        ?: $featuredTickerTopic->$tickerFallbackTitleVar;
                                @endphp
                                <a href="{{ Helper::topicURL($featuredTickerTopic->id, '', $featuredTickerTopic) }}"
                                   @if($tickerDuplicate) aria-hidden="true" tabindex="-1" @endif>
                                    <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>{{ $featuredTickerTitle }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <span class="sport-ticker-brand">{{ $tickerBrand }}</span>
            </div>
        </div>
    </section>
@endif
