<header id="header" class="{{ (Helper::GeneralSiteSettings("style_header"))?"fixed-top":"" }} {{ (Helper::GeneralSiteSettings("style_bg_type"))?"header-no-bg":"header-bg" }}">
    <div class="container d-flex align-items-center">
        <a class="logo sport-brand me-auto" href="{{ Helper::homeURL() }}" aria-label="967Sport - الرئيسية">
            <img alt="شعار 967Sport" src="{{ URL::to('uploads/settings/967sport-facebook-logo.jpg') }}" width="62" height="62">
            <span class="sport-brand-copy" aria-hidden="true">
                <b><span>967</span>SPORT</b>
                <small>منصة كرة القدم اليمنية</small>
            </span>
        </a>

        @include('frontEnd.layouts.menu')
    </div>
</header>
