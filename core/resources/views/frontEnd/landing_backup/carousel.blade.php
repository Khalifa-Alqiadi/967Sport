<?php
$title_var = "title_".@Helper::currentLanguage()->code;
$title_var2 = "title_".config('smartend.default_language');
$details_var = "details_".@Helper::currentLanguage()->code;
$details_var2 = "details_".config('smartend.default_language');

$block_style = "";
if (@$TopicBlock->bg_color != "") {
    $block_style = "background-color: ".@$TopicBlock->bg_color.";";
}
if (@$TopicBlock->divider_status) {
    @$TopicBlock->css_classes .= " divider";
}
if (@$TopicBlock->image_status && @$TopicBlockContents->{"bg_".@Helper::currentLanguage()->code} != "") {
    $block_style .= "background-image: url(".route("fileView",
            ["path" => 'topics/'.@$TopicBlockContents->{"bg_".@Helper::currentLanguage()->code}]).");background-size:cover;background-repeat: no-repeat;background-position: center top;";
}
$BlockTopics = [];
$section_url = "";
if (@$TopicBlockContents->module_id) {
    $BlockTopics = Helper::Topics(@$TopicBlockContents->module_id, @$TopicBlockContents->category_ids,
        @$TopicBlockContents->records_count, 0, @$TopicBlockContents->records_order);
    $section_url = Helper::sectionURL(@$TopicBlockContents->module_id);
}
?>

@if(count($BlockTopics) >0)
    <section id="landing-block-{{ @$TopicBlock->id }}" class="landing-block {{ @$TopicBlock->css_classes  }}" style="{{ $block_style }}">
        <div class="container">
            @if(@$TopicBlock->title_status || @$TopicBlock->desc_status)
                <div class="section-title meu-reveal">
                    <span class="meu-kicker" aria-hidden="true"></span>
                    @if(@$TopicBlock->title_status)
                        <h2>{{ @$TopicBlockContents->{"title_".@Helper::currentLanguage()->code} }}</h2>
                    @endif
                    @if(@$TopicBlock->desc_status)
                        <p>{!! nl2br(@$TopicBlockContents->{"desc_".@Helper::currentLanguage()->code}) !!}</p>
                    @endif
                </div>
            @endif
            <div class="row">
                <div class="col-lg-12">
                    <div id="owl-slider-block-{{@$TopicBlock->id}}" class="owl-carousel owl-theme listing meu-carousel meu-reveal meu-reveal-scale">
                        @foreach($BlockTopics as $Topic)
                                <?php
                                if ($Topic->$title_var != "") {
                                    $title = $Topic->$title_var;
                                } else {
                                    $title = $Topic->$title_var2;
                                }
                                if ($Topic->$details_var != "") {
                                    $details = $details_var;
                                } else {
                                    $details = $details_var2;
                                }
                                $topic_link_url = Helper::topicURL($Topic->id, "", $Topic);
                                $HomeSectionType = @$Topic->webmasterSection->type;
                                if (!@$require_mp3_player && $HomeSectionType == 3) {
                                    $require_mp3_player = 1;
                                }
                                ?>
                            <div class="item">
                                @include("frontEnd.topic.card",["Topic"=>$Topic])
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
            @if (@$TopicBlock->more_btn_status)
                <div class="row">
                    <div class="col-lg-12">
                        <div class="more-btn">
                            <a href="{{ url($section_url) }}" class="btn btn-theme"><i
                                    class="fa fa-angle-left"></i>&nbsp; {{ __('frontend.viewMore') }}
                                &nbsp;<i
                                    class="fa fa-angle-right"></i></a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
    @push("after-scripts")
        <script>
            $("#owl-slider-block-{{@$TopicBlock->id}}").owlCarousel({
                items: 3,
                autoplay: !0,
                rtl: {{(@Helper::currentLanguage()->direction=="rtl")?"true":"false"}},
                autoplayTimeout: 3e3,
                dots: !0,
                responsive: {0: {items: 1, loop: 1}, 600: {items: 2, loop: 1}, 1e3: {items: 3, loop: 1}}
            });
        </script>
    @endpush
@endif
