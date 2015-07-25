@extends("layouts.base")

@section("content")
    <div class="jumbotron">
        <h2>"{{ $query }}" 단어를 찾을 수 없습니다.</h2>
        <p>해당 단어가 등록되어 있지 않거나, 관리자의 승인을 기다리고 있습니다.</p>
        <a href="/"><h5><mark>3초</mark> 후 페이지가 자동으로 이동합니다.</h5></a>
    </div>
@stop

@section("script")
    <script>
        $(function() {
            var currentRemainSec = 3;

            var timer = setInterval(function () {
                $("mark").text(--currentRemainSec + "초");
            }, 1000);

            setTimeout(function() {
                clearInterval(timer);
                location.href = '/';
            }, 3000);
        });
    </script>
@stop
