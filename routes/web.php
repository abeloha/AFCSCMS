<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SessionAndTermController;
use App\Http\Controllers\DeptController;
use App\Http\Controllers\SyndicateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\FormController;
use App\Models\Div;
use App\Models\Syndicate;
use Illuminate\Support\Facades\DB;

use App\Functions\Event;
use App\Http\Controllers\AppConfigController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
Route::get('/', function () {
    return view('index');
});
*/
//Route::get('user/{id}', [UserController::class, 'show']);

Route::get('/', [LandingController::class, 'index']);

Route::get('login', [LandingController::class, 'login'])->name('login'); //Named route required for authentication
Route::post('login', [LandingController::class, 'authenticate']);

Route::get('logout', [LandingController::class, 'logout']);

Route::get('register', [LandingController::class, 'register']);
Route::post('register', [LandingController::class, 'create']);

Route::get('approve', [LandingController::class, 'approve']);
Route::get('deactivate', [LandingController::class, 'deactivate']);
Route::get('disabled', [LandingController::class, 'disabled']);

Route::middleware(['auth'])->group(function () {

    Route::prefix('user')->group(function () {

        Route::get('/dashboard', [UserController::class, 'dashboard'])->middleware(['approve','active','currentstudent']);     

        Route::get('/edit/{id?}', [UserController::class, 'edit']);
        Route::post('/edit', [UserController::class, 'editExcecute']);  
        Route::get('/approve/{id}', [UserController::class, 'approve']); 

        Route::get('/changepicture/{id}', [UserController::class, 'changepicture']); 
        Route::post('/changepicture', [UserController::class, 'changepictureExc']);  

        Route::get('/password/edit/{id?}', [UserController::class, 'passwordEdit']);
        Route::post('/password/change', [UserController::class, 'passwordEditExc']);
        Route::get('/password/reset/{id}', [UserController::class, 'passwordResetExc'])->middleware('ability:reset_user_password');

        Route::get('/staff', [UserController::class, 'staff'])->middleware('ability:view_staff'); 
        Route::get('/student', [UserController::class, 'student'])->middleware('ability:view_students');
        Route::get('/new', [UserController::class, 'new'])->middleware('ability:approve_user');

        Route::get('/deactivated', [UserController::class, 'deactivated'])->middleware('ability:delete_or_deactivate_user');

        
        Route::get('/{id}/delete', [UserController::class, 'delete'])->middleware('ability:delete_or_deactivate_user');
        Route::get('/{id}/deactivate', [UserController::class, 'deactivate'])->middleware('ability:delete_or_deactivate_user');

        //view older list of students
        Route::middleware('ability:view_students')->group(function () {
            Route::get('/sessions', [UserController::class, 'sessions']);
            Route::get('/archive', [UserController::class, 'archive']);
        });

        Route::get('/{id?}', [UserController::class, 'index']);
    });


    Route::prefix('mail')->middleware(['approve','active','currentstudent'])->group(function () {
        Route::get('/', [MailController::class, 'index']);

        Route::get('/inbox', [MailController::class, 'index']);
        Route::post('/inbox', [MailController::class, 'searchInbox']);//search

        Route::get('sent', [MailController::class, 'sent']);
        Route::post('/sent', [MailController::class, 'searchSent']);//search

        Route::get('view/{id}', [MailController::class, 'view']); 
        Route::get('delete/{id}', [MailController::class, 'delete']); 

        Route::get('compose', [MailController::class, 'compose']); 
        Route::post('send', [MailController::class, 'send']); 

    });

});

