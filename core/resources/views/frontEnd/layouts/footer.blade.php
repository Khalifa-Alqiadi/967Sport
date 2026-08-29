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
<footer id="footer" class="meu-footer sport-footer-v2" {!!  $footer_style !!}>
    <div class="sport-footer-backdrop" aria-hidden="true">
        <span class="sport-footer-grid"></span>
        <span class="sport-footer-number">967</span>
        <span class="sport-footer-stripe"></span>
    </div>

    <div class="footer-top">
        <div class="container">
            <div class="sport-footer-masthead">
                <div class="sport-footer-identity">
                    <span class="sport-footer-kicker"><i></i>{{ __('frontend.sportManifestoEyebrow') }}</span>
                    <a href="{{ Helper::homeURL() }}" class="footer-logo sport-brand">
                        <img alt="{{ __('frontend.sportLogoAlt') }}" src="{{ URL::to('uploads/settings/967sport-facebook-logo.jpg') }}" width="88" height="88">
                        <span class="sport-brand-copy">
                            <b><span>967</span>{{ __('frontend.sportBrandWord') }}</b>
                            <small>{{ __('frontend.sportPlatformName') }}</small>
                        </span>
                    </a>
                </div>
                <div class="sport-footer-statement">
                    @if(Helper::GeneralSiteSettings($site_desc_var) !="")
                        <p>{{ Helper::GeneralSiteSettings($site_desc_var) }}</p>
                    @endif
                    @include("frontEnd.layouts.social",["tt_position"=>"top"])
                </div>
            </div>

            <div class="sport-footer-content">
                @if(count($MenuLinks) >0)
                    <nav class="footer-links sport-footer-nav" aria-label="{{ __('frontend.quickLinks') }}">
                        <div class="footer-title">
                            <span>{{ __('frontend.quickLinks') }}</span>
                            <i class="bi bi-arrow-down-left" aria-hidden="true"></i>
                        </div>
                        <ul>
                            @foreach($MenuLinks as $MenuLink)
                                <li>
                                    @if(@$MenuLink->title)
                                        <a href="{{ @$MenuLink->url ?: '#' }}" target="{{ @$MenuLink->target }}">
                                            <span>{!! (@$MenuLink->icon)?"<i class='".@$MenuLink->icon."'></i> ":"" !!}{{ @$MenuLink->title }}</span>
                                            <i class="bi bi-arrow-up-left" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                    @if(@$MenuLink->sub)
                                        <ul>
                                            @foreach($MenuLink->sub as $SubLink)
                                                <li>
                                                    <a href="{{ @$SubLink->url }}" target="{{ @$SubLink->target }}">
                                                        <span>{!! (@$SubLink->icon)?"<i class='".@$SubLink->icon."'></i> ":"" !!}{{ @$SubLink->title }}</span>
                                                        <i class="bi bi-arrow-up-left" aria-hidden="true"></i>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif

                <div class="sport-footer-contact">
                    <div class="footer-title">
                        <span>{{ __('frontend.contactDetails') }}</span>
                        <i class="bi bi-headset" aria-hidden="true"></i>
                    </div>
                    <div class="sport-footer-contact-grid">
                        @if(Helper::GeneralSiteSettings("contact_t1_" . @Helper::currentLanguage()->code) !="")
                            <div class="footer-contact-item">
                                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                                <div>
                                    <strong>{{ __('frontend.address') }}</strong>
                                    <span>{{ Helper::GeneralSiteSettings("contact_t1_" . @Helper::currentLanguage()->code) }}</span>
                                </div>
                            </div>
                        @endif
                        @if(Helper::GeneralSiteSettings("contact_t3") !="")
                            <a class="footer-contact-item" href="tel:{{ Helper::GeneralSiteSettings("contact_t3") }}">
                                <i class="bi bi-telephone" aria-hidden="true"></i>
                                <div>
                                    <strong>{{ __('frontend.callUs') }}</strong>
                                    <span dir="ltr">{{ Helper::GeneralSiteSettings("contact_t3") }}</span>
                                </div>
                            </a>
                        @endif
                        @if(Helper::GeneralSiteSettings("contact_t6") !="")
                            <a class="footer-contact-item" href="mailto:{{ Helper::GeneralSiteSettings("contact_t6") }}">
                                <i class="bi bi-envelope" aria-hidden="true"></i>
                                <div>
                                    <strong>{{ __('frontend.email') }}</strong>
                                    <span>{{ Helper::GeneralSiteSettings("contact_t6") }}</span>
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container sport-footer-legal">
            <div class="copyright">
                &copy; <?php echo date("Y") ?> {{ __('frontend.AllRightsReserved') }}
                . {{ Helper::GeneralSiteSettings($site_title_var) }}
            </div>
            <div class="credits">
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
