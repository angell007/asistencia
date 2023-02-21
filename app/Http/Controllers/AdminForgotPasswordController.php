<?php

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Notifications\SendResetPasswordAdmins;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class AdminForgotPasswordController extends Controller
{

    use SendsPasswordResetEmails;


    protected function broker()
    {
        return Password::broker('admins');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $input = $request->all();
        $rules = array(
            'email' => "required",
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first());
        } else {

            // check if user already exists
            $users = Admin::all();
            if (!$users->isEmpty()) {
                foreach ($users as $user) {
                    if ($user->email == $request['email']) {
                        break;
                    }
                    $user = false;
                }
            } else {
                $user = false;
            }

            if (!$user) {
                $arr = array("status" => 400, "message" => "That user doesn't exist");
                return response()->json('', 200, '');
            }

            // create reset pass token
            $token = Password::getRepository()->create($user);
            $user->notify(new SendResetPasswordAdmins($token, $user->email));
            $arr = array("status" => 200, "message" => "Email to reset password sent");
            return response()->json('', 200, '');
        }
    }
}
