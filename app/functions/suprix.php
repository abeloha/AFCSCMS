<?php

/*
{!!$material->content!!} 
*/
use App\Models\AppConfig;
use App\Models\Course;
use App\Models\Dept;
use App\Models\Div;
use App\Models\Exercise;
use App\Models\ExerciseMaterial;
use App\Models\ExerciseEnrollment;
use App\Models\ExerciseRequirement;
use App\Models\GradingAssignment;
use App\Models\MessageFile;
use App\Models\MessageRecipient;
use App\Models\ReleasedExerciseResult;
use App\Models\ReleasedResult;
use App\Models\RequirementGrade;
use App\Models\RequirementSubmission;
use App\Models\ResultAddedTermWp;
use App\Models\ResultApproval;
use App\Models\Session;
use App\Models\Syndicate;
use App\Models\SyndicateEnrollment;
use App\Models\Term;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

define("APPNAME", "AFCSCMS"); //depreciated
define("APP_FULL_NAME","AFCSC Management System"); //deppreciated
define("DESCRIPTION", "Armed Forces and Command Staff College Management System");
define("PHONE","+2347034918517");
define("LINK_FB","https://www.facebook.com/SuprixTech");
define("LINK_TW","");
define("LINK_INST","");
define("LINK_WH","");

function get_app_name(){
    return get_config('app_name', 'AFCSCMS');
}

function get_app_full_name(){
    return get_config('app_full_name', 'Armed Forces and Command Staff College Management System');
}

function get_config($key, $default = ''){

    $config = AppConfig::where('key',$key)
        ->first('value'); 
    
    if($config){
        return $config->value;
    }
    return $default;

}

function parseDateTime($string, $timeZone=null) 
{
    $date = new DateTime(
        $string,
        $timeZone ? $timeZone : new DateTimeZone('UTC')
        // Used only when the string is ambiguous.
        // Ignored if string has a timeZone offset in it.
    );
    if ($timeZone) {
        // If our timeZone was ignored above, force it.
        $date->setTimezone($timeZone);
    }
    return $date;
}
  
function stripTime($datetime) 
{
    return new DateTime($datetime->format('Y-m-d'));
}

function get_events($start = '', $end='')
{

    if($start){
        $eventStart = date('Y-m-d',strtotime($start));
    }else{
        $eventStart = date('Y-m-d');
    }
    

    if($end){
        $eventEnd = date('Y-m-d',strtotime($end));
    }else{
        $eventEnd = $eventStart; // consider this a zero-duration event
    }

    if(!$start || $end){
        return Event::orderBy('id', 'desc')
            ->get(); 
    }
    return Event::whereDate('start','<',$eventEnd)
        ->whereDate('end','>=',$eventStart)
        ->orderBy('id', 'desc')
        ->get(); 

}

function get_user($id = 0)
{
    if($id){
        return User::find($id);
    }
    return User::all();
}

function user_id_to_code($id)
{
    $padded_id = str_pad($id, 6, '0', STR_PAD_LEFT);
    return my_encode($padded_id);
}

function code_to_user_id($code)
{
    return my_decode($code);
}

function count_new_reg($type=""){    
    if($type == "student"){
        return count(get_student('unapproved'));
    }
    if($type == "staff"){
        return User::where('approved',0)->where('role','>', 1)->count('id');
    }

    return User::where('approved',0)->count('id');
}

function get_user_id()
{
    if(!Auth::guest())
        return Auth::user()->id;

    return 0;
}

function get_user_name($user_id = 0)
{
    if(!$user_id){
        $user_id = get_user_id();
    }
    $data = User::find($user_id);
    if($data){
        return $data->surname.' '.$data->first_name;
    }
    return '';
}

function get_user_role()
{
    if(!Auth::guest())
        return Auth::user()->role;

    return 0;
}

function check_user_approved($user_id = 0)
{
    if(!$user_id){
        $user_id = get_user_id();
    }

    $user = User::find($user_id);
    if ($user) {
        return $user->approved;
    }

    return 0; 
}

function check_user_active($user_id = 0)
{

    if(!$user_id){
        $user_id = get_user_id();
    }

    $user = User::find($user_id);
    if ($user->deactivated){
        return 0;
    }

    return 1; 
}

function is_current_student($user_id = 0)
{
    if(!$user_id){
        $user_id = get_user_id();
    }
    $user = User::find($user_id);
    if ($user->role == 1) {
        if($user->session_id == get_current_session($user->course_id)){
            return 1;
        }
        return 0;        
    }
    return 1;
}

function get_user_role_url_prefix1()
{
    return '';
}

function get_user_route_prefix()
{
    return 'user';
    if (Auth::check()) {
        $role = Auth::user()->role;
        if ($role == 1){
            return 'student';
        }elseif ($role == 2 || $role == 3 || $role == 4 || $role == 5){
            return 'staff';
        }elseif ($role == '13' || $role == '14'){//Commander and Deputy Commander            
            return 'command';
        }elseif ($role == '15'){//admin
            return 'admin';
        }
    }
    return '';
}

function get_current_url()
{
    return request()->path();
}

//Mails
function get_unread_messages ()
{
    $user_id = get_user_id();
    return DB::table('messages')
            ->join('message_recipients', 'messages.id', '=', 'message_recipients.message_id')
            ->join('users', 'users.id', '=', 'messages.sender_id')
            ->select('messages.id', 'messages.sender_id', 'messages.subject','messages.created_at','message_recipients.is_read', 'users.surname', 'users.first_name', 'users.other_name', 'users.email', 'users.picture')            
            ->where('message_recipients.recipient_id', $user_id)
            ->where('message_recipients.is_read',0)
            ->where('message_recipients.recipient_delete',0)
            ->orderBy('messages.id', 'desc')
            ->get();
}

function mark_message_reciepient_as_read($message_reciepient_id)
{
    $record = MessageRecipient::find($message_reciepient_id);
    if($record){
        if(!$record->is_read){
            $record->is_read = 1;
            $record->save();
        }
    }
}

function get_message($id)
{
    return DB::table('messages')
        ->join('users', 'users.id', '=', 'messages.sender_id')
        ->select('messages.*', 'users.surname', 'users.first_name', 'users.other_name', 'users.email' , 'users.picture')            
        ->where('messages.id',$id)
        ->first();
}

function get_message_files($message_id)
{
    return MessageFile::where('message_id',$message_id)
        ->get();
}

function get_message_recipients($message_id)
{
    return DB::table('message_recipients')
        ->join('users', 'users.id', '=', 'message_recipients.recipient_id')
        ->select('message_recipients.id as message_recipient_id', 'message_recipients.recipient_id', 'users.surname', 'users.first_name', 'users.other_name', 'users.email')            
        ->where('message_recipients.message_id', $message_id)
        ->get();
}

function get_file_icon($extension)
{
    if($extension == 'png' || $extension == 'jpg' || $extension == 'gif' || $extension == 'jepg'){
        return 'fa fa-file-photo-o';
    }

    if($extension == 'xlsx' || $extension == 'xls' || $extension == 'csv'){
        return 'fa fa-file-excel-o';
    }

    if($extension == 'doc' || $extension == 'docx'){
        return 'fa fa-file-word-o';
    }

    if($extension == 'ppt'){
        return 'fa fa-file-powerpoint-o';
    }

    if($extension == 'zip'){
        return 'fa fa-file-zip-o';
    }

    if($extension == 'pdf'){
        return 'fa fa-file-pdf-o';
    }

    return 'fa fa-file';
}

