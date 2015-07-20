{{--updae.blade.php의 서브 뷰로 사용되는 뷰--}}

<div class="form-group">
    <label for="usages{{ $index }}">사용처</label>
    <input type="text" class="form-control" id="usages{{ $index }}" name="usages{{ $index }}" placeholder="사용처를 입력해주세요. (예. Cocoa, Cocoa Touch)" value="{{ $word->usages[$index] or "" }}">
</div>

<div class="form-group">
    <label for="kor_expressions{{ $index }}">한글표현</label>
    <input type="text" class="form-control" id="kor_expressions{{ $index }}" name="kor_expressions{{ $index }}" placeholder="한글표현을 입력해주세요. (예. 위임자, 델리게이트)" value="{{ $word->kor_expressions[$index] or "" }}">
</div>

<div class="form-group">
    <label for="examples{{ $index }}-0">사용 예</label>
    @if (isset($word))
        @for ($j = 0; $j < count($word->examples[$index]); $j++)
            <textarea rows="3" class="form-control" id="examples{{ $index }}-{{ $j }}" name="examples{{ $index }}-{{ $j }}" placeholder="사용 예를 입력해주세요. (예. The delegating object is typically a framework object, and the delegate is typically a custom controller object. (위임하는 객체는 보통 프레임워크 객체이고, 위임받는 객체는 보통 사 용자 정의 컨트롤러 객체이다))">{{ $word->examples[$index][$j] or "" }}</textarea>
        @endfor
    @else
        <textarea rows="3" class="form-control" id="examples{{ $index }}-0" name="examples{{ $index }}-0" placeholder="사용 예를 입력해주세요. (예. The delegating object is typically a framework object, and the delegate is typically a custom controller object. (위임하는 객체는 보통 프레임워크 객체이고, 위임받는 객체는 보통 사 용자 정의 컨트롤러 객체이다))"></textarea>
    @endif
    <button type="button" class="btn btn-default add-examples" data-index="{{ $index }}">사용 예 추가하기</button>
</div>

<div class="form-group">
    <label for="related_words{{ $index }}-0">관련 단어</label>
    @if (isset($word))
        @for ($j = 0; $j < count($word->related_words[$index]); $j++)
            <div class="form-inline" id="related_words{{ $index }}-{{ $j }}">
                <input type="text" class="form-control" id="related_words_words{{ $index }}-{{ $j }}" name="related_words_words{{ $index }}-{{ $j }}" placeholder="관련 단어의 단어를 입력해주세요. (예. Data source)" value="{{ $word->related_words[$index][$j]->word or "" }}">
                <input type="text" class="form-control" id="related_words_links{{ $index }}-{{ $j }}" name="related_words_links{{ $index }}-{{ $j }}" placeholder="관련 단어의 링크를 입력해주세요. (예. https://developer.apple.com/library/ios/documentation/WindowsViews/Conceptual/CollectionViewPGforIOS/CreatingCellsandViews/CreatingCellsandViews.html)" value="{{ $word->related_words[$index][$j]->link or "" }}">
            </div>
        @endfor
    @else
        <div class="form-inline" id="related_words{{ $index }}-0">
            <input type="text" class="form-control" id="related_words_words{{ $index }}-0" name="related_words_words{{ $index }}-0" placeholder="관련 단어의 단어를 입력해주세요. (예. Data source)">
            <input type="text" class="form-control" id="related_words_links{{ $index }}-0" name="related_words_links{{ $index }}-0" placeholder="관련 단어의 링크를 입력해주세요. (예. https://developer.apple.com/library/ios/documentation/WindowsViews/Conceptual/CollectionViewPGforIOS/CreatingCellsandViews/CreatingCellsandViews.html)">
        </div>
    @endif
    <button type="button" class="btn btn-default add-related_words" data-index="{{ $index }}">관련 단어 추가하기</button>
</div>

<div class="form-group">
    <label for="summaries{{ $index }}">간략 설명</label>
    <textarea rows="3" class="form-control" id="summaries{{ $index }}" name="summaries{{ $index }}" placeholder="간략 설명을 입력해주세요. (예. 다른 객체에 도움을 주기 위해 특정 행위를 위임받은 객체를 뜻한다)">{{ $word->summaries[$index] or "" }}</textarea>
</div>

<div class="form-group">
    <label for="related_links{{ $index }}-0">관련 링크</label>
    @if (isset($word))
        @for ($j = 0; $j < count($word->related_links[$index]); $j++)
            <input type="text" class="form-control" id="related_links{{ $index }}-{{ $j }}" name="related_links{{ $index }}-{{ $j }}" placeholder="관련 링크를 입력해주세요. (예. http://xiles.tistory.com/221)" value="{{ $word->related_links[$index][$j] or "" }}">
        @endfor
    @else
        <input type="text" class="form-control" id="related_links{{ $index }}-0" name="related_links{{ $index }}-0" placeholder="관련 링크를 입력해주세요. (예. http://xiles.tistory.com/221)">
    @endif
    <button type="button" class="btn btn-default add-related_links" data-index="{{ $index }}">관련 링크 추가하기</button>
</div>
