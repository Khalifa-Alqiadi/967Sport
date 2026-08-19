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
?>
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
        @if(@$TopicBlock->css_classes == 'image-view')
            <div class="row align-items-center g-4">
                <div class="col-md-6 meu-reveal meu-reveal-left">
                    <div class="image">
                        <img src="{{route("fileView",["path" =>'topics/'. $Topic->photo_file])}}" alt="" class="w-100 h-100" />
                    </div>
                </div>
                <div class="col-md-6 meu-reveal meu-reveal-right">
                    <div class="details meu-prose">
                        {!! str_replace('"#','"'.Request::url().'#',@$TopicBlockContents->{"details_".@Helper::currentLanguage()->code}) !!}

                    </div>
                </div>
            </div>
        @else
            <div class="meu-prose meu-reveal">
                {!! str_replace('"#','"'.Request::url().'#',@$TopicBlockContents->{"details_".@Helper::currentLanguage()->code}) !!}
            </div>
        @endif
    </div>
</section>
