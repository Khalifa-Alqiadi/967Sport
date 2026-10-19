@php
    $newsImage = $Topic->photo_file ?: optional($Topic->photos->first())->file;
    $newsSummary = trim(strip_tags((string) $Topic->{$details}));
    $newsDate = $Topic->date ?: $Topic->created_at;
@endphp
<article class="sport-news-entry mb-5">
    <a class="sport-news-entry__image" href="{{ $topic_link_url }}" aria-label="{{ $title }}">
        @if($newsImage)
            <img src="{{ route('fileView', ['path' => 'topics/'.$newsImage]) }}?w=720&h=450&r=fit" alt="{{ $title }}" loading="lazy">
        @else
            <span aria-hidden="true"><i class="bi bi-newspaper"></i></span>
        @endif
    </a>
    <div class="sport-news-entry__body">
        {{-- @if($newsDate)<time datetime="{{ \Carbon\Carbon::parse($newsDate)->toDateString() }}">{{ \Carbon\Carbon::parse($newsDate)->translatedFormat('d M Y') }}</time>@endif --}}
        <h2><a href="{{ $topic_link_url }}">{{ $title }}</a></h2>
        {{-- @if($newsSummary !== '')<p>{{ \Illuminate\Support\Str::limit($newsSummary, 125) }}</p>@endif --}}
    </div>
</article>
