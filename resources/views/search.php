<!DOCTYPE html>
<html>
<head>
    <title>Tech E to K</title>
    <script type="javascript">
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
<h1>검색어: <?php echo $keyword; ?></h1>
</body>
</html>