Route::middleware(['auth', 'approve', 'active', 'currentstudent'])->group(function () {

    Route::prefix('admin')->middleware('role:15')->group(function () {
        
        Route::get('/', [AdminController::class, 'index']);

        Route::get('decode', [AdminController::class, 'decode']); 

        Route::get('test', [AdminController::class, 'test']);
    });
    
    Route::get('/student', function () {
        return 'approved student';
    });

    Route::get('/staff', function () {
        return 'approved staff';
    });

    Route::prefix('exercise')->group(function () {

        Route::get('/', [ExerciseController::class, 'index']);

        Route::get('enroll', [ExerciseController::class, 'enroll']);
        Route::post('enroll/add', [ExerciseController::class, 'enrollAdd']);//exc
        Route::post('enroll/remove', [ExerciseController::class, 'enrollRemove']);//exc

        Route::get('list/', [ExerciseController::class, 'list'])->middleware('ability:is_academic');
        
        Route::middleware(['ability:manage_exercise'])->group(function () {
            Route::post('add', [ExerciseController::class, 'add']); 
            Route::get('{id}/delete', [ExerciseController::class, 'delete']);
            Route::get('{id}/edit', [ExerciseController::class, 'edit']);
            Route::post('edit', [ExerciseController::class, 'editExc']);
        });
    
        
        Route::get('{id}/material', [ExerciseController::class, 'addMaterial']);
        Route::get('material/{id}', [ExerciseController::class, 'materialDetails']);
        Route::get('material/{id}/delete', [ExerciseController::class, 'deleteMaterial']);
        Route::post('material', [ExerciseController::class, 'addMaterialExc']);

        
        Route::get('{id}/assigngraderlist', [ExerciseController::class, 'assignGraderList']); //Who is assigned to who to grade
        Route::get('{id}/assigngrader', [ExerciseController::class, 'assignGrader']); //assign who to grade
        Route::post('assigngrader', [ExerciseController::class, 'assignGraderExc']); //assign who to grade

        Route::get('requirement/{id}', [ExerciseController::class, 'requirementDetails']);
        Route::get('requirement/{id}/grade', [ExerciseController::class, 'requirementGrade']);
        Route::get('requirement/{id}/grade/utw', [ExerciseController::class, 'requirementUtw']);

        Route::get('{id}/grade/written', [ExerciseController::class, 'graderSummary']); //grade written req
        Route::get('{id}/grade', [ExerciseController::class, 'graderSummary']);
        Route::get('{id}/submissions', [ExerciseController::class, 'studentSubmissions']);
        Route::get('submission/{id}', [ExerciseController::class, 'studentRequirementSubmission']);
        Route::post('submission/grade', [ExerciseController::class, 'submissionGradeExc']);

        Route::post('requirement/grade/utw', [ExerciseController::class, 'requirementUtwGrade']);

        Route::post('requirement/submission', [ExerciseController::class, 'requirementSubmissionExc']);

        Route::get('{id}/requirement/add', [ExerciseController::class, 'addRequirement']);
        Route::post('requirement/add', [ExerciseController::class, 'addRequirementExc']);

        Route::get('requirement/view/{id}', [ExerciseController::class, 'addRequirement']);


        Route::get('/{id}', [ExerciseController::class, 'exerciseDetails']);        
        
    });

    //love letter
    Route::get('loveletters', [ExerciseController::class, 'loveletters']);

    //forms
    Route::get('forms', [FormController::class, 'index']);
    Route::get('forms/{id}/delete', [FormController::class, 'delete'])->middleware('ability:manage_forms');
    Route::post('forms', [FormController::class, 'add'])->middleware('ability:manage_forms');

    //syndicate
    Route::get('syndicates/{id?}', [SyndicateController::class, 'syndicates']);    
    Route::middleware(['ability:manage_syndicate'])->group(function () {        

        Route::get('syndicate/{id?}', [SyndicateController::class, 'syndicate']);//editing        
        Route::get('syndicate/{id}/assign', [SyndicateController::class, 'assignStudent']);

        
        Route::post('syndicate/assign/add', [SyndicateController::class, 'assignStudentAdd']);//exc
        Route::post('syndicate/assign/remove', [SyndicateController::class, 'assignStudentRemove']);//exc


        Route::get('syndicate/{id}/delete', [SyndicateController::class, 'deleteSyndicate']);

        Route::post('syndicate/edit', [SyndicateController::class, 'editSyndicate']);
        Route::post('syndicate', [SyndicateController::class, 'addSyndicate']);
    });

    //dept and div
    Route::middleware(['ability:manage_dept_and_div'])->group(function () {
        Route::get('depts/{id?}', [DeptController::class, 'depts']);
        Route::get('dept/{id?}', [DeptController::class, 'dept']);
        Route::post('dept', [DeptController::class, 'addDept']);
        Route::get('dept/{id}/delete', [DeptController::class, 'deleteDept']);
        Route::post('dept/edit', [DeptController::class, 'editDept']);

        Route::get('div/{id?}', [DeptController::class, 'div']);
        Route::post('div', [DeptController::class, 'addDiv']);
        Route::post('div/edit', [DeptController::class, 'editDiv']);
        Route::get('div/{id}/delete', [DeptController::class, 'deleteDiv']);
    });

    //term, session and course
    Route::middleware(['ability:can_manage_term_session_and_course'])->group(function () {
        Route::get('terms', [SessionAndTermController::class, 'terms']);
        Route::get('term/{id}/delete', [SessionAndTermController::class, 'deleteTerm']);
        Route::post('term', [SessionAndTermController::class, 'addTerm']);
        
        Route::get('sessions', [SessionAndTermController::class, 'sessions']);
        Route::get('session/{id}/delete', [SessionAndTermController::class, 'deleteSession']);
        Route::post('session', [SessionAndTermController::class, 'addSession']);

        Route::get('courses', [CourseController::class, 'courses']);		
        Route::get('course/{id?}', [CourseController::class, 'course']);
        Route::get('course/{id}/delete', [CourseController::class, 'deleteCourse']);	
        Route::post('course', [CourseController::class, 'addCourse']);
        Route::post('course/edit', [CourseController::class, 'editCourse']);
    });

    //results
    Route::middleware(['ability:is_academic'])->group(function () {
        
        Route::prefix('result')->group(function () {
            Route::get('/', [ResultController::class, 'index']);

            Route::get('session/{id}', [ResultController::class, 'session']);
            
            Route::get('overview', [ResultController::class, 'resultOverview']);//overview of session results

            Route::get('/show', [ResultController::class, 'show']); //for dept or div results
            Route::post('/moderate', [ResultController::class, 'moderateResult']); //for moderating dept result in a term

            Route::get('processing', [ResultController::class, 'processing']);

            Route::post('processing/submit/tc', [ResultController::class, 'submitTC']);//tc submit to CI
            Route::post('processing/submit/ci', [ResultController::class, 'submitCI']);//CI submit to director
            Route::post('processing/submit/dr', [ResultController::class, 'submitDr']);//DR submit to Comdt
            Route::post('processing/submit/comdt', [ResultController::class, 'submitComdt']);//Comdt approving result

            Route::get('exercise/{id}', [ResultController::class, 'exercise']);
            Route::post('exercise/moderate', [ResultController::class, 'moderateExercise']);

            Route::get('exercise/{id}/submitgradebook', [ResultController::class, 'submitGradeBook']);

            Route::get('student/{id}/exercise/{ex}', [ResultController::class, 'studentExercise']);
        });

    });

    //events
    Route::middleware(['ability:manage_event'])->group(function () {
        Route::get('events', [EventController::class, 'index']);

        Route::get('event/{id}/delete', [EventController::class, 'delete']);

        Route::post('event/add', [EventController::class, 'add']);

    });

    //configuration
    Route::get('configuration', [AppConfigController::class, 'index'])->middleware('ability:can_manage_config');
    Route::post('configuration', [AppConfigController::class, 'submit'])->middleware('ability:can_manage_config');

});

