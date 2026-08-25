@php
    $gatewayLang = @Helper::currentLanguage()->code;
    $gatewayDefaultLang = config('smartend.default_language');
    $gatewayTitleVar = 'title_'.$gatewayLang;
    $gatewayFallbackTitleVar = 'title_'.$gatewayDefaultLang;
    $gatewayDescVar = 'desc_'.$gatewayLang;
    $gatewayFallbackDescVar = 'desc_'.$gatewayDefaultLang;
    $gatewayLinkVar = 'link_'.$gatewayLang;
    $gatewayFallbackLinkVar = 'link_'.$gatewayDefaultLang;
    $gatewayAreaId = (int) (@$TopicBlockContents->banner_area_id ?: 0);
    $gatewayBanners = $gatewayAreaId > 0 ? Helper::BannersList($gatewayAreaId) : collect();
    $gatewayHeading = @$TopicBlockContents->$gatewayTitleVar ?: @$TopicBlockContents->$gatewayFallbackTitleVar;
    $gatewayDescription = @$TopicBlockContents->$gatewayDescVar ?: @$TopicBlockContents->$gatewayFallbackDescVar;
@endphp

<section id="landing-block-{{ @$TopicBlock->id }}"
         class="sport-hub sport-gateway-section {{ @$TopicBlock->css_classes }}"
         aria-label="{{ __('frontend.sportHubAria') }}">
    <div class="container sport-gateway-wrap">
        @if((@$TopicBlock->title_status && $gatewayHeading) || (@$TopicBlock->desc_status && $gatewayDescription))
            <div class="sport-gateway-head">
                <div>
                    <span class="sport-section-label"><i></i> {{ __('frontend.sportGatewayKicker') }}</span>
                    @if(@$TopicBlock->title_status && $gatewayHeading)
                        <h2>{{ $gatewayHeading }}</h2>
                    @endif
                </div>
                @if(@$TopicBlock->desc_status && $gatewayDescription)
                    <p>{!! nl2br(e($gatewayDescription)) !!}</p>
                @endif
            </div>
        @endif

        @if($gatewayBanners->isNotEmpty())
            <div class="sport-gateway-grid meu-stagger">
                @foreach($gatewayBanners as $gatewayBanner)
                    @php
                        $gatewayBannerTitle = $gatewayBanner->$gatewayTitleVar ?: $gatewayBanner->$gatewayFallbackTitleVar;
                        $gatewayBannerLink = $gatewayBanner->$gatewayLinkVar
                            ?: ($gatewayBanner->$gatewayFallbackLinkVar ?: $gatewayBanner->link_url);
                        $gatewayBannerIcon = $gatewayBanner->icon ?: 'bi-grid';
                    @endphp
                    <a href="{{ $gatewayBannerLink ?: '#' }}" class="sport-gateway-card">
                        <i class="bi {{ $gatewayBannerIcon }}" aria-hidden="true"></i>
                        <strong>{{ $gatewayBannerTitle }}</strong>
                        <span class="sport-gateway-arrow" aria-hidden="true"><i class="bi bi-arrow-up-left"></i></span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>
