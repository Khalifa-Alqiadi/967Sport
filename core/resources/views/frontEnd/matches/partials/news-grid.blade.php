<div class="sm-news-grid">
    @foreach($newsItems as $topic)
        @php
            $topicTitle = app()->getLocale() === 'en' ? ($topic->title_en ?: $topic->title_ar) : ($topic->title_ar ?: $topic->title_en);
        @endphp
        <a class="sm-news-card" href="{{ Helper::topicURL($topic->id, '', $topic) }}">
            <span class="sm-news-card__media">
                @if($topic->photo_file)<img src="{{ route('fileView', ['path' => 'topics/'.$topic->photo_file]) }}?w=760&h=460&r=fit" alt="{{ $topicTitle }}" loading="lazy">@else<i class="bi bi-newspaper"></i>@endif
            </span>
            <span class="sm-news-card__body"><small>{{ Helper::formatDate($topic->date) }}</small><strong>{{ $topicTitle }}</strong><em>{{ __('matches.readNews') }} <i class="bi bi-arrow-up-left"></i></em></span>
        </a>
    @endforeach
</div>
