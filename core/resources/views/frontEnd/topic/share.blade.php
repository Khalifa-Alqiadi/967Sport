<div class="bottom-article topic-share-bar mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="share-title d-flex align-items-center gap-2">
            <span class="share-title-icon"><i class="fa-solid fa-share-nodes"></i></span>
            <span>{{ __('frontend.share') }}</span>
        </div>
        <ul class="social-network share d-flex">
            <li><a href="{{ Helper::SocialShare("facebook", @$PageTitle)}}"
                   class="facebook"
                   data-bs-toggle="tooltip"
                   title="Facebook" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i
                        class="fa-brands fa-facebook"></i></a>
            </li>
            <li><a href="{{ Helper::SocialShare("whatsapp", @$PageTitle)}}"
                   class="whatsapp"
                   data-bs-toggle="tooltip" title="Whatsapp"
                   aria-label="Whatsapp" target="_blank" rel="noopener noreferrer"><i
                        class="fa-brands fa-whatsapp"></i></a></li>
            <li><a href="{{ Helper::SocialShare("twitter", @$PageTitle)}}"
                   class="twitter"
                   data-bs-toggle="tooltip" title="X" aria-label="X"
                   target="_blank" rel="noopener noreferrer"><i
                        class="bi bi-twitter-x"></i></a></li>
            <li><a href="{{ Helper::SocialShare("linkedin", @$PageTitle)}}"
                   class="linkedin"
                   data-bs-toggle="tooltip" title="LinkedIn" aria-label="LinkedIn"
                   target="_blank" rel="noopener noreferrer"><i
                        class="fa-brands fa-linkedin"></i></a></li>
            <li><a href="{{ Helper::SocialShare("tumblr", @$PageTitle)}}" class="tumblr"
                   data-bs-toggle="tooltip" title="Tumblr" aria-label="Tumblr"
                   target="_blank" rel="noopener noreferrer"><i
                        class="fa-brands fa-tumblr"></i></a></li>
        </ul>
    </div>
</div>
