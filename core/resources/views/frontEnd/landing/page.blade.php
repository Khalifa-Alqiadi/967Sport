@php($page_type = 'home')
@extends('frontEnd.layouts.master')

@if((int) @$Topic->id === 5)
    @section('bodyClass', 'sport-home-page')
@endif

@section('content')
    <?php
    $custom_css_code = @$WebmasterSection->css_code;
    $custom_js_code = @$WebmasterSection->js_code;
    $custom_body_code = @$WebmasterSection->body_code;

    if (@$WebmasterSection != "none") {
        if (@$WebmasterSection->$title_var != "") {
            $webmaster_section_title = @$WebmasterSection->$title_var;
        } else {
            $webmaster_section_title = @$WebmasterSection->$title_var2;
        }
        $page_title = $webmaster_section_title;
        if (@$WebmasterSection->photo != "") {
            $category_image = route("fileView", ["path" => 'topics/'.@$WebmasterSection->photo]);
        }
    }

    $custom_css_code .= $Topic->css_code;
    $custom_js_code .= $Topic->js_code;
    $custom_body_code .= $Topic->body_code;
    ?>
    @php($isSportHome = (int) @$Topic->id === 5)
    <div class="landing-page {{ $isSportHome ? 'sport-home' : '' }}">

        @if($Topic->topicBlocks->where("status",1)->count() >0)
            @foreach($Topic->topicBlocks->where("status",1) as $TopicBlock)
                @if ($TopicBlock->content != "")
                        <?php
                        try {
                            $TopicBlockContents = json_decode($TopicBlock->content);
                        } catch (Exception $e) {
                            $TopicBlockContents = [];
                        }
                        ?>
                    @if($TopicBlock->type == 4)
                        {{--Form--}}
                        @if(@$TopicBlockContents->view_style !="")
                            @if(@$TopicBlockContents->module_id >0)
                                @include('frontEnd.landing.'.strtolower(str_replace(" ","",@$TopicBlockContents->view_style)))
                            @else
                                @include('frontEnd.landing.contact')
                            @endif
                        @endif
                    @endif
                    @if($TopicBlock->type == 3)
                        {{--Dynamic--}}
                        @if(@$TopicBlockContents->view_style !="")
                            @include('frontEnd.landing.'.strtolower(str_replace(" ","",@$TopicBlockContents->view_style)))
                        @endif
                    @elseif($TopicBlock->type == 2)
                        {{--Banners--}}
                        @if(@$TopicBlockContents->banner_area_id >0 && @$TopicBlockContents->banner_style !="")
                            @include('frontEnd.layouts.'.@$TopicBlockContents->banner_style,["BannersSettingsId"=>@$TopicBlockContents->banner_area_id])
                        @endif
                    @elseif($TopicBlock->type == 1)
                        {{--Custom Code--}}
                        @include('frontEnd.landing.code')
                    @elseif($TopicBlock->type == 0)
                        {{--Static Content--}}
                        @include('frontEnd.landing.static')
                    @endif

                    @if($isSportHome && (int) @$TopicBlock->id === 5)
                        @include('frontEnd.homepage.sport-hub')
                    @endif
                @endif
            @endforeach
        @endif

        @include('frontEnd.layouts.popup',['Popup'=>@$Popup])
    </div>
@endsection
@if(@$isSportHome)
    @push('after-styles')
        <link href="{{ URL::asset('assets/frontend/css/967sport-home.css') }}?v={{ Helper::system_version() }}" rel="stylesheet"/>
    @endpush
@endif
@if($custom_css_code !="" || $custom_js_code !="")
    @push('after-styles')
        @if($custom_css_code !="")
            <style>
                {!! $custom_css_code !!}
            </style>
        @endif
        {!! $custom_js_code !!}
    @endpush
@endif
@if($custom_body_code !="")
    @push('before-footer')
        {!! Blade::render($custom_body_code) !!}
    @endpush
@endif
@push('after-scripts')
    <script>
        (function () {
            var targets = document.querySelectorAll('.landing-page .meu-reveal, .landing-page .meu-stagger');
            if (!targets.length) return;
            if (!('IntersectionObserver' in window)) {
                targets.forEach(function (el) { el.classList.add('meu-in'); });
                return;
            }
            var observer = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('meu-in');
                        obs.unobserve(entry.target);
                    }
                });
            }, {threshold: 0.15, rootMargin: '0px 0px -8% 0px'});
            targets.forEach(function (el) { observer.observe(el); });
        })();
    </script>
@endpush
