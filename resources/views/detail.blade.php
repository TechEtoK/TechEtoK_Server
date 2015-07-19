@extends("layouts.base")

@section("title")
    {{ $word }} :: TechEtoK
@stop

@section("content")
    {!! $published_html !!}
@stop

@section("script")
    <script>
        $(function() {
            $(".usage_nav").click(function () {
                // Nav
                $(".nav-pills").children().removeClass("active");
                $(this).addClass("active");

                // Well (Contents)
                var w_id = "well" + $(this).data("w_id");
                $("#well_list").children().hide();
                $("#" + w_id).show();
            });
        });
    </script>
@stop