Route::prefix('ajax')->group(function () {
    //Route::get('term/{course}', [AjaxController::class, 'index']);
    
    Route::get('/users', function(){
        //for mail
        $data = DB::select('select id, surname, first_name, other_name, email from users WHERE 	approved = 1 AND deactivated = 0 AND deleted_at is NULL');
        echo json_encode($data);
    });

    Route::get('/events', function(){

        //require dirname(__FILE__) . '/utils.php';

        $var_start = $_GET['start'];
        $var_end = $_GET['end'];

        $range_start = parseDateTime($var_start);
        $range_end = parseDateTime($var_end);

        $input_arrays = get_events($var_start, $var_end);
        $time_zone = null;
        // Accumulate an output array of event data arrays.
        $output_arrays = array();

        if($input_arrays){
            foreach ($input_arrays as $array) {
                // Convert the input array into a useful Event object
                $event = new Event($array, $time_zone);
                // If the event is in-bounds, add it to the output
                if ($event->isWithinDayRange($range_start, $range_end)) {
                    $output_arrays[] = $event->toArray();
                }
            }
        }
        
        // Send JSON to the client.
        echo json_encode($output_arrays);

    });

    Route::get('/events1', function(){

        //require dirname(__FILE__) . '/utils.php';
        $var_start = $_GET['start'];
        $var_end = $_GET['start'];

        $range_start = parseDateTime($var_start);
        $range_end = parseDateTime($var_end);

        

        return var_dump(get_events($var_start, $var_end));

    });

    Route::get('term/{course}', function ($course) {
       $term = get_term_by_course($course);   
        if($term){
            $data = '<option value="">Select</option>';
            foreach ($term as $item){
                $data .= '<option value="'.$item->id.'">'.$item->name.'</option>';
            }
            return $data;
        }
        return '<option value="">No term for this course</option>';
    });

    Route::get('releasedresult/term/{dept}', function ($dept) {
        $term = get_realeased_result_terms_by_dept($dept);   
         if($term){
             $data = '';
             foreach ($term as $item){
                 $data .= '<option value="'.$item->id.'">'.$item->name.'</option>';
             }
             $data .= '<option value="0">All Terms</option>';
             return $data;
         }
         return '<option value="0">All Terms</option>';
    });

    Route::get('releasedresultdiv/term/{div}', function ($div) {
        $div = Div::find($div);

        if(!$div){
            return '<option value="">Select Division</option>';
        }

        $term = get_realeased_result_terms_by_dept($div->dept_id);   
         if($term){
            $data = '';
            foreach ($term as $item){
                $data .= '<option value="'.$item->id.'">'.$item->name.'</option>';
            }
            return $data;
        }

        return '<option value="">Select Division</option>';
    });

    Route::post('check/requirement/dates', [AjaxController::class, 'check_requirement_dates']);//returns ajax
    Route::get('check/requirement/dates', [AjaxController::class, 'check_requirement_dates']);

});