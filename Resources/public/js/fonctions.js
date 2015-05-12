/**
 * Created by lsabatier on 20/04/15.
 */
$(document).ready(function() {

    var hostname = window.location.hostname;

    $('#calendar').fullCalendar({
        // http://fullcalendar.io/docs/
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        eventLimit: true, // allow "more" link when too many events
        events: 'http://'  + hostname + '/agenda/list/events.json',
        eventRender: function(event, element) {
            element.tooltip({
                "title": truncateString( event.description ),
                "html": true,
                "placement": 'auto'
            });
        },
        eventClick:  function(event, jsEvent, view) {
            $('#modalTitle').html(event.title);
            $('#modalBody').html(event.description);
            $('#eventUrl').attr('href',event.url);
            $('#fullCalModal').modal();
        }
    });

    function truncateString( myString ) {
        var length = 100;
        return myString.substring(0,length) + '...';
    }
});