<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>Securtiy Guards Schedule</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> <!-- Bootstrap -->

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.css" /> <!-- fullcalendar -->
    </head>
    <body>
      <div class="container">
        <div id="calendar"></div>
      </div>

      <!-- Add schedule modal -->
      <div class="modal" id="modalAdd">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Add roster</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              <form id="rosterAdd">
                <div class="form-group">
                  <label for="roster_start">Start</label>
                  <input type="text" class="form-control" id="roster_start" readonly>
                </div>
                <div class="form-group">
                  <label for="roster_end">End</label>
                  <input type="text" class="form-control" id="roster_end" readonly>
                </div>
                <div class="form-group">
                  <label for="roster_guard">Select a security guard</label>
                  <select class="form-control" id="roster_guard" required>
                    <option></option>
                    @foreach ($guards as $guard)
                        <option value="{{$guard->id}}">{{$guard->name}}</option>
                    @endforeach
                  </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
              </form>
            </div>

          </div>
        </div>
      </div>

      <!-- Remove schedule modal -->
      <div class="modal" id="modalRemove">
        <div class="modal-dialog">
          <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">Remove roster</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
              <form id="rosterRemove">
                <div class="form-group">
                  <label for="remove_roster_start">Start</label>
                  <input type="text" class="form-control" id="remove_roster_start" readonly>
                </div>
                <div class="form-group">
                  <label for="remove_roster_end">End</label>
                  <input type="text" class="form-control" id="remove_roster_end" readonly>
                </div>
                <div class="form-group">
                  <label for="remove_roster_guard">Guard</label>
                  <input type="text" class="form-control" id="remove_roster_guard" readonly>
                </div>
                <input type="hidden" id="remove_roster_id">
                <button type="submit" class="btn btn-primary">Remove</button>
              </form>
            </div>

          </div>
        </div>
      </div>
    </body>

    <!-- Moment -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>

    <!-- jQuery -->
    <script
			  src="https://code.jquery.com/jquery-3.4.1.min.js"
			  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
			  crossorigin="anonymous"></script>

    <!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <!-- fullcalendar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js"></script>

    <script>
      $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var calendar = $('#calendar').fullCalendar({
          header:{
           left:'prev,next today',
           center:'title',
           right:'month,agendaWeek,agendaDay'
          },
          defaultView: 'agendaWeek',
          allDaySlot: false,
          events: {
            url: 'ajax/roster_load',
            method: 'POST',
            failure: function() {
              alert('there was an error while fetching events!');
            },
          },
          selectable:true,
          selectHelper:true,
          select: function(start, end)
          {
            $("#roster_guard").val("");
            $('#modalAdd').modal('show');
            $("#roster_start").val(moment(start).format("YYYY-MM-DD HH:mm"));
            $("#roster_end").val(moment(end).format("YYYY-MM-DD HH:mm"));
          },
          eventClick:function(event)
          {
            var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm");
            var end = $.fullCalendar.formatDate(event.end, "YYYY-MM-DD HH:mm");
            var guard = event.title;
            var id = event.id;
            $('#modalRemove').modal('show');
            $("#remove_roster_start").val(start);
            $("#remove_roster_end").val(end);
            $("#remove_roster_guard").val(guard);
            $("#remove_roster_id").val(id);
          },
        });

        $( "#rosterAdd" ).submit(function( event ) {
      	  	event.preventDefault();
          	var guard_id = $("#roster_guard").val();
          	var start = $("#roster_start").val();
          	var end = $("#roster_end").val();

          	$.ajax({
  	    	    url:"ajax/roster_store",
  	       	  type:"POST",
  	       	  data:{guard_id:guard_id, start:start, end:end},
  	       	  success:function(data)
  	       	  {
                $('#modalAdd').modal('hide');
                calendar.fullCalendar('refetchEvents');
                alert(data.success);
  	       	  }
    	      })
      	});

        $( "#rosterRemove" ).submit(function( event ) {
      	  	event.preventDefault();
          	var remove_roster_id = $("#remove_roster_id").val();

          	$.ajax({
  	    	    url:"ajax/roster_remove",
  	       	  type:"POST",
  	       	  data:{remove_roster_id:remove_roster_id},
  	       	  success:function(data)
  	       	  {
                $('#modalRemove').modal('hide');
                calendar.fullCalendar('refetchEvents');
                alert(data.success);
  	       	  }
    	      })
      	});

      });
    </script>
</html>
