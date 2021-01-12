<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Session;
use App\Models\Term;
class SessionAndTermController extends Controller
{

    public function __construct()
	{
       
    }

    public function terms(Request $request)
    {
        $terms = Term::orderBy('course_id', 'asc')->get();
        return view('session.terms',['terms' => $terms]);
    }

    public function addTerm(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'course' => 'required',
        ]);
        
        $is_joint = 0;
        if($request->is_joint){
            $is_joint = 1;
        }

        $term = new Term;
        $term->name = $request->name;
        $term->course_id = $request->course;
        $term->is_joint = $is_joint;
        $term->save();
        return redirect("terms?added");
    }

    public function deleteTerm(Request $request, $id)
    {
        $term = Term::find($id);
        $term->delete();
        return redirect("terms?deleted");
    }


    public function sessions(Request $request)
    {
        $sessions = Session::orderBy('id', 'desc')->get();
        return view('session.sessions',['sessions' => $sessions]);
    }

    public function addSession(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'course' => 'required',
        ]);

        $session = new Session;
        $session->name = $request->name;
        $session->course_id = $request->course;
        $session->save();
        return redirect("sessions?added");
    }

    public function deleteSession(Request $request, $id)
    {
        $session = Session::find($id);
        $session->delete();
        return redirect("sessions?deleted");
    }

}
