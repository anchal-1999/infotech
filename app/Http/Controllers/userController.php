<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userModel;
use Hash;
use Log;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
class userController extends Controller
{
    //
//add eployee
    public function userRegistration(Request $req)
    {
        $f_name = $req -> f_name;
        $l_name = $req -> l_name;
        $role = $req ->role;
        $email = $req ->email;
        $password = $req -> password;
        $data = userModel::where('email', $email) -> where('is_deleted','N') -> get();
        if(count($data)>0)
        {
            return response() -> json([
                "code" => 400,
                "message" => "email is already registered. please use another email"
            ]);
        }
        $insert = new userModel;
        $insert->f_name = $req->f_name;
        $insert->l_name = $req->l_name;
        $insert->role =   $req -> role;
        $insert->email = $req->email;
        $insert -> creation_date =  date("Y-m-d");
        $insert->password = Hash::make($password, ['rounds => 10']);

        $user = $insert->save();

    
            if($user)
            {
                $data = userModel::where('email', $email)->get()->first();
                $payload = JWTFactory::sub($data->id)->myCustomObject($data)->make();
                $token = JWTAuth::fromUser($data, $payload); //token for email verification
                
                //$this->email($email, $token, $req); //calling email function
                return response()->json([
                 "code" => 200,
                 'title' => "Succesfuly registered",
                // 'message' => "Verify your account, varification mail send to $data->email.", 
                 "token" => $token,
                 "submitted_data" => $data
                ]);
              
            }else{
                return response() -> json([
                    "code" => 400,
                    "message" => "submission failed"
                ]);
            }
    }

    //login api
    public function user_login(Request $request)
    {
      $email = $request->email;
      $password = $request->password;
  
      $data = userModel::where('email', $email)->where('is_deleted', "N") -> where('status', "Active")->first();
      if ($data) {
        $dbpass = $data->password;
  
        $answer = Hash::check($password, $dbpass);
  
  
        if ($answer) {
          $payload = JWTFactory::sub($data->id)->myCustomObject($data)->make();
          $token = JWTAuth::fromUser($data, $payload);
          return response()->json([
                            'code' => 200, 
                            'message' => "successfuly login as a " . $data->role,  
                            'data' => $data, 
                            'token' => $token
                          ]);
        } else {
          return response()->json(['code' => 400, 'message' => 'Login Failed Please check pasword']);
        }
      }
      return response()->json(['code' => 400, 'message' => 'Login Failed Please check email/ user not found']);
    }


//request for changing password
   public function request_forget_password(Request $req)
   {
 
     $data = null;
    
       $data = employeeModel::where("emp_email", $req->email) -> where('status', 'Active')->get() -> first();
       if (empty($data)) {
         return response()->json(["code" => 400, "message" => "No user found for this email"]);
       }

     //return $data;
     $payload = JWTFactory::sub($data->id)->myCustomObject($data)->make();
     $token = JWTAuth::fromUser($data, $payload);
     $this->email_forget($req->email, $token, $req);
     return response()->json(["code" => 200, "message" => "A Password Change Link Sent to you mail"]);
   }
 
   public function email_forget($email, $token, $req)
   {
     $link = $req->getSchemeAndHttpHost() . "/forget/" . $token;
     $user['to'] = $email;
     Mail::send('emailVerify', ['link' => $link], function ($message) use ($user) {
       $message->to($user['to']);
       $message->subject('email verification');
     });
   }


   //forgotpassword
   public function forgate_pass(Request $req)
  {
    $auth = $this->decryptt($req->header("Authorization"));
    Log::info($auth);
    $emp_id = $auth["id"];
    $emp_email = $auth['emp_email'];
    $password = $req->password;
    $c_password = $req->cpassword;
    if ($password == $c_password) {
      $hash_pass = Hash::make($password, ['rounds => 10']);
      if ($emp_id) {
    
          $employee = employeeModel::where('emp_email', $emp_email) -> where('status', 'Active') ->get()->first();

          if ($employee) {
            $update = employeeModel::where('emp_email', $emp_email) -> where('status', 'Active') ->update(['password' => $hash_pass]);
            $payload = JWTFactory::sub($employee->id)->myCustomObject($employee)->make();
            $token = JWTAuth::fromUser($employee, $payload);
            $this->passEmail($emp_email, $token); //calling passEmail function
            return response()->json(["code" => 200, 'message' => 'Paswword reset successful.', "token" => $token]);
          }
          return response()->json(["code" => 400, 'message' => 'error']);
        
      }
      return response()->json(["code" => 400, 'message' => 'invalid user']);
    }
  }



   //registration mail function
   public function email($email, $token, $request)
  {
    $link = $request->getSchemeAndHttpHost() . "/verify/" . $token;
    // echo $link;
    $user['to'] = $email;
    Mail::raw('verify your email', function ($message) use ($user) {

      $message->to($user['to']);
      $message->subject('email verification');
    });
  }
}
