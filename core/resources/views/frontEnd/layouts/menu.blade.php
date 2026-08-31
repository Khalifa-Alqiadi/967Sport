@if(Helper::GeneralWebmasterSettings("header_menu_id") >0)
    <?php
    // Get list of main menu links
    $MenuLinks = \App\Helpers\SiteMenu::List(Helper::GeneralWebmasterSettings("header_menu_id"));
    ?>
    @if(count($MenuLinks)>0)
        <nav id="navbar" class="navbar order-last order-lg-0" aria-label="Main navigation">
            <ul id="main-navigation">
                @foreach($MenuLinks as $MenuLink)
                    <?php
                        $menuChildren = collect(@$MenuLink->sub ?: []);
                        // Every top-level dropdown uses the wide editorial layout.
                        // Its description remains optional and is shown only when the
                        // linked section actually has localized details.
                        $menuIntro = $MenuLink;
                        $isMegaMenu = $menuChildren->isNotEmpty();
                        $hasTopicColumns = $menuChildren->contains(function ($item) {
                            return collect(@$item->topics ?: [])->isNotEmpty();
                        });
                    ?>
                    <li class="{{ (@$MenuLink->sub)?"dropdown":"" }} {{ $isMegaMenu ? 'mega-dropdown' : '' }}"><a
                            class="nav-link {{ \App\Helpers\SiteMenu::ActiveLink(url()->current(),@$MenuLink,@$WebmasterSection) }}"
                            href="{{ @$MenuLink->url }}" target="{{ @$MenuLink->target }}">
                            {!! (@$MenuLink->icon)?"<i class='".@$MenuLink->icon." mx-2'></i> ":"" !!} {{ @$MenuLink->title }}
                            @if(@$MenuLink->sub)
                                <i class="drop-arrow bi bi-chevron-down"></i>
                            @endif
                        </a>

                        @if(@$MenuLink->sub)
                            <ul class="{{ $isMegaMenu ? 'mega-menu' : '' }} {{ $hasTopicColumns ? 'mega-menu-topic-columns' : '' }}">
                                @if($isMegaMenu && $menuIntro)
                                    <li class="mega-menu-intro {{ @$menuIntro->photo ? 'has-photo' : '' }}"
                                        @if(@$menuIntro->photo)
                                            style="--mega-intro-image: url('{{ @$menuIntro->photo }}')"
                                        @endif>
                                        <h3>{{ @$menuIntro->title }}</h3>
                                        @if(trim((string) @$menuIntro->details) !== '')
                                            <p>{{ \Illuminate\Support\Str::limit(@$menuIntro->details, 190) }}</p>
                                        @endif
                                        @if(@$menuIntro->url && @$menuIntro->url !== '#')
                                            <a class="mega-menu-more" href="{{ @$menuIntro->url }}" target="{{ @$menuIntro->target }}">
                                                <span>{{ __('frontend.moreDetails') }}</span>
                                                <i class="bi bi-arrow-{{ @Helper::currentLanguage()->right }}"></i>
                                            </a>
                                        @endif
                                    </li>
                                @endif
                                @if($hasTopicColumns)
                                    <li class="mega-sections-area">
                                        <div class="mega-sections-grid">
                                            @foreach($MenuLink->sub as $SubLink)
                                                <section class="mega-section-column">
                                                    <a class="mega-section-heading"
                                                       href="{{ @$SubLink->url }}"
                                                       target="{{ @$SubLink->target }}">
                                                        <span>{!! (@$SubLink->icon)?"<i class='".@$SubLink->icon."'></i> ":"" !!} {{ @$SubLink->title }}</span>
                                                        <i class="bi bi-arrow-{{ @Helper::currentLanguage()->right }}"></i>
                                                    </a>

                                                    <?php
                                                        $columnTopics = collect(@$SubLink->topics ?: []);
                                                        $columnChildren = collect(@$SubLink->sub ?: []);
                                                    ?>
                                                    @if($columnTopics->isNotEmpty() || $columnChildren->isNotEmpty())
                                                        <ul class="mega-topic-list">
                                                            @foreach($columnTopics as $MenuTopic)
                                                                <li>
                                                                    <a href="{{ @$MenuTopic->url }}" target="{{ @$MenuTopic->target }}">
                                                                        {!! (@$MenuTopic->icon)?"<i class='".@$MenuTopic->icon."'></i> ":"" !!}
                                                                        <span>{{ @$MenuTopic->title }}</span>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                            @if($columnTopics->isEmpty())
                                                                @foreach($columnChildren as $ColumnChild)
                                                                    <li>
                                                                        <a href="{{ @$ColumnChild->url }}" target="{{ @$ColumnChild->target }}">
                                                                            {!! (@$ColumnChild->icon)?"<i class='".@$ColumnChild->icon."'></i> ":"" !!}
                                                                            <span>{{ @$ColumnChild->title }}</span>
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    @endif
                                                </section>
                                            @endforeach
                                        </div>
                                    </li>
                                @else
                                @foreach($MenuLink->sub as $SubLink)
                                    <li class="{{ (@$SubLink->sub)?"dropdown":"" }}"><a class="nav-link"
                                                                                        href="{{ @$SubLink->url }}"
                                                                                        target="{{ @$SubLink->target }}">
                                            <span class="mega-menu-link-copy">
                                                <span class="mega-menu-link-title">{!! (@$SubLink->icon)?"<i class='".@$SubLink->icon."'></i> ":"" !!} {{ @$SubLink->title }}</span>
                                            </span>
                                            @if(@$SubLink->sub)
                                                <i class="drop-arrow bi bi-chevron-{{ @Helper::currentLanguage()->right }}"></i>
                                            @endif
                                        </a>
                                        @if(@$SubLink->sub)
                                            <ul>
                                                @foreach($SubLink->sub as $SubLink2)
                                                    <li class="{{ (@$SubLink2->sub)?"dropdown":"" }}"><a
                                                            class="nav-link"
                                                            href="{{ @$SubLink2->url }}"
                                                            target="{{ @$SubLink2->target }}">{!! (@$SubLink2->icon)?"<i class='".@$SubLink2->icon."'></i> ":"" !!} {{ @$SubLink2->title }}
                                                            @if(@$SubLink2->sub)
                                                                <i class="drop-arrow bi bi-chevron-{{ @Helper::currentLanguage()->right }}"></i>
                                                            @endif
                                                        </a>
                                                        @if(@$SubLink2->sub)
                                                            <ul>
                                                                @foreach($SubLink2->sub as $SubLink3)
                                                                    <li><a
                                                                            class="nav-link"
                                                                            href="{{ @$SubLink3->url }}"
                                                                            target="{{ @$SubLink3->target }}">{!! (@$SubLink3->icon)?"<i class='".@$SubLink3->icon."'></i> ":"" !!} {{ @$SubLink3->title }}</a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @endforeach
                                @endif
                            </ul>
                        @endif
                    </li>
                @endforeach

