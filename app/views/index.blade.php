@extends('layouts.master')
@section('content-header')
<h1>
    Milestones
</h1>
<ol class="breadcrumb">
    <li><a href="/"><i class="fa fa-dashboard"></i> Milestones</a></li>
</ol>
@stop
@section('content')
<div id='calendar'>
</div>
@stop

@section('js')
<script type='text/javascript'>
$(document).ready(function() {
    var projectColorClassNames = [
        '#f56954',
        '#f39c12',
        '#00c0ef',
        '#3c8dbc',
        '#39cccc',
        '#0073b7',
        '#3d9970',
        '#01ff70',
        '#ff851b',
        '#001f3f',
        '#f012be',
        '#932ab6',
        '#00a65a',
        '#85144b',
    ];
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        loading: function(bool, view) {
            if (bool) {
                $('#loading').show();
                $('#loading').hide();
            } else {
                $('#loading').hide();
                $('#calendar').show();
            }
        },
        eventSources: [{
          url: 'https://www.google.com/calendar/feeds/japanese__ja%40holiday.calendar.google.com/public/basic',
          currentTimezone: 'Asia/Tokyo',
          color: '#ff9999'
        }],
        viewRender: function (view, elem) {
            var events = [];
            $('#calendar').fullCalendar('removeEvents');
            $.ajax({
                type: "GET",
                url : "/milestones",
                success : function(milestones){
                   Object.keys(milestones).forEach(function (pk) {
                       var project = milestones[pk];
                       Object.keys(project).forEach(function (mk) {
                           var m = project[mk];
                           if (m['due_date'] === null || m['due_date'] === undefined) {
                               return;
                           }
                           events.push({
                               id: m['project_id'],
                               title: '[' + m['path_with_namespace'] + ']' + m['title'],
                               start: m['due_date'],
                               allDay: true,
                               url: m['web_url'],
                               color: m['state'] !== 'active' ? '#eaeaec' : projectColorClassNames[parseInt(m['project_id'] % projectColorClassNames.length)],
                           });
                       });
                   });
                   $('#calendar').fullCalendar('addEventSource', events);
                   $.ajax({
                      type: "GET",
                      url: "https://www.google.com/calendar/feeds/japanese__ja%40holiday.calendar.google.com/public/basic",
                      success: function (es) {
                        es.forEach(function (e) {
                            events.push(e);
                        })
                        $('#calendar').fullCalendar('addEventSource', events);
                      }
                   })
                }
            },"json");
        },
        eventClick: function(event) {
            if (event.url) {
                window.open(event.url);
                return false;
            }
        }
    });
});
</script>
@stop
