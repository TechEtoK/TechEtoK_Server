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
        <form id="wordForm" action="/api/word/{{ isset($word) ? "edit" : "add" }}">
            <div class="form-group">
                <label for="title">단어명(영어)</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="단어명을 입력해주세요. (예. Delegate)" {!! isset($word->title) ? "value='" . $word->title . "' readonly" : "" !!}>
            </div>

            <div id="word_contents">
                @if (isset($word))
                    @for ($i = 0; $i < count($word->usages); $i++)
                        @include("update_form", ["index" => $i, "word" => $word])
                    @endfor
                @else
                    @include("update_form", ["index" => 0])
                @endif
            </div>
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

            $(".delete_word_content").click(function () {
                $(this).parent().remove();
            });

            $(".add-platforms").click(function () {
                var wordContent = $("#word_content").last().clone(true);

                // 여러 개를 입력할 수 있는 경우에는, 하나만 남기고 모두 제거
                wordContent.find("#examples:not(:first)").remove();
                wordContent.find("#related_words:not(:first)").remove();
                wordContent.find("#related_links:not(:first)").remove();

                // '지우기' 버튼 활성화
                wordContent.find(".delete_word_content").show();

                // 값 초기화
                wordContent.find("input,textarea").each(function (key, value) {
                    value.value = "";
                });

                $("#word_contents").append(wordContent);
            });

            $(".add-examples").click(function () {
                var example = $("#examples").last().clone(true);
                example.text("");
                $(this).before(example);
            });

            $(".add-related_words").click(function () {
                var relatedWord = $("#related_words").last().clone(true);
                relatedWord.children().val("");
                $(this).before(relatedWord);
            });

            $(".add-related_links").click(function () {
                var relatedLink = $("#related_links").last().clone(true);
                relatedLink.val("");
                $(this).before(relatedLink);
            });
        });
    </script>
@stop
