<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\MessageFile;
use App\Models\MessageRecipient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MailController extends Controller
{
    public function index(Request $request)
    {
        $user_id = get_user_id();

        $messages = DB::table('messages')
            ->join('message_recipients', 'messages.id', '=', 'message_recipients.message_id')
            ->join('users', 'users.id', '=', 'messages.sender_id')
            ->select('messages.id', 'messages.sender_id', 'messages.subject','messages.created_at','message_recipients.is_read', 'users.surname', 'users.first_name', 'users.other_name', 'users.email')            
            ->where('message_recipients.recipient_id', $user_id)
            ->where('message_recipients.recipient_delete',0)
            ->orderBy('messages.id', 'desc')
            ->get();

        return $this->showInbox($messages);
    }

    public function searchInbox(Request $request)
    {
        $user_id = get_user_id();
        $s = $request->s;
        $messages = DB::table('messages')
            ->join('message_recipients', 'messages.id', '=', 'message_recipients.message_id')
            ->join('users', 'users.id', '=', 'messages.sender_id')
            ->select('messages.id', 'messages.sender_id', 'messages.subject','messages.created_at','message_recipients.is_read', 'users.surname', 'users.first_name', 'users.other_name', 'users.email')            
            ->where('message_recipients.recipient_id', $user_id)
            ->where('message_recipients.recipient_delete',0)
            ->where(function($query) use($s){
                $query->where('messages.subject','like', '%'.$s.'%')
                        ->orWhere('messages.message','like', '%'.$s.'%')
                        ->orWhere('users.surname','like', '%'.$s.'%')
                        ->orWhere('users.first_name','like', '%'.$s.'%')
                        ->orWhere('users.email','like', '%'.$s.'%');
            })
            ->orderBy('messages.id', 'desc')
            ->get();

        return $this->showInbox($messages, $s);
    }

    private function showInbox($messages, $s=''){
        return view('mail.inbox',['messages'=>$messages,'search'=>$s]);
    }

    public function sent(Request $request)
    {
        $user_id = get_user_id();

        $messages = DB::table('messages')
            ->select('messages.id', 'messages.sender_id', 'messages.subject','messages.created_at')            
            ->where('messages.sender_id', $user_id)
            ->where('messages.sender_delete',0)
            ->orderBy('messages.id', 'desc')
            ->get();

        return $this->showSent($messages);
    }

    public function searchSent(Request $request)
    {
        $user_id = get_user_id();
        $s = $request->s;

        $messages = DB::table('messages')
            ->select('messages.id', 'messages.sender_id', 'messages.subject','messages.created_at')            
            ->where('messages.sender_id', $user_id)
            ->where('messages.sender_delete',0)
            ->where(function($query) use($s){
                $query->where('messages.subject','like', '%'.$s.'%')
                        ->orWhere('messages.message','like', '%'.$s.'%');
            })
            ->orderBy('messages.id', 'desc')
            ->get();

        return $this->showSent($messages, $s);
    }

    private function showSent($messages, $s=''){
        return view('mail.sent-mail',['messages'=>$messages,'search'=>$s]);
    }

    public function view(Request $request, $id)
    {
        $message = get_message($id);

        return view('mail.view',['message'=>$message]);
    }

    public function delete(Request $request, $id)
    {
        $user_id = get_user_id();        
        $message = Message::find($id);

        if($message){
            if($message->sender_id == $user_id){
                $message->sender_delete = 1;
                $message->save();
            }
        }

       
        $receiver = MessageRecipient::where('message_id',$id)
            ->where('recipient_id',$user_id)
            ->where('recipient_delete',0)
            ->first();

        if($receiver){
            $record = MessageRecipient::find($receiver->id);
            $record->recipient_delete = 1;
            $record->save();
        }

        return redirect('mail?msg=Mail deleted');

    }

    public function compose(Request $request)
    {
        $is_forward = 0;
        $parent_id = 0;
        $is_reply = 0;
        $recipient = '';
        $subject = '';
        
        if($request->reply){
            $id = $request->reply;
            $message = Message::find($id);
            if(!$message){
                return redirect('mail');
            }
            $is_reply = 1;
            $parent_id = $message->id;
            $recipient = $message->sender_id;
            $subject = 'RE: '.$message->subject;
        }elseif($request->forward){
            $id = $request->forward;
            $message = Message::find($id);
            if(!$message){
                return redirect('mail');
            }
            $is_forward = 1;
            $parent_id = $message->id;
            $subject = 'FWD: '.$message->subject;
        }

        if($request->to)
        {
            $user = User::find($request->to);
            if($user){
                $recipient = $user->id;
            }
        }
        
        return view('mail.compose',['parent_id'=>$parent_id, 'is_forward'=>$is_forward, 'is_reply'=>$is_reply, 'recipient'=>$recipient, 'subject'=>$subject]);
    }

    public function send(Request $request)
    {
        $receivers = $request->receivers;
        $recipients = explode(',',$receivers);
        
        $message = new Message();

        $message->sender_id = get_user_id();
        $message->subject = $request->subject;
        $message->message = $request->message;
        $message->parent_message_id = $request->parent_id;
        $message->is_forward = $request->is_forward;

        $message->save();
        
        $message_id = $message->id;

        if($message_id){

            //save attachments
            if ($request->hasFile('files')){
                $destination = 'public/mail';

                foreach($request->file('files') as $file){

                    $extension = $file->extension();
                    $original_name = $file->getClientOriginalName();
                    $original_name = pathinfo($original_name,PATHINFO_FILENAME);
                    $original_name = normalize_string($original_name);

                    $file_name = $original_name.'_'.time().'.'.$extension;
                    $path = $file->storeAs($destination, $file_name);

                    $message_file = new MessageFile();
                    $message_file->message_id = $message_id;
                    $message_file->file = $file_name;
                    $message_file->name = $original_name;
                    $message_file->type = $extension;
                    $message_file->save();

                } 

            }

            //save recipients
            foreach($recipients as $recipient){
                $message_recipient = new MessageRecipient();
                $message_recipient->message_id = $message_id;
                $message_recipient->recipient_id = $recipient;
                $message_recipient->save();
            }

        }

        return redirect('mail?msg=Mail sent');

    }

}
