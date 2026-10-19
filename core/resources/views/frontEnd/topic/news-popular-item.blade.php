@php
    $popularTitleField = 'title_'.@Helper::currentLanguage()->code;
    $popularFallbackField = 'title_'.config('smartend.default_language');
    $popularTitle = $Post->{$popularTitleField} ?: $Post->{$popularFallbackField};
    $popularImage = $Post->photo_file ?: optional($Post->photos->first())->file;
@endphp
<a class="sport-news-popular__item" href="{{ Helper::topicURL($Post->id, '', $Post) }}">
    @if($popularImage)
        <img src="{{ route('fileView', ['path' => 'topics/'.$popularImage]) }}?w=160&h=120&r=fit" alt="" loading="lazy">
    @endif
    <span>{{ $popularTitle }}</span>
</a>