@if(count(Helper::languagesList()) >1)
                    <?php
                        // show a single direct toggle to the other language
                        // instead of a dropdown listing every language
                        $otherLanguage = collect(Helper::languagesList())
                            ->first(fn($lang) => $lang->code != @Helper::currentLanguage()->code);
                    ?>
                    @if($otherLanguage)
                        <li class="nav-lang-switch">
                            <a class="nav-link"
                               href="{{ Helper::languageURL($otherLanguage->code, @$page_type , @$page_id) }}">
                                @if($otherLanguage->icon !="")
                                    <img class="nav-lang-flag"
                                         src="{{ asset('assets/dashboard/images/flags/'.$otherLanguage->icon.".svg") }}"
                                         alt="{{ $otherLanguage->code }}" loading="lazy">
                                @endif
                                {{ $otherLanguage->title }}
                            </a>
                        </li>
                    @endif
                @endif
            </ul>
            <button type="button"
                    class="bi bi-list mobile-nav-toggle"
                    aria-label="Menu"
                    aria-controls="main-navigation"
                    aria-expanded="false"></button>
        </nav>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const navbar = document.getElementById('navbar');
                const toggle = navbar ? navbar.querySelector('.mobile-nav-toggle') : null;

                if (!navbar || !toggle) return;

                const syncMenuState = function () {
                    const isOpen = navbar.classList.contains('navbar-mobile');
                    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    document.body.classList.toggle('mobile-menu-open', isOpen);
                };

                new MutationObserver(syncMenuState).observe(navbar, {
                    attributes: true,
                    attributeFilter: ['class']
                });

                navbar.addEventListener('click', function (event) {
                    if (event.target === navbar && navbar.classList.contains('navbar-mobile')) {
                        toggle.click();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && navbar.classList.contains('navbar-mobile')) {
                        toggle.click();
                        toggle.focus();
                    }
                });

                window.addEventListener('resize', function () {
                    if (window.innerWidth > 991 && navbar.classList.contains('navbar-mobile')) {
                        toggle.click();
                    }
                });

                syncMenuState();
            });
        </script>

    @endif
@endif
