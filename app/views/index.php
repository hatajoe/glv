
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
    <div id='calendar'></div>


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

    <script type='text/javascript'>
        $(document).ready(function() {
         $.ajax({
            type: "GET",
            url : "/milestones",
            success : function(milestones){
                var events = [];
                Object.keys(milestones).forEach(function (pk) {
                    console.log('project ' + pk);
                    var project = milestones[pk];
                    Object.keys(project).forEach(function (mk) {
                        console.log('milestone ' + mk);
                        var m = project[mk];
                        if (m['due_date'] == '') {
                            return;
                        }
                        var s = m['due_date'] + " 00:00:00";
                        var e = m['due_date'] + " 23:59:59";
                        events.push({
                            title: m['title'],
                            start: s,
                            end: e
                        });
                    });
                });
                $('#calendar').fullCalendar({
                    events: events
                });
            }
        },"json");
     });
 </script>

</body>
