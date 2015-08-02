<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="techetok translate words programming developing development etok teche2k" name="keywords" />
    <meta content="영어로된 기술 용어를 이해하기 쉬운 한국어로 번역" name="description"/>
    <meta content="Yagom & Yoobato" name="author" />

    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/images/favicon.ico" type="image/x-icon">

    <title>@yield("title", "TechEtoK")</title>

    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/techetok.bootstrap.css" />
    <link rel="stylesheet" href="/css/techetok.css" />

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Analytics -->
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-65738490-1', 'auto');
        ga('require', 'linkid', 'linkid.js');
        ga('require', 'displayfeatures');
        ga('send', 'pageview');
    </script>
</head>

<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Tech<b>E</b>to<b>K</b></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="/"><i class="glyphicon glyphicon-home"></i> Home</a></li>
                <li><a href="#"><i class="glyphicon glyphicon-question-sign"></i> About</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    @yield("content")
</div>

<footer class="footer">
    <div class="container">
        <p class="text-muted">Copyright © 2015 TechEtoK Team.</p>
    </div>
</footer>

<script src="/bower_components/modernizr/modernizr.js"></script>
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/js/ie10-viewport-bug-workaround.js"></script>
@yield("script")
</body>
</html>
