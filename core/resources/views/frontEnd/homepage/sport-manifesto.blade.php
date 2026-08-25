@php
    $manifestoLang = @Helper::currentLanguage()->code;
    $manifestoDefaultLang = config('smartend.default_language');
    $manifestoTitleVar = 'title_'.$manifestoLang;
    $manifestoFallbackTitleVar = 'title_'.$manifestoDefaultLang;
    $manifestoDescVar = 'desc_'.$manifestoLang;
    $manifestoFallbackDescVar = 'desc_'.$manifestoDefaultLang;
    $manifestoDetailsVar = 'details_'.$manifestoLang;
    $manifestoFallbackDetailsVar = 'details_'.$manifestoDefaultLang;
    $manifestoLinkVar = 'link_'.$manifestoLang;
    $manifestoFallbackLinkVar = 'link_'.$manifestoDefaultLang;
    $manifestoTitle = @$TopicBlockContents->$manifestoTitleVar ?: @$TopicBlockContents->$manifestoFallbackTitleVar;
    $manifestoDescription = @$TopicBlockContents->$manifestoDescVar ?: @$TopicBlockContents->$manifestoFallbackDescVar;
    $manifestoDetails = @$TopicBlockContents->$manifestoDetailsVar ?: @$TopicBlockContents->$manifestoFallbackDetailsVar;
    $manifestoBannerAreaId = (int) (@$TopicBlockContents->banner_area_id ?: 0);
    $manifestoBanners = $manifestoBannerAreaId > 0
        ? Helper::BannersList($manifestoBannerAreaId)
        : collect();
    $manifestoStyle = '';
    if (@$TopicBlock->bg_color) {
        $manifestoStyle .= 'background-color:'.@$TopicBlock->bg_color.';';
    }
    $manifestoBackground = @$TopicBlockContents->{'bg_'.$manifestoLang}
        ?: @$TopicBlockContents->{'bg_'.$manifestoDefaultLang};
    if (@$TopicBlock->image_status && $manifestoBackground) {
        $manifestoStyle .= 'background-image:url('.route('fileView', ['path' => 'topics/'.$manifestoBackground]).');background-size:cover;background-repeat:no-repeat;background-position:center top;';
    }
@endphp

<section id="landing-block-{{ @$TopicBlock->id }}"
         class="landing-block sport-manifesto {{ @$TopicBlock->css_classes }} {{ @$TopicBlock->divider_status ? 'divider' : '' }}"
         style="{{ $manifestoStyle }}">
    <div class="sport-manifesto-lines" aria-hidden="true"></div>
    <div class="container position-relative">
        <div class="row align-items-center gy-5">
            <div class="col-lg-5">
                <div class="sport-manifesto-mark meu-reveal">
                    <span class="sport-manifesto-eyebrow">{{ __('frontend.sportManifestoEyebrow') }}</span>
                    <strong>967</strong>
                    <b>{{ __('frontend.sportBrandWord') }}</b>
                    <span class="sport-manifesto-ball"><i class="bi bi-dribbble"></i></span>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="sport-manifesto-copy meu-reveal meu-reveal-right">
                    <span class="sport-section-label"><i></i> {{ __('frontend.sportPlatformName') }}</span>
                    @if(@$TopicBlock->title_status && $manifestoTitle)
                        <h2>{{ $manifestoTitle }}</h2>
                    @endif
                    @if(@$TopicBlock->desc_status && $manifestoDescription)
                        <p class="sport-manifesto-lead">{!! nl2br(e($manifestoDescription)) !!}</p>
                    @endif
                    @if($manifestoDetails)
                        <div class="sport-manifesto-text">
                            {!! str_replace('"#', '"'.Request::url().'#', $manifestoDetails) !!}
                        </div>
                    @endif
                    @if($manifestoBanners->isNotEmpty())
                        <nav class="sport-manifesto-tags" aria-label="{{ __('frontend.sportManifestoCoverageLinks') }}">
                            @foreach($manifestoBanners as $manifestoBanner)
                                @php
                                    $manifestoBannerTitle = $manifestoBanner->$manifestoTitleVar ?: $manifestoBanner->$manifestoFallbackTitleVar;
                                    $manifestoBannerLink = $manifestoBanner->$manifestoLinkVar
                                        ?: ($manifestoBanner->$manifestoFallbackLinkVar ?: $manifestoBanner->link_url);
                                @endphp
                                <a href="{{ $manifestoBannerLink ?: '#' }}">
                                    @if($manifestoBanner->icon)
                                        <i class="bi {{ $manifestoBanner->icon }}" aria-hidden="true"></i>
                                    @endif
                                    <span>{{ $manifestoBannerTitle }}</span>
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
