<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppForm;

class FormController extends Controller
{
    public function index(){
        $record = AppForm::all();
        return view('form/list',['collection'=>$record]);
    }
    
    public function add(Request $request){

        $validatedData = $request->validate([
            'title' => 'required|unique:forms,name',
            'file' => 'required',
        ]);

        $file_name = '';
        $destination = 'public/form';

        if ($request->hasFile('file')){
            $extension = $request->file->extension();
            $file_name = $request->title.'.'.$extension;
            $path = $request->file->storeAs($destination, $file_name);
        }

        if(!$path){
            return redirect('forms/?err=failed to upload file');
        }

        $record = new AppForm();
        $record->name = $request->title;
        $record->description = $request->description;
        $record->file = $file_name;
        $record->save();
        return redirect('forms/?added');
    }

    public function delete(Request $request, $id)
    {
        $record = AppForm::find($id);
        $record->delete();
        return redirect('forms?deleted');        
    }

}
