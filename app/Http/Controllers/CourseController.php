<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function __construct()
	{
        
	}

    public function courses(Request $request)
    {
        $courses = Course::orderBy('name', 'asc')->get();
        return view('course.courses',['courses' => $courses]);
    }

    public function course(Request $request, $id=0)
    {
       
        if(!$id){
            return redirect('courses');
        }

        $course = Course::find($id);        
        return view('course.course',['course' => $course]);
    }

    public function addCourse(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $course = new Course;
        $course->name = $request->name;
        $course->save();
        return redirect('courses?added');
    }

    public function editCourse(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        
        $id = $request->id;
        $course = Course::find($id); 
        $course->name = $request->name;
        $course->exercise_enrollment = $request->exercise_enrollment;
        $course->current_term_id = $request->current_term;
        $course->current_session_id = $request->current_session;
        $course->reg_start_at = $request->reg_start;
        $course->reg_end_at = $request->reg_end;
        $course->save();
        return redirect("course/$id/?sucess");
    }

    public function deleteCourse(Request $request, $id)
    {
        $course = Course::find($id);
        $course->delete();
        return redirect('courses?deleted');        
    }

}
