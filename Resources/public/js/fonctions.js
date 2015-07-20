/**
 * Created by lsabatier on 20/04/15.
 */
$(document).ready(function () {
    $('.fullcalendar-calendar').each(function (index) {
        var calendar = $(this);
        calendar.fullCalendar({
            // http://fullcalendar.io/docs/
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'month',
            eventLimit: true, // allow "more" link when too many events
            events: calendar.attr('data-event-url'),
            eventRender: function (event, element) {
                element.tooltip({
                    "title": truncateString(event.description),
                    "html": true,
                    "placement": 'auto'
                });
            },
        });
    });

    $('.fullcalendar-calendar-folder').each(function (index) {
        var calendar = $(this);
        var locationIds = calendar.attr('data-event-source').split(',');
        var baseUrl = calendar.attr('data-event-url');
        var eventSources = [];
        locationIds.forEach(function (entry) {
            eventSources.push({
                url: baseUrl + "?locationId=" + entry,
                color: '#' + Math.floor((Math.random() * 1000000) + 1)
            });
        });
        calendar.fullCalendar({
            // http://fullcalendar.io/docs/
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'month',
            eventLimit: true, // allow "more" link when too many events
            eventSources: eventSources,
            eventRender: function (event, element) {
                element.tooltip({
                    "title": truncateString(event.description),
                    "html": true,
                    "placement": 'auto'
                });
            }
        });
    });

    $('.fullcalendar-calendar-mini').each(function (index) {
        var calendar = $(this);
        calendar.fullCalendar({
            header: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            height: 'auto',
            timeFormat: 'H:mm',
            handleWindowResize: true,
            eventLimit: true, // allow "more" link when too many events
            events: calendar.attr('data-event-url'),
            // add event name to title attribute on mouseover
            eventMouseover: function (event, jsEvent, view) {
                if (view.name !== 'agendaDay') {
                    $(jsEvent.target).attr('title', event.title);
                }
            },
            /*eventClick: function(event) {
             if (event.url) {
             window.open(event.url);
             return false;
             }
             }*/
        });
    });

    function truncateString(myString) {
        var length = 100;
        return myString.substring(0, length) + '...';
    }
});