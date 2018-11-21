(function () {
    data = function(){
        let result = null;
        $.ajax({
            url: '/tags',
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
            return event.value === e.value &&
                event.label === e.label;
        });
        if (events.length === 0) {
            tags.push(event);
        }
    });

    $( "#tags" ).autocomplete({
        minLength: 3,
        source: tags,
        select: function( event, ui ) {
            $( "#tags" ).val( ui.item.label );
            $( "#search" ).attr('action', '/show/' + ui.item.value);
            return false;
        }
    });
}());
