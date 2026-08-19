@if(@$BannersSettingsId >0)
        <?php
        $SliderBanners = Helper::BannersList($BannersSettingsId);
        $SliderBannersCount = count($SliderBanners);
        ?>
    @if(count($SliderBanners)>0)
        <section id="matchday" class="matchday-hero">
            <div class="matchday-stage">
                @foreach($SliderBanners->slice(0,1) as $SliderBanner)
                        <?php
                        try {
                            $SliderBanner_type = $SliderBanner->webmasterBanner->type;
                        } catch (Exception $e) {
                            $SliderBanner_type = 0;
                        }
                        ?>
                @endforeach
                    <?php
                    $title_var = "title_".@Helper::currentLanguage()->code;
                    $title_var2 = "title_".config('smartend.default_language');
                    $details_var = "details_".@Helper::currentLanguage()->code;
                    $details_var2 = "details_".config('smartend.default_language');
                    $file_var = "file_".@Helper::currentLanguage()->code;
                    $file_var2 = "file_".config('smartend.default_language');
                    $link_var = "link_".@Helper::currentLanguage()->code;
                    ?>
                @if($SliderBanner_type==0)
                    {{-- Text/Code Banners--}}
                    <div class="text-center">
                        @foreach($SliderBanners as $SliderBanner)
                                <?php
                                if ($SliderBanner->$details_var != "") {
                                    if ($SliderBanner->$details_var != "") {
                                        $BDetails = $SliderBanner->$details_var;
                                    } else {
                                        $BDetails = $SliderBanner->$details_var2;
                                    }
                                } else {
                                    $BDetails = $SliderBanner->$details_var2;
                                }
                                ?>
                            @if($BDetails !="")
                                <div>{!! $BDetails !!}</div>
                            @endif
                        @endforeach
                    </div>
                @elseif($SliderBanner_type==1)
                    {{-- Photo Slider Banners--}}
                    <div id="matchdayCarousel" class="carousel slide {{ $SliderBannersCount === 1 ? 'matchday-single-slide' : '' }}" data-bs-ride="carousel"
                         data-bs-interval="7000" data-bs-pause="hover">
                        <div class="carousel-inner">

                            <?php $i = 0; ?>
                            @foreach($SliderBanners as $SliderBanner)
                                    <?php
                                    if ($SliderBanner->$title_var != "") {
                                        $BTitle = $SliderBanner->$title_var;
                                    } else {
                                        $BTitle = $SliderBanner->$title_var2;
                                    }
                                    $BDetails = $SliderBanner->$details_var;
                                    if ($SliderBanner->$file_var != "") {
                                        $BFile = $SliderBanner->$file_var;
                                    } else {
                                        $BFile = $SliderBanner->$file_var2;
                                    }
                                    $SliderBanner->loadMissing(['fixture.league', 'fixture.season', 'fixture.homeTeam', 'fixture.awayTeam']);
                                    $BFixture = $SliderBanner->fixture;
                                    ?>
                                <div class="carousel-item {{ ($i==0)?"active":"" }}">
                                    <div class="matchday-slide" style="--matchday-image:url('{{ route("fileView",["path" =>'banners/'.$BFile ]) }}')">
                                        <div class="matchday-image" aria-hidden="true"></div>
                                        <div class="matchday-cut" aria-hidden="true"></div>
                                        <div class="matchday-outline" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                                        <div class="container matchday-content">
                                            <div class="matchday-kicker">
                                                <span class="matchday-brand"><b>967</b>SPORT</span>
                                                <span class="matchday-live"><i></i>{{ __('frontend.heroLiveCoverage') }}</span>
                                            </div>
                                            <div class="matchday-headline">
                                                <span>{{ __('frontend.heroTopStory') }}</span>
                                                @if($BTitle !="")
                                                    <h2>{!! $BTitle !!}</h2>
                                                @endif
                                            </div>
                                            <div class="matchday-summary">
                                                @if($BFixture && $BFixture->homeTeam && $BFixture->awayTeam)
                                                    <?php
                                                        $isEnglish = Helper::currentLanguage()?->code === 'en';
                                                        $homeName = $isEnglish ? ($BFixture->homeTeam->name_en ?: $BFixture->homeTeam->name_ar) : ($BFixture->homeTeam->name_ar ?: $BFixture->homeTeam->name_en);
                                                        $awayName = $isEnglish ? ($BFixture->awayTeam->name_en ?: $BFixture->awayTeam->name_ar) : ($BFixture->awayTeam->name_ar ?: $BFixture->awayTeam->name_en);
                                                        $leagueName = $isEnglish ? ($BFixture->league?->name_en ?: $BFixture->league?->name_ar) : ($BFixture->league?->name_ar ?: $BFixture->league?->name_en);
                                                        $stateCode = strtoupper((string) $BFixture->state_code);
                                                        $isLive = !$BFixture->is_finished && ($BFixture->minute || preg_match('/LIVE|INPLAY|BREAK|HT/', $stateCode));
                                                        $homeScore = !is_null($BFixture->ft_home_score) ? $BFixture->ft_home_score : $BFixture->home_score;
                                                        $awayScore = !is_null($BFixture->ft_away_score) ? $BFixture->ft_away_score : $BFixture->away_score;
                                                    ?>
                                                    <div class="matchday-fixture {{ $isLive ? 'is-live' : '' }}">
                                                        <div class="matchday-fixture-top">
                                                            <span>{{ $leagueName }}@if($BFixture->season?->name) · {{ $BFixture->season->name }}@endif</span>
                                                            <b>
                                                                @if($isLive)
                                                                    <i></i>{{ __('frontend.sliderMatchLive') }}@if($BFixture->minute) {{ $BFixture->minute }}′@endif
                                                                @elseif($BFixture->is_finished)
                                                                    {{ __('frontend.sliderMatchFinished') }}
                                                                @else
                                                                    {{ __('frontend.sliderMatchUpcoming') }}
                                                                @endif
                                                            </b>
                                                        </div>
                                                        <div class="matchday-fixture-board">
                                                            <div class="matchday-club">
                                                                @if($BFixture->homeTeam->image_path)<img src="{{ $BFixture->homeTeam->image_path }}" alt="">@else<span class="matchday-club-placeholder">{{ mb_substr($homeName, 0, 1) }}</span>@endif
                                                                <strong>{{ $homeName }}</strong>
                                                            </div>
                                                            <div class="matchday-fixture-center">
                                                                @if($BFixture->is_finished || $isLive || !is_null($homeScore) || !is_null($awayScore))
                                                                    <span class="matchday-score"><em>{{ $homeScore ?? 0 }}</em><small>:</small><em>{{ $awayScore ?? 0 }}</em></span>
                                                                    @if(!is_null($BFixture->pen_home) && !is_null($BFixture->pen_away))
                                                                        <small>{{ __('frontend.sliderMatchPenalty') }} {{ $BFixture->pen_home }}-{{ $BFixture->pen_away }}</small>
                                                                    @endif
                                                                @else
                                                                    <time datetime="{{ $BFixture->starting_at?->toIso8601String() }}">
                                                                        <b>{{ $BFixture->starting_at?->format('H:i') }}</b>
                                                                        <small>{{ $BFixture->starting_at?->translatedFormat('d M Y') }}</small>
                                                                    </time>
                                                                @endif
                                                            </div>
                                                            <div class="matchday-club">
                                                                @if($BFixture->awayTeam->image_path)<img src="{{ $BFixture->awayTeam->image_path }}" alt="">@else<span class="matchday-club-placeholder">{{ mb_substr($awayName, 0, 1) }}</span>@endif
                                                                <strong>{{ $awayName }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if($BDetails !="")
                                                    <p>{!! nl2br($BDetails) !!}</p>
                                                @endif
                                                @if($SliderBanner->$link_var !="")
                                                    <a href="{!! $SliderBanner->$link_var !!}" class="matchday-read" aria-label="{{ __('frontend.moreDetails') }}">
                                                        <i class="bi bi-arrow-up-left"></i>
                                                        <span>{{ __('frontend.moreDetails') }}</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="matchday-stamp" aria-hidden="true">
                                            <b>YEMEN</b><span>FOOTBALL</span><small>967 / MEDIA</small>
                                        </div>
                                        <div class="matchday-progress" aria-hidden="true"><span></span></div>
                                    </div>
                                </div>
                                <?php $i++; ?>
                            @endforeach
                        </div>

                        <div class="container matchday-navigation">
                            <div class="matchday-story-list" role="tablist" aria-label="{{ __('frontend.heroStories') }}">
                                @foreach($SliderBanners as $SliderBanner)
                                    <?php $tabTitle = $SliderBanner->$title_var ?: $SliderBanner->$title_var2; ?>
                                    <button type="button" class="matchday-story {{ $loop->first ? 'active' : '' }}"
                                            data-bs-target="#matchdayCarousel" data-bs-slide-to="{{ $loop->index }}"
                                            aria-label="{{ strip_tags($tabTitle) }}" @if($loop->first) aria-current="true" @endif>
                                        <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <b>{{ \Illuminate\Support\Str::limit(strip_tags($tabTitle), 48) }}</b>
                                    </button>
                                @endforeach
                            </div>
                            @if(count($SliderBanners) >1)
                                <div class="matchday-controls">
                                    <button type="button" data-bs-target="#matchdayCarousel" data-bs-slide="prev" aria-label="{{ __('frontend.heroPrevious') }}">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                    <span><b class="matchday-current">01</b> / {{ str_pad($SliderBannersCount, 2, '0', STR_PAD_LEFT) }}</span>
                                    <button type="button" data-bs-target="#matchdayCarousel" data-bs-slide="next" aria-label="{{ __('frontend.heroNext') }}">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Video Banners--}}
                    <div class="text-center">
                        @foreach($SliderBanners as $SliderBanner)
                                <?php
                                if ($SliderBanner->$title_var != "") {
                                    $BTitle = $SliderBanner->$title_var;
                                } else {
                                    $BTitle = $SliderBanner->$title_var2;
                                }
                                if ($SliderBanner->$details_var != "") {
                                    $BDetails = $SliderBanner->$details_var;
                                } else {
                                    $BDetails = $SliderBanner->$details_var2;
                                }
                                if ($SliderBanner->$file_var != "") {
                                    $BFile = $SliderBanner->$file_var;
                                } else {
                                    $BFile = $SliderBanner->$file_var2;
                                }
                                ?>
                            @if($SliderBanner->youtube_link !="")
                                @if($SliderBanner->video_type ==1)
                                        <?php
                                        $Youtube_id = Helper::Get_youtube_video_id($SliderBanner->youtube_link);
                                        ?>
                                    @if($Youtube_id !="")
                                        {{-- Youtube Video --}}
                                        <iframe width="100%" height="500" frameborder="0" allowfullscreen
                                                src="https://www.youtube.com/embed/{{ $Youtube_id }}?autoplay=1&mute=1"
                                                allow="autoplay">
                                        </iframe>
                                    @endif
                                @elseif($SliderBanner->video_type ==2)
                                        <?php
                                        $Vimeo_id = Helper::Get_vimeo_video_id($SliderBanner->youtube_link);
                                        ?>
                                    @if($Vimeo_id !="")
                                        {{-- Vimeo Video --}}
                                        <iframe width="100%" height="500" frameborder="0" allowfullscreen
                                                src="https://player.vimeo.com/video/{{ $Vimeo_id }}?title=0&amp;byline=0">
                                        </iframe>
                                    @endif
                                @endif
                            @endif
                            @if($SliderBanner->video_type ==0)
                                @if($BFile !="")
                                    {{-- Direct Video --}}
                                    <video width="100%" height="500" controls autoplay>
                                        <source src="{{ route("fileView",["path" =>'banners/'.$BFile ]) }}"
                                                type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            @endif
                            @if($BDetails !="")
                                <div>{!! $BDetails !!}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif
    @push('after-styles')
        @if(Helper::GeneralSiteSettings("style_header") && Helper::GeneralSiteSettings("style_bg_type"))
            <style>
                .fixed-top-margin {
                    margin-top: 0 !important;
                }


                .header-bg, .header-bg a {
                    color: #444444;
                }

                @media (min-width: 968px) {

                    .header-no-bg, .header-no-bg a, .topbar-no-bg, .topbar-no-bg a {
                        color: #fff;
                    }

                    .header-no-bg .navbar a, .topbar-no-bg .header-dropdown .btn {
                        color: #fff;
                    }

                    .dropdown-item {
                        color: #212529 !important;
                    }

                    .header-scrolled .navbar a, .header-scrolled .header-dropdown .btn {
                        color: #444444;
                    }
                }

                .topbar-no-bg {
                    box-shadow: 0 0 1px rgba(255, 255, 255, 0.5) !important;
                }
            </style>
        @endif
    @endpush
    @push('after-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var carousel = document.getElementById('matchdayCarousel');
                if (!carousel) return;

                var tabs = Array.prototype.slice.call(carousel.querySelectorAll('.matchday-story'));
                var current = carousel.querySelector('.matchday-current');
                carousel.addEventListener('slid.bs.carousel', function (event) {
                    tabs.forEach(function (tab, index) {
                        var active = index === event.to;
                        tab.classList.toggle('active', active);
                        if (active) tab.setAttribute('aria-current', 'true');
                        else tab.removeAttribute('aria-current');
                    });
                    if (current) current.textContent = String(event.to + 1).padStart(2, '0');
                });
            });
        </script>
    @endpush
@endif
