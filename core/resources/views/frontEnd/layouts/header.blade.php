<header id="header" class="{{ (Helper::GeneralSiteSettings("style_header"))?"fixed-top":"" }} {{ (Helper::GeneralSiteSettings("style_bg_type"))?"header-no-bg":"header-bg" }}">
    <div class="container d-flex align-items-center">
        <a class="logo sport-brand me-auto" href="{{ Helper::homeURL() }}" aria-label="{{ __('frontend.sportHomeAria') }}">
            <img alt="{{ __('frontend.sportLogoAlt') }}" src="{{ URL::to('uploads/settings/967sport-facebook-logo.jpg') }}" width="62" height="62">
            <span class="sport-brand-copy" aria-hidden="true">
                <b><span>967</span>{{ __('frontend.sportBrandWord') }}</b>
                <small>{{ __('frontend.sportPlatformName') }}</small>
            </span>
        </a>

        @include('frontEnd.layouts.menu')
    </div>
</header>
