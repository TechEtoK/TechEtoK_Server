@extends("layouts.base")

@section("title")
    {{ $word }} :: TechEtoK
@stop

@section("content")
    {!! $published_html !!}

    <div class="form-actions">
        <button class="btn btn-primary">다시 검색하기</button>
        <button class='btn btn-warning pull-right'>수정하기</button>
    </div>
@stop

@section("script")
    <script>
        $(function() {
            $(".btn-primary").click(function () {
                location.href = "/?q=delegate";
                return false;
            });

            $(".btn-warning").click(function () {
                if (confirm("더 좋은 생각이 있으세요? 수정해주시겠어요?")) {
                    // TODO: 수정 페이지로 이동
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
