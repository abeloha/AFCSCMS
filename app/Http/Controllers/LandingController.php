<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{

    public function index(Request $request)
    {
        
        if (Auth::check()) {
            $url_prefix = 'user/dashboard'; //get_user_route_prefix();
            
            //if ($url_prefix){
            $msg = '';
            if(isset($_GET['unauthorised'])){
                $msg = 'unauthorised='.$_GET['unauthorised'];
            }
            return redirect($url_prefix."?$msg");
            //}
        }
            
        return redirect ('/login?continue');
    }

    public function login(Request $request)
    {
        return view('landing.login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/');
    }

    public function authenticate(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
		
        $credentials = $request->only('email', 'password');
        $remember = false;
        if (Auth::attempt($credentials, $remember)) {
            return redirect('/');
        }else{
            return redirect('login?failed');
        }
    }

    public function register(Request $request)
    {
        

        $options = array();

        $dtype = $request->t; //data from url
        $dcourse = $request->c; //data from url

        $type='';
        $course = '';
        $show_reg =false;

        $options = array();

        //set $type and course only when registration for them is open and set show_reg as true
        if (check_reg_open_for_staff()){
            $data = array('t'=>'aut','c'=>'', 'n'=>'Register As A staff');
            array_push($options, $data);
            
            if($dtype == 'aut'){
                $type = 'Staff';
                $show_reg = true;
            }

        }

        if(!$show_reg){
            $courses = get_courses_open_for_reg();

            if($courses){

                    if($dtype == 'std'){
                        $type = 'Student';
                    }

                foreach($courses as $c){
                        $data = array('t'=>'std','c'=>$c->id, 'n'=>'Register As '.$c->name.' Student');
                        array_push($options, $data);

                        if($type == 'Student'){
                            if($dcourse == $c->id){
                                $course = $c->id;
                                $show_reg=true;
                                break;
                            }
                        }

                    }
            }
        }

       if($show_reg){
            return view('landing.register', ['type'=>$type, 'course'=>$course]);
        }
               
        //return var_dump($options);
        return view('landing.register-type', ['options'=>$options]);
    }

    public function create(Request $request)
    {
               
        $validatedData = $request->validate([
            'email' => 'required|unique:users',
            'surname' => 'required',
            'first_name' => 'required',
            'role' => 'required',
            'password' => 'required|confirmed',
        ]);

        $user = new User;
        $user->first_name = $request->first_name;
        $user->surname = $request->surname;
        $user->other_name = $request->other_name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->dept_id = $request->dept;
        $user->course_id = $request->course;
        $user->session_id = $request->session;
        $user->password = bcrypt($request->password);
        $user->save();
        return redirect('login?new');
    }

    public function approve(Request $request)
    {
        if (Auth::check()) {
            $approved = Auth::user()->approved;            
            if(!$approved){
                return view('landing.approve');
            }            
        }
        return redirect('/');        
    }

    public function deactivate(Request $request)
    {
        if (Auth::check()) {
            $deactivated = Auth::user()->deactivated;            
            if($deactivated){
                return view('landing.deactivate');
            }            
        }
        return redirect('/');        
    }

    public function disabled(Request $request)
    {
        $is_current_student = is_current_student();

        if(!$is_current_student){
            return view('landing.disabled');
        }

        return redirect('/');        
    }
	
}
