<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\questionModel;
use App\Models\userModel;
use Hash;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class managementController extends Controller
{
    //add user
    public function add_user(Request $req)
    {

      $auth = $this->decryptt($req->header("Authorization"));
      if(!$auth)
      {
          return response() -> json([
              "code" => 400,
              "message" => "you are unauthorized to access this section"
            ]);
      }
  $auth_id = $auth["id"];
  $user_record = userModel::where('id', $auth_id) -> where('role',"Management")->where('is_deleted','N')
                        ->where('status','Active') -> get() -> first();
  if(!$user_record)
  {
      return response() -> json([
          "code" => 400,
          "message" => "Invalid token"
        ]);
  }
        $f_name = $req -> f_name;
        $l_name = $req -> l_name;
        $role = $req ->role;
        $email = $req ->email;
        $password = $req -> password;
        $status = $req -> status;
        $data = userModel::where('email', $email) ->where('is_deleted','N') -> get();
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
        $insert->status = $req->status;
        $insert -> creation_date =  date("Y-m-d");
        $insert->password = Hash::make($password, ['rounds => 10']);

        $user = $insert->save();
    
            if($user)
            {
                $data = userModel::where('email', $email) ->where('is_deleted','N')->get()->first();
                //$payload = JWTFactory::sub($data->id)->myCustomObject($data)->make();
                //$token = JWTAuth::fromUser($data, $payload); //token for email verification
                
               // $this->email($email, $password, $f_name); //calling email function
                return response()->json([
                 "code" => 200,
                 'title' => "Succesfuly registered",
                 'message' => "Login details has benn sent to ". $data->email, 
                 "submitted_data" => $data
                ]);
              
            }else{
                return response() -> json([
                    "code" => 400,
                    "message" => "submission failed"
                ]);
            }
    }

    //update employee
    public function update_user(Request $req)
    {
        $auth = $this->decryptt($req->header("Authorization"));
        if(!$auth)
        {
            return response() -> json([
                "code" => 400,
                "message" => "you are unauthorized to access this section"
              ]);
        }
    $auth_id = $auth["id"];
    $user_record = userModel::where('id', $auth_id) -> where('role',"Management")->where('is_deleted','N')
                          ->where('status','Active') -> get() -> first();

                          //return $user_record;
    if(!$user_record)
    {
        return response() -> json([
            "code" => 400,
            "message" => "Invalid token"
          ]);
    }

   // return $role;
    if($user_record)
    {
        $f_name = $req -> f_name;
        $l_name = $req -> l_name;
        $role = $req ->role;
        $id = $req -> id;
        $email = $req ->email;
        $status = $req -> status;
        $record = userModel::where('id', $id) ->where('is_deleted','N') -> get()-> first();

        if ($record) {
           $data = userModel:: where('email', $email) ->where('is_deleted','N') -> get() -> first();
          if ($data) {
            if ($data["id"] == $id) {
           
               $result = userModel:: where('id', $id) ->where('is_deleted','N') -> update([  //return number of modified row to result variable
               "f_name" => $f_name? $f_name : $data->f_name ,
               "l_name" => $l_name? $l_name : $data->l_name ,
               "role" =>   $role? $role : $data->role ,
               "email" => $email,
               "updation_date" =>  date("Y-m-d"),
               "status" => $status
                ]);
               if ($result) {
                   $updated_data = userModel:: where('id', $id) -> where('email', $email) ->where('is_deleted','N') -> get() -> first();
                   if (!$updated_data) {
                      return response() -> json(["message"=> "unable to update"]);
                      }
                    return response() -> json(
                     ["code"=> 200,
                      "message"=> "successfuly updated",
                      "updated data"=> $updated_data
                      ]);
                   }else {
                      return response() -> json([
                        "code"=> 400,
                        "message"=> "error"
                      ]);
                      }
            } else {
               return response() -> json(["message"=> "this email id is already exist", "code"=> 400]);
              }
          }
  
          $data = userModel:: where('id', $id) ->where('is_deleted','N') -> get() -> first();
          $result = userModel:: where('id', $id) ->where('is_deleted','N') -> update([  //return number of modified row to result variable
            "f_name" => $f_name? $f_name : $data->f_name ,
            "l_name" => $l_name? $l_name : $data->l_name ,
            "role" =>  $role? $role : $data->role ,
            "email" => $email,
            "updation_date" =>  date("Y-m-d"),
            "status" => $status
            ]);
  
        if ($result) {
        $updated_data = userModel:: where('id', $id) -> where('email', $email) -> where('is_deleted','N') ->first();
        if (!$updated_data) {
          return response() -> json(["message"=> "unable to update"]);
        }
        //$payload = JWTFactory:: sub($admin -> id) -> myCustomObject($updated_data) -> make();
        //$token = JWTAuth:: fromUser($updated_data, $payload);
  
        //$this->update_profile($request -> email, $record['email']);
          
          
        return response() -> json(
          ["code"=> 200,
            "message"=> "successfuly updated",
            "updated data"=> $updated_data
          ]);
       }
      else {
        return response() -> json([
          "code"=> 400,
          "message"=> "error"
        ]);
      }
  
    } else {
      return response() -> json([
        "code"=> 400,
        "message"=> "user is not available"
      ]);
    }
  }
    }

//Retrive all employee details
   public function fetch_all_user(Request $req)
   {

    $auth = $this->decryptt($req->header("Authorization"));
    if(!$auth)
    {
        return response() -> json([
            "code" => 400,
            "message" => "you are unauthorized to access this section"
          ]);
    }
    $auth_id = $auth["id"];
    $user_record = userModel::where('id', $auth_id) -> where('role',"Management")->where('is_deleted','N')
                      ->where('status','Active') -> get() -> first();
    if(!$user_record)
    {
        return response() -> json([
            "code" => 400,
            "message" => "Invalid token"
          ]);
    }
    $data = userModel::where('is_deleted','N') -> get();
    if(count($data)>0)
    {
        return response() -> json([
            "code" => 200,
            "retrived_data" => $data
        ]);
    }else{
        return response() -> json([
            "code" => 400,
            "message" => "failed"
        ]);
    }        
   }

//Retrive employee by id
   public function fetch_user_byid(Request $req)
   {

    $auth = $this->decryptt($req->header("Authorization"));
    if(!$auth)
    {
        return response() -> json([
            "code" => 400,
            "message" => "you are unauthorized to access this section"
          ]);
    }
     $auth_id = $auth["id"];
     $user_record = userModel::where('id', $auth_id) -> where('role',"Management")->where('is_deleted','N')
                      ->where('status','Active') -> get() -> first();
     if(!$user_record)
    {
    return response() -> json([
        "code" => 400,
        "message" => "Invalid token"
      ]);
     }
    $data = userModel::where('id', $req -> id) ->  where('is_deleted','N') -> get();
    if(count($data)>0)
    {
        return response() -> json([
            "code" => 200,
            "retrived_data" => $data
        ]);
    }else{
        return response() -> json([
            "code" => 400,
            "message" => "failed"
        ]);
    }
   }

//delete employee by id
   public function delete_user(Request $req)
   {
    $auth = $this->decryptt($req->header("Authorization"));
    if(!$auth)
    {
        return response() -> json([
            "code" => 400,
            "message" => "you are unauthorized to access this section"
          ]);
    }
     $auth_id = $auth["id"];
     $user_record = userModel::where('id', $auth_id) -> where('role',"Management")->where('is_deleted','N')
                      ->where('status','Active') -> get() -> first();
     if(!$user_record)
    {
    return response() -> json([
        "code" => 400,
        "message" => "Invalid token"
      ]);
     }
    $delete = userModel::where('id', $req -> id) -> where('is_deleted','N') -> update(['is_deleted' => 'Y']);

    if($delete)
    {
        $data = userModel::where('id', $req -> id) -> where('is_deleted','Y') -> get();

        return response() -> json([
            "code" => 200,
            "message" => "deleted",
            "deleted_data" => $data
        ]);
    }else{
        return response() -> json([
            "code" => 400,
            "message" => "deletion failed"
        ]);
    }
   }


   //token decryption
public function decryptt($token) {
  $exptoken = explode(".", $token);
    $tokenPayload = base64_decode($exptoken[1]);
    $jwtPayload = json_decode($tokenPayload, true);
    return $jwtPayload['myCustomObject'];
  
}


   //email function
   public function sendLoginIdPass($email, $pass, $name)
  {

    $user['to'] = $email;
    Mail::send('login', ["email" => $email, "password" => $pass, "name" => $name], function ($message) use ($user) {

      $message->to($user['to']);
      $message->subject('Your Login credentials');
    });
  }

}
