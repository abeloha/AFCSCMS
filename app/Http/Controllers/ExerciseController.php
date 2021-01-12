<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Exercise;
use App\Models\ExerciseEnrollment;
use App\Models\ExerciseMaterial;
use App\Models\Course;
use App\Models\Div;
use App\Models\ExerciseRequirement;
use App\Models\GradingAssignment;
use App\Models\RequirementGrade;
use App\Models\RequirementSubmission;
use Illuminate\Support\Facades\DB;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        if(is_student()){
            return $this->student();
        }

        return redirect("exercise/list");
    }

    private function student()
    {
        $data = get_exercise_enrolled();
        return view('exercise.exercises',['collection'=>$data]);
    }

    public function enroll(Request $request)
    {    

        $msg = '';

        if(is_student()){

            if(!is_my_exercise_reg_open()){
                $msg = 'Exercise enrollment has been closed';
                return redirect("exercise/?msg=$msg");
            }

            $user = get_student_data();
            $exercise_enrolled = get_exercise_enrolled();
            if($user){

                $course = Course::find($user->course_id);
                
                if(!$course){
                    $msg = 'Course details could not be loaded.';
                    return redirect("exercise/?msg=$msg");
                }

                $session = $course->current_session_id;
                $term = $course->current_term_id;

                if(!$term){
                    $msg = 'You cannot enroll for exercise in this term';
                    return redirect("exercise/?msg=$msg");
                }
                
                $available_exercise = get_exercise_available_for_enrollment();
                return view('exercise.enroll',['user'=>$user,'exercise_enrolled'=>$exercise_enrolled,'available_exercise'=>$available_exercise, 'term'=>$term, 'session'=>$session]);
                
            }

            $msg = 'User data cannot be found at this moment';
            return redirect("exercise/?msg=$msg");        
        }

        $msg = 'Only students can enroll for a course';
        return redirect("exercise/?msg=$msg");
    }

    public function enrollAdd(Request $request)
    {
        $user_id = get_user_id();
        $exercises = $request->exercises;
        
        if($exercises){
            foreach($exercises as $exercise){
               $enrollment = ExerciseEnrollment::updateOrCreate(
                    ['user_id' => $user_id, 'exercise_id' => $exercise, 'session_id' => $request->session],
                    ['term_id' => $request->term]
                );

            }
        }

        return redirect("exercise/enroll?added");       
    }

    public function enrollRemove(Request $request)
    {
        $user_id = get_user_id();

        if($request->user_id != $user_id){
            $msg = 'You do not have authorisation to remove this enrollment';
            return redirect("exercise/?msg=$msg");
        }

        $enrollments = $request->enrollments;
        
        if($enrollments){
            foreach($enrollments as $enrollment){
               $record = ExerciseEnrollment::find($enrollment);               
               $record->delete();
            }
        }

        return redirect("exercise/enroll?removed");
    }

    public function exerciseDetails(Request $request, $id)
    {
        $record = DB::table('exercises')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->where('exercises.id', $id)            
            ->whereNull('exercises.deleted_at')
            ->orderBy('exercises.name', 'asc')
            ->first();

            return view('exercise.exercise-details',['item'=>$record]);

    }

    public function materialDetails(Request $request, $id)
    {
        
        $dir = $request->dir;

        $material = get_exercise_material($id, $dir);

        if(!$material){

            $ex = $request->ex;
            if($ex){
                return redirect("exercise/$ex");
            }

            return redirect('/?unauthorised');
        }

        $exercise_id = $material->exercise_id;

        $record = DB::table('exercises')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->where('exercises.id', $exercise_id)            
            ->whereNull('exercises.deleted_at')
            ->first();

            return view('exercise.material-details',['item'=>$record,'material'=>$material]);

    }

    public function list(Request $request)
    { 
        
        $dept=$request->dept; $course=$request->course; $term=$request->term;

        
        if(!$dept){
            $user_id = get_user_id();
            $user_data = get_user($user_id);
            if($user_data){
                $dept = $user_data->dept_id;
            }
        }
        
        if($dept == 'all'){
            $dept = 0;
        }
        if($course == 'all'){
            $course = 0;
        }

        return $this->view_all_exercise($dept,$course,$term);
    }

    private function view_all_exercise($dept=0, $course=0,$term=0)
    {
        /*
        $collection = DB::table('exercises')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->whereNull('exercises.deleted_at')
            ->orderBy('exercises.name', 'asc')
            ->get();
        */

        $sql = "select `exercises`.*, `depts`.`name` as `dept`, `courses`.`name` as `course`, `terms`.`name` as `term` from `exercises` inner join `depts` on `depts`.`id` = `exercises`.`dept_id` inner join `courses` on `courses`.`id` = `exercises`.`course_id` inner join `terms` on `terms`.`id` = `exercises`.`term_id` where ";

        if($dept){
            $sql .= "`exercises`.`dept_id` = $dept AND ";
        }

        if($course){
            $sql .= "`exercises`.`course_id` = $course AND ";
        }

        if($term){
            $sql .= "`exercises`.`term_id` = $term AND ";
        }

        $sql .= "`exercises`.`deleted_at` is null order by `exercises`.`name` asc";

        $collection = DB::select($sql);

        return view('exercise.list-all',['collection'=>$collection,'dept_search'=>$dept,'course_search'=>$course,'term_search'=>$term]);
    }

    public function add(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'weighted_point' => 'required',
            'description' => 'required',
            'course' => 'required',
            'dept' => 'required',            
            'term' => 'required',            
        ]);

        $record = new Exercise();
        $record->name = $request->name;
        $record->weighted_point = $request->weighted_point;
        $record->description = $request->description;
        $record->course_id = $request->course;
        $record->dept_id = $request->dept;
        $record->term_id = $request->term;
        $record->sponsor_user_id = $request->sponsor;
        $record->cosponsor_user_id = $request->cosponsor;
        $record->save();
        return redirect('/exercise/list?added');
    }

    public function edit(Request $request, $id)
    {
        //$record = Exercise::find($id);
        $record = DB::table('exercises')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->where('exercises.id', $id)
            ->whereNull('exercises.deleted_at')
            ->orderBy('exercises.name', 'asc')
            ->first();
        
        return view('exercise.edit',['item'=>$record]);       
    }

    public function editExc(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'weighted_point' => 'required',
            'description' => 'required',
            'course' => 'required',
            'dept' => 'required',            
            'term' => 'required',
            'sponsor' => 'required',            
            'cosponsor' => 'required',
        ]);

        $id = $request->id;
        $record = Exercise::find($id);
        $record->name = $request->name;
        $record->weighted_point = $request->weighted_point;
        $record->description = $request->description;
        $record->course_id = $request->course;
        $record->dept_id = $request->dept;
        $record->term_id = $request->term;
        $record->sponsor_user_id = $request->sponsor;
        $record->cosponsor_user_id = $request->cosponsor;
        $record->save();
        return redirect("exercise/$id/edit?success");
    }

    public function delete(Request $request, $id)
    {
        $record = Exercise::find($id);
        $record->delete();
        return redirect('/exercise/list?deleted');        
    }

    public function addMaterial(Request $request, $id)
    {

        if(!check_can_manage_exercise_materials($id)){
            return redirect('/?unauthorised');
        }
    
       $record = Exercise::find($id);
       return view('exercise.add-materials',['item'=>$record]);

    }

    public function addMaterialExc(Request $request)
    {
        $validatedData = $request->validate([ 
            'title' => 'required',           
            'content' => 'required',
        ]);

        //$request->file_1->getClientOriginalName();

        //hello
        $exercise_id = $request->exercise;

        $record = new ExerciseMaterial();
        $record->title = $request->title;
        $record->exercise_id = $request->exercise;
        $record->content = $request->content;
        
        $destination = 'public/material';
        if ($request->hasFile('file_1')){
            $extension = $request->file_1->extension();
            $file_name = 'Exercise '.$request->exercise.'_1'.time().'_1.'.$extension;
            $path = $request->file_1->storeAs($destination, $file_name);
            $record->file_1 = $file_name;
        }
        if ($request->hasFile('file_2')){
            $extension = $request->file_2->extension();
            $file_name = 'Exercise '.$request->exercise.'_2'.time().'_2.'.$extension;
            $path = $request->file_2->storeAs($destination, $file_name);
            $record->file_2 = $file_name;
        }
        if ($request->hasFile('file_3')){
            $extension = $request->file_3->extension();
            $file_name = 'Exercise '.$request->exercise.'_3'.time().'_3.'.$extension;
            $path = $request->file_3->storeAs($destination, $file_name);
            $record->file_3 = $file_name;
        }

        $record->save();

        return redirect("exercise/$exercise_id/?success");
    }

    public function deleteMaterial(Request $request, $id)
    {
        $record = ExerciseMaterial::find($id);
        $exercise_id = $record->exercise_id;
        $record->delete();
        return redirect("exercise/$exercise_id?deleted");        
    }

    public function addRequirement(Request $request, $id)
    {

        if(!check_can_manage_exercise_materials($id)){
            return redirect('/?unauthorised');
        }
        
        $type = $request->type;
        $record = Exercise::find($id);

        if($type == 1 || $type == 2){
            return view('exercise.add-requirements',['item'=>$record, 'type'=>$type]);
        }

        if($record){
            return redirect("exercise/$record->id"); 
        }

        return redirect('/?unauthorised');
    }

    public function addRequirementExc(Request $request)
    {
        $exercise_id = $request->exercise;

        $ex_record = Exercise::find($exercise_id);
        
        if(!$ex_record){
            return redirect('/?unauthorised');
        }

        $session = get_current_session($ex_record->course_id);
        $term = get_current_term($ex_record->course_id);

        $record = new ExerciseRequirement();
        $record->exercise_id = $request->exercise;
        $record->title = $request->title;
        $record->marks = $request->marks;
        $record->req_type = $request->type;

        $destination = 'public/requirement';

        if($request->type == 1){

            $record->submission_type = $request->submission_type;        
            $record->question = $request->question;

            if ($request->hasFile('question_file')){
                $extension = $request->question_file->extension();
                $file_name = 'req_question_ex_'.$request->exercise.'_'.time().'.'.$extension;
                $path = $request->question_file->storeAs($destination, $file_name);
                $record->question_file = $file_name;
            }

        }

        $record->grading_instruction = $request->grading_instruction;
        
        if ($request->hasFile('grading_file_1')){
            $extension = $request->grading_file_1->extension();
            $file_name = 'grading_inst_ex_'.$request->exercise.'_'.time().'_1.'.$extension;
            $path = $request->grading_file_1->storeAs($destination, $file_name);
            $record->grading_file_1 = $file_name;
        }
        if ($request->hasFile('grading_file_2')){
            $extension = $request->grading_file_2->extension();
            $file_name = 'grading_inst_ex_'.$request->exercise.'_'.time().'_2.'.$extension;
            $path = $request->grading_file_2->storeAs($destination, $file_name);
            $record->grading_file_2 = $file_name;
        }

        if($request->type == 1){
            $start = $request->start_at_date.' '.$request->start_at_time;
            $end = $request->end_at_date.' '.$request->end_at_time;

            if($request->show_at_date && $request->show_at_time){
                $show = $request->show_at_date.' '.$request->show_at_time;
            }else{
                $show = $start;
            }
                
            $record->start_at = date('Y-m-d H:i', strtotime($start));
            $record->end_at = date('Y-m-d H:i', strtotime($end));
            $record->show_at = date('Y-m-d H:i', strtotime($show));
        }

        $record->term_id = $term;
        $record->session_id = $session;
        $record->user_id = get_user_id();

        $record->save();

        return redirect("exercise/$exercise_id/?addedreq");
    }

    public function requirementDetails(Request $request, $id)
    {
        $requirement = ExerciseRequirement::find($id);

        if(!$requirement){
            return redirect('/?unauthorised');
        }

        $exercise_id = $requirement->exercise_id;

        $record = DB::table('exercises')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->where('exercises.id', $exercise_id)            
            ->whereNull('exercises.deleted_at')
            ->first();

        return view('exercise.requirement-details',['item'=>$record,'requirement'=>$requirement]);
        
    }

    public function requirementSubmissionExc(Request $request)
    {
        $user_id = get_user_id();
        
        $record = new RequirementSubmission();
        $record->user_id = $user_id;
        $record->requirement_id = $request->requirement;

        $record->submitted_text = $request->submission_text;

        $destination = 'public/submission';
        if ($request->hasFile('submission_file')){
            $extension = $request->submission_file->extension();
            $file_name = 'submission_.'.$user_id.'_req_'.$request->requirement.'_'.time().'.'.$extension;
            $path = $request->submission_file->storeAs($destination, $file_name);
            $record->submitted_file = $file_name;
        }

        $record->save();

        return redirect("exercise/requirement/$request->requirement/?success");
    }

    public function requirementGrade(Request $request, $id)
    {
        $requirement = ExerciseRequirement::find($id);

        if($requirement){

            if($requirement->req_type == 1){
                return redirect("exercise/$requirement->exercise_id/grade"); //for written
            }
            
            if($requirement->req_type == 2){
                return redirect("exercise/requirement/$id/grade/utw");
            }

            return redirect('/?unauthorised=grade_type_not_determined');
        }
        
        return redirect('/?unauthorised=requirement_not_found');
        
    }

    public function requirementUtw(Request $request, $id)
    {
        $requirement = ExerciseRequirement::find($id);

        if(!$requirement){
            return redirect('/?unauthorised=req_not_found');
        }

        $exercise = Exercise::find($requirement->exercise_id);

        if(!$exercise){
            return redirect('/?unauthorised=ex_not_found');
        }


        $students = get_exercise_enrolled_students($exercise->id);
        $header_title = "Students Grade - Requirement: $requirement->title - Exercise: $exercise->name";

        $action = $request->action;

        if($action == 'grade'){
            if(!check_can_manage_exercise_materials($exercise->id)){
                return redirect('/?unauthorised=user_not_qualified_to_grade');
            }
            return view('grade.requirement-students-grade',['collection'=>$students, 'requirement'=>$requirement, 'exercise'=>$exercise, 'header_title'=>$header_title]);
        }
        
        if(!check_can_view_utw_requirement_grades($exercise->id)){
            return redirect('/?unauthorised=user_not_qualified_to_view_grade');
        }
        return view('grade.requirement-students-list',['collection'=>$students, 'requirement'=>$requirement, 'exercise'=>$exercise, 'header_title'=>$header_title]);

    }

    public function requirementUtwGrade(Request $request)
    {
        
        $students = $request->student;
        $requirement_id = $request->requirement_id; 
        $requirement_name = str_replace(';','_', $request->requirement_name); //because logs are seperated by ;        
        $grader_name = str_replace(';','_', $request->grader_name); //cannot have ; in content
        $grader_id = $request->grader_id; 

        if($students){

            $exercise = Exercise::find($request->exercise_id);
            $exercise_wp = $exercise->weighted_point;
            
            if(!$exercise_wp){
                return $this->redirectUnautorised('exercise_wp_for_computation_not_found');
            }

            $id =  $students['id'];
            $grade =  $students['grade'];
            $enrollment_id =  $students['enrollment'];

            $n = count($id);             

            if($n){
                for ($i=0; $i < $n; $i++) { 
                    $reqGrade = new RequirementGrade();
                    $reqGrade->user_id = $id[$i];
                    $reqGrade->requirement_id = $requirement_id;
                    $reqGrade->grade = $grade[$i];
                    $reqGrade->grader_id = $grader_id;
                    $chk = $reqGrade->save();

                    if($chk){
                        $enroll = ExerciseEnrollment::find($enrollment_id[$i]);
                        if($enroll){
                            $old_score = $enroll->oral_grade;
                            $old_log = $enroll->log;
                            $date = date('d/m/Y h:i a');
                            $log = "$grade[$i] marks awarded by $grader_name for requirement $requirement_name on $date;";
                            $enroll->oral_grade = $old_score + $grade[$i];

                            $enroll->total_grade = $enroll->oral_grade + $enroll->written_grade;
                            $wp = $enroll->total_grade * $exercise_wp * 0.01;
                            $enroll->wp_grade = $wp;
                            $enroll->total_wp = $enroll->wp_grade + $enroll->ci_wp_grade + $enroll->dpty_cmd_wp_grade;

                            $enroll->log = $old_log.$log;
                            $enroll->save();
                        }                        
                    }

                }
            }

        }
        
        return redirect("exercise/requirement/$requirement_id/grade/utw");

    }

    public function graderSummary(Request $request, $exercise_id)
    {
        $header_title = 'Students Assigned for Grading';

        $user_id = $request->grader;
        $i_am_grader = 0;

        $my_user_id = get_user_id();

        if(!$user_id){
            $user_id = $my_user_id;
        }
        
        $exercise = Exercise::find($exercise_id);
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }
        
        $user_data = get_user($user_id);
        if(!$user_data){
            return $this->redirectUnautorised('grader_not_found');
        }

        if($my_user_id == $user_data->id){
            $i_am_grader = 1;
        }

        $grader_name = $user_data->rank.' '.$user_data->surname.' '.$user_data->first_name;

        $header_title .= " - Exercise: $exercise->name - DS: $grader_name";

        $assigned_students = get_students_assigned_to_grader($exercise->id, $user_data->id);  

        return view('grade.grader-summary',['assigned_students'=>$assigned_students,'grader_name'=>$grader_name,'exercise'=>$exercise,'i_am_grader'=>$i_am_grader, 'header_title'=>$header_title]);

    }

    public function studentSubmissions(Request $request, $exercise_id)
    {
        $header_title = 'Student Submissions';

        $exercise = Exercise::find($exercise_id);
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }

        $student_data = '';
        $student_id = 0;
        $student_code = $request->student;
        if($student_code){
            $student_id = code_to_user_id($student_code);
            if($student_id){$student_data = get_user($student_id);}            
        }
         
        if(!$student_data){
            return $this->redirectUnautorised('student_not_found');
        }

        $grader_name = '';
        $i_am_grader = 0;
        $grader_id = 0;
        $grader_data = get_grader_assigned_to_student($exercise_id, $student_data->id);
        if($grader_data){

            $grader_name = $grader_data->rank.' '.$grader_data->surname.' '.$grader_data->first_name;
            if($grader_data->id == get_user_id()){
                $i_am_grader = 1;
            }

        }

        $header_title .= " - Exercise: $exercise->name - Student: $student_code  - DS: $grader_name";

        $enrollment_id = check_if_exercise_is_enrolled($exercise->id, $student_data->id);
        
        if(!$enrollment_id){
            return $this->redirectUnautorised('student_not_enrolled_for_exercise');
        }

        //$enrollment = ExerciseEnrollment::find($enrollment_id);

        $grading_open = is_grading_open($exercise->id);

        $submissions = get_student_exercise_submission($exercise->id, $student_data->id);        
        
        return view('grade.student-submissions',['submissions'=>$submissions, 'student_data'=>$student_data,'grader_id'=>$grader_id, 'grader_name'=>$grader_name,'exercise'=>$exercise, 'enrollment_id'=>$enrollment_id, 'grading_open'=>$grading_open, 'i_am_grader'=>$i_am_grader, 'header_title'=>$header_title]);

    }

    public function submissionGradeExc(Request $request)
    { 
        $student_code = $request->student_code;

        $students = $request->student;

        $grader_name = str_replace(';','_', $request->grader_name); //cannot have ; in content
        $grader_id = $request->grader_id; 
        $enrollment_id = $request->enrollment_id;

        $exercise = Exercise::find($request->exercise_id);
        $exercise_wp = $exercise->weighted_point;
        
        if(!$exercise_wp){
            return $this->redirectUnautorised('exercise_wp_for_computation_not_found');
        }
        
        if($students){

            $grade =  $students['grade'];
            $submission =  $students['submission'];
            $requirement_name =  $students['requirement_name'];

            $log = '';
            $msg = 'success';

            $new_grade = 0;

            $n = count($submission);             

            if($n){
                for ($i=0; $i < $n; $i++) { 
                    $subGrade = RequirementSubmission::find($submission[$i]);
                    if($subGrade){
                        $subGrade->grade = $grade[$i]; 
                        $subGrade->grader_id = $grader_id;
                        $subGrade->graded_at = date('Y-m-d H:i');
                        
                        $new_grade += $grade[$i];

                        $requirement_log_name = str_replace(';','_', $requirement_name[$i]); //because logs are seperated by ;   
                        $date = date('d/m/Y h:i a');
                        $log .= "$grade[$i] marks awarded by $grader_name for submission on requirement $requirement_log_name on $date;";
                            
                        $subGrade->save();
                    }
                }
            }

            
            $enroll = ExerciseEnrollment::find($enrollment_id);
            $destination = 'public/love_letter';
            
            if ($request->hasFile('love_letter')){
                $extension = $request->love_letter->extension();
                $file_name = $student_code.'_Love_Letter_Exercise_'.$request->exercise_id.'_'.time().'.'.$extension;
                $path = $request->love_letter->storeAs($destination, $file_name);
                $enroll->love_letter = $file_name;
            }
            
            $old_log = $enroll->log;
            $old_grade = $enroll->written_grade;
            $enroll->written_grade = $old_grade + $new_grade;
            $enroll->total_grade = $enroll->oral_grade + $enroll->written_grade;
            $wp = $enroll->total_grade * $exercise_wp * 0.01;
            $enroll->wp_grade = $wp;
            $enroll->total_wp = $enroll->wp_grade + $enroll->ci_wp_grade + $enroll->dpty_cmd_wp_grade;
            $enroll->log = $old_log.$log;
            $enroll->save();

        }

        return redirect("exercise/$request->exercise_id/submissions?student=$request->student_code&$msg");

    }

    public function studentRequirementSubmission(Request $request, $submission_id)
    {
        $submission = RequirementSubmission::find($submission_id);
        
        if(!$submission){
            return $this->redirectUnautorised('requirement_submission_not_found');
        }

        $requirement = ExerciseRequirement::find($submission->requirement_id);
        if(!$requirement){
            return $this->redirectUnautorised('requirement_not_found');
        }

        $exercise_id = $requirement->exercise_id;

        $record = DB::table('exercises')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->where('exercises.id', $exercise_id)            
            ->whereNull('exercises.deleted_at')
            ->first();

        return view('grade.submission-details',['item'=>$record,'requirement'=>$requirement, 'submission'=>$submission]);
        
    }

    public function assignGraderList(Request $request, $id)
    {
        $header_title = 'Assigned Graders List';

        $div_id = $request->div;
        if(!$div_id){
            return $this->redirectUnautorised('missing_required_paramenter_div');
        }

        $div = Div::find($div_id);
        if(!$div){
            return $this->redirectUnautorised('div_not_found');
        }

        if($div->tc_user_id != get_user_id()){
            return $this->redirectUnautorised('not_div_tc');
        }

        $exercise = Exercise::find($id);        
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }

        $arr_depts = get_dept_joint_ids();

        if(in_array($exercise->dept_id,$arr_depts) || $exercise->dept_id == $div->dept_id){
            if($exercise->course_id == $div->course_id){
                $enrolled_students = get_exercise_enrolled_students_in_div($exercise->id, $div->id);
                $header_title .= " - Exercise: $exercise->name - Div: $div->name";
                return view('grade.grader-assigned-list',['enrolled_students'=>$enrolled_students, 'exercise'=>$exercise, 'div'=>$div, 'header_title'=>$header_title]);

            }
            return $this->redirectUnautorised('exercise_not_in_tc_course'); 
        }
        
        return $this->redirectUnautorised('exercise_not_in_tc_dept');
    }

    public function assignGrader(Request $request, $id)
    {
        $header_title = 'Assign Graders';

        $div_id = $request->div;
        if(!$div_id){
            return $this->redirectUnautorised('missing_required_paramenter_div');
        }

        $div = Div::find($div_id);
        if(!$div){
            return $this->redirectUnautorised('div_not_found');
        }

        if($div->tc_user_id != get_user_id()){
            return $this->redirectUnautorised('not_div_tc');
        }

        $exercise = Exercise::find($id);        
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }

        $arr_depts = get_dept_joint_ids();

        if(in_array($exercise->dept_id,$arr_depts) || $exercise->dept_id == $div->dept_id){
            if($exercise->course_id == $div->course_id){
                $enrolled_students = get_exercise_enrolled_students_in_div($exercise->id, $div->id);

                $div_ds = get_ds_by_div($div->id);

                $header_title .= " - Exercise: $exercise->name - Div: $div->name";
                return view('grade.grader-assign',['enrolled_students'=>$enrolled_students,'div_ds'=>$div_ds,'exercise'=>$exercise, 'div'=>$div, 'header_title'=>$header_title]);

            }
            return $this->redirectUnautorised('exercise_not_in_tc_course'); 
        }
        
        return $this->redirectUnautorised('exercise_not_in_tc_dept');
    }

    public function assignGraderExc (Request $request)
    {
        $student_ids = $request->student_ids;
        $exercise_id = $request->exercise_id;

        $exercise = Exercise::find($exercise_id);        
        if(!$exercise){
            return $this->redirectUnautorised('exercise_not_found');
        }

        $data = Course::find($exercise->course_id);    
        $session_id = $data->current_session_id; //keeping the sessions sepreate for each exexercise assignment
        $term_id = $data->current_term_id; //for each term and sessions

        if(!$session_id){
            return $this->redirectUnautorised('session_not_set');
        }

        if(!$term_id){
            return $this->redirectUnautorised('term_not_set');
        }

        $arry_students = array();
        if($student_ids){
            $arry_students = explode(',', $student_ids);
            shuffle($arry_students);
        }

        $ds = $request->ds;
        $ds_id =  $ds['id'];
        $ds_no_of_students =  $ds['no_of_students'];        
        $n = count($ds_id);

        for ($i=0; $i < $n; $i++) {

            $no_assigned = $ds_no_of_students[$i];
            $ds_id_for_assigning = $ds_id[$i];

            if($no_assigned){
                for ($j=0; $j < $no_assigned; $j++) {
                    $student_to_assign = array_pop($arry_students);
                    if(!$student_to_assign){ continue; }
                    $assignment = GradingAssignment::updateOrCreate(
                        ['user_id' => $student_to_assign, 'exercise_id' => $exercise->id],
                        ['session_id' => $session_id, 'term_id' => $term_id, 'assigned_user_id' => $ds_id_for_assigning]
                    ); 
                    $assignment->session_id=$session_id;
                    $assignment->term_id=$term_id;
                    $assignment->save();
                }                
            }

        }
        return redirect("exercise/$exercise->id/assigngrader?div=$request->div&success");
        
    }

    
    private function redirectUnautorised($err_code = '')
    {
        return redirect("/?unauthorised=$err_code");
    }

    public function loveletters(Request $request)
    {
        return view('exercise.loveletters');
    }
}
