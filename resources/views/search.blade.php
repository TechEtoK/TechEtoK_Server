<!DOCTYPE html>
<html>
<head>
    <title>Tech E to K</title>
</head>

<body>
<h1>검색어: {{ $keyword }}</h1>
<table>
    <thead>
    <tr>
        <td>ID</td>
        <td>Word</td>
        <td>File</td>
    </tr>
    </thead>
    <tbody>
    @foreach ($words as $word)
        <tr>
            <td><a href="/{{ $word->word }}">{{ $word->word }}</a></td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
