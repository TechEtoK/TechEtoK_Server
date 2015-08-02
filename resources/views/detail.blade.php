@extends("layouts.base")

@section("title")
    {{ $word }} :: TechEtoK
@stop

@section("content")
    @if (count($published_htmls) > 1)
        <ul class="nav nav-pills">
            @for ($i = 0; $i < count($published_htmls); $i++)
                <li role="presentation"><a href="#{{ $i }}">{{ $usages[$i] }}</a></li>
            @endfor
        </ul>
    @endif

    <div class="well-list">
        @for ($i = 0; $i < count($published_htmls); $i++)
            <div class="well" id="well{{ $i }}">
                {!! $published_htmls[$i] !!}
            </div>
        @endfor
    </div>

    <div class="form-actions">
        <button class="btn btn-primary">이전으로</button>
        <button class="btn btn-warning pull-right">수정하기</button>
    </div>
@stop

@section("script")
    <script>
        $(function() {
            $(".btn-primary").click(function () {
                history.back();
                return false;
            });

            $(".btn-warning").click(function () {
                if (confirm("더 좋은 생각이 있으세요? 수정해주시겠어요?")) {
                    location.href = "/update?word={{ $word }}";
                }
                return false;
            });

            $("ul.nav-pills li").click(function () {
                // Nav
                $("ul.nav-pills li").removeClass("active");
                $(this).addClass("active");

                // Content
                $(".well").hide();
                var wellID = $("ul.nav-pills li").index(this);
                $("#well" + wellID).fadeIn();
            });

            // Hide all content
            $(".well").hide();

            if(document.location.hash != "") {
                showTabByHash();
            }  else {
                showFirstTab();
            }
        });

        function showFirstTab() {
            // Nav
            $("ul.nav-pills li:first").addClass("active");

            // Content
            $("div.well:first").show();
        }

        function showTabByHash() {
            var wellID = window.location.hash.substr(1);

            var well = $("#well" + wellID);
            if (well.length) {
                // Nav
                $("ul.nav-pills li:nth-child(" + (++wellID) +")").addClass("active");

                // Content
                well.show();
            } else {
                // Hash가 잘못되었을 때
                showFirstTab();
            }
        }
    </script>
@stop
