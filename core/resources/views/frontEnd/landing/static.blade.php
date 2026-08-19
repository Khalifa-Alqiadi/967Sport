<?php
$block_style = "";
if (@$TopicBlock->bg_color != "") {
    $block_style = "background-color: ".@$TopicBlock->bg_color.";";
}
if (@$TopicBlock->divider_status) {
    @$TopicBlock->css_classes .= " divider";
}
if (@$TopicBlock->image_status && @$TopicBlockContents->{"bg_".@Helper::currentLanguage()->code} !="") {
    $block_style .= "background-image: url(".route("fileView",["path" =>'topics/'.@$TopicBlockContents->{"bg_". @Helper::currentLanguage()->code}]).");background-size:cover;background-repeat: no-repeat;background-position: center top;";
}
$block_classes = trim((string) @$TopicBlock->css_classes);
$is_image_view = in_array('image-view', preg_split('/\s+/', $block_classes), true);
?>
@if((int) @$TopicBlock->id === 6)
<section id="landing-block-{{ @$TopicBlock->id }}" class="landing-block sport-manifesto" style="{{ $block_style }}">
    <div class="sport-manifesto-lines" aria-hidden="true"></div>
    <div class="container position-relative">
        <div class="row align-items-center gy-5">
            <div class="col-lg-5">
                <div class="sport-manifesto-mark meu-reveal">
                    <span class="sport-manifesto-eyebrow">YEMEN FOOTBALL</span>
                    <strong>967</strong>
                    <b>SPORT</b>
                    <span class="sport-manifesto-ball"><i class="bi bi-dribbble"></i></span>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="sport-manifesto-copy meu-reveal meu-reveal-right">
                    <span class="sport-section-label"><i></i> منصة كرة القدم اليمنية</span>
                    @if(@$TopicBlock->title_status)
                        <h2>{{ @$TopicBlockContents->{"title_".@Helper::currentLanguage()->code} }}</h2>
                    @endif
                    @if(@$TopicBlock->desc_status)
                        <p class="sport-manifesto-lead">{!! nl2br(@$TopicBlockContents->{"desc_".@Helper::currentLanguage()->code}) !!}</p>
                    @endif
                    <div class="sport-manifesto-text">
                        {!! str_replace('"#','"'.Request::url().'#',@$TopicBlockContents->{"details_".@Helper::currentLanguage()->code}) !!}
                    </div>
                    <div class="sport-manifesto-tags" aria-label="تغطية المنصة">
                        <span>الدوري اليمني</span><span>المنتخبات</span><span>النتائج</span><span>المحترفون</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<section id="landing-block-{{ @$TopicBlock->id }}" class="landing-block meu-static-block {{ $is_image_view ? 'meu-static-visual' : 'meu-static-document' }} {{ $block_classes }}" style="{{ $block_style }}">
    <span class="meu-static-decor meu-static-decor-one" aria-hidden="true"></span>
    <span class="meu-static-decor meu-static-decor-two" aria-hidden="true"></span>
    <div class="container">
        @if(@$TopicBlock->title_status || @$TopicBlock->desc_status)
            <div class="section-title meu-reveal">
                <span class="meu-kicker" aria-hidden="true"></span>
                <div class="meu-heading">
                    @if(@$TopicBlock->title_status)
                        <h2>{{ @$TopicBlockContents->{"title_".@Helper::currentLanguage()->code} }}</h2>
                    @endif
                    @if(@$TopicBlock->desc_status)
                        <p>{!! nl2br(@$TopicBlockContents->{"desc_".@Helper::currentLanguage()->code}) !!}</p>
                    @endif
                </div>
            </div>
        @endif
        @if($is_image_view)
            <div class="row align-items-center g-5 meu-static-layout">
                <div class="col-lg-5 meu-reveal meu-reveal-left">
                    <div class="image meu-static-media">
                        <span class="meu-static-media-ring" aria-hidden="true"></span>
                        <span class="meu-static-media-shine" aria-hidden="true"></span>
                        <img src="{{route("fileView",["path" =>'topics/'. $Topic->photo_file])}}"
                             alt="{{ @$TopicBlockContents->{"title_".@Helper::currentLanguage()->code} }}" loading="lazy" />
                    </div>
                </div>
                <div class="col-lg-7 meu-reveal meu-reveal-right">
                    <div class="details meu-prose meu-static-copy meu-stagger">
                        {!! str_replace('"#','"'.Request::url().'#',@$TopicBlockContents->{"details_".@Helper::currentLanguage()->code}) !!}

                    </div>
                </div>
            </div>
        @else
            <div class="meu-static-document-frame meu-reveal">
                <div class="meu-static-document-toolbar" aria-hidden="true">
                    <span></span><span></span><span></span>
                    <b>967SPORT</b>
                </div>
                <span class="meu-static-document-watermark" aria-hidden="true">967</span>
                <div class="meu-prose meu-static-copy meu-static-copy-wide meu-stagger">
                    {!! str_replace('"#','"'.Request::url().'#',@$TopicBlockContents->{"details_".@Helper::currentLanguage()->code}) !!}
                </div>
            </div>
        @endif
    </div>
</section>
@endif
