(function () {
    var data,nodes,json,edges;

    json = function(scriptUrl = '/graph'){
        let uri = location.pathname;
        let split  = uri.split('/');
        if(split.length == 3){
            scriptUrl = '/json/'+split[2];
            var category = $('#tags').val();
            console.log(category);
        }
        data = function(){
            let result = null;
            $.ajax({
                url: scriptUrl,
                type: 'get',
                dataType: 'json',
                data: {category:category},
                async: false,
                success: function(data) {
                    result = data;
                }
            });
            return result;
        }();


        nodes = [];
        $.each(data.nodes, function(index, event) {
            var events = $.grep(nodes, function (e) {
                return event.id === e.id &&
                    event.label === e.label;
            });
            if (events.length === 0) {
                nodes.push(event);
            }
        });
        nodes = new vis.DataSet(nodes);

        edges = new vis.DataSet(data.edges);
        return {
            nodes: nodes,
            edges: edges,
        };
    };

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

            selfReferenceSize: 10,
            length: 20,
        },

        //physics: false
    };

    var container = document.getElementById('graph');

    var graph = new vis.Network(container, json(), options);


    graph.on('click', function (params) {
        if (params.nodes.length === 1) {
            var node = nodes.get(params.nodes[0]);
            if(node.url != undefined){
                window.location.href = node.url;
            }
        }
    });
    graph.on('doubleClick', onDoubleClick);

    var doubleClickTime = 0;
    var threshold = 200;

    function onClick() {
        var t0 = new Date();
        if (t0 - doubleClickTime > threshold) {
            setTimeout(function () {
                if (t0 - doubleClickTime > threshold) {
                    graph.on('click', function (params) {
                        if (params.nodes.length === 1) {
                            var node = nodes.get(params.nodes[0]);
                            if(node.url != undefined){
                                window.location.href = node.url;
                            }
                        }
                    });
                }
            },threshold);
        }
    }

    function onDoubleClick() {
        window.location.href = "http://pozvonochnik.org/concept:2"
    }

}());
