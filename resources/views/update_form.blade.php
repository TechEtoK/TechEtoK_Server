{{--updae.blade.php의 서브 뷰로 사용되는 뷰--}}

<div id="word_content">
    <span class="delete_word_content"><i class="glyphicon glyphicon-trash"></i> 지우기</span>
    <hr>

    <div class="form-group" id="usage_group">
        <label for="usages">사용처</label>
        <input type="text" class="form-control" id="usages" placeholder="사용처를 입력해주세요. (예. Cocoa, Cocoa Touch)" value="{{ $word->usages[$index] or "" }}">
    </div>

    <div class="form-group" id="kor_expressions_group">
        <label for="kor_expressions">한글표현</label>
        <input type="text" class="form-control" id="kor_expressions" placeholder="한글표현을 입력해주세요. (예. 위임자, 델리게이트)" value="{{ $word->kor_expressions[$index] or "" }}">
    </div>

    <div class="form-group" id="examples_group">
        <label for="examples">사용 예</label>
        @if (isset($word))
            @foreach ($word->examples[$index] as $example)
                <textarea rows="3" class="form-control" id="examples" placeholder="사용 예를 입력해주세요. (예. The delegating object is typically a framework object, and the delegate is typically a custom controller object. (위임하는 객체는 보통 프레임워크 객체이고, 위임받는 객체는 보통 사용자 정의 컨트롤러 객체이다))">{{ $example }}</textarea>
            @endforeach
        @else
            <textarea rows="3" class="form-control" id="examples" placeholder="사용 예를 입력해주세요. (예. The delegating object is typically a framework object, and the delegate is typically a custom controller object. (위임하는 객체는 보통 프레임워크 객체이고, 위임받는 객체는 보통 사용자 정의 컨트롤러 객체이다))"></textarea>
        @endif
        <button type="button" class="btn btn-default add-examples">사용 예 추가하기</button>
    </div>

    <div class="form-group" id="related_words_group">
        <label for="related_words">관련 단어</label>
        @if (isset($word))
            @foreach ($word->related_words[$index] as $related_word)
                <div class="form-inline" id="related_words">
                    <input type="text" class="form-control" id="related_words_words" placeholder="관련 단어의 단어를 입력해주세요. (예. Data source)" value="{{ $related_word->word }}">
                    <input type="text" class="form-control" id="related_words_links" placeholder="관련 단어의 링크를 입력해주세요. (예. https://developer.apple.com/library/ios/documentation/WindowsViews/Conceptual/CollectionViewPGforIOS/CreatingCellsandViews/CreatingCellsandViews.html)" value="{{ $related_word->link or "" }}">
                </div>
            @endforeach
        @else
            <div class="form-inline" id="related_words">
                <input type="text" class="form-control" id="related_words_words" placeholder="관련 단어의 단어를 입력해주세요. (예. Data source)">
                <input type="text" class="form-control" id="related_words_links" placeholder="관련 단어의 링크를 입력해주세요. (예. https://developer.apple.com/library/ios/documentation/WindowsViews/Conceptual/CollectionViewPGforIOS/CreatingCellsandViews/CreatingCellsandViews.html)">
            </div>
        @endif
        <button type="button" class="btn btn-default add-related_words">관련 단어 추가하기</button>
    </div>

    <div class="form-group" id="summaries_group">
        <label for="summaries">간략 설명</label>
        <textarea rows="3" class="form-control" id="summaries" placeholder="간략 설명을 입력해주세요. (예. 다른 객체에 도움을 주기 위해 특정 행위를 위임받은 객체를 뜻한다)">{{ $word->summaries[$index] or "" }}</textarea>
    </div>

    <div class="form-group" id="related_links_group">
        <label for="related_links">관련 링크</label>
        @if (isset($word))
            @foreach ($word->related_links[$index] as $related_link)
                <input type="text" class="form-control" id="related_links" placeholder="관련 링크를 입력해주세요. (예. http://xiles.tistory.com/221)" value="{{ $related_link }}">
            @endforeach
        @else
            <input type="text" class="form-control" id="related_links" placeholder="관련 링크를 입력해주세요. (예. http://xiles.tistory.com/221)">
        @endif
        <button type="button" class="btn btn-default add-related_links">관련 링크 추가하기</button>
    </div>
</div>
