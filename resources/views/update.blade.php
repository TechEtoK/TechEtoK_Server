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
            <div class="form-group" id="title_group">
                <label for="title">단어명(영어)<span class="text-danger">*</span></label>
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

            <div class="add-platform">
                <button type="button" class="btn btn-success add-platforms">플랫폼(언어) 추가하기</button>
            </div>

            <div class="form-group" id="tag_group">
                <label for="tags">태그 (검색 키워드)</label>
                @if (isset($tags) && count($tags) > 0)
                    @foreach ($tags as $tag)
                        <input type="text" class="form-control" id="tags" placeholder="태그를 입력해주세요. (예. datasource)" value="{{ $tag->tag }}">
                    @endforeach
                @else
                    <input type="text" class="form-control" id="tags" placeholder="태그를 입력해주세요. (예. datasource)">
                @endif
                <button type="button" class="btn btn-default add-tags">태그 추가하기</button>
            </div>

            <div class="form-group" id="author_name_group">
                <label for="author_name">작성자(이름)</label>
                <input type="text" class="form-control" id="author_name" name="author_name" placeholder="이름을 입력해주세요.">
            </div>

            <div class="form-group" id="author_email_group">
                <label for="author_email">이메일</label>
                <input type="email" class="form-control" id="author_email" name="author_email" placeholder="이메일 주소를 입력해주세요.">
            </div>
        </form>

        <span class="text-danger"><b>*</b>로 표시된건 필수로 입력해야하는 항목입니다.</span>
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
                    normalizeFormElementsName();

                    $.post("/api/edit", $("#wordForm").serialize(), function () {
                        alert("감사합니다 :)");
                        location.href = "/";
                    }).fail(function () {
                        alert("오류가 발생하였습니다! 다시 시도해주세요 :(");
                    });
                }
                return false;
            });

            $(".btn-info").click(function () {
                if (confirm("관리자의 승인 후에 추가됩니다. 추가하시겠습니까?")) {
                    normalizeFormElementsName();

                    $.post("/api/add", $("#wordForm").serialize(), function () {
                        alert("감사합니다 :)");
                        location.href = "/";
                    }).fail(function () {
                        alert("오류가 발생하였습니다! 다시 시도해주세요 :(");
                    });
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

            $(".add-tags").click(function () {
                var tag = $("#tags").last().clone(true);
                tag.val("");
                $(this).before(tag);
            });

            function normalizeFormElementsName() {
                $("#word_contents").children().each(function (index, word_content) {
                    // 사용처 name 정규화
                    $(this).find("div#usage_group input").attr("name", "usages[" + index + "]");

                    // 한글표현 name 정규화
                    $(this).find("div#kor_expressions_group input").attr("name", "kor_expressions[" + index + "]");

                    // 사용 예 name 정규화
                    $(this).find("div#examples_group textarea").each(function (j, textarea) {
                        $(this).attr("name", $(this).attr("id") + "[" + index + "][" + j + "]");
                    });

                    // 관련단어 name 정규화
                    $(this).find("div#related_words_group div#related_words").each(function (j, div) {
                        $(this).find("input").each(function () {
                            $(this).attr("name", $(this).attr("id") + "[" + index + "][" + j + "]");
                        });
                    });

                    // 간략 설명 name 정규화
                    $(this).find("div#summaries_group textarea").attr("name", "summaries[" + index + "]");

                    // 관련 링크 name 정규화
                    $(this).find("div#related_links_group input").each(function (j, input) {
                        $(this).attr("name", $(this).attr("id") + "[" + index + "][" + j + "]");
                    });

                    // 태그 name 정규화
                    $(this).find("div#tag_group input").attr("name", "tags[" + index + "]");
                });
            }
        });
    </script>
@stop
