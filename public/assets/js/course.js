(function () {
    var data,nodes,json,edges;

    json = function(scriptUrl = '/course-graph'){
        let uri = location.pathname;
        let split  = uri.split('/');
        console.log(scriptUrl);
        if(split.length == 3){
            scriptUrl = '/course-json/'+split[2];
            console.log(scriptUrl);
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
                return event.id === e.id;
            });
            if (events.length === 0) {
                nodes.push(event);
            }
        });
        console.log(nodes);
        nodes = new vis.DataSet(nodes);

        edges = [];

        $.each(data.edges, function(index, event) {
            var events = $.grep(edges, function (e) {
                return event.from === e.from &&
                    event.to === e.to;
            });
            if (events.length === 0) {
                edges.push(event);
            }
        });
        edges = new vis.DataSet(edges);
        return {
            nodes: nodes,
            edges: edges,
        };
    };
    //UD, DU, LR, R
    var zoomViewValue = true;
    /*var options =//{};
        {
            nodes: {
                shape: 'dot'
                , size: 15
                , widthConstraint: {
                    maximum: 250
                }
                , font:{
                    size:18
                }
            }
            ,
            edges: {
                arrows: 'to'
                , smooth: {
                    type: 'cubicBezier'
                    , forceDirection: 'horizontal'
                }
            }

            , interaction: {
                hover: true
                , zoomView: zoomViewValue
            },

            layout: {
                hierarchical: {
                    direction: 'UD',
                }
            },


        };*/

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


    graph.on('click', onClick);
    graph.on('doubleClick', onDoubleClick);

    var doubleClickTime = 0;
    var threshold = 200;

    function onClick(params) {
        var t0 = new Date();
        if (t0 - doubleClickTime > threshold) {
            setTimeout(function () {
                if (t0 - doubleClickTime > threshold) {
                    doOnClick(params);
                }
            },threshold);
        }
    }

    function doOnClick(params) {
        if (params.nodes.length === 1) {
            var node = nodes.get(params.nodes[0]);
            if(node.url != undefined){
                window.location.href = node.url;
            }
        }
    }

    function onDoubleClick(params) {
        doubleClickTime = new Date();
        if (params.nodes.length === 1) {
            var node = nodes.get(params.nodes[0]);
            if(node.url2 != undefined){
                console.log(node.url2);
                window.location.href = node.url2
            }
        }

    }

}());
