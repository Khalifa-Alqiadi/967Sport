@php
    $sliderItems = collect();
    $languageCode = optional(Helper::currentLanguage())->code ?: config('smartend.default_language');
    $defaultLanguageCode = config('smartend.default_language');
    $titleField = 'title_'.$languageCode;
    $fallbackTitleField = 'title_'.$defaultLanguageCode;
    $detailsField = 'details_'.$languageCode;
    $fallbackDetailsField = 'details_'.$defaultLanguageCode;
    $legacyBanners = collect();
    $legacyBannerType = null;

    if (isset($SliderTopics) && count($SliderTopics)) {
        $sliderItems = collect($SliderTopics)->map(function ($topic) use ($titleField, $fallbackTitleField, $detailsField, $fallbackDetailsField) {
            $title = $topic->{$titleField} ?: $topic->{$fallbackTitleField};
            $details = $topic->{$detailsField} ?: $topic->{$fallbackDetailsField};
            $category = $topic->category($topic->id);

            return (object) [
                'title' => $title,
                'details' => $details,
                'image' => $topic->photo_file
                    ? route('fileView', ['path' => 'topics/'.$topic->photo_file]).'?w=1920&h=1080&r=fit'
                    : null,
                'link' => Helper::topicURL($topic->id, '', $topic),
                'category' => $category ? ($category->{$titleField} ?: $category->{$fallbackTitleField}) : null,
            ];
        })->filter(fn ($item) => filled(strip_tags((string) $item->title)) && filled($item->image))->values();
    } elseif (!empty($BannersSettingsId)) {
        $banners = Helper::BannersList($BannersSettingsId);
        $legacyBanners = collect($banners);
        $legacyBannerType = (int) optional(optional($legacyBanners->first())->webmasterBanner)->type;
        $fileField = 'file_'.$languageCode;
        $fallbackFileField = 'file_'.$defaultLanguageCode;
        $linkField = 'link_'.$languageCode;

        $sliderItems = collect($banners)->filter(function ($banner) {
            return (int) optional($banner->webmasterBanner)->type === 1;
        })->map(function ($banner) use ($titleField, $fallbackTitleField, $detailsField, $fallbackDetailsField, $fileField, $fallbackFileField, $linkField) {
            $file = $banner->{$fileField} ?: $banner->{$fallbackFileField};

            return (object) [
                'title' => $banner->{$titleField} ?: $banner->{$fallbackTitleField},
                'details' => $banner->{$detailsField} ?: $banner->{$fallbackDetailsField},
                'image' => $file ? route('fileView', ['path' => 'banners/'.$file]) : null,
                'link' => $banner->{$linkField},
                'category' => null,
            ];
        })->filter(fn ($item) => filled($item->image))->values();
    }

    $sliderCount = $sliderItems->count();
    $sliderId = 'matchday-carousel-'.($TopicBlock->id ?? $BannersSettingsId ?? uniqid());
@endphp

