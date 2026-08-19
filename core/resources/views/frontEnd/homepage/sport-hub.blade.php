@php
    $sportLang = @Helper::currentLanguage()->code;
    $sportTitleVar = 'title_'.$sportLang;
    $sportFallbackTitle = 'title_'.config('smartend.default_language');
    $sportHeadlines = \App\Models\Topic::query()
        ->where('webmaster_id', 3)
        ->where('status', 1)
        ->latest('date')
        ->latest('id')
        ->limit(5)
        ->get();
    $sportCategories = \App\Models\Section::query()
        ->where('webmaster_id', 3)
        ->where('status', 1)
        ->orderBy('row_no')
        ->limit(6)
        ->get();
    $sportCategoryIcons = ['bi-trophy', 'bi-flag', 'bi-award', 'bi-person-badge', 'bi-arrow-left-right', 'bi-building'];
@endphp

<section class="sport-hub" aria-label="بوابة 967Sport السريعة">
    @if($sportHeadlines->isNotEmpty())
        <div class="sport-ticker">
            <div class="container sport-ticker-inner">
                <div class="sport-ticker-label"><span></span> الآن</div>
                <div class="sport-ticker-window">
                    <div class="sport-ticker-track">
                        @foreach([false, true] as $tickerDuplicate)
                            @foreach($sportHeadlines as $headline)
                                @php($headlineTitle = $headline->$sportTitleVar ?: $headline->$sportFallbackTitle)
                                <a href="{{ Helper::topicURL($headline->id, '', $headline) }}" @if($tickerDuplicate) aria-hidden="true" tabindex="-1" @endif>
                                    <i class="bi bi-lightning-charge-fill"></i>{{ $headlineTitle }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
                <span class="sport-ticker-brand">967 NEWSROOM</span>
            </div>
        </div>
    @endif

    <div class="container sport-gateway-wrap">
        <div class="sport-gateway-head">
            <div>
                <span class="sport-section-label"><i></i> اعرف ملعبك</span>
                <h2>كل الرياضة اليمنية<br><em>بلمسة واحدة</em></h2>
            </div>
            <p>انتقل مباشرة إلى البطولة أو المنتخب أو القصة التي تهمك، وتابع آخر المستجدات من قلب الحدث.</p>
        </div>
        <div class="sport-gateway-grid meu-stagger">
            @foreach($sportCategories as $category)
                @php($categoryTitle = $category->$sportTitleVar ?: $category->$sportFallbackTitle)
                <a href="{{ Helper::categoryURL($category->id, '', $category) }}" class="sport-gateway-card">
                    <span class="sport-gateway-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <i class="bi {{ $sportCategoryIcons[$loop->index] ?? 'bi-grid' }}"></i>
                    <strong>{{ $categoryTitle }}</strong>
                    <span class="sport-gateway-arrow"><i class="bi bi-arrow-up-left"></i></span>
                </a>
            @endforeach
        </div>
    </div>
</section>
