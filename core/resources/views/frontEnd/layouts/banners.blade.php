@if(@$BannersSettingsId >0)
        <?php
        $block_style = "";
        if (@$TopicBlock->bg_color != "") {
            $block_style = "background-color: ".@$TopicBlock->bg_color.";";
        }
        if (@$TopicBlock->image_status && @$TopicBlockContents->{"bg_".@Helper::currentLanguage()->code} != "") {
            $block_style .= "background-image: url(".route("fileView",
                    ["path" => 'topics/'.@$TopicBlockContents->{"bg_".@Helper::currentLanguage()->code}]).");";
        }
        $title_var = "title_".@Helper::currentLanguage()->code;
        $title_var2 = "title_".config('smartend.default_language');
        $details_var = "details_".@Helper::currentLanguage()->code;
        $details_var2 = "details_".config('smartend.default_language');

        // Get banners list array by settings ID (You can get settings ID from Webmaster >> Banners settings)
        $BannersList = Helper::BannersList($BannersSettingsId);
        ?>
    @if(count($BannersList)>0)
        <div class="widget meu-banners {{ @$TopicBlock->css_classes  }}" style="{{ $block_style }}">
            <!-- Slider -->
                <?php
                $SideBanner_type = 0;
                ?>
            @foreach($BannersList->slice(0,1) as $SideBanner)
                    <?php
                    try {
                        $SideBanner_type = $SideBanner->webmasterBanner->type;
                    } catch (Exception $e) {
                        $SideBanner_type = 0;
                    }
                    ?>
            @endforeach
                <?php
                $title_var = "title_".@Helper::currentLanguage()->code;
                $details_var = "details_".@Helper::currentLanguage()->code;
                $file_var = "file_".@Helper::currentLanguage()->code;
                $file_var2 = "file_".config('smartend.default_language');
                $link_var = "link_".@Helper::currentLanguage()->code;
                ?>
            @if($SideBanner_type==0)
                {{-- Text/Code Banners--}}
                    <?php
                    $media_banners = $BannersList->where("icon", "!=", "")->count();
                    ?>
                <section class="meu-banners-section {{ ($media_banners >0)?"services":"" }}">
                    <div class="container">
                        @if(@$TopicBlock->title_status || @$TopicBlock->desc_status)
                            <div class="section-title meu-banners-heading">
                                <span class="meu-banners-heading-mark" aria-hidden="true"></span>
                                <div class="meu-banners-heading-copy">
                                @if(@$TopicBlock->title_status)
                                    <h1>{{ @$TopicBlockContents->{"title_".@Helper::currentLanguage()->code} }}</h1>
                                @endif
                                @if(@$TopicBlock->desc_status)
                                    <p>{!! nl2br(@$TopicBlockContents->{"desc_".@Helper::currentLanguage()->code}) !!}</p>
                                @endif
                                </div>
                            </div>
                        @endif
                        <div class="row g-4 meu-banners-grid">
                                <?php
                                $col_width = 12;
                                $col_md_width = 12;
                                if (count($BannersList) == 2) {
                                    $col_width = 6;
                                    $col_md_width = 6;
                                }
                                if (count($BannersList) == 3) {
                                    $col_width = 4;
                                    $col_md_width = 6;
                                }
                                if (count($BannersList) > 3 && count($BannersList) < 6) {
                                    $col_width = 3;
                                    $col_md_width = 6;
                                }
                                if (count($BannersList) >= 6) {
                                    $col_width = 4;
                                    $col_md_width = 6;
                                }
                                ?>
                            @foreach($BannersList as $TextBanner)
                                    <?php
                                    if ($TextBanner->$title_var != "") {
                                        $BTitle = $TextBanner->$title_var;
                                    } else {
                                        $BTitle = $TextBanner->$title_var2;
                                    }
                                    if ($TextBanner->$details_var != "") {
                                        $BDetails = $TextBanner->$details_var;
                                    } else {
                                        $BDetails = $TextBanner->$details_var2;
                                    }
                                    if ($TextBanner->$file_var != "") {
                                        $BFile = $TextBanner->$file_var;
                                    } else {
                                        $BFile = $TextBanner->$file_var2;
                                    }
                                    ?>
                                <div class="col-lg-{{$col_width}} col-md-{{ $col_md_width }} d-flex align-items-stretch meu-banner-col">
                                    @if($TextBanner->$link_var !="")
                                        <a class="meu-banner-link" href="{!! $TextBanner->$link_var !!}">
                                            @endif
                                            @if($BannersSettingsId == 2)
                                                <div class="card meu-banner-card">
                                                    @if($TextBanner->icon !="")
                                                        <div class="icon meu-banner-icon">
                                                            <i class="{{$TextBanner->icon}} fa-4x"></i>
                                                        </div>
                                                    @elseif($BFile !="")
                                                        <img src="{{ route("fileView",["path" =>'banners/'.$BFile ]) }}" loading="lazy"
                                                            class="meu-banner-image" alt="{{ $BTitle }}"/>
                                                    @else
                                                        <div class="meu-banner-icon meu-banner-icon-fallback" aria-hidden="true"></div>
                                                    @endif
                                                    <div class="details meu-banner-content">
                                                        <h2>{!! $BTitle !!}</h2>
                                                        @if($BDetails !="")
                                                            <p>{!! nl2br($BDetails) !!}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <div class="meu-banner-card {{ ($media_banners >0) && $BannersSettingsId != 6 ?"icon-box":"card border-0" }}">
                                                    @if($TextBanner->code !="")
                                                        {!! $TextBanner->code !!}
                                                    @else

                                                        @if($TextBanner->icon !="")
                                                            <div class="icon meu-banner-icon">
                                                                <i class="{{$TextBanner->icon}} fa-3x"></i>
                                                            </div>
                                                        @elseif($BFile !="")
                                                            <img src="{{ route("fileView",["path" =>'banners/'.$BFile ]) }}" loading="lazy"
                                                                class="meu-banner-image" alt="{{ $BTitle }}"/>
                                                        @else
                                                            <div class="meu-banner-icon meu-banner-icon-fallback" aria-hidden="true"></div>
                                                        @endif
                                                        <h2>{!! $BTitle !!}</h2>
                                                        @if($BDetails !="")
                                                            <p>{!! nl2br($BDetails) !!}</p>
                                                        @endif

                                                    @endif
                                                </div>
                                            @endif
                                            @if($TextBanner->$link_var !="")
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @elseif($SideBanner_type==1)
                {{-- Photo Slider Banners--}}
                <div class="container">
                    <div class="row">
                        @foreach($BannersList as $SideBanner)
                            <div class="col-lg-12 col-md-12 text-center">
                                @if($SideBanner->$link_var !="")
                                    <a href="{!! $SideBanner->$link_var !!}">
                                        @endif
                                        @if($SideBanner->$file_var !="")
                                            <img src="{{ route("fileView",["path" =>'banners/'.$SideBanner->$file_var ]) }}"
                                                 alt="{{ $SideBanner->$title_var }}"/>
                                        @endif
                                        @if($SideBanner->$link_var !="")
                                    </a>
                                @endif
                                @if($SideBanner->$details_var !="")
                                    <p>{!! nl2br($SideBanner->$details_var) !!}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Video Banners--}}
                <div class="container">
                    <div class="row">
                        @foreach($BannersList as $SideBanner)
                            <div class="col-lg-12 col-md-12 text-center">
                                @if($SideBanner->youtube_link !="")
                                    @if($SideBanner->video_type ==1)
                                            <?php
                                            $Youtube_id = Helper::Get_youtube_video_id($SideBanner->youtube_link);
                                            ?>
                                        @if($Youtube_id !="")
                                            {{-- Youtube Video --}}
                                            <iframe width="100%" height="500" frameborder="0" allowfullscreen
                                                    src="https://www.youtube.com/embed/{{ $Youtube_id }}?autoplay=1&mute=1" allow="autoplay">
                                            </iframe>
                                        @endif
                                    @elseif($SideBanner->video_type ==2)
                                            <?php
                                            $Vimeo_id = Helper::Get_vimeo_video_id($SideBanner->youtube_link);
                                            ?>
                                        @if($Vimeo_id !="")
                                            {{-- Vimeo Video --}}
                                            <iframe width="100%" height="500" frameborder="0" allowfullscreen
                                                    src="https://player.vimeo.com/video/{{ $Vimeo_id }}?title=0&amp;byline=0">
                                            </iframe>
                                        @endif
                                    @endif
                                @endif
                                @if($SideBanner->video_type ==0)
                                    @if($SideBanner->$file_var !="")
                                        {{-- Direct Video --}}
                                        <video width="100%" height="500" controls>
                                            <source src="{{ route("fileView",["path" =>'banners/'.$SideBanner->$file_var ]) }}"
                                                    type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @endif
                                @endif
                                @if($SideBanner->$details_var !="")
                                    <div>{!! $SideBanner->$details_var !!}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <!-- end slider -->
        </div>
    @endif
    @if(@$TopicBlockContents->{"bg_".@Helper::currentLanguage()->code} != "")
        @push('after-styles')
            <style>
                .widget{
                    background-position: center;
                    background-repeat: no-repeat;
                    background-attachment: fixed;
                    background-size: cover;
                    position: relative;
                }
                .widget::after{
                    content: "";
                    left: 0;
                    right: 0;
                    top: 0;
                    bottom: 0;
                    background-color: #000;
                    opacity: 60%;
                    position: absolute;
                }
                .widget .section-title h1,
                .widget .section-title p{
                    color: #fff;
                }
                .widget .container{
                    position: relative;
                    z-index: 1;
                }
            </style>
        @endpush
    @endif
@endif
