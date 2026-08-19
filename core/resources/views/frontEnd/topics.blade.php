@extends('frontEnd.layouts.master')

@section('content')
    <div>
        <?php
        $title_var = "title_".@Helper::currentLanguage()->code;
        $title_var2 = "title_".config('smartend.default_language');
        $details_var = "details_".@Helper::currentLanguage()->code;
        $details_var2 = "details_".config('smartend.default_language');
        $slug_var = "seo_url_slug_".@Helper::currentLanguage()->code;
        $slug_var2 = "seo_url_slug_".config('smartend.default_language');

        $webmaster_section_title = "";
        $category_title = "";
        $page_title = "";
        $category_image = "";

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
                $category_image = route("fileView",["path" =>'topics/'.@$WebmasterSection->photo ]);
            }
        }
        if ($CurrentCategory != "none") {
            if (!empty($CurrentCategory)) {
                if (@$CurrentCategory->$title_var != "") {
                    $category_title = @$CurrentCategory->$title_var;
                } else {
                    $category_title = @$CurrentCategory->$title_var2;
                }
                $page_title = $category_title;
                if (@$CurrentCategory->photo != "") {
                    $category_image = route("fileView",["path" =>'sections/'.@$CurrentCategory->photo ]);
                }

                $custom_css_code .= @$CurrentCategory->css_code;
                $custom_js_code .= @$CurrentCategory->js_code;
                $custom_body_code .= @$CurrentCategory->body_code;
            }
        }
        if (!empty(@$DBTag)) {
            $page_title = $DBTag->title;
        }
        $Category_description = null;
        if(trim(@$CurrentCategory->$details_var) !=""){
            $Category_description = @$CurrentCategory->$details_var;
        }
        if(@$page_type == "tag" && trim(@$TagDescription)){
            $Category_description = @$TagDescription;
        }
        $is_services_section = (int) @$WebmasterSection->id === 2;
        $has_categories = @count($Categories) > 0;
        ?>

        <section class="breadcrumbs d-flex align-items-center justify-content-center {{ $is_services_section ? 'services-breadcrumbs' : '' }}" style="background-image: url({{$category_image}})">
            <div class="container">
                <div class="">
                    <h1>{{  (@$search_word !="")?(__('backend.resultsFoundFor')." [ ".@$search_word." ]"):$page_title }}</h1>
                    <ol>
                        <li><a href="{{ Helper::homeURL() }}">{{ __("backend.home") }}</a></li>
                        @if(@$search_word !="")
                            <li class="active">{!! __("backend.search") !!}</li>
                        @elseif($webmaster_section_title !="")
                            <li class="active"><a
                                    href="{{ Helper::sectionURL(@$WebmasterSection->id) }}">{!! $webmaster_section_title !!}</a>
                            </li>
                        @elseif(@$search_word!="")
                            <li class="active">{{ @$search_word }}</li>
                        @elseif(!empty(@$DBTag))
                            <li class="active">{{ @$DBTag->title }}</li>
                        @else
                            <li class="active">{{ @$User->name }}</li>
                        @endif
                        @if($category_title !="")
                            <li class="active"><a
                                    href="{{ Helper::categoryURL(@$CurrentCategory->id) }}">{{ $category_title }}</a>
                            </li>
                        @endif
                    </ol>
                </div>
                @if($Category_description)
                    <div class="text-muted mt-2 category-details">
                        {!! nl2br($Category_description) !!}
                    </div>
                @endif
            </div>
        </section>
        <section id="content" class="{{ $is_services_section ? 'services-directory-content' : '' }}">
            <div class="container">
                <div class="row g-lg-5">
                    @if($has_categories)
                        @include('frontEnd.layouts.side')
                    @endif
                    <div
                        class="col-lg-{{ $has_categories ? "9":"12" }} col-md-{{ $has_categories ? "7":"12" }} col-sm-12 col-xs-12 {{ $is_services_section ? 'services-directory-main' : '' }}">
                        @if($Topics->total() == 0)
                            <div class="p-5 card text-center no-data">
                                <i class="fa fa-desktop fa-5x opacity-50"></i>
                                <h5 class="mt-3 text-muted">{{ __('frontend.noData') }}</h5>
                            </div>
                        @else
                            <div class="row {{ $is_services_section ? 'services-directory-grid g-4' : '' }}">
                                @if($Topics->total() > 0)

                                        <?php
                                        $i = 0;
                                        $cols_lg = 4;
                                        $cols_md = 6;
                                        if (count($Categories) > 0) {
                                            $cols_lg = 6;
                                            $cols_md = 12;
                                        }
                                        ?>
                                    @foreach($Topics as $Topic)
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
                                            ?>
                                        <div
                                            class="col-lg-{{$cols_lg}} col-md-{{$cols_md}} {{ $is_services_section ? 'd-flex' : '' }}">
                                            @if($is_services_section)
                                                <article class="service-directory-card" style="--service-order: {{ $loop->index }}">
                                                    <a class="service-directory-link" aria-label="{{ $title }}" href="{{ $topic_link_url }}">
                                                        <span class="service-directory-number" aria-hidden="true">
                                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                                        </span>
                                                        <span class="service-directory-icon" aria-hidden="true">
                                                            @if($Topic->icon !="")
                                                                <i class="{!! $Topic->icon !!}"></i>
                                                            @else
                                                                <i class="bi bi-briefcase"></i>
                                                            @endif
                                                        </span>
                                                        <span class="service-directory-body">
                                                            <h2>{{ $title }}</h2>
                                                            @if(strip_tags($Topic->$details) !="")
                                                                <span class="service-directory-description">
                                                                    {{ mb_substr(strip_tags($Topic->$details), 0, 150) }}{{ mb_strlen(strip_tags($Topic->$details)) > 150 ? '…' : '' }}
                                                                </span>
                                                            @endif
                                                        </span>
                                                        {{-- <span class="service-directory-action">
                                                            <span>{{ __('frontend.moreDetails') }}</span>
                                                            <i class="bi bi-arrow-left"></i>
                                                        </span> --}}
                                                    </a>
                                                </article>
                                            @else
                                                @include("frontEnd.topic.card",["Topic"=>$Topic])
                                            @endif
                                        </div>
                                            <?php
                                            $i++;
                                            ?>
                                    @endforeach

                            </div>
                            <div class="row {{ $is_services_section ? 'services-directory-pagination align-items-center' : '' }}">
                                <div class="col-lg-8">
                                    {!! $Topics->appends($_GET)->links() !!}
                                </div>
                                <div class="col-lg-4 text-end">
                                    <h5 class="{{ $is_services_section ? 'services-results-count' : '' }}" style="padding-top: 18px">{{ $Topics->firstItem() }}
                                        - {{ $Topics->lastItem() }} {{ __('backend.of') }}
                                        ( {{ $Topics->total()  }} ) {{ __('backend.records') }}</h5>
                                </div>
                            </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('frontEnd.layouts.popup',['Popup'=>@$Popup])
@endsection
@if (@in_array(@$WebmasterSection->type, [3]))
    @push('before-styles')
        <link rel="stylesheet"
              href="{{ URL::asset('assets/frontend/vendor/green-audio-player/css/green-audio-player.min.css') }}?v={{ Helper::system_version() }}"/>
    @endpush
    @push('after-scripts')
        <script
            src="{{ URL::asset('assets/frontend/vendor/green-audio-player/js/green-audio-player.min.js') }}?v={{ Helper::system_version() }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                GreenAudioPlayer.init({
                    selector: '.audio-player',
                    stopOthersOnPlay: true,
                    showTooltips: true,
                });
            });
        </script>
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
