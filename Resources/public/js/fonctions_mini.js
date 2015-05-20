/**
 * Created by lsabatier on 20/04/15.
 */
$(document).ready(function() {

    var hostname = window.location.hostname;

    $('#calendar').fullCalendar({
        header: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },
        height: 'auto',
        timeFormat: 'H:mm',
        handleWindowResize: true,
        eventLimit: true, // allow "more" link when too many events
        events: 'http://'  + hostname + '/agenda/list/events.json',
        // add event name to title attribute on mouseover
        eventMouseover: function(event, jsEvent, view) {
            if (view.name !== 'agendaDay') {
                $(jsEvent.target).attr('title', event.title);
            }
        },
        eventClick: function(event) {
            if (event.url) {
                window.open(event.url);
                return false;
            }
        }
    });
});