<?php

namespace App\Http\Controllers;

use App\Models\AppConfig;
use Illuminate\Http\Request;

class AppConfigController extends Controller
{
    public function index(){
        return view('config.index');
    }

    public function submit(Request $request){

        $batch_save_data = array(
            
            'app_name' => $request->app_name,
            'app_full_name' => $request->app_full_name,

            'show_love_letter_to_students' => $request->show_love_letter_to_students,
            'open_staff_registration' => $request->open_staff_registration,

			'manage_exercise' => implode('|', $request->manage_exercise),
			'view_all_exercise' => implode('|', $request->view_all_exercise),
            'approve_user' => implode('|', $request->approve_user),
            'delete_or_deactivate_user' => implode('|', $request->delete_or_deactivate_user),
			'reset_user_password' => implode('|', $request->reset_user_password),
            'view_students' => implode('|', $request->view_students),
            'view_staff' => implode('|', $request->view_staff),

            'can_edit_user' => implode('|', $request->can_edit_user),
            'can_change_user_picture' => implode('|', $request->can_change_user_picture),
            'can_change_user_role' => implode('|', $request->can_change_user_role),
            'can_view_user_detailed_profile' => implode('|', $request->can_view_user_detailed_profile),

            'manage_syndicate' => implode('|', $request->manage_syndicate),
            'manage_dept_and_div' => implode('|', $request->manage_dept_and_div),
            'manage_event' => implode('|', $request->manage_event),
            'manage_forms' => implode('|', $request->manage_forms),
            'manage_term_session_and_course' => implode('|', $request->manage_term_session_and_course),
            'always_see_identity' => implode('|', $request->always_see_identity),
        );

        return $this->save($batch_save_data);
        
    }

    private function save($data){
        foreach($data as $key=>$value)
        {
            $record = AppConfig::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
        return redirect('configuration?success');        
    }

}
