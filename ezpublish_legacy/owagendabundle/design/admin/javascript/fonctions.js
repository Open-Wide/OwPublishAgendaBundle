/**
 * Created by lsabatier on 20/04/15.
 */
$(document).ready(function() {

    $('#calendar').fullCalendar({
        // http://fullcalendar.io/docs/
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        //editable: true,
        //selectable: true,
        eventLimit: true, // allow "more" link when too many events
        events: 'http://benevolat.local/agenda/list/events.json', // Put front-office hostname
        eventRender: function(event, element) {
            element.tooltip({
                "title": event.description,
                "html": true,
                "placement": 'auto'
            });
        }
    });
});