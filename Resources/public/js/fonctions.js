/**
 * Created by lsabatier on 20/04/15.
 */
$(document).ready(function () {
    var baseCalendarConfig = {
        // http://fullcalendar.io/docs/
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        eventLimit: true, // allow "more" link when too many events
        eventRender: function (event, element) {
            element.tooltip({
                title: truncateString(event.description),
                html: true,
                placement: 'auto'
            });
        }
    };
    var baseMiniCalendarConfig = {
        header: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },
        height: 'auto',
        timeFormat: 'H:mm',
        handleWindowResize: true,
        eventLimit: true, // allow "more" link when too many events
        // add event name to title attribute on mouseover
        eventMouseover: function (event, jsEvent, view) {
            if (view.name !== 'agendaDay') {
                $(jsEvent.target).attr('title', event.title);
            }
        }

    };
    $('.fullcalendar-calendar.fullcalendar-calendar-agenda').each(function (index) {
        var calendar = $(this);
        var calendarConfig = baseCalendarConfig;
        calendarConfig["events"] = calendar.attr('data-event-url');
        calendar.fullCalendar(calendarConfig);
    });

    $('.fullcalendar-calendar.fullcalendar-calendar-folder').each(function (index) {
        var calendar = $(this);
        var locationIds = calendar.attr('data-event-source').split(',');
        var baseUrl = calendar.attr('data-event-url');
        var eventSources = [];
        locationIds.forEach(function (entry) {
            eventSources.push({
                url: baseUrl + "?locationId=" + entry
            });
        });
        var calendarConfig = baseCalendarConfig;
        calendarConfig["eventSources"] = eventSources;
        calendar.fullCalendar(calendarConfig);
    });

    $('.fullcalendar-calendar-mini.fullcalendar-calendar-agenda').each(function (index) {
        var calendar = $(this);
        var calendarConfig = baseMiniCalendarConfig;
        calendarConfig["events"] = calendar.attr('data-event-url');
        calendar.fullCalendar(calendarConfig);
    });

    $('.fullcalendar-calendar-mini.fullcalendar-calendar-folder').each(function (index) {
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
        var calendarConfig = baseMiniCalendarConfig;
        calendarConfig["eventSources"] = eventSources;
        calendar.fullCalendar(calendarConfig);
    });

    function truncateString(myString) {
        var length = 100;
        return myString.substring(0, length) + '...';
    }
});