<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Term;

class AjaxController extends Controller
{
    public function manage(Request $request, $course)
    {
        $term = get_term_by_course($course);   
        if($term){
            $data = '';
            foreach ($term as $item){
                $data .= '<option value="'.$item->id.'">'.$item->name.'</option>';
            }
        }
        return '<option value="">No term for this course</option>';
    }

    public function check_requirement_dates(Request $request)
    {
        $data = "Failed to validate. Try again";        
        $done = true;
        
        $start = $request->start_at_date.' '.$request->start_at_time;
        $end = $request->end_at_date.' '.$request->end_at_time;
        if($request->show_at_date && $request->show_at_time){
            $show = $request->show_at_date.' '.$request->show_at_time;
        }else{
            $show = $start;
        }
        

        $startStm = strtotime($start);
        $endStm = strtotime($end);
        $showStm = strtotime($show);

        $today = strtotime(date('Y m d H'));
        
        if($today > $startStm){
            $done = false;
            $data = 'Start Submission Date cannot be in the past';
        }elseif($endStm < $startStm){
            $done = false;
            $data = 'Stop Submission Date cannot be a date before Start Submission Date';
        }elseif($showStm > $startStm){
            $done = false;
            $data = 'Show To Student Date cannot be after the Start Submission Date';
        }

        $response['done'] = $done;
        $response['data'] = $data;
        

        echo json_encode($response);
    }
}
