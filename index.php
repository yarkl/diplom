<?php
/**
 * Created by PhpStorm.
 * User: yaro
 * Date: 19.10.18
 * Time: 11:51
 */
?>
<!doctype html>
<html>
<head>
    <title>vis.js Graph demo</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.21.0/vis.min.css" rel="stylesheet" type="text/css" />
</head>
<style type="text/css">
    #graph {
        width: 1000px;
        height: 800px;
        border: 1px solid lightgray;
    }
</style>

<body>
<div id="graph"></div>
<script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
<script type="text/javascript">
    var nodes,edges,json;
    //Делаем гет запрос на index2.php и в свойстов response записываем результат
    // json_encode(['labels' => $labels,'nodes' => $array],JSON_UNESCAPED_UNICODE); этой функции из index2.php
    data = $.get('/index2.php',function (response) {
        //Это для вывода в коносоль браузера
        console.log(response);
        //В свойство json парсим джсон свойство response
        json = $.parseJSON(response);
        console.log(json);
        console.log(json.labels);
        /*В результате парсинга получаем два массива обьектов (смотри в консоле браузера)
        * json.labels - это Концепты {id:1,label:Pozvovnochnik}
        * json.nodes- это массив обьектов {from: 1, to:2} (тоесть в первый концепт вложен второй)
        * */

        labels = [];
        //Здесь удаляем дубликаты и записываем их в labels = []
        $.each(json.labels, function(index, event) {
            var events = $.grep(labels, function (e) {
                return event.id === e.id &&
                    event.label === e.label;
            });
            if (events.length === 0) {
                labels.push(event);
            }
        });
        console.log(labels);
        nodes = [];
        //Здесь удаляем дубликаты  записываем их в nodes = []
        $.each(json.nodes, function(index, event) {
            var events = $.grep(nodes, function (e) {
                return event.from === e.from &&
                    event.to === e.to;
            });
            if (events.length === 0) {
                nodes.push(event);
            }
        });
        console.log(nodes);
        
        var options = {
            nodes: {
                shape: 'dot',
                size: 60,
                font: {
                    size: 10,
                    color: 'black'
                },
                borderWidth: 2
            },
            edges: {
                width: 2,
                scaling:{
                    label: true,
                },

                arrows: {
                    to:     {enabled: false, scaleFactor:1, type:'arrow'},
                    middle: {enabled: false, scaleFactor:1, type:'arrow'},
                    from:   {enabled: true, scaleFactor:1, type:'arrow'}
                },
                selfReferenceSize: 10,
                length: 20,
                dashes:true
                },

            //physics: false
        };

        /*
        * Берем тег graph и внутри него создаем граф
        * */
        var container = document.getElementById('graph');
        var data = {
            nodes: labels,
            edges: nodes,
        };
        var graph = new vis.Network(container, data, options);
    });

</script>
</body>
</html>
