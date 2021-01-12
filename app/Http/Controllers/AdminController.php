<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Dept;
use App\Models\Exercise;
use App\Models\ExerciseEnrollment;
use App\Models\ResultAddedTermWp;
use App\Models\Session;
use App\Models\Syndicate;
use App\Models\SyndicateEnrollment;

use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{

    public function __construct()
	{
        
	}
    

    public function index(Request $request)
    {
        return view('admin.index');
    }



    public function decode(){
        return view('admin.decode');      
    }

    public function test(){

        echo var_dump (add_result_statistics_to_student(13,2,4));


        $wp_added_to_student = ResultAddedTermWp::where('user_id', 13)
            ->sum('wp');

        echo 'Test';

        echo var_dump($wp_added_to_student);
        

        return '<br><hr>hello'; 

        return is_exercise_moderating_open(3);
        //return bcrypt('admin@afcsc.mil.ng');      
    }

    public function testbycript(){
        return bcrypt('admin@afcsc.mil.ng');      
    }
    
}
