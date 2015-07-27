@extends("layouts.base")

@section("content")
    <div class="jumbotron">
        <h3>어떤 단어가 궁금하신가요?</h3>
        <div class="input-group input-group-lg">
            <span class="input-group-addon" id="sizing-addon1"><i class="glyphicon glyphicon-console"></i></span>
            <input type="search" id="word_search" class="form-control" placeholder="검색어를 입력해주세요" aria-describedby="sizing-addon1" autofocus="autofocus" autocomplete="off" value="{{ $query or "" }}">
        </div>
    </div>

    <div class="list-group"
        @if (!isset($query) || count($words) === 0)
            style="display: none;"
        @endif>
        <div class="list-group-item disabled">
            <i class="glyphicon glyphicon-search"></i> 검색결과
        </div>

        <div class="search-results">
            @foreach ($words as $word)
                <a href="/{{ $word->word }}" class="list-group-item">{{ $word->word }}</a>
            @endforeach
        </div>
    </div>

    <div class="panel panel-warning"
        @if (!isset($query) || count($words) > 0)
            style="display: none;"
        @endif>
        <div class="panel-heading">
            <h3 class="panel-title"><i class="glyphicon glyphicon-alert"></i> 검색 결과가 없습니다.</h3>
        </div>
        <div class="panel-body">
            <a href="#" class="lead add-word">단어를 추가해주시겠어요?</a>
        </div>
    </div>
@stop

@section("script")
    <script>
        $(function() {
            var req = null;
            $("#word_search").each(function() {
                var element = $(this);

                // Save current value of element
                element.data("oldVal", element.val());

                // Look for changes in the value
                element.bind("propertychange change click keyup input past", function() {
                    // If value has changed...
                    if (element.data("oldVal") != element.val()) {
                        // Updated stored value
                        element.data("oldVal", element.val());

                        // Change URL path
                        window.history.pushState("", "", "/?q=" + $(this).val());

                        // Stop querying before
                        if (req !== null) {
                            req.abort();
                            req = null;
                        }

                        // Search words query
                        if ($(this).val() != "") {
                            req = $.get("/api/search?q=" + $(this).val(), function(r) {
                                if (r.error) {
                                    alert(r.error);
                                } else {
                                    $(".search-results").empty();

                                    if (r.words.length == 0) {
                                        $(".panel-warning").show();
                                        $(".list-group").hide();
                                    } else {
                                        $.each(r.words, function(index, value) {
                                            $(".search-results").append("<a href='/" + value.word + "' class='list-group-item'>" + value.word + "</a>");
                                        });

                                        $(".panel-warning").hide();
                                        $(".list-group").show();
                                    }
                                }
                            });
                        } else {
                            $(".search-results").empty();

                            $(".panel-warning").show();
                            $(".list-group").hide();
                        }
                    }
                });
            });

            $(".add-word").click(function () {
                var query = $("#word_search").val();
                location.href = "/update?word=" + query;
            });
        });
    </script>
@stop
