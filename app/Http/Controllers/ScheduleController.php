<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Schedule;
use App\Guard;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display the roster page.
     */
    public function index()
    {
        $guards = Guard::all();
        return view('schedule', ['guards' => $guards]);
    }

    /**
     * Load all rosters
     */
    public function load()
    {
        $schedules = Schedule::all();

        foreach ($schedules as $schedule) {
            $data[] = array(
              'id'   	    => $schedule->id,
              'title'     => $schedule->guard_info->name,
              'start'     => $schedule->start_event,
              'end'       => $schedule->end_event,
             );
        }

        return response()->json($data);
    }

    /**
     * Create a new roster
     */
    public function store(Request $request)
    {
        $guard_id = $request->input('guard_id');
        $start = $request->input('start');
        $end = $request->input('end');

        // The guards cannot work longer than 12 hours, and cannot work less than 3.5 hours during the day due to strict labour laws.
        $to = Carbon::createFromFormat('Y-m-d H:i:s', $start.":00");
        $from = Carbon::createFromFormat('Y-m-d H:i:s', $end.":00");
        $diff_in_minutes = $to->diffInMinutes($from);

        if($diff_in_minutes > 720){
            return response()->json(['success'=>'Sorry the guard cannot work longer than 12 hours']);
        }
        else if ($diff_in_minutes < 210) {
            return response()->json(['success'=>'Sorry the guard cannot work less than 3.5 hours']);
        }

        $previous_twenty_four_hours = date('Y-m-d H:i',strtotime('-1439 minutes',strtotime($start)));
        $next_twenty_four_hours = date('Y-m-d H:i',strtotime('+1439 minutes',strtotime($start)));

        // check if the guard has roster for the day
        if(Schedule::where('guard_id', $guard_id)->whereBetween('start_event', [$previous_twenty_four_hours, $start])->exists() || Schedule::where('guard_id', $guard_id)->whereBetween('start_event', [$start, $next_twenty_four_hours])->exists()){
            return response()->json(['success'=>'Sorry the guard cannot work more shift within 24 hours.']);
        }

        $schedule = new Schedule;

        $schedule->guard_id = $guard_id;
        $schedule->start_event = $start;
        $schedule->end_event = $end;

        $schedule->save();

        return response()->json(['success'=>'Roster is added successfully.']);
    }

    /**
     * Remove a roster
     */
    public function remove(Request $request)
    {
        $id = $request->input('remove_roster_id');

        $schedule = Schedule::find($id);

        $schedule->delete();

        return response()->json(['success'=>'Roster is removed successfully.']);
    }

}
