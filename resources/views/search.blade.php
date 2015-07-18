<!DOCTYPE html>
<html>
<head>
    <title>Tech E to K</title>
</head>

<body>
<h1>검색어: {{ $keyword }}</h1>
<ul>
    @foreach ($words as $word)
        <li><a href="/{{ $word->word }}">{{ $word->word }} (유사성: {{ $word->similar_percent }}%)</a></li>
    @endforeach
</ul>
</body>
</html>
