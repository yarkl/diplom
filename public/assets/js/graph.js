(function () {
    var nodes,edges,json;
//Делаем гет запрос на index2.php и в свойстов response записываем результат

// json_encode(['labels' => $labels,'nodes' => $array],JSON_UNESCAPED_UNICODE); этой функции из index2.php

    data = $.get('/graph',function (response) {
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
}());
