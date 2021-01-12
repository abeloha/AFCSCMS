<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Syndicate;
use App\Models\SyndicateEnrollment;

class SyndicateController extends Controller
{
    public function syndicates(Request $request, $param_div_id=0)
    {

        $div_id = 0;
        $div_name = '';
        $session = '';
        $course_name = '';

        if($param_div_id){
            $div = get_div($param_div_id);
            if($div){
                $div_id = $div->id;
                $div_name = $div->name;
                $session = $div->session_id;
                $course_name = $div->course;
            }
        }
        $divs = get_div();
        return view('syndicate.syndicates',['divs' => $divs,'div_id' => $div_id,'div_name' => $div_name, 'course_name' => $course_name, 'session' => $session]);      
    }

    public function syndicate(Request $request, $id=0)
    {

        if(!$id){
            return redirect("syndicates");     
        }

        $record = get_syndicate($id);
        return view('syndicate.syndicate',['data' => $record]);      
    }

    public function editSyndicate(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'ds' => 'required',
        ]);

        $id = $request->id;

        $syndicate = Syndicate::find($request->id);
        $syndicate->name = $request->name;
        $syndicate->ds_user_id = $request->ds;
        $syndicate->save();

        return redirect("syndicate/$id/?sucess");
    }

    public function addSyndicate(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'div' => 'required',
        ]);

        $div_id = $request->div;

        $syndicate = new Syndicate;
        $syndicate->name = $request->name;
        $syndicate->div_id = $request->div;
        $syndicate->ds_user_id = $request->ds;
        $syndicate->session_id = $request->session;
        $syndicate->save();

        return redirect("syndicates/$div_id/?added");
    }

    public function deleteSyndicate(Request $request, $id)
    {
        $record = Syndicate::find($id);
        $div_id = $record->div_id;
        $record->delete();
        return redirect("syndicates/$div_id/?deleted");      
    }

    public function assignStudent(Request $request, $id=0)
    {

        $syndicate = get_syndicate($id);
        if(!$id){
            return redirect("syndicates");     
        }
        $syndicate_students = '';
        $all_students = '';

        if($syndicate){

            $syndicate_students = get_students_by_syndicate($syndicate->id);

            $is_joint = 0;
            if($syndicate->term_id){
                $term = get_term($syndicate->term_id);
                if($term){
                    $is_joint = $term->is_joint;
                }
            }
            
            if($is_joint){
                $all_students = get_students_by_dept_and_course(0,$syndicate->course_id);
            }else{
                $all_students = get_students_by_dept_and_course($syndicate->dept_id,$syndicate->course_id);
            }

            return view('syndicate.assign-students',['syndicate'=>$syndicate, 'syndicate_students'=>$syndicate_students,'all_students'=>$all_students]);      
        }

        return redirect("syndicates");  
    }

    public function assignStudentAdd(Request $request)
    {
        
        $students = $request->students;
        
        if($students){
            foreach($students as $student){
               $enrollment = SyndicateEnrollment::updateOrCreate(
                    ['user_id' => $student, 'term_id' => $request->term, 'session_id' => $request->session],
                    ['syndicate_id' => $request->syndicate]
                );
            }
        }
        //syndicate/{id}/assign
        return redirect("syndicate/$request->syndicate/assign?added");       
    }

    public function assignStudentRemove(Request $request)
    {
        
        $students = $request->students;
        
        if($students){
            foreach($students as $student){
                $record = SyndicateEnrollment::where('user_id', $student)
                ->where('syndicate_id', $request->syndicate)
                ->first();

                $record->delete();
            }
        }
        //syndicate/{id}/assign
        return redirect("syndicate/$request->syndicate/assign?removed");       
    }

}
