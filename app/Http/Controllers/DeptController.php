<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dept;
use App\Models\Div;

class DeptController extends Controller
{
    public function __construct()
	{
        
    }
    
    public function depts(Request $request, $id=0)
    {
        $depts = Dept::orderBy('name', 'asc')->get();
        
        $dept_id = 0;
        $dept_name = '';

        if($id){
            $dept = Dept::find($id);
            if($dept){
                $dept_id = $dept->id;
                $dept_name = $dept->name;
            }
        }
        
        return view('dept.depts',['depts' => $depts, 'dept_id' => $dept_id, 'dept_name' => $dept_name]);
    }

    public function dept(Request $request, $id=0)
    {
        
        if(!$id){
            return redirect('depts');
        }

        $dept = Dept::find($id);      
        return view('dept.dept',['data' => $dept]);
    }

    public function addDept(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $is_joint = 0;
        if($request->is_joint){
            $is_joint = 1;
        }

        $dept = new Dept;
        $dept->name = $request->name;
        $dept->is_joint = $is_joint;
        $dept->save();
        return redirect("depts?added");
    }

    public function editDept(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'director' => 'required',
        ]);

        $is_joint = 0;
        if($request->is_joint){
            $is_joint = 1;
        }

        $id = $request->id;
        $dept = Dept::find($id); 
        $dept->name = $request->name;
        $dept->director_user_id = $request->director;
        $dept->is_joint = $is_joint;
        $dept->save();
        return redirect("dept/$id/?sucess");
    }

    public function deleteDept(Request $request, $id)
    {
        $dept = Dept::find($id);
        $dept->delete();
        return redirect("depts?deleted");        
    }

    //divs
    public function addDiv(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'course' => 'required',
            'dept' => 'required',
        ]);

        $dept_id = $request->dept;

        $div = new Div;
        $div->name = $request->name;
        $div->dept_id = $request->dept;
        $div->course_id = $request->course;
        $div->save();
        return redirect('/depts/'.$dept_id.'?div-added');
    }

    public function div(Request $request, $id=0)
    {
        $div = Div::find($id);   
        if($div){
            return view('dept.div',['data' => $div]);
        }  
        return redirect("depts");
    }

    public function editDiv(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'course' => 'required',
            'dept' => 'required',
            'ci' => 'required',
            'tc' => 'required',
        ]);
        
        $id = $request->id;
        $div = Div::find($id); 
        $div->name = $request->name;
        $div->dept_id = $request->dept;
        $div->course_id = $request->course;
        $div->ci_user_id = $request->ci;
        $div->tc_user_id = $request->tc;
        $div->save();
        return redirect("div/$id/?sucess");
    }

    public function deleteDiv(Request $request, $id)
    {
        $div = Div::find($id);
        $dept_id = $div->dept_id;
        $div->delete();
        return redirect('/depts/'.$dept_id.'?div-deleted');        
    }
}
