<?php

namespace App\Http\Controllers;

use App\Models\Dept;
use App\Models\Session;
use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;

class UserController extends Controller
{

    public function dashboard(Request $request)
    {
        return view('user.dashboard');      
    }
    
    public function index(Request $request, $id = 0)
    {
        
        if(!$id){
            $id = get_user_id();
        }

        $data = User::find($id); 
        return view('user.profile',['data' => $data]);      
    }

    public function edit(Request $request, $id = 0)
    {

        $my_user_id = get_user_id();

        if(!$id){
            $id = $my_user_id;
        }

        if($id != $my_user_id){
            if(!check_can_edit_user_profile()){
                return redirect("user/$id/?unauthorised");
            }
        }

        $data = User::find($id);        
        return view('user.edit',['data' => $data]);    
    }

    public function editExcecute(Request $request)
    {

        $validatedData = $request->validate([            
            'surname' => 'required',
            'first_name' => 'required',
        ]);

        $id = $request->user_id;

        Validator::make($request->all(), [
            'svc_no' => [
                'required',
                Rule::unique('users')->ignore($id),
            ],
        ])->validate();
        
        $id = $request->user_id;

        
        $user = User::find($id);
        $user->surname = $request->surname;
        $user->first_name = $request->first_name;
        $user->other_name = $request->other_name;
        $user->phone = $request->phone;
        
        //test for admin actions
        if($request->approve){
            $user->approved = 1;
        }

        if($request->dept){
            $user->dept_id = $request->dept;
        }

        if($request->course){
            $user->course_id = $request->course;
        }

        if($request->role){
            $user->role = $request->role;
        }
        
        //end test

        $user->rank = $request->rank;
        $user->svc_no = $request->svc_no;
        $user->service = $request->service;
        $user->corps = $request->corps;
        $user->branch = $request->branch;
        $user->specialty = $request->specialty;
        $user->commission = $request->commission;
        $user->sex = $request->sex;
        $user->country = $request->country;
        $user->account = $request->account;
        $user->bank = $request->bank;
        $user->last_unit_1 = $request->last_unit_1;
        $user->last_appointment_1 = $request->last_appointment_1;
        $user->last_unit_2 = $request->last_unit_2;
        $user->last_appointment_2 = $request->last_appointment_2;
        
        $user->save();
        return redirect("user/edit/$id/?success");

    }

    public function approve(Request $request, $id)
    {
        if(!check_can_approve_user_profile()){
            return redirect("user/$id/?unauthorised=can_not_apporve");
        }

        $user = User::find($id);
        $user->approved = 1;
        $user->save();       
        return redirect("user/$id/?success");   
    }

    public function changepicture(Request $request, $id)
    {
        if(!check_can_change_user_picture()){
            return redirect("user/$id/?unauthorised");
        }

        $data = User::find($id); 
        return view('user.change-picture',['data' => $data]);   
    }

    public function changepictureExc(Request $request)
    {

        $validatedData = $request->validate([            
            'picture' => 'required',
        ]);
        
        $id = $request->user_id; 

        $ext_array = array("jpeg","jpg","bmp","ico","png","JPEG","JPG","BMP","PNG","ICO");
        
        $extension = $request->picture->extension();
        
        if (in_array($extension,$ext_array)){

            $file_name = $id.'_'.time().'.'.$extension;

            $destination = 'public/user';

            $path = $request->picture->storeAs($destination, $file_name);
        
            $user = User::find($id);
            $user->picture = $file_name;
            $user->save();
            return redirect("user/changepicture/$id/?success");
        }else{
            return redirect("user/changepicture/$id/?failed=unsupported image file");
        }
        

    }

    public function passwordEdit(Request $request, $id = 0)
    {

        $my_user_id = get_user_id();

        if(!$id){
            $id = $my_user_id;
        }

        if($id != $my_user_id){
            if(!check_can_edit_user_profile()){
                return redirect("user/$id/?unauthorised");
            }
        }

        $data = User::find($id);        
        return view('user.change-password',['data' => $data]);    
    }

    public function passwordEditExc(Request $request)
    {

        $validatedData = $request->validate([            
            'password' => 'required|confirmed',
            'current_password' => 'required',
        ]);

        $err = '';
        
        $id = $request->user_id; 

        $record = User::find($id);

        if($record){
            $chk = password_verify($request->current_password, $record->password);
            if($chk){

                $record->password = bcrypt($request->password);
                $record->save();

                return redirect("user/password/edit/$id/?success"); 
                
            }else{
                $err = 'The current password you entered is incorrect.';
            }
        }else{
            $err = 'Account details not found at this time';
        }

        return redirect("user/password/edit/$id/?error=$err");

    }