function get_staff($type='')
{
    if($type=='unapproved'){        
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->select('users.*', 'depts.name as dept')
            ->whereNull('users.deleted_at')
            ->where('approved', 0)
            ->where('users.role', '>', 1)
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    if($type=='all'){
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->select('users.*', 'depts.name as dept')
            ->whereNull('users.deleted_at')
            ->where('users.role', '>', 1)
            ->where('users.deactivated', 0)
            ->orderBy('users.surname', 'asc')
            ->get();
    }
    
    //deactivated staff
    if($type=='deactivated'){        
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->select('users.*', 'depts.name as dept')
            ->whereNull('users.deleted_at')
            ->where('users.deactivated', 1)
            ->where('users.role', '>', 1)
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    //approved staff
    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->select('users.*', 'depts.name as dept')
        ->whereNull('users.deleted_at')
        ->where('users.approved', 1)
        ->where('users.role', '>', 1)
        ->where('users.deactivated', 0)
        ->orderBy('users.surname', 'asc')
        ->get();


    
   
}

function get_ds_by_div($div_id)
{
    return DB::table('syndicates')
        ->join('users', 'users.id', '=', 'syndicates.ds_user_id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')        
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('users.picture', 'users.id as user_id', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'divs.name as div', 'syndicates.id as syndicate_id', 'syndicates.name as syndicate')
        ->where('divs.id', $div_id)
        ->where('users.deactivated', 0)
        ->whereColumn('syndicates.session_id','courses.current_session_id')
        ->whereNull('users.deleted_at')
        ->orderBy('users.surname', 'asc')
        ->get(); 
}

function get_student($type='')
{
    //results are for current students

    if($type=='unapproved'){
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
            ->whereNull('users.deleted_at')
            ->where('approved', 0)
            ->where('users.role', 1)
            ->whereColumn('users.session_id','courses.current_session_id')        
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    if($type=='deactivated'){
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
            ->whereNull('users.deleted_at')
            ->where('deactivated', 1)
            ->where('users.role', 1)
            ->whereColumn('users.session_id','courses.current_session_id')        
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    if($type=='all'){
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
            ->whereNull('users.deleted_at')
            ->where('users.role', 1)
            ->whereColumn('users.session_id','courses.current_session_id') 
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    //approved student and activated
    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
        ->whereNull('users.deleted_at')
        ->where('users.approved', 1)
        ->where('users.role', 1)
        ->whereColumn('users.session_id','courses.current_session_id') 
        ->orderBy('users.surname', 'asc')
        ->get();
   
}

function get_students_by_session($session, $dept = 0)
{

    if($dept){
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
            ->whereNull('users.deleted_at')
            ->where('users.approved', 1)
            ->where('users.role', 1)
            ->where('users.dept_id', $dept)
            ->where('users.session_id', $session)
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
        ->whereNull('users.deleted_at')
        ->where('users.approved', 1)
        ->where('users.role', 1)
        ->where('users.session_id', $session)
        ->orderBy('users.surname', 'asc')
        ->get();

}

function get_students_by_dept_and_course($dept = 0, $course = 0)
{

    if($dept){

        if($course){
            //$current_session = get_current_session($course);
            return DB::table('users')
                ->join('depts', 'depts.id', '=', 'users.dept_id')
                ->join('courses', 'courses.id', '=', 'users.course_id')
                ->join('sessions', 'sessions.id', '=', 'users.session_id')
                ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
                ->whereNull('users.deleted_at')
                ->where('users.approved', 1)
                ->where('users.role', 1)
                ->where('users.course_id', $course)
                ->where('users.dept_id', $dept)
                ->whereColumn('users.session_id','courses.current_session_id') 
                ->orderBy('users.surname', 'asc')
                ->get();
        }

        //$current_session = get_current_session();
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
            ->whereNull('users.deleted_at')
            ->where('users.approved', 1)
            ->where('users.role', 1)
            ->where('users.dept_id', $dept)
            ->whereColumn('users.session_id','courses.current_session_id') 
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    if($course){
        //$current_session = get_current_session($course);
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
            ->whereNull('users.deleted_at')
            ->where('users.approved', 1)
            ->where('users.role', 1)
            ->where('users.course_id', $course)
            ->whereColumn('users.session_id','courses.current_session_id') 
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    return get_student();
}

function get_course_students_by_dept($dept_id, $course_id, $session_id, $organic = 1) //created during result
{ 
    $dept = Dept::find($dept_id);
    
    if($dept->is_joint && !$organic){
        return get_course_students_all($course_id, $session_id);
    }

    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
        ->whereNull('users.deleted_at')
        ->where('users.approved', 1)
        ->where('users.role', 1)
        ->where('users.course_id', $course_id)
        ->where('users.dept_id', $dept_id)
        ->where('users.session_id', $session_id)
        ->orderBy('users.surname', 'asc')
        ->get();

}

function get_course_students_all($course_id, $session_id) //created during result
{    
    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
        ->whereNull('users.deleted_at')
        ->where('users.approved', 1)
        ->where('users.role', 1)
        ->where('users.course_id', $course_id)
        ->where('users.session_id', $session_id)
        ->orderBy('users.surname', 'asc')
        ->get();
}

function get_course_students_by_div($div_id, $course, $session_id, $organic = 0, $term_id = 0) //created during result
{    

    if(!$term_id){
        $term_id = get_current_term($course);
    }

    if($organic){
        $div = Div::find($div_id);

        if(!$div){return array();}

        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
            ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
            ->join('divs', 'divs.id', '=', 'syndicates.div_id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session', 'syndicate_enrollments.syndicate_id as syndicate_id','divs.name as div','syndicates.name as syndicate')
            ->whereNull('users.deleted_at')
            ->where('users.course_id', $course)
            ->where('divs.id', $div_id)
            ->where('users.dept_id', $div->dept_id)
            ->where('users.session_id', $session_id)
            ->where('syndicate_enrollments.term_id', $term_id)
            ->orderBy('users.surname', 'asc')
            ->get();
    }

    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
        ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session', 'syndicate_enrollments.syndicate_id as syndicate_id','divs.name as div','syndicates.name as syndicate')
        ->whereNull('users.deleted_at')
        ->where('syndicate_enrollments.term_id', $term_id)
        ->where('users.course_id', $course)
        ->where('divs.id', $div_id)
        ->orderBy('users.surname', 'asc')
        ->get();

}

function get_students_by_syndicate($syndicate_id, $term_id = 0)
{

    if($term_id){
        return DB::table('users')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->join('sessions', 'sessions.id', '=', 'users.session_id')
            ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
            ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session', 'syndicate_enrollments.syndicate_id as syndicate_id')
            ->whereNull('users.deleted_at')
            ->where('syndicate_enrollments.syndicate_id', $syndicate_id)
            ->where('syndicate_enrollments.term_id', $term_id)
            ->whereColumn('users.session_id','courses.current_session_id')
            ->orderBy('users.surname', 'asc')
            ->get();
    }
    
    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session', 'syndicate_enrollments.syndicate_id as syndicate_id')
        ->whereNull('users.deleted_at')
        ->where('syndicate_enrollments.syndicate_id', $syndicate_id)
        ->whereColumn('syndicate_enrollments.term_id', 'courses.current_term_id')
        ->whereColumn('users.session_id','courses.current_session_id')
        ->orderBy('users.surname', 'asc')
        ->get();
}

function get_students_by_div($div_id, $organic = 0, $session_id = 0, $term_id = 0)
{
    if($organic){
        return get_organic_div_students($div_id);
    }

    $div = Div::find($div_id);
    
    $course = Course::find($div->course_id);

    if(!$session_id){        
        $session_id = $course->current_session_id;
    }
    if(!$term_id){        
        $term_id = $course->current_term_id;
    }

    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
        ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session', 'syndicate_enrollments.syndicate_id as syndicate_id','divs.name as div','syndicates.name as syndicate')
        ->whereNull('users.deleted_at')
        ->where('syndicate_enrollments.term_id', $term_id)
        ->where('divs.id', $div_id)
        ->where('users.session_id', $session_id)
        ->orderBy('users.surname', 'asc')
        ->get();
}

function get_organic_div_students($div_id, $session_id = 0, $term_id = 0)
{
    $div = Div::find($div_id);

    if(!$div){
        return array();
    }
    
    $course = Course::find($div->course_id);

    if(!$session_id){        
        $session_id = $course->current_session_id;
    }
    if(!$term_id){        
        $term_id = $course->current_term_id;
    }
    
    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
        ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session', 'syndicate_enrollments.syndicate_id as syndicate_id','divs.name as div','syndicates.name as syndicate')
        ->whereNull('users.deleted_at')
        ->where('syndicate_enrollments.term_id', $term_id)
        ->where('divs.id', $div_id)
        ->where('users.dept_id', $div->dept_id)
        ->where('users.session_id', $session_id)
        ->orderBy('users.surname', 'asc')
        ->get();
}

function get_student_data($student_id = 0)
{
    if(!$student_id){
        $student_id = get_user_id();
    }
    return DB::table('users')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('sessions', 'sessions.id', '=', 'users.session_id')
        ->select('users.*', 'depts.name as dept', 'courses.name as course', 'sessions.name as session')
        ->whereNull('users.deleted_at')
        ->where('users.id', $student_id)
        ->first();
}

function get_student_syndicate($student_id, $term_id = 0, $session_id = 0)
{

    if(!$term_id || !$session_id){
        $user = User::find($student_id);
        $course_id = $user->course_id;
        if($course_id){
            $course = Course::find($course_id);
            $term_id = $course->current_term_id;
            $session_id = $course->current_session_id;
        }
    }
    
    if(!$term_id || !$session_id){
        return array();
    }

    return DB::table('syndicates')    
        ->join('syndicate_enrollments', 'syndicate_enrollments.syndicate_id', '=', 'syndicates.id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('syndicates.*','divs.name as div', 'depts.name as dept','depts.id as dept_id', 'courses.name as course', 'courses.id as course_id', 'courses.current_term_id as term_id', 'courses.current_session_id as session_id')
        ->where('syndicate_enrollments.user_id', $student_id)
        ->where('syndicate_enrollments.term_id', $term_id)
        ->where('syndicate_enrollments.session_id', $session_id)
        ->first();
}

function shorten_syndicate_name($syndicate_name)
{
    $syndicate_name = strtolower($syndicate_name);
    $syndicate_name = str_replace('syndicate','',$syndicate_name);
    $syndicate_name = str_replace('syn','',$syndicate_name);

    return $syndicate_name;
}

function get_current_session($course = 0)
{
    
    if(!$course){
        $data = Course::all();
        $current_sessions = array();
        
        foreach($data as $item){
            array_push($current_sessions, $item->current_session_id);
        }

        return $current_sessions;
    }
    
    $data = Course::find($course);
    if($data){
        return $data->current_session_id;
    }

    return '';

}

function get_current_session_data_by_course($course)
{
    $course = Course::find($course);
    if($course){
        return Session::find($course->current_session_id);
    }
    return array();
}

function get_current_session_data_of_exercise($exercise_id)
{
    $exercise = Exercise::find($exercise_id);
    $course = Course::find($exercise->course_id);
    if($course){
        return Session::find($course->current_session_id);
    } 
    return array();
}

function get_current_term($course)
{ 

    $data = Course::find($course);
    if($data){
        return $data->current_term_id;
    }

    return '';
}

function get_current_term_data_by_course($course)
{
    $course = Course::find($course);
    if($course){
        return Term::find($course->current_term_id);
    }
    return array();
}

function get_current_term_data_of_exercise($exercise_id)
{
    $exercise = Exercise::find($exercise_id);
    $course = Course::find($exercise->course_id);
    if($course){
        return Term::find($course->current_term_id);
    } 
    return array();
}

function get_user_by_role_name($role_name='')
{
    $role = role_code($role_name);
    return User::where('role', $role)
    ->where('approved', 1)
    ->where('users.deactivated', 0)
    ->orderBy('surname', 'asc')
    ->get();
}

function get_syndicate($id = 0)
{    
    if($id){
        return DB::table('syndicates')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('syndicates.*','divs.name as div', 'depts.name as dept','depts.id as dept_id', 'courses.name as course', 'courses.id as course_id', 'courses.current_term_id as term_id', 'courses.current_session_id as session_id')
        ->whereNull('syndicates.deleted_at')
        ->where('syndicates.id', $id)
        ->orderBy('syndicates.id', 'asc')
        ->first();
    }
    return array();
}

function get_syndicate_by_div($div_id)
{
    $current_session = '';

    $div = get_div($div_id);
    if($div){
        $current_session = $div->session_id;
    }

    if(!$current_session){
        return array();
    }    

    return DB::table('syndicates')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('syndicates.*','divs.name as div', 'depts.name as dept','depts.id as dept_id', 'courses.name as course', 'courses.id as course_id', 'courses.current_term_id as term_id', 'courses.current_session_id as session_id')
        ->whereNull('syndicates.deleted_at')
        ->where('syndicates.div_id', $div_id)
        ->where('syndicates.session_id', $current_session)
        ->orderBy('syndicates.id', 'asc')
        ->get();

}

function get_term($id = 0)
{
    if($id){
        return Term::find($id);
    }
    return Term::all();
}

function get_term_by_course($course_id)
{    
    return Term::where('course_id', $course_id)
    ->orderBy('id', 'desc')
    ->get();
}

function get_session($id = 0)
{
    if($id){
        return Session::find($id);
    }
    return Session::all();
}

function get_session_by_course($course_id)
{
    return Session::where('course_id', $course_id)
    ->orderBy('id', 'desc')
    ->get();
}

function get_course($id = 0)
{
    if($id){
        return Course::find($id);
    }

    return Course::all(); 
}

function get_dept($id = 0)
{
    if($id){
        return Dept::find($id);
    }
    return Dept::all();
}

function get_dept_joint_ids()//returns array
{
    $depts = array();

    $other_depts = Dept::all();
    if($other_depts){
        foreach($other_depts as $item){
            if($item->is_joint){
                array_push($depts, $item->id);
            }            
        }
    }

    return $depts;
}

function get_div($id = 0)
{
    if($id){
        return DB::table('divs')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('divs.*', 'depts.name as dept', 'courses.name as course', 'courses.current_session_id as session_id')
        ->where('divs.id', $id)
        ->whereNull('divs.deleted_at')
        ->first();
    }
    //return Div::all();
    return DB::table('divs')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('divs.*', 'depts.name as dept', 'courses.name as course', 'courses.current_session_id as session_id')
        ->whereNull('divs.deleted_at')
        ->orderBy('courses.name', 'asc')
        ->orderBy('depts.name', 'asc')
        ->orderBy('divs.name', 'asc')
        ->get();
}

function get_div_by_dept($dept_id, $course_id = 0)
{
    if($course_id){
        return DB::table('divs')
            ->join('depts', 'depts.id', '=', 'divs.dept_id')
            ->join('courses', 'courses.id', '=', 'divs.course_id')
            ->select('divs.*', 'depts.name as dept', 'courses.name as course')
            ->where('divs.dept_id', $dept_id)
            ->where('divs.course_id', $course_id)
            ->whereNull('divs.deleted_at')
            ->orderBy('courses.name', 'asc')
            ->orderBy('divs.name', 'asc')
            ->get();
    }

    return DB::table('divs')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('divs.*', 'depts.name as dept', 'courses.name as course')
        ->where('divs.dept_id', $dept_id)
        ->whereNull('divs.deleted_at')
        ->orderBy('courses.name', 'asc')
        ->orderBy('divs.name', 'asc')
        ->get();
}

function get_div_by_course($course_id)
{
    return DB::table('divs')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('divs.*', 'depts.name as dept', 'courses.name as course')
        ->where('divs.course_id', $course_id)
        ->whereNull('divs.deleted_at')
        ->orderBy('courses.name', 'asc')
        ->orderBy('divs.name', 'asc')
        ->get();
}

function get_tc_divs($user_id = 0)
{
    if(!$user_id){ $user_id = get_user_id(); }
    return DB::table('divs')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('divs.*', 'depts.name as dept', 'courses.name as course', 'courses.current_term_id as term_id', 'courses.current_session_id as session_id')
        ->where('tc_user_id',$user_id)
        ->whereNull('divs.deleted_at')
        ->orderBy('divs.name', 'asc')
        ->get();

}

function get_ci_divs($user_id = 0)
{
    if(!$user_id){ $user_id = get_user_id(); }
    return DB::table('divs')
        ->join('depts', 'depts.id', '=', 'divs.dept_id')
        ->join('courses', 'courses.id', '=', 'divs.course_id')
        ->select('divs.*', 'depts.name as dept', 'courses.name as course', 'courses.current_term_id as term_id', 'courses.current_session_id as session_id')
        ->where('ci_user_id',$user_id)
        ->whereNull('divs.deleted_at')
        ->orderBy('divs.name', 'asc')
        ->get();
}

function get_director_depts($user_id = 0)
{
    if(!$user_id){ $user_id = get_user_id(); }
    
    return Dept::where('director_user_id',$user_id)
        ->get();

}

function is_director($dept_id, $user_id = 0)
{
    if(!$user_id){ $user_id = get_user_id(); }

    $dept = Dept::find($dept_id);
    
    return ($dept->director_user_id  == $user_id);    
}

function is_ci($div_id, $user_id = 0)
{
    if(!$user_id){ $user_id = get_user_id(); }

    $div = Div::find($div_id);
    
    return ($div->ci_user_id == $user_id);    
}

function get_ds_exercise($user_id = 0)
{
    if(!$user_id){ $user_id = get_user_id(); }

    return Exercise::where('sponsor_user_id',$user_id)
        ->orWhere('cosponsor_user_id', $user_id)
        ->orderBy('name', 'asc')
        ->get();
}

function get_exercise_enrolled($student_id = 0, $term = 0)
{
   
    if(!$student_id){
        $student_id = get_user_id();
    }

    $user = User::find($student_id);

    if($user){
        $session = '';
        $data = Course::find($user->course_id);    

        if($data){
            $session = $data->current_session_id;
            if(!$term){
                $term = $data->current_term_id;
             }
        }

        return DB::table('exercise_enrollments')
            ->join('exercises', 'exercises.id', '=', 'exercise_enrollments.exercise_id')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.oral_grade','exercise_enrollments.written_grade','exercise_enrollments.ci_wp_grade','exercise_enrollments.dpty_cmd_wp_grade','exercise_enrollments.wp_grade','exercise_enrollments.total_grade','exercise_enrollments.love_letter','exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->where('exercise_enrollments.user_id', $student_id)
            ->where('exercise_enrollments.term_id', $term)
            ->where('exercise_enrollments.session_id', $session) 
            ->whereNull('exercises.deleted_at')
            ->whereNull('exercise_enrollments.deleted_at')
            ->orderBy('exercises.name', 'asc')
            ->get();
    }

    return array();
    
}

function get_students_terms_with_exercise_enrolled()
{
    $student_id = get_user_id();
    return DB::table('exercise_enrollments')
        ->join('terms', 'terms.id', '=', 'exercise_enrollments.term_id')
        ->select('terms.id','terms.name')
        ->groupBy('id','name')
        ->where('exercise_enrollments.user_id', $student_id)
        ->whereNull('exercise_enrollments.deleted_at')
        ->orderBy('terms.name', 'asc')      
        ->get();
}

function get_exercise_enrolled_students($exercise_id)
{
   
    $session = '';
    $exercise = Exercise::find($exercise_id);
    $data = Course::find($exercise->course_id);    

    if($data){
        $session = $data->current_session_id;
    }

    return DB::table('exercise_enrollments')
        ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'exercise_enrollments.oral_grade as oral_grade','exercise_enrollments.written_grade','exercise_enrollments.ci_wp_grade','exercise_enrollments.dpty_cmd_wp_grade','exercise_enrollments.wp_grade','exercise_enrollments.total_grade', 'users.id', 'users.picture',  'users.country', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
        ->where('exercise_enrollments.exercise_id', $exercise_id) 
        ->where('exercise_enrollments.session_id', $session) 
        ->whereNull('exercise_enrollments.deleted_at')
        ->whereNull('users.deleted_at')
        ->orderBy('users.surname', 'asc')
        ->get(); 
}

function get_exercise_enrollment($exercise_id)
{
    return DB::table('exercise_enrollments')
        ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'users.id', 'users.picture', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
        ->where('exercise_enrollments.exercise_id', $exercise_id) 
        ->where('exercise_enrollments.user_id', $session) 
        ->whereNull('exercise_enrollments.deleted_at')
        ->whereNull('users.deleted_at')
        ->orderBy('users.surname', 'asc')
        ->get(); 
}

function get_exercise_enrollment_data($student_id, $exercise_id, $session_id)
{
    return ExerciseEnrollment::where('user_id', $student_id)
            ->where('exercise_id', $exercise_id)
            ->where('session_id', $session_id)
            ->first();
}

function get_exercise_enrolled_students_in_div($exercise_id, $div_id, $session = 0)
{
   
    $exercise = Exercise::find($exercise_id);
    $term_id = $exercise->term_id;

    if(!$session){
        $course = Course::find($exercise->course_id);    
        $session = $course->current_session_id;
    }

    return DB::table('exercise_enrollments')
        ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
        ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')
        ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'users.id', 'users.picture', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
        ->where('exercise_enrollments.exercise_id', $exercise_id)
        ->where('divs.id', $div_id) 
        ->where('exercise_enrollments.session_id', $session)
        ->whereNull('exercise_enrollments.deleted_at')
        ->whereNull('users.deleted_at')
        ->where('syndicate_enrollments.term_id', $term_id)
        ->orderBy('users.surname', 'asc')
        ->get(); 
}

function is_grading_open($exercise_id)
{
    $exercise = Exercise::find($exercise_id);
    $data = Course::find($exercise->course_id);    

    if($data){
        return !is_exercise_result_released($exercise_id,$data->current_term_id,$data->current_session_id);
    }

    return 0;    
}

function is_exercise_moderating_open($exercise_id, $dept_id = 0) //$dept_id in the case of CI
{
    $exercise = Exercise::find($exercise_id);
    $course = Course::find($exercise->course_id);    

    if(!$dept_id){
        $dept_id = $exercise->dept_id;
    }

    if($course){
        $exercise_released = is_exercise_result_released($exercise_id,$course->current_term_id,$course->current_session_id);
        if($exercise_released){
            return !is_department_result_released($dept_id,$exercise->course_id,$course->current_term_id,$course->current_session_id);
        }
    }

    return 0;    
}

function is_dept_moderating_open($dept_id, $course_id)
{
    $course = Course::find($course_id); 
    if($course){
        return !is_department_result_released($dept_id,$course_id,$course->current_term_id,$course->current_session_id);   
    }
    return 0;    
}

function is_department_result_released($dept_id, $course_id, $term_id, $session_id, $approval = 1)
{
    return ReleasedResult::where('dept_id', $dept_id)
        ->where('course_id',$course_id)
        ->where('session_id',$session_id)
        ->where('term_id',$term_id)
        ->where('approval', $approval)
        ->first();
}

function get_department_result_released($dept_id, $course_id, $term_id, $session_id)
{
    return ReleasedResult::where('dept_id', $dept_id)
        ->where('course_id',$course_id)
        ->where('session_id',$session_id)
        ->where('term_id',$term_id)
        ->first();
}

function get_division_result_released($div_id, $course_id, $term_id, $session_id)
{
    return ReleasedResult::where('div_id', $div_id)
        ->where('course_id',$course_id)
        ->where('session_id',$session_id)
        ->where('term_id',$term_id)
        ->first();
}

function get_grader_assigned_to_student($exercise_id, $student_id) 
{  

    $session_id = 0;
    $exercise = Exercise::find($exercise_id);
    if($exercise){
        $data = Course::find($exercise->course_id);    
        $session_id = $data->current_session_id;
    }
    return DB::table('users')
        ->join('grading_assignments', 'grading_assignments.assigned_user_id', '=', 'users.id')
        ->select('users.id','users.rank','users.surname','users.first_name','users.surname','grading_assignments.id as assignment_id', 'grading_assignments.assigned_user_id')
        ->where('grading_assignments.exercise_id',$exercise_id)
        ->where('grading_assignments.session_id',$session_id)
        ->where('grading_assignments.user_id', $student_id)
        ->first();

}

function is_exercise_grader($exercise_id, $grader_id = 0){
    if(!$grader_id){
        $grader_id = get_user_id();
    }

    $session_id = 0;
    $exercise = Exercise::find($exercise_id);
    if($exercise){
        $data = Course::find($exercise->course_id);    
        $session_id = $data->current_session_id;
    }

    $data = GradingAssignment::where('exercise_id',$exercise_id)
    ->where('session_id',$session_id)->where('assigned_user_id',$grader_id)->first('id');

    if($data)
        return $data->id;

    return 0;
}


function get_students_assigned_to_grader($exercise_id, $grader_id)
{
    $session_id = 0;
    $exercise = Exercise::find($exercise_id);
    if($exercise){
        $data = Course::find($exercise->course_id);    
        $session_id = $data->current_session_id;
    }

    return GradingAssignment::where('exercise_id',$exercise_id)
        ->where('assigned_user_id', $grader_id)
        ->where('session_id', $session_id)
        ->get();
}

function get_grader_assigned_exercises($grader_id = 0)
{
    //Get exercises the grader has grading role
    if(!$grader_id){
        $grader_id = get_user_id();
    }

    return DB::table('grading_assignments')
        ->join('exercises', 'exercises.id', '=', 'grading_assignments.exercise_id')
        ->join('courses', 'courses.id', '=', 'exercises.course_id')
        ->select('exercises.id', 'exercises.name')
        ->groupBy('id','name')
        ->where('grading_assignments.assigned_user_id', $grader_id) 
        ->whereColumn('grading_assignments.term_id','courses.current_term_id')
        ->whereColumn('grading_assignments.session_id','courses.current_session_id')
        ->get();
    
}

function check_if_exercise_is_enrolled($exercise_id, $user_id=0) //returns enrolled id if true
{
    if(!$user_id){
        $user_id = get_user_id();
    }

    $enrolled = ExerciseEnrollment::where('user_id', $user_id)
            ->where('exercise_id', $exercise_id)
            ->first('id');

    if($enrolled)
        return $enrolled->id;

    return 0;
}

function get_exercise_available_for_enrollment($term = 0)
{

    $student_id = get_user_id();
    $user = User::find($student_id);

    if(!$user){
        return array();
    } 

    if(!$term){        
        $term = get_current_term($user->course_id);
    }

    return get_exercise_available_for_enrollment_by_dept($user->dept_id, $user->course_id, $term);

    $depts = array();
    array_push($depts, $user->dept_id);

    $other_depts = get_dept();
    if($other_depts){
        foreach($other_depts as $item){
            if($item->is_joint){
                array_push($depts, $item->id);
            }            
        }
    } 
      
}

function get_exercise_available_for_enrollment_by_dept($dept_id, $course_id, $term_id)
{

    
    $dept = Dept::find($dept_id);

    if(!$dept){
        return array();
    }

    $depts = array();
    array_push($depts, $dept->id);

    $other_depts = get_dept();
    if($other_depts){
        foreach($other_depts as $item){
            if($item->is_joint){
                array_push($depts, $item->id);
            }            
        }
    }
    
    return DB::table('exercises')
            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'exercises.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->whereIn('depts.id', $depts)
            ->where('exercises.course_id', $course_id)
            ->where('exercises.term_id', $term_id)
            ->whereNull('exercises.deleted_at')
            ->orderBy('exercises.name', 'asc')
            ->get();  

}

function get_exercise_materials($exercise_id)
{
    return ExerciseMaterial::where('exercise_id', $exercise_id)
    ->get(['id','title','file_1','file_2','file_3']);
}

function get_exercise_material($material_id, $dir='')
{

    if($dir == 'previous'){
        return ExerciseMaterial::where('id', '<', $material_id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->first();
    }

    if($dir == 'next'){
        return ExerciseMaterial::where('id', '>', $material_id)
            ->whereNull('deleted_at')
            ->orderBy('id', 'asc')
            ->first();
    }

    return ExerciseMaterial::find($material_id);
}

//requirements
function get_req_marks_already_set($exercise_id, $req_type = 0)
{

    $ex = Exercise::find($exercise_id);

    if($ex){
        $session = get_current_session($ex->course_id);

        if($req_type){
            return ExerciseRequirement::where('exercise_id',$exercise_id)
                ->where('session_id',$session)
                ->where('req_type',$req_type)
                ->whereNull('deleted_at')
                ->sum('marks');
        }

        return ExerciseRequirement::where('exercise_id',$exercise_id)
            ->where('session_id',$session)
            ->whereNull('deleted_at')
            ->sum('marks');

    }

    return 0;
    // /all()
}

function get_exercise_requirement($exercise_id)
{

    $ex = Exercise::find($exercise_id);

    if($ex){
        $session = get_current_session($ex->course_id);
        return ExerciseRequirement::where('exercise_id',$exercise_id)
            ->where('session_id',$session)
            ->get();
    }

    return array();
}

function get_my_requirement_submission($requirement_id)
{
    return get_student_requirement_submission($requirement_id, get_user_id()); 
}

function get_student_requirement_submission($requirement_id, $student_id)
{
    return RequirementSubmission::where('requirement_id',$requirement_id)
        ->where('user_id',$student_id)
        ->first();        
}

function get_student_exercise_submission($exercise_id, $student_id)
{
    return DB::table('requirement_submissions')
            ->join('exercise_requirements', 'exercise_requirements.id', '=', 'requirement_submissions.requirement_id')
            ->join('exercises', 'exercises.id', '=', 'exercise_requirements.exercise_id')
            ->select('requirement_submissions.id as submission_id','requirement_submissions.requirement_id','requirement_submissions.grade','requirement_submissions.submitted_file','requirement_submissions.created_at as submitted_at','requirement_submissions.graded_at','exercises.name as exercise', 'exercise_requirements.title as requirement', 'exercise_requirements.marks as requirment_marks')
            ->where('exercises.id', $exercise_id)
            ->where('requirement_submissions.user_id', $student_id)
            ->whereNull('exercises.deleted_at')
            ->whereNull('exercise_requirements.deleted_at')
            ->orderBy('exercise_requirements.id', 'asc')
            ->get();
}

function get_student_exercise_submission_ungraded($exercise_id, $student_id)
{
    return DB::table('requirement_submissions')
            ->join('exercise_requirements', 'exercise_requirements.id', '=', 'requirement_submissions.requirement_id')
            ->join('exercises', 'exercises.id', '=', 'exercise_requirements.exercise_id')
            ->select('requirement_submissions.id as submission_id','requirement_submissions.requirement_id','requirement_submissions.grade','requirement_submissions.submitted_file','requirement_submissions.created_at as submitted_at','requirement_submissions.graded_at','exercises.name as exercise', 'exercise_requirements.title as requirement', 'exercise_requirements.marks as requirment_marks')
            ->where('exercises.id', $exercise_id)
            ->where('requirement_submissions.user_id', $student_id)
            ->whereNull('requirement_submissions.graded_at')
            ->whereNull('exercises.deleted_at')
            ->whereNull('exercise_requirements.deleted_at')
            ->orderBy('exercise_requirements.id', 'asc')
            ->get();
}

function get_requirement_grade($requirement_id, $student_id)
{
    $data = RequirementGrade::where('requirement_id',$requirement_id)
        ->where('user_id',$student_id)
        ->first('grade'); 

    if($data){
        return $data->grade;
    }
    return '';
}

function set_session_msg($msg, $type = 0)
{
    if($type == 0)
        session()->put('msg', $msg);
    elseif($type == 1)
        session()->put('newcart', $msg);
}

function get_session_msg($type = 0)
{
    $msg = '';
    if($type == 0)
        $msg = session()->pull('msg');
    elseif($type == 1)
        $msg = session()->pull('newcart');
    
    return $msg;
}

function set_session_url($url)
{
    session()->put('currentUrl', $url);
}

function get_session_url()
{
    $url = session()->pull('currentUrl');
    return $url;
}

function my_encode($input)
{
    return base64_encode($input);
}

function my_decode($input)
{
    return base64_decode($input);
}

function my_substring($string, $lenght)
{
    if (strlen($string) > $lenght)
        $string = substr($string, 0, $lenght-5).'...';
    return $string;
}

function normalize_string ($str = '') //used to add name to url making it human readable
{
    $str = strip_tags($str); 
    $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
    $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
    $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
    $str = htmlentities($str, ENT_QUOTES, "utf-8");
    $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
    $str = str_replace(' ', '-', $str);
    $str = rawurlencode($str);
    $str = str_replace('%', '-', $str);
    return $str;
}

function form_data_country()
{
    $data = array(
        'Nigeria',
        'Niger',
        'Ghana',
        'Afghanistan',
        'Åland Islands',
        'Albania',
        'Algeria',
        'American Samoa',
        'Andorra',
        'Angola',
        'Anguilla',
        'Antarctica',
        'Antigua and Barbuda',
        'Argentina',
        'Armenia',
        'Aruba',
        'Australia',
        'Austria',
        'Azerbaijan',
        'Bahamas',
        'Bahrain',
        'Bangladesh',
        'Barbados',
        'Belarus',
        'Belgium',
        'Belize',
        'Benin',
        'Bermuda',
        'Bhutan',
        'Bolivia, Plurinational State of',
        'Bonaire, Sint Eustatius and Saba',
        'Bosnia and Herzegovina',
        'Botswana',
        'Bouvet Island',
        'Brazil',
        'British Indian Ocean Territory',
        'Brunei Darussalam',
        'Bulgaria',
        'Burkina Faso',
        'Burundi',
        'Cambodia',
        'Cameroon',
        'Canada',
        'Cape Verde',
        'Cayman Islands',
        'Central African Republic',
        'Chad',
        'Chile',
        'China',
        'Christmas Island',
        'Cocos (Keeling) Islands',
        'Colombia',
        'Comoros',
        'Congo',
        'Congo, the Democratic Republic of the',
        'Cook Islands',
        'Costa Rica',
        'Côte dIvoire',
        'Croatia',
        'Cuba',
        'Curaçao',
        'Cyprus',
        'Czech Republic',
        'Denmark',
        'Djibouti',
        'Dominica',
        'Dominican Republic',
        'Ecuador',
        'Egypt',
        'El Salvador',
        'Equatorial Guinea',
        'Eritrea',
        'Estonia',
        'Ethiopia',
        'Falkland Islands (Malvinas)',
        'Faroe Islands',
        'Fiji',
        'Finland',
        'France',
        'French Guiana',
        'French Polynesia',
        'French Southern Territories',
        'Gabon',
        'Gambia',
        'Georgia',
        'Germany',
        'Ghana',
        'Gibraltar',
        'Greece',
        'Greenland',
        'Grenada',
        'Guadeloupe',
        'Guam',
        'Guatemala',
        'Guernsey',
        'Guinea',
        'Guinea-Bissau',
        'Guyana',
        'Haiti',
        'Heard Island and McDonald Islands',
        'Holy See (Vatican City State)',
        'Honduras',
        'Hong Kong',
        'Hungary',
        'Iceland',
        'India',
        'Indonesia',
        'Iran, Islamic Republic of',
        'Iraq',
        'Ireland',
        'Isle of Man',
        'Israel',
        'Italy',
        'Jamaica',
        'Japan',
        'Jersey',
        'Jordan',
        'Kazakhstan',
        'Kenya',
        'Kiribati',
        'Korea',
        'Kuwait',
        'Kyrgyzstan',
        'Lao',
        'Latvia',
        'Lebanon',
        'Lesotho',
        'Liberia',
        'Libya',
        'Liechtenstein',
        'Lithuania',
        'Luxembourg',
        'Macao',
        'Macedonia',
        'Madagascar',
        'Malawi',
        'Malaysia',
        'Maldives',
        'Mali',
        'Malta',
        'Marshall Islands',
        'Martinique',
        'Mauritania',
        'Mauritius',
        'Mayotte',
        'Mexico',
        'Micronesia, Federated States of',
        'Moldova, Republic of',
        'Monaco',
        'Mongolia',
        'Montenegro',
        'Montserrat',
        'Morocco',
        'Mozambique',
        'Myanmar',
        'Namibia',
        'Nauru',
        'Nepal',
        'Netherlands',
        'New Caledonia',
        'New Zealand',
        'Nicaragua',
        'Niger',
        'Nigeria',
        'Niue',
        'Norfolk Island',
        'Northern Mariana Islands',
        'Norway',
        'Oman',
        'Pakistan',
        'Palau',
        'Palestinian Territory, Occupied',
        'Panama',
        'Papua New Guinea',
        'Paraguay',
        'Peru',
        'Philippines',
        'Pitcairn',
        'Poland',
        'Portugal',
        'Puerto Rico',
        'Qatar',
        'Réunion',
        'Romania',
        'Russian Federation',
        'Rwanda',
        'Saint Barthélemy',
        'Saint Helena, Ascension and Tristan da Cunha',
        'Saint Kitts and Nevis',
        'Saint Lucia',
        'Saint Martin (French part)',
        'Saint Pierre and Miquelon',
        'Saint Vincent and the Grenadines',
        'Samoa',
        'San Marino',
        'Sao Tome and Principe',
        'Saudi Arabia',
        'Senegal',
        'Serbia',
        'Seychelles',
        'Sierra Leone',
        'Singapore',
        'Sint Maarten (Dutch part)',
        'Slovakia',
        'Slovenia',
        'Solomon Islands',
        'Somalia',
        'South Africa',
        'South Georgia and the South Sandwich Islands',
        'South Sudan',
        'Spain',
        'Sri Lanka',
        'Sudan',
        'Suriname',
        'Svalbard and Jan Mayen',
        'Swaziland',
        'Sweden',
        'Switzerland',
        'Syrian Arab Republic',
        'Taiwan',
        'Tajikistan',
        'Tanzania',
        'Thailand',
        'Timor-Leste',
        'Togo',
        'Tokelau',
        'Tonga',
        'Trinidad and Tobago',
        'Tunisia',
        'Turkey',
        'Turkmenistan',
        'Turks and Caicos Islands',
        'Tuvalu',
        'Uganda',
        'Ukraine',
        'United Arab Emirates',
        'United Kingdom',
        'United States',
        'Uruguay',
        'Uzbekistan',
        'Vanuatu',
        'Venezuela',
        'Viet Nam',
        'Virgin Islands',
        'Wallis and Futuna',
        'Western Sahara',
        'Yemen',
        'Zambia',
        'Zimbabwe'
    );
    return $data;
}

function check_can_view_user_profile(){
    return check_has_ability('can_view_user_detailed_profile');
}

function check_can_edit_user_profile(){
    return check_has_ability('can_edit_user');
}

function check_can_approve_user_profile()
{
    return check_has_ability('approve_user');
}

function check_can_change_user_role()
{
    //can also change dept and course
    return check_has_ability('can_change_user_role');
}

function check_can_change_user_picture()
{
    return check_has_ability('can_change_user_picture');
}

function check_can_manage_exercise_materials($exercise)
{

    $user_id = get_user_id();
    $record = Exercise::find($exercise);

    if($record){
        if($record->sponsor_user_id == $user_id || $record->cosponsor_user_id == $user_id){
            return 1;
        }
    }
    

    return 0;
}

function check_can_view_exercise_requirements($exercise)
{
    return check_can_manage_exercise_materials($exercise);
}

function check_can_view_utw_requirement_grades($exercise)
{
    return check_can_view_exercise_requirements($exercise);
}

function check_has_ability($action, $role = 0)//set default at 12. Remove
{   
    /* 
    $acceptable_action = array('can_manage_config','is_academic','manage_exercise','view_all_exercise',
    'approve_user','reset_user_password', 'delete_or_deactivate_user', 
    'view_students','view_staff','manage_syndicate','manage_dept_and_div',
    'manage_term_session_and_course', 'always_see_identity','manage_event','manage_forms');
    can_change_user_picture,
    */

    if(!$role){
        $role = get_user_role();
    }

    $role = (int)$role;

    if($action == 'is_academic'){
        return is_academic();
    }    

    //always allow admin to configure
    if($action == 'can_manage_config'){
        if($role == 15){
            return 1;
        }
    }

    $can_do = explode('|', get_config($action));
    return in_array($role, $can_do);

}

function check_has_abilityOld($action, $role = 0)
{    
    
    if(!$role){
        $role = get_user_role();
    }

    $role = (int)$role;

    $acceptable = array('can_manage_config','is_academic','manage_exercise','view_all_exercise',
    'approve_user','reset_user_password', 'delete_or_deactivate_user', 
    'view_students','view_staff','manage_syndicate','manage_dept_and_div',
    'manage_term_session_and_course', 'always_see_identity','manage_event','manage_forms');

    if($action == 'is_academic'){
        return is_academic();
    }    

    if($action == 'can_manage_config'){
        if($role == 15){
            return 1;
        }
    }

    if($action == 'manage_event'){
        $can_do = array(13,14,15);
        return in_array($role, $can_do);
    }

    if($action == 'always_see_identity'){
        $can_do = array(13,14,15);
        return in_array($role, $can_do);
    }


    if($role == 15){
        return 1;
    }

    return 0;

}

function show_love_letter_to_students()
{
    return get_config('show_love_letter_to_students',0);
}

function role_name($role,$short=0)
{
    if($short){
        if($role == 1){
            return 'Student';
        }elseif ($role == 2){
            return 'Staff';
        }elseif ($role == 3){
            return 'DS';
        }elseif ($role == 4){
            return 'CI';
        }elseif ($role == 5){
            return 'TC';
        }elseif ($role == 6){
            return 'Director';
        }elseif ($role == 13){
            return 'Dy Comdt';
        }elseif ($role == 14){
            return 'Comdt';
        }elseif ($role == 15){
            return 'Admin';
        }
    }else{
        if($role == 1){
            return 'Student';
        }elseif ($role == 2){
            return 'Admin Staff';
        }elseif ($role == 3){
            return 'Directing Staff';
        }elseif ($role == 4){
            return 'Chief Instructor';
        }elseif ($role == 5){
            return 'Term Coordinator';
        }elseif ($role == 6){
            return 'Director';
        }elseif ($role == 13){
            return 'Deputy Commandant';
        }elseif ($role == 14){
            return 'Commandant';
        }elseif ($role == 15){
            return 'Administrator';
        }
    }
    
    return 'User';
}

function role_code($role_name='')
{  
    $role_name = strtolower($role_name);
    $role = 0;
    if($role_name == 'student'){$role = 1;}
    elseif($role_name == 'staff' || $role_name == 'admin staff'){$role = 2;}
    elseif($role_name == 'ds' || $role_name == 'directing staff'){$role = 3;}
    elseif($role_name == 'ci' || $role_name == 'chief instructor'){$role = 4;}
    elseif($role_name == 'tc' || $role_name == 'term coordinator'){$role = 5;}
    elseif($role_name == 'director'){$role = 6;}
    elseif($role_name == 'deputy commandant'){$role = 13;}
    elseif($role_name == 'commandant'){$role = 14;}
    elseif($role_name == 'admin' || $role_name == 'administrator'){$role = 15;}

    return $role;
}

function get_system_roles($type = '', $short_name = 0)
{
    if($type == 'staff'){ //exclude administrator (15)
        $codes = array(2, 3, 4, 5, 6, 13, 14);
    }elseif($type == 'all staff'){ //include administrator (15)
        $codes = array(2, 3, 4, 5, 6, 13, 14, 15);
    }else{ 
       $codes = array(1, 2, 3, 4, 5, 6, 13, 14); 
    }
    
    $role = array();
    for($i = 0; $i < count($codes); $i++){
        $name = role_name($codes[$i], $short_name);
        $new_role = array('code'=>$codes[$i], 'name'=>$name);
        array_push($role, $new_role);
    }
    return $role;
}

function is_commandant($role = 0){
    if(!$role){
        $role = get_user_role();
    }
    return $role == role_code('commandant');
}

function is_deputy_commandant($role = 0){
    if(!$role){
        $role = get_user_role();
    }
    return $role == role_code('deputy commandant');
}

function is_academic($role = 0)
{
    if(!$role){
        $role = get_user_role();
    }
    if($role == 3 || $role == 4 || $role == 5 || $role == 6 || $role == 13 || $role == 14 || $role == 15){
        return 1;
    }
    return 0;
}

function is_student($role = 0)
{
    if(!$role){
        $role = get_user_role();
    } 
    if($role == 1){
        return 1;
    } 
    return 0;
}

function is_my_exercise_reg_open(){

    $user = get_student_data();
    $course = Course::find($user->course_id);  
    
    if($course->current_session_id == $user->session_id){
        return $course->exercise_enrollment;
    }

    return 0; 
}

function show_registration_link()
{
    if(check_reg_open_for_staff()){
        return 1;
    }
    if(get_courses_open_for_reg()){
        return 1;
    }
    return 0;
}

function check_reg_open_for_staff(){
    return get_config('open_staff_registration',0);
}

function check_reg_open_for_course($course_id){
    $courses = get_courses_open_for_reg();
    if($courses){
        foreach($courses as $course){
            if($course_id == $course->id){
                return 1;
            }
        }
    }
    return 0;
}

function get_courses_open_for_reg(){
    $date = date('Y-m-d');
    return Course::whereNotNull('current_term_id')
    ->whereNotNull('current_session_id')
    ->where('reg_start_at','<=',$date)
    ->where('reg_end_at','>=',$date)
    ->orderBy('name', 'asc')
    ->get();
}

//Results

function can_show_student_identity($var_type, $var_id, $term_id, $session_id, $course_id = 0)
{
    
    if(check_has_ability('always_see_identity')){
        return 1;
    }

    if($var_type == 'exercise'){
        $exercise = Exercise::find($var_id);
        if($exercise){
            $exercise_released = is_exercise_result_released($exercise->id,$term_id,$session_id);
            if($exercise_released){
                return is_department_result_released($exercise->dept_id,$exercise->course_id,$term_id,$session_id);
            }
        }
        return 0;
    }

    if($var_type == 'div'){
        $div = Div::find($var_id);
        if($div){
            return is_department_result_released($div->dept_id,$course_id,$term_id,$session_id);
        }
        return 0;
    }

    if($var_type == 'dept'){
        $dept = Dept::find($var_id);
        if($dept){
            return is_department_result_released($dept->id,$course_id,$term_id,$session_id);
        }
        return 0;
    }
    
    return 0;
}

function get_realeased_result_sessions($course_id)
{
    return DB::table('released_results')
        ->join('sessions', 'sessions.id', '=', 'released_results.session_id')
        ->select('sessions.id','sessions.name')
        ->groupBy('id','name')
        ->where('released_results.approval', 1) 
        ->where('sessions.course_id', $course_id)
        ->orderBy('released_results.id', 'desc')      
        ->get();  
}

function get_realeased_result_depts_by_session($session_id)
{ //sessions in a dept with approved result
    return DB::table('released_results')
        ->join('depts', 'depts.id', '=', 'released_results.dept_id')
        ->select('depts.id','depts.name')
        ->groupBy('id','name')
        ->where('released_results.approval', 1) 
        ->where('released_results.session_id', $session_id)
        ->orderBy('depts.name', 'asc')      
        ->get();  
}

function get_realeased_result_divs_by_session($session_id)
{ //sessions in a divs whose results has been approved
    return DB::table('released_results')
        ->join('divs', 'divs.id', '=', 'released_results.div_id')
        ->select('divs.id','divs.name')
        ->groupBy('id','name')
        ->where('released_results.approval', 1) 
        ->where('released_results.session_id', $session_id)
        ->whereNull('divs.deleted_at')
        ->orderBy('divs.name', 'asc')      
        ->get();
}

function get_realeased_result_terms_by_dept($dept_id, $asc = 0) 
{ //terms in a dept with approved result
    $order_type = 'desc';
    if($asc){
        $order_type = 'asc';
    }

    return DB::table('released_results')
        ->join('terms', 'terms.id', '=', 'released_results.term_id')
        ->select('terms.id','terms.name')
        ->groupBy('id','name')
        ->where('released_results.approval', 1) 
        ->where('released_results.dept_id', $dept_id)
        ->orderBy('released_results.id', $order_type)      
        ->get();  
}

function is_exercise_result_released($exercise_id, $term_id, $session_id)
{
    return ReleasedExerciseResult::where('session_id',$session_id)
            ->where('term_id',$term_id)
            ->where('exercise_id',$exercise_id)
            ->first();

}

function get_released_exercise_result($exercise_id, $term_id, $session_id)
{
    return is_exercise_result_released($exercise_id, $term_id, $session_id);
}

function get_released_result_exercises_by_dept($dept_id, $course_id, $term_id, $session_id)
{    
    $dept = Dept::find($dept_id);

    if(!$dept){
        return array();
    }

    $depts = array();
    array_push($depts, $dept->id);

    $other_depts = get_dept();
    if($other_depts){
        foreach($other_depts as $item){
            if($item->is_joint){
                array_push($depts, $item->id);
            }            
        }
    }
    
    return DB::table('released_exercise_results')

            ->join('exercises', 'exercises.id', '=', 'released_exercise_results.exercise_id')

            ->join('depts', 'depts.id', '=', 'exercises.dept_id')
            ->join('courses', 'courses.id', '=', 'exercises.course_id')
            ->join('terms', 'terms.id', '=', 'released_exercise_results.term_id')
            ->select('exercises.*', 'depts.name as dept', 'courses.name as course', 'terms.name as term')
            ->whereIn('depts.id', $depts)
            ->where('exercises.course_id', $course_id)

            ->where('released_exercise_results.term_id', $term_id)
            ->where('released_exercise_results.session_id', $session_id)

            ->whereNull('exercises.deleted_at')
            ->orderBy('exercises.name', 'asc')
            ->get();  

}

function get_result_exercise_enrolled_students($exercise_id, $session_id)
{
    return DB::table('exercise_enrollments')
        ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')
        ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'exercise_enrollments.oral_grade as oral_grade','exercise_enrollments.written_grade','exercise_enrollments.ci_wp_grade','exercise_enrollments.dpty_cmd_wp_grade','exercise_enrollments.wp_grade','exercise_enrollments.total_wp','exercise_enrollments.total_grade', 'users.id', 'users.picture',  'users.country', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
        ->where('exercise_enrollments.exercise_id', $exercise_id) 
        ->where('exercise_enrollments.session_id', $session_id) 
        ->whereNull('exercise_enrollments.deleted_at')
        ->whereNull('users.deleted_at')
        ->orderBy('exercise_enrollments.total_wp', 'desc')
        ->get(); 
}

function get_result_exercise_enrolled_students_by_div($exercise_id, $session_id, $div_id, $organic = 0)
{

    $exercise = Exercise::find($exercise_id);
    $term_id = $exercise->term_id;

    if($organic){

        $div = Div::find($div_id);

        return DB::table('exercise_enrollments')
            ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')

            ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
            ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
            ->join('divs', 'divs.id', '=', 'syndicates.div_id')

            ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'exercise_enrollments.oral_grade as oral_grade','exercise_enrollments.written_grade','exercise_enrollments.ci_wp_grade','exercise_enrollments.dpty_cmd_wp_grade','exercise_enrollments.wp_grade','exercise_enrollments.total_wp','exercise_enrollments.total_grade', 'users.id', 'users.picture',  'users.country', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
            ->where('exercise_enrollments.exercise_id', $exercise_id) 
            ->where('exercise_enrollments.session_id', $session_id) 
            ->whereNull('exercise_enrollments.deleted_at')
            ->whereNull('users.deleted_at')

            ->where('divs.id', $div_id)
            ->where('users.dept_id', $div->dept_id)

            ->where('syndicate_enrollments.term_id', $term_id)
            ->whereNull('syndicate_enrollments.deleted_at')
            ->whereNull('syndicates.deleted_at')

            ->orderBy('exercise_enrollments.total_wp', 'desc')
            ->get(); 
    }
    return DB::table('exercise_enrollments')
        ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')

        ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
        ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')

        ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'exercise_enrollments.oral_grade as oral_grade','exercise_enrollments.written_grade','exercise_enrollments.ci_wp_grade','exercise_enrollments.dpty_cmd_wp_grade','exercise_enrollments.wp_grade','exercise_enrollments.total_wp','exercise_enrollments.total_grade', 'users.id', 'users.picture',  'users.country', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
        ->where('exercise_enrollments.exercise_id', $exercise_id) 
        ->where('exercise_enrollments.session_id', $session_id) 
        ->whereNull('exercise_enrollments.deleted_at')
        ->whereNull('users.deleted_at')

        ->where('divs.id', $div_id)
        ->where('syndicate_enrollments.term_id', $term_id)
        ->whereNull('syndicate_enrollments.deleted_at')
        ->whereNull('syndicates.deleted_at')

        ->orderBy('exercise_enrollments.total_wp', 'desc')
        ->get(); 
}

function get_result_exercise_enrolled_students_by_dept($exercise_id, $session_id, $dept_id, $organic = 0)
{
    if($organic){
        return DB::table('exercise_enrollments')
            ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
            ->join('depts', 'depts.id', '=', 'users.dept_id')
            ->join('courses', 'courses.id', '=', 'users.course_id')
            ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'exercise_enrollments.oral_grade as oral_grade','exercise_enrollments.written_grade','exercise_enrollments.ci_wp_grade','exercise_enrollments.dpty_cmd_wp_grade','exercise_enrollments.wp_grade','exercise_enrollments.total_wp','exercise_enrollments.total_grade', 'users.id', 'users.picture',  'users.country', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
            ->where('exercise_enrollments.exercise_id', $exercise_id) 
            ->where('exercise_enrollments.session_id', $session_id)
            ->where('users.dept_id', $dept_id) 
            ->whereNull('exercise_enrollments.deleted_at')
            ->whereNull('users.deleted_at')
            ->orderBy('exercise_enrollments.total_wp', 'desc')
            ->get(); 
    }

    $divs = array();

    $exercise = Exercise::find($exercise_id);

    $other_divs = get_div_by_dept($dept_id, $exercise->course_id);
    if($other_divs){
        foreach($other_divs as $item){
            array_push($divs, $item->id);
        }
    }

    $term_id = $exercise->term_id;

    return DB::table('exercise_enrollments')
        ->join('users', 'users.id', '=', 'exercise_enrollments.user_id')
        ->join('depts', 'depts.id', '=', 'users.dept_id')
        ->join('courses', 'courses.id', '=', 'users.course_id')

        ->join('syndicate_enrollments', 'syndicate_enrollments.user_id', '=', 'users.id')
        ->join('syndicates', 'syndicates.id', '=', 'syndicate_enrollments.syndicate_id')
        ->join('divs', 'divs.id', '=', 'syndicates.div_id')

        ->select('exercise_enrollments.id as enrollment_id', 'exercise_enrollments.user_id', 'exercise_enrollments.oral_grade as oral_grade','exercise_enrollments.written_grade','exercise_enrollments.ci_wp_grade','exercise_enrollments.dpty_cmd_wp_grade','exercise_enrollments.wp_grade','exercise_enrollments.total_wp','exercise_enrollments.total_grade', 'users.id', 'users.picture',  'users.country', 'users.rank', 'users.surname', 'users.first_name', 'users.other_name', 'depts.name as dept', 'courses.name as course')
        ->where('exercise_enrollments.exercise_id', $exercise_id) 
        ->where('exercise_enrollments.session_id', $session_id) 
        ->whereNull('exercise_enrollments.deleted_at')
        ->whereNull('users.deleted_at')

        ->whereIn('divs.id', $divs)

        ->where('syndicate_enrollments.term_id', $term_id)
        ->whereNull('syndicate_enrollments.deleted_at')
        ->whereNull('syndicates.deleted_at')

        ->orderBy('exercise_enrollments.total_wp', 'desc')
        ->get(); 
}

function get_student_result_statistics_in_termV1($student_id, $session_id, $term_id)
{
        $student_total_wp = 0;
        $student_wp_factor = 0;
        $exercise_wp = 0;
        $no_of_exercise = 0;

        $record = DB::table('exercise_enrollments')
            ->join('exercises', 'exercises.id', '=', 'exercise_enrollments.exercise_id')
           
            ->select(DB::raw('count(exercises.id) as no_of_exercise, SUM(exercises.weighted_point) as exercise_wp, SUM(exercise_enrollments.total_wp) as student_wp'))
            
            ->where('exercise_enrollments.user_id', $student_id) 
            ->where('exercise_enrollments.term_id', $term_id)
            ->where('exercise_enrollments.session_id', $session_id)
            ->whereNull('exercise_enrollments.deleted_at')
            ->first();


        if($record){ 

            $no_of_exercise = $record->no_of_exercise;

            $exercise_wp = $record->exercise_wp;
            $student_total_wp += $record->student_wp;

            if($exercise_wp){
                $student_wp_factor = 100/ $exercise_wp;
            }            
        }

        //Wp added to the student in this term
        $wp_added_to_student = ResultAddedTermWp::where('user_id', $student_id)            
        ->where('term_id', $term_id)
        ->where('session_id', $session_id)
            ->sum('wp');
        
        $student_total_wp += $wp_added_to_student;
        $score = number_format($student_total_wp * $student_wp_factor, 2);        
        return array( 'term_no_of_exercise'=>$no_of_exercise, 'term_exercise_wp'=>$exercise_wp, 'term_wp'=>$student_total_wp, 'term_score'=>$score);
}

function get_student_result_statistics_totalV1($student_id, $session_id)
{
        $student_total_wp = 0;
        $student_wp_factor = 0;
        $exercise_wp = 0;
        $no_of_exercise = 0;

        $record = DB::table('exercise_enrollments')
            ->join('exercises', 'exercises.id', '=', 'exercise_enrollments.exercise_id')
           
            ->select(DB::raw('count(exercises.id) as no_of_exercise, SUM(exercises.weighted_point) as exercise_wp, SUM(exercise_enrollments.total_wp) as student_wp'))
            
            ->where('exercise_enrollments.user_id', $student_id)
            ->where('exercise_enrollments.session_id', $session_id)
            ->whereNull('exercise_enrollments.deleted_at')
            ->first();


        if($record){ 

            $no_of_exercise = $record->no_of_exercise;

            $exercise_wp = $record->exercise_wp;
            $student_total_wp += $record->student_wp;

            if($exercise_wp){
                $student_wp_factor = 100/ $exercise_wp;
            }            
        }

        //Wp added to the student in this term
        $wp_added_to_student = ResultAddedTermWp::where('user_id', $student_id)
        ->where('session_id', $session_id)
            ->sum('wp');
        
        $student_total_wp += $wp_added_to_student;
        $score = number_format($student_total_wp * $student_wp_factor, 2);        
        return array( 'total_no_of_exercise'=>$no_of_exercise, 'total_exercise_wp'=>$exercise_wp, 'total_wp'=>$student_total_wp, 'total_score'=>$score);
}

function get_student_result_statistics_in_term($student_id, $session_id, $term_id) 
{
        /*Sums up results for realease exercises in term*/

        $student_total_wp = 0;
        $student_wp_factor = 0;
        $exercise_wp = 0;
        $no_of_exercise = 0;

        $record = DB::table('exercise_enrollments')
            ->join('exercises', 'exercises.id', '=', 'exercise_enrollments.exercise_id')
            
            ->join('released_exercise_results', 'released_exercise_results.exercise_id', '=', 'exercise_enrollments.exercise_id')

            ->select(DB::raw('count(exercises.id) as no_of_exercise, SUM(exercises.weighted_point) as exercise_wp, SUM(exercise_enrollments.total_wp) as student_wp'))
            
            ->where('exercise_enrollments.user_id', $student_id) 
            ->where('exercise_enrollments.term_id', $term_id)
            ->where('exercise_enrollments.session_id', $session_id)

             
            ->where('released_exercise_results.term_id', $term_id)
            ->where('released_exercise_results.session_id', $session_id)

            ->whereNull('exercise_enrollments.deleted_at')
            ->first();


        if($record){ 

            $no_of_exercise = $record->no_of_exercise;

            $exercise_wp = $record->exercise_wp;
            $student_total_wp += $record->student_wp;

            if($exercise_wp){
                $student_wp_factor = 100/ $exercise_wp;
            }            
        }

        //Wp added to the student in this term
        $wp_added_to_student = ResultAddedTermWp::where('user_id', $student_id)            
        ->where('term_id', $term_id)
        ->where('session_id', $session_id)
            ->sum('wp');
        
        $student_total_wp += $wp_added_to_student;
        $score = number_format($student_total_wp * $student_wp_factor, 2);        
        return array( 'term_no_of_exercise'=>$no_of_exercise, 'term_exercise_wp'=>$exercise_wp, 'term_wp'=>$student_total_wp, 'term_score'=>$score);
}

function get_student_result_statistics_total($student_id, $session_id)
{
     /*Sums up results for realease exercises in session*/

        $student_total_wp = 0;
        $student_wp_factor = 0;
        $exercise_wp = 0;
        $no_of_exercise = 0;

        $record = DB::table('exercise_enrollments')
            ->join('exercises', 'exercises.id', '=', 'exercise_enrollments.exercise_id')
           
            ->join('released_exercise_results', 'released_exercise_results.exercise_id', '=', 'exercise_enrollments.exercise_id')

            ->select(DB::raw('count(exercises.id) as no_of_exercise, SUM(exercises.weighted_point) as exercise_wp, SUM(exercise_enrollments.total_wp) as student_wp'))
            
            ->where('exercise_enrollments.user_id', $student_id)
            ->where('exercise_enrollments.session_id', $session_id)

            ->where('released_exercise_results.session_id', $session_id)

            ->whereNull('exercise_enrollments.deleted_at')
            ->first();


        if($record){ 

            $no_of_exercise = $record->no_of_exercise;

            $exercise_wp = $record->exercise_wp;
            $student_total_wp += $record->student_wp;

            if($exercise_wp){
                $student_wp_factor = 100/ $exercise_wp;
            }            
        }

        //Wp added to the student in this term
        $wp_added_to_student = ResultAddedTermWp::where('user_id', $student_id)
        ->where('session_id', $session_id)
            ->sum('wp');
        
        $student_total_wp += $wp_added_to_student;
        $score = number_format($student_total_wp * $student_wp_factor, 2);        
        return array( 'total_no_of_exercise'=>$no_of_exercise, 'total_exercise_wp'=>$exercise_wp, 'total_wp'=>$student_total_wp, 'total_score'=>$score);
}

function add_result_statistics_to_student($student, $session_id, $term_id = 0)
{
    
    $stat = array();
    
    if($term_id){
        $term_stat = get_student_result_statistics_in_term($student->id, $session_id, $term_id);
        $stat = array_merge($stat, $term_stat);
    }

    $total_stat = get_student_result_statistics_total($student->id, $session_id);
    $stat = array_merge($stat, $total_stat);

    $modified_student = (object) array_merge( (array)$student, $stat);

    return $modified_student;
}

function cmp_term($a, $b) 
{
    return $b->term_score - $a->term_score;
	//$a->term_score <=> $b->term_score; //original
}

function sort_student_result_by_term_total($students, $session_id, $term_id)
{
    $arr_students = array();
    if($students){
        foreach($students as $student){            
            $modified_student = add_result_statistics_to_student($student, $session_id, $term_id);
            array_push($arr_students, $modified_student);
        }
    }
    
	//usort($arr_students, fn($b, $a) => $a->term_score <=> $b->term_score);
	usort($arr_students, "cmp_term");
    
	return $arr_students; 
}

function cmp_total($a, $b) 
{
    return $b->total_score - $a->total_score;
	//$a->term_score <=> $b->term_score;
}

function sort_student_result_by_total($students, $session_id)
{
    $arr_students = array();
    if($students){
        foreach($students as $student){            
            $modified_student = add_result_statistics_to_student($student, $session_id);
            array_push($arr_students, $modified_student);
        }
    }
    //usort($arr_students, fn($b, $a) => $a->total_score <=> $b->total_score);
	usort($arr_students, "cmp_total");
    return $arr_students; 
}

function get_grade($score)
{
    $grades = array('F','C-','LC','C','HC','C+','B','B+','A'); 

    if($score < 40){
        return $grades[0];
    }elseif($score < 50){
        return $grades[1];
    }elseif($score < 55){
        return $grades[2];
    }elseif($score < 60){
        return $grades[3];
    }elseif($score < 65){
        return $grades[4];
    }elseif($score < 70){
        return $grades[5];
    }elseif($score < 75){
        return $grades[6];
    }elseif($score < 85){
        return $grades[7];
    }else{
        return $grades[8];
    }

}

# Function to represent a number like '2nd', '10th', '101st' etc
function get_position($n)
{
    # Array holding the teen numbers. If the last 2 numbers of $n are in this array, then we'll add 'th' to the end of $n
    $teen_array = array(11, 12, 13, 14, 15, 16, 17, 18, 19);
   
    # Array holding all the single digit numbers. If the last number of $n, or if $n itself, is a key in this array, then we'll add that key's value to the end of $n
    $single_array = array(1 => 'st', 2 => 'nd', 3 => 'rd', 4 => 'th', 5 => 'th', 6 => 'th', 7 => 'th', 8 => 'th', 9 => 'th', 0 => 'th');
   
    # Store the last 2 digits of $n in order to check if it's a teen number.
    $if_teen = substr($n, -2, 2);
   
    # Store the last digit of $n in order to check if it's a teen number. If $n is a single digit, $single will simply equal $n.
    $single = substr($n, -1, 1);
   
    # If $if_teen is in array $teen_array, store $n with 'th' concantenated onto the end of it into $new_n
    if ( in_array($if_teen, $teen_array) )
    {
        $new_n = $n . 'th';
    }
    # $n is not a teen, so concant the appropriate value of it's $single_array key onto the end of $n and save it into $new_n
    elseif ( $single_array[$single] )
    {
        $new_n = $n . $single_array[$single];   
    }
   
    # Return new
    return $new_n;
}

function latest(){
    return 15;
}

?>
