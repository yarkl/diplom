<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ url('/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/vis.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/my.css') }}">
    <title>Diplom</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Diplom</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="/">Понятия <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Курс</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/about">Про проект</a>
            </li>
        </ul>
        <form id="search" action="/show/0" class="form-inline my-2 my-lg-0" >
            <input id="tags" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0 " type="submit">Search</button>
        </form>
    </div>
</nav>

@yield('content')

<script src="{{ url('/assets/js/jquery.js') }}"></script>
<script src="{{ url('/assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ url('/assets/js/proper.js') }}"></script>
<script src="{{ url('/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ url('/assets/js/vis.min.js') }}"></script>
<script src="{{ url('/assets/js/graph.js') }}"></script>
<script src="{{ url('/assets/js/bootstrap3-typeahead.min.js') }}"></script>
<script src="{{ url('/assets/js/tags.js') }}"></script>


</body>
</html>