    public function passwordResetExc(Request $request,  $id)
    {
      
        $record = User::find($id);

        if($record){
            $surname = strtolower($record->surname);
            //perform action;
            if($surname){
                $record->password = bcrypt($surname);
                $record->save();
                return redirect("user/edit/$id/?preset");
            }

            $err = 'User account does not have surname';           

        }else{
            $err = 'Account details not found at this time';
        }
        return redirect("user/edit/$id/?error=$err");

    }

    public function staff(Request $request)
    {  
        $data = get_staff(); 
        return view('user.list',['collection' => $data, 'header_title'=>'Staff Users', 'is_student_list'=>0]);    
    }

    public function student(Request $request)
    {   
        $header_title = 'List of Students';

        $dept = 0;
        $course = 0;
        $div = 0;
        $syndicate = 0;

        if(isset($_GET['dept'])){
            if(is_numeric($_GET['dept'])){
                $dept = $_GET['dept'] + 0;
            }
        }
        if(isset($_GET['course'])){
            if(is_numeric($_GET['course'])){
                $course = $_GET['course'] + 0;
            }
        }
        if(isset($_GET['div'])){
            if(is_numeric($_GET['div'])){
                $div = $_GET['div'] + 0;
            }
        }
        if(isset($_GET['syndicate'])){
            if(is_numeric($_GET['syndicate'])){
                $syndicate = $_GET['syndicate'] + 0;
            }
        }

        if($div){

            $header_title = 'List of Students in Div';
            $data = get_students_by_div($div);
            $div_data = get_div($div);
            if($div_data){
                $header_title .= ': '.$div_data->name;
            }

        }elseif($syndicate){

            $header_title = 'List of Students in Syndicate';
            $data = get_students_by_syndicate($syndicate);
            $syndicate_data = get_syndicate($syndicate);
            if($syndicate_data){
                $header_title .= ': '.$syndicate_data->name;
            }

        }else{
            $data = get_students_by_dept_and_course($dept, $course);
            if($dept){
                $dept_data = get_dept($dept);
                if($dept_data){ 
                    $header_title .= " - Department: $dept_data->name";
                }
            }
            if($course){
                $course_data = get_course($course);
                if($course_data){ 
                    $header_title .= " - Course: $course_data->name";
                }
            }
        }
          
        return view('user.list',['collection' => $data, 'header_title'=>$header_title, 'is_student_list'=>1]);    
    }

    public function new(Request $request)
    {  
        $type = $request->type;

        if($type == 'staff'){
            $data = get_staff('unapproved'); 
            return view('user.list',['collection' => $data, 'header_title'=>'New Staff Registrations', 'is_student_list'=>0]);    
        }

        $data = get_student('unapproved'); 
        return view('user.list',['collection' => $data, 'header_title'=>'New Student Registrations', 'is_student_list'=>1]);    
        
    }

    public function deactivated(Request $request)
    {  
        $type = $request->type;

        if($type == 'staff'){
            $data = get_staff('deactivated'); 
            return view('user.list',['collection' => $data, 'header_title'=>'Deactivated Staff Accounts', 'is_student_list'=>0]);    
        }

        $data = get_student('deactivated');         
        return view('user.list',['collection' => $data, 'header_title'=>'Deactivated Student Accounts', 'is_student_list'=>1]);    
        
    }

    public function delete(Request $request, $id)
    {  
        $user = User::find($id);
        if($user){
            $user->delete();
            return view('user.deleted');
        }
        return redirect("user/$id/?unauthorised=user_not_found");
    }

    public function deactivate(Request $request, $id)
    {  
        $value = 0;
        $msg = '';

        if($request->value == 'activate'){
            $value = 0;
            $msg = 'User account has been activated';
        }elseif($request->value == 'deactivate'){
            $value = 1;
            $msg = 'User account has been deactivated';
        }else{
            return redirect("user/$id/?unauthorised=parameter_not_determined");
        }

        $user = User::find($id);
        if($user){
            $user->deactivated = $value;
            $user->save();
            return redirect("user/edit/$id/?msg=$msg");
        }
        return redirect("user/$id/?unauthorised=user_not_found");
    }

    public function sessions(Request $request)
    {  
        return view('user.sessions');
    }

    public function archive(Request $request)
    {  
        
        //test session?
        
        $session = Session::find($request->session);

        if(!$session){
            return redirect('user/sessions');
        }
        
        $header_title = "$session->name Students";        
        $dept_id = $request->dept;
        $dept = Dept::find($dept_id);
        if($dept){            
            $header_title .= " - Department: $dept->name";
        }

        $data = get_students_by_session($session->id, $dept_id);
            
        return view('user.list',['collection' => $data, 'header_title'=>$header_title, 'is_student_list'=>1]);

    }

}
