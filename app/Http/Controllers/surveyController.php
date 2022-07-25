<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\surveyModel;
use App\Models\userModel;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
class surveyController extends Controller
{
    //

    public function add_survey(Request $req)
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
        $status = $req ->status;
        $survey = surveyModel::where('title', $title) ->where('is_deleted','N')
                                         -> get() -> first();
        if($survey)
        {
            return response() -> json([
                "code" => 400,
                "message" => "Survey of this title is already added"
              ]);
        }
        $insert = new surveyModel;
        $insert->title = $title;
        $insert->description = $description;
        $insert->status = $status;
        $insert -> creation_date =  date("Y-m-d");
        $question = $insert->save();
    
            if($question)
            {
               return response()->json([
                 "code" => 200,
                 'message' => "Succesfuly added",
                 'question' => $insert
                ]);
              
            }else{
                return response() -> json([
                    "code" => 400,
                    "message" => "submission failed"
                ]);
            }
    }

    //update employee
    public function update_survey(Request $req)
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

    else if($user_record)
    {
        $qId = $req -> SId;
        $title = $req -> title;
        $description = $req -> description;
        $status = $req ->status;
        $survey = surveyModel::where('id', $SId) ->where('is_deleted','N') -> get()-> first();

       if ($survey) {
           
          $result = surveyModel:: where('id', $SId) ->where('is_deleted','N') -> update([  //return number of modified row to result variable
            "title" => $title? $title : $question->title ,
            "description" => $description? $description : $question->description ,
            "updation_date" =>  date("Y-m-d"),
            "status" =>   $status? $status : $question->status ,
          ]);
          if ($result) {
            $updated_data = surveyModel:: where('id', $SId) ->where('is_deleted','N') -> get(); 
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
    
    } 
  }
    }

//Retrive all employee details
   public function fetch_all_survey(Request $req)
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
    $data = surveyModel::where('is_deleted','N') -> get();
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
   public function fetch_survey_byid(Request $req)
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
    $data = surveyModel::where('id', $req -> Sid) ->  where('is_deleted','N') -> get();
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
   public function delete_survey(Request $req)
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
    $delete = surveyModel::where('id', $req -> Sid) -> where('is_deleted','N') -> update(['is_deleted' => 'Y']);

    if($delete)
    {
        $data = surveyModel::where('id', $req -> Sid) ->  where('is_deleted','Y') -> get();

        return response() -> json([
            "code" => 200,
            "message" => "deleted",
            "deleted_data" => $data
        ]);
    }else{
        return response() -> json([
            "code" => 400,
            "message" => "deletion failed/ already deleted"
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
