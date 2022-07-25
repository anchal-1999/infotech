<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\userModel;
use App\Models\surveyModel;
use App\Models\questionModel;
use App\Models\answerModel;

class staffController extends Controller
{
    //
    //Retrive all employee details 
   public function fetchAllSurvey_ForStaff(Request $req)
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
$user_record = userModel::where('id', $auth_id) -> where('role',"Staff")->where('is_deleted','N')
                      ->where('status','Active') -> get() -> first();
if(!$user_record)
{
    return response() -> json([
        "code" => 400,
        "message" => "Invalid token"
      ]);
}
    $data = surveyModel::where('is_deleted','N') -> where('status','Published') -> get();
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


//Retrive wuestion by id
public function fetchQuestionBySurveyTitle(Request $req)
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
  $user_record = userModel::where('id', $auth_id) -> where('role',"Staff")->where('is_deleted','N')
                   ->where('status','Active') -> get() -> first();
  if(!$user_record)
 {
 return response() -> json([
     "code" => 400,
     "message" => "Invalid token"
   ]);
  }
 $data = questionModel::where('title', $req -> title) ->  where('is_deleted','N') -> get();
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


//save answer
public function save_answer(Request $req)
{

    $staff_uid = $req -> staff_uid;
    $questionId = $req -> questionId;
    $answer = $req -> answer;
    $title = $req -> title;
    $question = $req ->question; 

 $auth = $this->decryptt($req->header("Authorization"));
 if(!$auth)
 {
     return response() -> json([
         "code" => 400,
         "message" => "you are unauthorized to access this section"
       ]);
 }
  $auth_id = $auth["id"];
  $user_record = userModel::where('id', $auth_id) -> where('role',"Staff")->where('is_deleted','N')
                   ->where('status','Active') -> get() -> first();
  if(!$user_record)
 {
 return response() -> json([
     "code" => 400,
     "message" => "Invalid token"
   ]);
  }
 $save_answer = answerModel::create([
    "staffId" => $staff_uid,
    "questionid" => $questionId,
    "title" => $title,
    "question" => $question,
    "answer" =>$answer,
    "creation_date" => date("Y-m-d"),
 ]);
    ;
 if($save_answer)
 {
     return response() -> json([
         "code" => 200,
         "message" => "saved",
         "saved_answer" => $save_answer
     ]);
 }else{
     return response() -> json([
         "code" => 400,
         "message" => "failed"
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




