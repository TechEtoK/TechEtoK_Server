@extends("layouts.base")

@section("content")
    <div class="jumbotron">
        <h3>어떤 단어가 궁금하신가요?</h3>
        <div class="input-group input-group-lg">
            <span class="input-group-addon" id="sizing-addon1"><i class="glyphicon glyphicon-console"></i></span>
            <input type="search" id="word_search" class="form-control" placeholder="검색어를 입력해주세요" aria-describedby="sizing-addon1" autofocus="autofocus" autocomplete="off">
        </div>
    </div>

    <div class="list-group">
        <a href="#" class="list-group-item disabled">
            <i class="glyphicon glyphicon-search"></i> 검색결과 (n건)
        </a>
        <a href="#" class="list-group-item">linked list</a>
        <a href="#" class="list-group-item">delegate</a>
    </div>

    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-alert"></i> 검색 결과가 없습니다.</h3>
        </div>
        <div class="panel-body">
            <a href="#" class="lead">단어를 추가해주시겠어요?</a>
        </div>
    </div>
@stop

@section("script")
    <script>
        $(function() {
            $("#word_search").each(function() {
                var element = $(this);

                // Save current value of element
                element.data("oldVal", element.val());

                // Look for changes in the value
                element.bind("propertychange change click keyup input past", function(event) {
                    // If value has changed...
                    if (element.data("oldVal") != element.val()) {
                        // Updated stored value
                        element.data("oldVal", element.val());

                        // Change URL path
                        window.history.pushState("", "", "/?q=" + $(this).val());

                        // TODO: 검색
                    }
                });
            });
        });
    </script>
@stop
