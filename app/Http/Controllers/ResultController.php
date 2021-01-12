<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Dept;
use App\Models\Div;
use App\Models\Exercise;
use App\Models\ExerciseEnrollment;
use App\Models\ReleasedExerciseResult;
use App\Models\ReleasedResult;
use App\Models\ResultAddedTermWp;
use App\Models\Session;
use App\Models\Term;
use App\Models\User;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        return view('result.index');
    }

    public function session(Request $request, $id)
    {
        $msg = '';
        $session = Session::find($id);
        if($session){
            $course = Course::find($session->course_id);
            if($course){
                return view('result.released',['course'=>$course,'session'=>$session]);
            }
            $msg = 'course data not found';
        }else{
            $msg = 'session data not found';
        }

        return redirect("result/?msg=$msg");
    }

    public function processing(Request $request)
    {
        return view('result.processing');
    }

    public function submitTC(Request $request)
    {

        $msg = '';
        $div_id = $request->div_id;
        $t = $request->t;
        $s = $request->s;
        $user_id = get_user_id();
        
        $div = Div::find($div_id);

        if($div){

            if($div->tc_user_id == $user_id){
                if(!get_division_result_released($div->id, $div->course_id, $t, $s)){
                                       
                    $record = new ReleasedResult();
                    $record->div_id = $div->id;
                    $record->course_id = $div->course_id;
                    $record->user_id = $user_id;
                    $record->session_id = $s;
                    $record->term_id = $t;
                    $record->save();

                }
                $msg = 'Result submitted';
            }else{
                return $this->redirectUnautorised('not_div_tc');
            }

        }else{
            $msg = 'Div not found';
        }

        return redirect("result/processing/?msg=$msg");

    }

    public function submitCI(Request $request)
    {

        $msg = '';
        $released_result_id = $request->released_result_id;
        $t = $request->t;
        $s = $request->s;
        $user_id = get_user_id();

        $comment = $request->comment;
        
        $record = ReleasedResult::find($released_result_id);

        if($record){

            $record->approval = 1;
            $record->approved_by = $user_id;
            $record->ci_comment = $comment;
            $record->save();

        }else{
            $msg = 'Data not found';
        }

        return redirect("result/processing/?msg=$msg");

    }

    public function submitDr(Request $request)
    {

        $msg = '';
        $dept_id = $request->dept_id;
        $t = $request->t;
        $s = $request->s;
        $c = $request->c;
        
        $comment = $request->comment;

        $user_id = get_user_id();
        
        $dept = Dept::find($dept_id);

        if($dept){

            if($dept->director_user_id == $user_id){
                if(!get_department_result_released($dept->id, $c, $t, $s)){
                                       
                    $record = new ReleasedResult();
                    $record->dept_id = $dept->id;
                    $record->course_id = $c;
                    $record->user_id = $user_id;
                    $record->session_id = $s;
                    $record->term_id = $t;
                    $record->director_comment = $comment;
                    $record->save();

                }
                $msg = 'Result submitted';
            }else{
                return $this->redirectUnautorised('not_dept_dr');
            }

        }else{
            $msg = 'Dept not found';
        }

        return redirect("result/processing/?msg=$msg");

    }
    
    public function submitComdt(Request $request)
    {

        $msg = '';
        $released_result_id = $request->released_result_id;
        $t = $request->t;
        $s = $request->s;

        $approver_type = $request->approver_type;

        $user_id = get_user_id();

        $comment = $request->comment;
        
        $record = ReleasedResult::find($released_result_id);

        if($record){

            $record->approval = 1;
            $record->approved_by = $user_id;

            if($approver_type == 'deputy'){
                $record->depty_cmd_comment = $comment;
            }elseif($approver_type == 'comdt'){
                $record->cmd_comment = $comment;
            }
            
            $record->save();

            $msg = 'Result Approved';

        }else{
            $msg = 'Result data not found';
        }

        return redirect("result/processing/?msg=$msg");

    }

    public function exercise(Request $request, $id)
    {
        $exercise = Exercise::find($id);
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }

        $session = '';
        $term = '';

        if($request->s){
            $session = Session::find($request->s);
        }

        if($request->t){
            $term = Term::find($request->t);
        }

        if(!$session){
            return $this->redirectUnautorised('session_not_found');
        }
        if(!$term){
            return $this->redirectUnautorised('term_not_found');
        }
                
        $div = '';
        $div_id = '0';
        if($request->div){
            $div = Div::find($request->div);
            if(!$div){
                return $this->redirectUnautorised('division_not_found');
            }
        }

        $dept = '';
        $dept_id = '0';
        if($request->dept){
            $dept = Dept::find($request->dept);
            if(!$dept){
                return $this->redirectUnautorised('department_not_found');
            }
        }


        $show_identity = can_show_student_identity('exercise', $exercise->id, $term->id, $session->id);

        $sub_details_menu = "Exercise: $exercise->name";
        $sub_details = "<u>Exercise</u>: $exercise->name";

        $organic = 0;
        $organic_title = '';

        if($request->organic){
            $organic = 1;
            $organic_title = '(organic)';
        }

        $allow_moderation = 0;
        $moderation_type = '';
        $can_moderate = 0;
        $user_id = get_user_id();
        
        if($div){

            $div_id = $div->id;
            $dept_id = $div->dept_id;

            //is organic, show students of that department in div by Syndicate? otherwise, div is determined by syndicate
            $students = get_result_exercise_enrolled_students_by_div($exercise->id,$session->id, $div->id, $organic);
            $sub_details_menu .= " Div: $div->name $organic_title";
            $sub_details .= " <u>Div</u>: $div->name $organic_title";
            
            if(is_deputy_commandant()){
                $can_moderate = is_exercise_moderating_open($exercise->id, $dept_id);            
                $moderation_type = 'dpty_cmd_wp_grade';
            }elseif($div->ci_user_id == $user_id){
                $can_moderate = is_exercise_moderating_open($exercise->id, $dept_id);   //test for a ci div in released results            
                $moderation_type = 'ci_wp_grade';
            } 

        }elseif($dept){
            //is organic department i want to view? otherwise, department is determined by syndicate
            $students = get_result_exercise_enrolled_students_by_dept($exercise->id,$session->id, $dept->id, $organic);
            $sub_details_menu .= " Dept: $dept->name $organic_title";
            $sub_details .= " <u>Dept</u>: $dept->name $organic_title";

            if(is_deputy_commandant()){                             
                $can_moderate = is_exercise_moderating_open($exercise->id);
                $moderation_type = 'dpty_cmd_wp_grade';
            }
            
            $dept_id = $dept->id;

        }else{
            $students = get_result_exercise_enrolled_students($exercise->id,$session->id);
        }
        
        $sub_details_menu .= " Term: $term->name Session:$session->name";
        $sub_details .= " <u>Term</u>: $term->name <u>Session</u>: $session->name";

        if($can_moderate){
            if($request->moderate){
                $allow_moderation = 1;
            }
        }

        return view('result.exercise',['students'=>$students, 'exercise'=>$exercise, 'session'=>$session, 'term'=>$term,
         'show_identity'=>$show_identity, 'sub_details'=>$sub_details,'sub_details_menu'=>$sub_details_menu,
         'can_moderate'=>$can_moderate, 'allow_moderation'=>$allow_moderation, 'moderation_type'=>$moderation_type, 
         'div_id'=>$div_id, 'dept_id'=>$dept_id]);

    }

    public function moderateExercise(Request $request)
    {

        $exercise_id = $request->exercise_id;

        if(!$exercise_id){            
            return $this->redirectUnautorised('parm_ex_id_not_found');
        }

        if(!is_exercise_moderating_open($exercise_id)){            
            return $this->redirectUnautorised('modration_not_open');
        }

        $s = $request->s;
        $t = $request->t;
        $div = $request->div;
        $dept = $request->dept;

        $grade_type = $request->grade_type;

        $students = $request->student;

        $grader_name = str_replace(';','_', $request->grader_name); //cannot have ; in content
        $grader_id = $request->grader_id; 
        
        if($students){

            $student_ids =  $students['id'];
            $grade =  $students['grade'];
            $enrollment_id =  $students['enrollment']; 

            $log = '';
            $msg = 'success';

            $n = count($enrollment_id);             

            if($n){
                for ($i=0; $i < $n; $i++) {                     
                    
                    if($grade[$i]){
                        $enroll = ExerciseEnrollment::find($enrollment_id[$i]);
                        if($enroll){

                            $old_log = $enroll->log;
                            $date = date('d/m/Y h:i a');
                            $log = "$grade[$i] wp awarded by $grader_name on $date;";
                           
                            if($grade_type == 'dpty_cmd_wp_grade'){
                                $enroll->dpty_cmd_wp_grade = $enroll->dpty_cmd_wp_grade + $grade[$i];
                            }elseif($grade_type =='ci_wp_grade'){
                                $enroll->ci_wp_grade = $enroll->ci_wp_grade + $grade[$i];
                            }
                            
                            $enroll->total_wp = $enroll->wp_grade + $enroll->ci_wp_grade + $enroll->dpty_cmd_wp_grade;

                            $enroll->log = $old_log.$log;
                            $enroll->save();

                        }
                    }                    

                }
            }

        }

        return redirect('result/exercise/'.$exercise_id.'/?s='.$s.'&t='.$t.'&dept='.$dept.'&div='.$div.'&msg=Moderation value saved');

    }
    
    public function studentExercise(Request $request, $user_id, $exercise_id)
    {
        $exercise = Exercise::find($exercise_id);
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }

        $student = User::find($user_id);

        if(!$student){
            return $this->redirectUnautorised('student_not_found');
        }

        $session = '';
        $term = '';

        if($request->s){
            $session = Session::find($request->s);
        }

        if($request->t){
            $term = Term::find($request->t);
        }

        if(!$session){
            return $this->redirectUnautorised('session_not_found');
        }
        if(!$term){
            return $this->redirectUnautorised('term_not_found');
        }
        
        $show_identity = can_show_student_identity('exercise', $exercise->id, $term->id, $session->id);
        
        if($show_identity){
            $identity = $student->surname.' '.$student->first_name;
        }else{
            $identity = user_id_to_code($student->id);
        }

        $sub_details_menu = "Student: $identity Exercise: $exercise->name Term: $term->name Session:$session->name";
        $sub_details = "<u>Student</u>: $identity <u>Exercise</u>: $exercise->name <u>Term</u>: $term->name <u>Session</u>: $session->name";
                
        $result = ExerciseEnrollment::where('user_id', $user_id)
                ->where('exercise_id', $exercise_id)
                ->where('session_id', $session->id)
                ->first();

        return view('result.student-exercise',['result'=>$result, 'identity'=>$identity, 'student'=>$student, 'exercise'=>$exercise, 'session'=>$session, 'term'=>$term, 'show_identity'=>$show_identity, 'sub_details'=>$sub_details,'sub_details_menu'=>$sub_details_menu]);
    }

    public function submitGradeBook(Request $request, $id)
    {

        $exercise = Exercise::find($id);
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }

        if(!check_can_manage_exercise_materials($exercise->id)){
            return $this->redirectUnautorised('cannot_manage_exercise_materials');
        }

        $session = '';
        $term = '';

        if($request->s){
            $session = Session::find($request->s);
        }
        
        if($request->t){
            $term = Term::find($request->t);
        }

        if(!$session){
            return $this->redirectUnautorised('session_not_found');
        }
        if(!$term){
            return $this->redirectUnautorised('term_not_found');
        }

        if(!is_exercise_result_released($exercise->id, $term->id, $session->id))
        {
            $record = new ReleasedExerciseResult();
            $record->session_id = $session->id;
            $record->term_id = $term->id;
            $record->exercise_id = $exercise->id;
            $record->user_id = get_user_id();
            $record->save();

        }
        
        return redirect('result/exercise/'.$exercise->id.'/?s='.$session->id.'&t='.$term->id);
    }

    public function show(Request $request)
    {
        if($request->t == '0'){
            return $this->allTermsResult($request);
        }

        return $this->termResult($request); 
    }

    public function termResult(Request $request)
    { 
        $students = '';

        $div_id = 0;
        $dept_id = 0;
            
        $course = '';
        $session = '';
        $term = '';

        if($request->c){
            $course = Course::find($request->c);
        }

        if($request->s){
            $session = Session::find($request->s);
        }

        if($request->t){
            $term = Term::find($request->t);
        }

        if(!$course){
            return $this->redirectUnautorised('course_not_found');
        }
        if(!$session){
            return $this->redirectUnautorised('session_not_found');
        }

        if(!$term){
            return $this->redirectUnautorised('term_not_found');
        }
        
        $sub_details_menu = '';
        $sub_details = '';

        $organic = 0;
        $organic_title = '';

        if($request->organic){
            $organic = 1;
            $organic_title = '(organic)';
        }
        
        $can_moderate = 0;

        $div = '';
        if($request->div){
            $div = Div::find($request->div);
            if($div){
                $div_id = $div->id;
                $show_identity = can_show_student_identity('div', $div->id, $term->id, $session->id, $course->id);                
                $exercises = get_released_result_exercises_by_dept($div->dept_id,$course->id,$term->id, $session->id);               
                $sub_details_menu .= " Division: $div->name $organic_title";
                $sub_details .= " <u>Division</u> $div->name $organic_title";
                $students = get_course_students_by_div($div->id, $session->id, $course->id, $organic, $term->id);
            }
        }else{

            $dept = '';

            if($request->dept){
                $dept = Dept::find($request->dept);
            }else{
                if($term->is_joint){ //select joint department for joint term
                    $dept = Dept::where('is_joint',1)->first();
                }                
            }                
                
            if($dept){

                $dept_id = $dept->id;
                $show_identity = can_show_student_identity('dept', $dept->id, $term->id, $session->id, $course->id);                
                $exercises = get_released_result_exercises_by_dept($dept->id,$course->id,$term->id, $session->id);                
                $sub_details_menu .= " Dept: $dept->name $organic_title";
                $sub_details .= " <u>Dept</u>: $dept->name $organic_title";

                $students = get_course_students_by_dept($dept->id, $course->id, $session->id, $organic);

                //$students = sort_student_result_by_term_total($students, $session->id, $term->id);

                if(is_deputy_commandant()){                             
                    $can_moderate = is_dept_moderating_open($dept->id,$course->id);
                }

            }else{
                return $this->redirectUnautorised('dept_not_found');
            }
            
            
        }

        $sub_details_menu .= " Term: $term->name Session:$session->name";
        $sub_details .= " <u>Term</u>: $term->name <u>Session</u>: $session->name";

        $students = sort_student_result_by_term_total($students, $session->id, $term->id);       

        if($can_moderate){
            if($request->moderate){
                return view('result.term-list-moderate',['students'=>$students, 'exercises'=>$exercises, 'session'=>$session, 'term'=>$term,
                'course'=>$course, 'can_moderate'=>$can_moderate,
                'show_identity'=>$show_identity, 'sub_details'=>$sub_details,'sub_details_menu'=>$sub_details_menu,
                'div_id'=>$div_id, 'dept_id'=>$dept_id]);
            }
        }

        return view('result.term-list',['students'=>$students, 'exercises'=>$exercises, 'session'=>$session, 'term'=>$term,
            'course'=>$course, 'can_moderate'=>$can_moderate,
            'show_identity'=>$show_identity, 'sub_details'=>$sub_details,'sub_details_menu'=>$sub_details_menu,
            'div_id'=>$div_id, 'dept_id'=>$dept_id, 'organic'=>$organic]);
    }

    private function allTermsResult(Request $request)
    { 
        
        $students = '';

        $div_id = 0;
        $dept_id = 0;
            
        $course = '';
        $session = '';

        if($request->c){
            $course = Course::find($request->c);
        }

        if($request->s){
            $session = Session::find($request->s);
        }

        if(!$course){
            return $this->redirectUnautorised('course_not_found');
        }
        if(!$session){
            return $this->redirectUnautorised('session_not_found');
        }
        
        $sub_details_menu = '';
        $sub_details = '';

        $organic = 1;
        $organic_title = '(organic)';
        
        $can_moderate = 0;

        $div = '';
        if($request->div){
            $div = Div::find($request->div);
            if($div){
                $dept_id = $div->dept_id;

                $dept = Dept::find($dept_id); 
                if($dept){
                    $dept_id = $dept->id;
                    $sub_details_menu .= " Dept: $dept->name $organic_title";
                    $sub_details .= " <u>Dept</u>: $dept->name $organic_title";
                    $students = get_course_students_by_dept($dept->id, $course->id, $session->id, $organic);
                }

            }

        }elseif($request->dept){
            $dept = Dept::find($request->dept); 
            if($dept){
                $dept_id = $dept->id;
                $sub_details_menu .= " Dept: $dept->name $organic_title";
                $sub_details .= " <u>Dept</u>: $dept->name $organic_title";
                $students = get_course_students_by_dept($dept->id, $course->id, $session->id, $organic);
            }
        }

        if(!$dept_id){
            return $this->redirectUnautorised('dept/div_not_found');
        }

        $sub_details_menu .= " Session:$session->name";
        $sub_details .= " <u>Session</u>: $session->name";

        $students = sort_student_result_by_total($students, $session->id); 

        return view('result.all-terms',['students'=>$students, 'session'=>$session,
            'course'=>$course, 'sub_details'=>$sub_details,'sub_details_menu'=>$sub_details_menu,
            'div_id'=>$div_id, 'dept_id'=>$dept_id, 'organic'=>$organic]);

    }

    public function resultOverview(Request $request)
    { 
        $session = Session::find($request->s);        
        if(!$session){
            return $this->redirectUnautorised('session_not_found');
        }
        
        $course = Course::find($session->course_id);
        if(!$course){
            return $this->redirectUnautorised('course_not_found');
        }
        
        
        $students = get_course_students_all($course->id, $session->id);

        $students = sort_student_result_by_total($students, $session->id); 

        return view('result.overview',['students'=>$students, 'session'=>$session, 'course'=>$course]);

    }

    
    public function moderateResult(Request $request)
    {
        $can_moderate = 0;

        $s = $request->s;
        $t = $request->t;
        $c = $request->c;
        $div = $request->div;
        $dept = $request->dept;

        if(is_deputy_commandant()){                             
            $can_moderate = is_dept_moderating_open($dept, $c);
        }
        
        if(!$can_moderate){
            return $this->redirectUnautorised('moderation_closed_or_unauthorised');
        }

        $students = $request->student;
        $grader_id = $request->grader_id; 
        
        if($students){

            $student_ids =  $students['id'];
            $grade =  $students['grade'];

            $n = count($student_ids);         

            if($n){
                for ($i=0; $i < $n; $i++) {                     
                    
                    if($grade[$i]){

                        $addedWp = ResultAddedTermWp::where('user_id',$student_ids[$i])
                            ->where('session_id',$s)
                            ->where('term_id',$t)
                            ->where('added_by_user_id',$grader_id)
                            ->first();

                        if($addedWp){
                            $addedWp->wp += $grade[$i];
                            $addedWp->save();
                        }else{
                            $newaddedWp = new ResultAddedTermWp();
                            $newaddedWp->user_id = $student_ids[$i];
                            $newaddedWp->wp = $grade[$i];
                            $newaddedWp->session_id = $s;
                            $newaddedWp->term_id = $t;
                            $newaddedWp->added_by_user_id = $grader_id;
                            $newaddedWp->save();
                        }
                        
                    }                    

                }
            }

        }

        return redirect('result/show?s='.$s.'&t='.$t.'&dept='.$dept.'&div='.$div.'&c='.$c.'&msg=Moderation value saved');

    }

    private function redirectUnautorised($err_code = '')
    {
        return redirect("/?unauthorised=$err_code");
    }

}
