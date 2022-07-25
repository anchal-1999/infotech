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
class questionController extends Controller
{
    //


    public function add_question(Request $req)
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

        $title = $req -> title;
        $description = $req -> description;
        $q_type = $req ->q_type;
        $status = $req ->status;
        $option = $req -> option;
        $insert = null;

        if($q_type == "MCQ")
        {
            $insert = new questionModel;
            $insert->title = $title;
            $insert->description = $description;
            $insert->q_type =   $q_type;
            $insert->status = $status;
            $insert -> creation_date =  date("Y-m-d");
            $insert -> option = $option;

        }

        if($q_type <> "MCQ")
        {
            $insert = new questionModel;
            $insert->title = $title;
            $insert->description = $description;
            $insert->q_type =   $q_type;
            $insert->status = $status;
            $insert -> creation_date =  date("Y-m-d");
        }
       
       
        $question = $insert->save();

            if($question)
            {
               return response()->json([
                 "code" => 200,
                 'message' => "Succesfuly added",
                 'added_question' => $insert
                ]);
              
            }else{
                return response() -> json([
                    "code" => 400,
                    "message" => "submission failed"
                ]);
            }
    }

    //update employee
    public function update_question(Request $req)
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
    $user_record = userModel::where('id', $auth_id) -> where('role',"Management")->where('is_deleted','Y')
                          ->where('status','Active') -> get() -> first();
    if(!$user_record)
    {
        return response() -> json([
            "code" => 400,
            "message" => "Invalid token"
          ]);
    }

    else if($user_record)
    {
        $result = null;
        $qId = $req -> qId;
        $title = $req -> title;
        $description = $req -> description;
        $q_type = $req ->q_type;
        $status = $req ->status;
        $option = $req -> option;

        $question = questionModel::where('id', $qId) ->where('is_deleted','N') -> get()-> first();

       if ($question) {
           if($q_type == "MCQ")
           {
            $result = questionModel:: where('id', $qId) ->where('is_deleted','N') -> update([  //return number of modified row to result variable
                "title" => $title? $title : $question->title ,
                "description" => $description? $description : $question->description ,
                "q_type" =>   $q_type? $q_type : $question->q_type ,
                "updation_date" =>  date("Y-m-d"),
                "status" =>   $status? $status : $question->status ,
                "MCQ" => $option? $option : $question -> option
              ]);
              
           }
           if($q_type <> "MCQ"){
              $result = questionModel:: where('id', $qId) ->where('is_deleted','N') -> update([  //return number of modified row to result variable
                "title" => $title? $title : $question->title ,
                "description" => $description? $description : $question->description ,
                "q_type" =>   $q_type? $q_type : $question->q_type ,
                "updation_date" =>  date("Y-m-d"),
                "status" =>   $status? $status : $question->status ,
              ]);
           }
          if ($result) {
            $updated_data = questionModel:: where('id', $qId) ->where('is_deleted','N') -> get(); 
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
    
    } 
  }
    }

//Retrive all employee details
   public function fetch_all_question(Request $req)
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
    $data = questionModel::where('is_deleted','N') -> get();
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

//Retrive question by id
   public function fetch_question_byid(Request $req)
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
    $data = questionModel::where('id', $req -> Qid) ->  where('is_deleted','N') -> get();
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

//delete question by id
   public function delete_question(Request $req)
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
    $delete = questionModel::where('id', $req -> Qid) -> where('is_deleted','N') -> update(['is_deleted' => 'Y']);

    if($delete)
    {
        $data = questionModel::where('id', $req -> Qid) -> where('is_deleted','Y') -> get();

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

}
