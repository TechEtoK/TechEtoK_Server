<!DOCTYPE html>
<html>
    <head>
        <title>Tech E to K</title>
        <script type="text/javascript">
            function validateKeyword() {
                var form = document.searchForm;

                if (form.keyword.value == "") {
                    alert("검색어를 입력해주세요.");
                    form.keyword.focus();
                    return;
                }

                form.action = "/search";
                form.submit();
            }
        </script>
    </head>

    <body>
        <h1>어떤 단어가 궁금하신가요?</h1>
        <form name="searchForm" method="get">
            <input type="text" size="20" placeholder="검색어를 입력해주세요." name="keyword" />
            <input type="button" onclick="validateKeyword()" value="검색" />
        </form>
    </body>
</html>
