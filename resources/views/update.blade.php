@extends("layouts.base")

@section("title")
    @if (isset($word))
        {{ $word->title }} 수정 :: TechEtoK
    @else
        단어 추가 :: TechEtoK
    @endif
@stop

@section("content")
    <h3>
    @if (isset($word))
        {{ $word->title }} 수정
    @else
        단어 추가
    @endif
    </h3>

    <div class="well">
        <form action="/api/word/{{ isset($word) ? "edit" : "add" }}">
            <div class="form-group">
                <label for="title">단어명(영어)</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="단어명을 입력해주세요. (예. Delegate)" {!! isset($word->title) ? "value='" . $word->title . "' readonly" : "" !!}>
            </div>

            <hr>

            @if (isset($word))
                @for ($i = 0; $i < count($word->usages); $i++)
                    @include("update_form", ["index" => $i, "word" => $word])

                    @if ($i != count($word->usages) - 1)
                        <hr>
                    @endif
                @endfor
            @else
                @include("update_form", ["index" => 0])
            @endif
        </form>
    </div>

    <div class="add-platform">
        <button type="button" class="btn btn-success add-platforms">플랫폼(언어) 추가하기</button>
    </div>

    <div class="form-actions">
        <button class="btn btn-danger">취소하기</button>
        @if (isset($word))
            <button class="btn btn-warning pull-right">수정하기</button>
        @else
            <button class="btn btn-info pull-right">추가하기</button>
        @endif
    </div>
@stop

@section("script")
    <script>
        $(function() {
            $(".btn-danger").click(function () {
                history.back();
                return false;
            });

            $(".btn-warning").click(function () {
                if (confirm("관리자의 승인 후에 수정됩니다. 수정하시겠습니까?")) {
                    // TODO: 수정 API 호출
                }
                return false;
            });

            $(".btn-info").click(function () {
                if (confirm("관리자의 승인 후에 추가됩니다. 추가하시겠습니까?")) {
                    // TODO: 추가 API 호출
                }
                return false;
            });

            $(".add-platforms").click(function () {
                // TODO: Platform 추가
            });

            $(".add-examples").click(function () {
                var index = $(this).data('index');
                // TODO: 사용 예 추가
            });

            $(".add-related_words").click(function () {
                var index = $(this).data('index');
                // TODO: 관련 단어 추가
            });

            $(".add-related_links").click(function () {
                var index = $(this).data('index');
                // TODO: 관련 링크 추가
            });
        });
    </script>
@stop
