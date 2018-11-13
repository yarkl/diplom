(function () {
    var data,nodes,json,edges;

    json = function(scriptUrl = '/graph'){
        data = function(){
            let result = null;
            $.ajax({
                url: scriptUrl,
                type: 'get',
                dataType: 'json',
                async: false,
                success: function(data) {
                    result = data;
                }
            });
            return result;
        }();
        nodes = new vis.DataSet(data.nodes);
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

            arrows: {
                to:     {enabled: true, scaleFactor:1, type:'arrow'},
                middle: {enabled: false, scaleFactor:1, type:'arrow'},
                from:   {enabled: false, scaleFactor:1, type:'arrow'}
            },
            selfReferenceSize: 10,
            length: 20,
            dashes:true
        },

        //physics: false
    };

    var container = document.getElementById('graph');

    var graph = new vis.Network(container, json(), options);

    graph.on("selectNode", function (params) {
        if (params.nodes.length === 1) {
            var node = nodes.get(params.nodes[0]);
            if(node.url != undefined){

            }
        }
    });

}());
