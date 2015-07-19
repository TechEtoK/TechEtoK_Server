@extends("layouts.base")

@section("title")
    {{ $word }} :: TechEtoK
@stop

@section("content")
    <div class="well">
        {!! $data !!}
    </div>
@stop

@section("script")
    <script>
        $(function() {
            // TODO:
        });
    </script>
@stop
