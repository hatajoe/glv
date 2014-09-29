
<head>

    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="//code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- fullCalendar -->
    <link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.0.2/fullcalendar.css" rel="stylesheet" type="text/css" />
    <link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.0.2/fullcalendar.print.css" rel="stylesheet" type="text/css" media='print' />
    <!-- Theme style -->
    <link href="../css/AdminLTE.css" rel="stylesheet" type="text/css" />
    <style type='text/css'>
        #calendar {
            width: 900px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div id='loading' class="box" style="min-height: 367px;display: none;">
        <div class="overlay"></div>
        <div class="loading-img"></div>
    </div>

    <div id='calendar'>
    </div>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="//code.jquery.com/ui/1.11.1/jquery-ui.min.js" type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="../js/AdminLTE/app.js" type="text/javascript"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../js/AdminLTE/demo.js" type="text/javascript"></script>
    <!-- fullCalendar -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.7.0/moment.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.0.2/fullcalendar.min.js" type="text/javascript"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.0.2/gcal.js" type="text/javascript"></script>

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

</body>
