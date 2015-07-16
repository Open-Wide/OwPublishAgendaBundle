/**
 * Created by lsabatier on 20/04/15.
 */
$(document).ready(function() {

    var hostname = window.location.hostname;
    var locationId = $('#locationId').val();

    $('#calendar').fullCalendar({
        // http://fullcalendar.io/docs/
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        eventLimit: true, // allow "more" link when too many events
        events: 'http://intra-vendee.loc/agenda/list/events.json?locationId='+locationId+'&admin=1',
        eventRender: function(event, element) {
            element.tooltip({
                "title": event.description,
                "html": true,
                "placement": 'auto'
            });
        },
     
    });
});