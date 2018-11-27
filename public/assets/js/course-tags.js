(function () {
    data = function(){
        let result = null;
        $.ajax({
            url: '/course-tags',
            type: 'get',
            dataType: 'json',
            async: false,
            success: function(data) {
                result = data
            }
        });
        return result;
    }();
    let tags = [];
    $.each(data, function(index, event) {
        var events = $.grep(tags, function (e) {
            return event.id === e.id &&
                event.name === e.name;
        });
        if (events.length === 0) {
            tags.push(event);
        }
    });
    $("#tags").typeahead({

        // data source
        source: tags,

        // how many items to show
        items: 8,

        // default template
        menu: '<ul class="typeahead dropdown-menu" role="listbox"></ul>',
        item: '<li><a class="dropdown-item" href="#" role="option" style="font-size: 12px"></a></li>',
        headerHtml: '<li class="dropdown-header"></li>',
        headerDivider: '<li class="divider" role="separator"></li>',
        itemContentSelector:'a',

        // min length to trigger the suggestion list
        minLength: 1,

        // number of pixels the scrollable parent container scrolled down
        scrollHeight: 0,

        // auto selects the first item
        autoSelect: true,

        // callbacks
        afterSelect: $.noop,
        afterEmptySelect: $.noop,

        // adds an item to the end of the list
        addItem: false,

        // delay between lookups
        delay: 0,
        updater: function(item) {
            $( "#search" ).attr('action', '/course-show/' + item.show);
            return item;
        }
    });
}());
