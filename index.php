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
    data = $.get('https://diplom.local/index2.php',function (response) {
        json = $.parseJSON(response);
        console.log(json.labels);
        labels = [];
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

        // nodes = [];
        // $.each(json.labels, function(data) {
        //     let iam = $(this);
        //     nodes.push({id:iam[0].id,label:iam[0].label});
        // });
        // edges = [];
        // $.each(json.nodes, function(data) {
        //     let iam = $(this);
        //     edges.push({id:iam[0].from,to:iam[0].to});
        // });

        var container = document.getElementById('graph');
        var data = {
            nodes: labels,
            edges: nodes,
        };
        var graph = new vis.Network(container, data, {});
    });

</script>
</body>
</html>
