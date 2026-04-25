{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ $user->display_name }} on mura.</title>
        <link>{{ route('users.show', $user) }}</link>
        <description>Latest thoughts from {{ $user->display_name }}</description>
        <atom:link href="{{ route('users.feed', $user) }}" rel="self" type="application/rss+xml" />
        <language>en-us</language>
        <lastBuildDate>{{ now()->toRfc2822String() }}</lastBuildDate>

        @foreach($quotes as $quote)
            <item>
                <title>Thought on {{ $quote->created_at->format('M d, Y') }}</title>
                <link>{{ route('quotes.show', $quote) }}</link>
                <guid isPermaLink="true">{{ route('quotes.show', $quote) }}</guid>
                <description><![CDATA[{{ $quote->content }}]]></description>
                <pubDate>{{ $quote->created_at->toRfc2822String() }}</pubDate>
            </item>
        @endforeach
    </channel>
</rss>