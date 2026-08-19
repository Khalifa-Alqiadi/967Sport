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
    <section id="landing-block-{{ @$TopicBlock->id }}" class="blog landing-block {{ @$TopicBlock->css_classes  }}" style="{{ $block_style }}">
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
            <div class="meu-editorial meu-stagger">
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
                        $Category = @$Topic->category($Topic->id);
                        ?>
                    <article class="meu-editorial-row">
                        <div class="meu-editorial-num">{{ sprintf('%02d', $loop->iteration) }}</div>
                        @if($Topic->photo_file !="")
                            <a href="{{ $topic_link_url }}" class="meu-editorial-media">
                                <img loading="lazy"
                                     src="{{ route("fileView",["path" =>'topics/'.$Topic->photo_file ]) }}?w=500&h=375&r=fit"
                                     alt="{{ $title }}"/>
                            </a>
                        @endif
                        <div class="meu-editorial-body">
                            @if($Category)
                                <span class="meu-post-cat">{{ @$Topic->category($Topic->id)->$title_var }}</span>
                            @endif
                            <a href="{{ $topic_link_url }}">
                                <h3 class="item-title">{!! $title !!}</h3>
                            </a>
                            @include("frontEnd.topic.fields",["cols"=>12,"Fields"=>@$Topic->webmasterSection->customFields->where("in_listing",true)])
                            @if(strip_tags($Topic->$details) !="")
                                <p class="card-text mb-auto">
                                    {!! mb_substr(strip_tags($Topic->$details),0, 140)."..." !!}
                                    <a href="{{ $topic_link_url }}">{{ __("frontend.moreDetails") }}</a>
                                </p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
            @if (@$TopicBlock->more_btn_status)
                <div class="row mt-4">
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
@endif
