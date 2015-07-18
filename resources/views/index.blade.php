@extends("layouts.base")

@section("content")
    <div class="jumbotron">
        <h3>어떤 단어가 궁금하신가요?</h3>
        <div class="input-group input-group-lg">
            <span class="input-group-addon" id="sizing-addon1">@</span>
            <input type="text" id="query" class="form-control" placeholder="검색어를 입력해주세요" aria-describedby="sizing-addon1">
        </div>
    </div>

    <div class="list-group">
        <a href="#" class="list-group-item disabled">
            검색결과 (n건)
        </a>
        <a href="#" class="list-group-item">linked list</a>
        <a href="#" class="list-group-item">delegate</a>
    </div>

    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">검색 결과가 없습니다.</h3>
        </div>
        <div class="panel-body">
            <a href="#" class="lead">단어를 추가해주시겠어요?</a>
        </div>
    </div>
@stop

@section("script")
    <script>
        $(function() {
            $("#query").change(function () {
                // TODO: 검색
            });
        });
    </script>
@stop
