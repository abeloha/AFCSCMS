<?php

namespace App\Http\Controllers;

use App\Models\Event as ModelsEvent;
use Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = get_events();        
        return view('event.list',['events' => $events]);
    }

    public function delete(Request $request,$id)
    {
        $event = ModelsEvent::find($id);  
        if($event){
            $event->delete();
        }
        return redirect("events/?deleted");
    }

    public function add(Request $request)
    {
        $start = $request->start_at_date.' '.$request->start_at_time;
        $end = $request->end_at_date.' '.$request->end_at_time;
         
        $start_at = date('Y-m-d H:i', strtotime($start));
        $end_at = date('Y-m-d H:i', strtotime($end));

        $event = new ModelsEvent();
        $event->title = $request->title;
        $event->start = $start_at;
        $event->end = $end_at;
        $event->save();
        return redirect("events/?added");
    }
}