@if($sliderCount)
    <section id="landing-slider-{{ $TopicBlock->id ?? 'main' }}" class="matchday-hero" aria-label="{{ __('frontend.heroStories') }}">
        <div class="matchday-stage">
            <div id="{{ $sliderId }}"
                 class="carousel slide matchday-carousel {{ $sliderCount === 1 ? 'matchday-single-slide' : '' }}"
                 data-matchday-carousel
                 data-slider-interval="5000"
                 dir="{{ $languageCode === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="carousel-inner">
                    @foreach($sliderItems as $sliderItem)
                        @php
                            $plainTitle = trim(strip_tags((string) $sliderItem->title));
                            $plainDetails = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $sliderItem->details)));
                        @endphp
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-matchday-slide>
                            <article class="matchday-slide">
                                <div class="matchday-image" aria-hidden="true">
                                    <img src="{{ $sliderItem->image }}" alt="" width="1920" height="1080"
                                         {{ $loop->first ? 'fetchpriority=high' : 'loading=lazy' }}>
                                </div>
                                <div class="matchday-shade" aria-hidden="true"></div>
                                <div class="matchday-outline" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>

                                <div class="container matchday-content">
                                    <div class="matchday-copy">
                                        <div class="matchday-kicker">
                                            <span class="matchday-brand"><b>967</b>SPORT</span>
                                            <span class="matchday-live"><i></i>{{ $sliderItem->category ?: __('frontend.featuredNews') }}</span>
                                        </div>
                                        <div class="matchday-headline">
                                            <span>{{ __('frontend.heroTopStory') }}</span>
                                            <h2>{{ $plainTitle }}</h2>
                                        </div>
                                        @if($plainDetails)
                                            <p class="matchday-description">{{ \Illuminate\Support\Str::limit($plainDetails, 180) }}</p>
                                        @endif
                                        @if($sliderItem->link)
                                            <a href="{{ $sliderItem->link }}" class="matchday-read">
                                                <span>{{ __('frontend.readMore') }}</span>
                                                <i class="bi bi-arrow-up-left" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                <div class="matchday-progress" aria-hidden="true"><span></span></div>
                            </article>
                        </div>
                    @endforeach
                </div>

                @if($sliderCount > 1)
                    <div class="container matchday-navigation">
                        <div class="matchday-controls" aria-label="{{ __('frontend.heroStories') }}">
                            <button type="button" data-matchday-action="previous" aria-label="{{ __('frontend.heroPrevious') }}">
                                <i class="bi bi-chevron-{{ $languageCode === 'ar' ? 'right' : 'left' }}" aria-hidden="true"></i>
                            </button>
                            <span class="matchday-counter" aria-live="polite">
                                <b data-matchday-current>01</b>
                                <small>/ {{ str_pad($sliderCount, 2, '0', STR_PAD_LEFT) }}</small>
                            </span>
                            <button type="button" data-matchday-action="next" aria-label="{{ __('frontend.heroNext') }}">
                                <i class="bi bi-chevron-{{ $languageCode === 'ar' ? 'left' : 'right' }}" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="matchday-indicators" role="tablist" aria-label="{{ __('frontend.heroStories') }}">
                            @foreach($sliderItems as $sliderItem)
                                <button type="button" class="{{ $loop->first ? 'is-active' : '' }}"
                                        data-matchday-to="{{ $loop->index }}"
                                        aria-label="{{ __('frontend.slideNumber', ['number' => $loop->iteration]) }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}"></button>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @once
        @push('after-scripts')
            <script src="{{ URL::asset('assets/frontend/js/967sport-slider.js') }}?v={{ Helper::system_version() }}"></script>
        @endpush
    @endonce
@elseif($legacyBanners->isNotEmpty())
    <section class="legacy-slider-media text-center">
        @if($legacyBannerType === 0)
            @foreach($legacyBanners as $banner)
                @php
                    $legacyDetails = $banner->{$detailsField} ?: $banner->{$fallbackDetailsField};
                @endphp
                @if($legacyDetails)
                    <div>{!! $legacyDetails !!}</div>
                @endif
            @endforeach
        @else
            @foreach($legacyBanners as $banner)
                @php
                    $legacyFileField = 'file_'.$languageCode;
                    $legacyFallbackFileField = 'file_'.$defaultLanguageCode;
                    $legacyFile = $banner->{$legacyFileField} ?: $banner->{$legacyFallbackFileField};
                @endphp
                @if($banner->youtube_link && (int) $banner->video_type === 1 && Helper::Get_youtube_video_id($banner->youtube_link))
                    <iframe width="100%" height="500" frameborder="0" allowfullscreen
                            src="https://www.youtube.com/embed/{{ Helper::Get_youtube_video_id($banner->youtube_link) }}?autoplay=1&mute=1"
                            allow="autoplay"></iframe>
                @elseif($banner->youtube_link && (int) $banner->video_type === 2 && Helper::Get_vimeo_video_id($banner->youtube_link))
                    <iframe width="100%" height="500" frameborder="0" allowfullscreen
                            src="https://player.vimeo.com/video/{{ Helper::Get_vimeo_video_id($banner->youtube_link) }}?title=0&amp;byline=0"></iframe>
                @elseif((int) $banner->video_type === 0 && $legacyFile)
                    <video width="100%" height="500" controls autoplay muted playsinline>
                        <source src="{{ route('fileView', ['path' => 'banners/'.$legacyFile]) }}" type="video/mp4">
                    </video>
                @endif
            @endforeach
        @endif
    </section>
@endif
