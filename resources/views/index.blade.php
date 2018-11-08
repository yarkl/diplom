<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ url('/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('/assets/css/vis.min.css') }}">
    <title>Diplom</title>
</head>
<style type="text/css">
    #graph {
        width: 800px;
        height: 600px;
        border: 1px solid lightgray;
    }
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Diplom</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">Понятия <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Курс</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Про проект</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
</nav>
<div class="container">
    <div class="row mt-5">
        <section>
            <div id="graph" class="row col-lg-12" ></div>
        </section>
        <section class="col-lg-3" style="margin-top: 2vh">
            <div class="row">
                <ul class="list-group">
                    <li class="list-group-item">Cras justo odio</li>
                    <li class="list-group-item">Dapibus ac facilisis in</li>
                    <li class="list-group-item">Morbi leo risus</li>
                    <li class="list-group-item">Porta ac consectetur ac</li>
                    <li class="list-group-item">Vestibulum at eros</li>
                </ul>
            </div>

        </section>
    </div>

</div>


<script src="{{ url('/assets/js/jquery.js') }}"></script>
<script src="{{ url('/assets/js/proper.js') }}"></script>
<script src="{{ url('/assets/js/bootstrap.min.js') }}"></script>
<script src="{{ url('/assets/js/vis.min.js') }}"></script>
<script src="{{ url('/assets/js/graph.js') }}"></script>

</body>
</html>
