<?php
$footer_style = "";
if (Helper::GeneralSiteSettings("style_footer_bg") != "") {
    $bg_file = route("fileView", ["path" => 'settings/'.Helper::GeneralSiteSettings("style_footer_bg")]);
    $footer_style = "style='background-image: url($bg_file);'";
}
$MenuLinks = [];
if (Helper::GeneralWebmasterSettings("footer_menu_id") >0) {
    // Get list of footer menu links by group Id
    $MenuLinks = \App\Helpers\SiteMenu::List(Helper::GeneralWebmasterSettings("footer_menu_id"));
}
$site_title_var = "site_title_".@Helper::currentLanguage()->code;
$site_desc_var = "site_desc_".@Helper::currentLanguage()->code;
?>
<footer id="footer" class="meu-footer" {!!  $footer_style !!}>
    <span class="footer-fx" aria-hidden="true">
        <span class="footer-fx-grid"></span>
        <span class="footer-fx-orb footer-fx-orb-1"></span>
        <span class="footer-fx-orb footer-fx-orb-2"></span>
        <span class="footer-fx-watermark">967</span>
        <span class="footer-fx-seal"></span>
    </span>
    <div class="footer-top">
        <div class="container">
            <div class="row footer-grid gy-5 gx-lg-5 align-items-start">

                <div class="col-lg-3 col-md-6 footer-brand">
                    <div class="footer-panel">
                        <a href="{{ Helper::homeURL() }}" class="footer-logo sport-brand">
                            <img alt="{{ __('frontend.sportLogoAlt') }}" src="{{ URL::to('uploads/settings/967sport-facebook-logo.jpg') }}" width="78" height="78">
                            <span class="sport-brand-copy" aria-hidden="true">
                                <b><span>967</span>{{ __('frontend.sportBrandWord') }}</b>
                                <small>{{ __('frontend.sportPlatformName') }}</small>
                            </span>
                        </a>
                        @if(Helper::GeneralSiteSettings($site_desc_var) !="")
                            <p class="footer-tagline">{{ Helper::GeneralSiteSettings($site_desc_var) }}</p>
                        @endif
                        @include("frontEnd.layouts.social",["tt_position"=>"top"])
                    </div>
                </div>

                @if(count($MenuLinks) >0)
                    <div class="col-lg-6 col-md-6 footer-links footer-links-2col">
                        <div class="footer-panel">
                            <div class="footer-title">
                                <span class="footer-title-icon"><i class="bi bi-link-45deg"></i></span>
                                <h3>{{ __('frontend.quickLinks') }}</h3>
                            </div>
                            <ul>
                                @foreach($MenuLinks as $MenuLink)
                                    <li>
                                        {{-- <a class="nav-link" href="{{ @$MenuLink->url }}"
                                           target="{{ @$MenuLink->target }}">{!! (@$MenuLink->icon)?"<i class='".@$MenuLink->icon."'></i> ":"" !!} {{ @$MenuLink->title }}</a> --}}
                                        @if(@$MenuLink->sub)
                                            <ul>
                                                @foreach($MenuLink->sub as $SubLink)
                                                    <li><a class="nav-link" href="{{ @$SubLink->url }}"
                                                           target="{{ @$SubLink->target }}">{!! (@$SubLink->icon)?"<i class='".@$SubLink->icon."'></i> ":"" !!} {{ @$SubLink->title }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="col-lg-3 col-md-6 footer-contact-col">
                    <div class="footer-panel">
                        <div class="footer-title">
                            <span class="footer-title-icon"><i class="bi bi-headset"></i></span>
                            <h3>{{ __('frontend.contactDetails') }}</h3>
                        </div>
                        @if(Helper::GeneralSiteSettings("contact_t1_" . @Helper::currentLanguage()->code) !="")
                            <div class="footer-contact-item">
                                <i class="bi bi-geo-alt"></i>
                                <div>
                                    <strong>{{ __('frontend.address') }}</strong>
                                    {{ Helper::GeneralSiteSettings("contact_t1_" . @Helper::currentLanguage()->code) }}
                                </div>
                            </div>
                        @endif
                        @if(Helper::GeneralSiteSettings("contact_t3") !="")
                            <div class="footer-contact-item">
                                <i class="bi bi-telephone"></i>
                                <div>
                                    <strong>{{ __('frontend.callUs') }}</strong>
                                    <a href="tel:{{ Helper::GeneralSiteSettings("contact_t3") }}"><span
                                            dir="ltr">{{ Helper::GeneralSiteSettings("contact_t3") }}</span></a>
                                </div>
                            </div>
                        @endif
                        @if(Helper::GeneralSiteSettings("contact_t6") !="")
                            <div class="footer-contact-item">
                                <i class="bi bi-envelope"></i>
                                <div>
                                    <strong>{{ __('frontend.email') }}</strong>
                                    <a href="mailto:{{ Helper::GeneralSiteSettings("contact_t6") }}">{{ Helper::GeneralSiteSettings("contact_t6") }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-md-flex align-items-center justify-content-between py-4">
            <div class="copyright text-center text-md-start">
                &copy; <?php echo date("Y") ?> {{ __('frontend.AllRightsReserved') }}
                . {{ Helper::GeneralSiteSettings($site_title_var) }}
            </div>
            <div class="credits text-center text-md-end">
                {{ __('frontend.designedBy') }} <a href="https://yemenhosting.com">{{ __('frontend.yemenHostingName') }}</a>
            </div>
        </div>
    </div>
</footer>
@if(Helper::GeneralSiteSettings('whatsapp_no') !="")
    <a href="https://wa.me/{{Helper::GeneralSiteSettings('whatsapp_no')}}" class="whatsapp_float" target="_blank" aria-label="{{ __('frontend.whatsappAria') }}"
       rel="noopener noreferrer">
        <i class="fa fa-whatsapp"></i>
    </a>
@endif
@if (@Auth::check())
    @if(!Helper::GeneralSiteSettings("site_status"))
        <div class="text-center alert alert-warning m-0">
            <div class="h6 mb-0">
                {{__('backend.websiteClosedForVisitors')}}
            </div>
        </div>
    @endif
@endif
