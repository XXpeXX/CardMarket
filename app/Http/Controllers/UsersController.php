<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Buys;
use App\Models\Card;
use App\Models\User;
use App\Models\Sales;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function register(Request $req){

        $response = ['error_code' => 1, 'error_msg' => ''];

        $validator = Validator::make(json_decode($req->getContent(), true), [
            "name" => 'required|max:50',
            "email" => 'required|email|unique:App\Models\User,email|max:50',
            "password" => 'required|regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}/',
            "position" => 'required|in:particular,professional,administrator'
        ]);

        if ($validator->fails()){

            $response = ['error_code' => 0, 'error_msg' => $validator->errors()]; 

        }else {

            $data = json_decode($req -> getContent()); 

            try {

                $user = new User();
                $user->name = $data->name;
                $user->email = $data->email;
                $user->password = Hash::make($data->password);
                $user->position = $data->position;
                $user->save();

                $response =['error_msg' => 'User saved with id ' .$user->id];

            }catch (\Exception $e){

                $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
            }
            
        }
       return response()->json($response);
    }
    
    public function login(Request $req){

        $response = ['error_code' => 1, 'error_msg' => ''];

        $data = json_decode($req -> getContent());
        
        $user = User::where('name', '=', $data->name)->first();

        if ($user){

            if (Hash::check($data->password, $user->password)){

                do {
                    $token = Hash::make($user->id.now());
                }while (User::where('api_token', $token)->first());

                $user->api_token = $token;
                $user->save();

                $response =['error_msg' => 'Correct login'];

            }else {

                $response = ['error_code' => 0, 'error_msg' => 'Wrong Password'];
            }
        }else {

            $response = ['error_code' => 0, 'error_msg' => 'User not found'];
        }
        return response()->json($response);  
    }

    public function resetPassword(Request $req){
        
 
        $response = ['error_code' => 1, 'error_msg' => ''];

        $data = json_decode($req -> getContent());

        $user = User::where('email', $data->email)->first();

        try{

            if ($user){
            
                $user->api_token = null;
                
                $password = "qwertyuiopasdfghjklzxcvbnm1234567890";
                $passwordLength = strlen($password);
                $newPassword = "";

                for($i = 0; $i < 6; $i++){
                    $newPassword .= $password[rand(0, $passwordLength -1)];
                }
                    
                $user->password = Hash::make($newPassword);
                $user->save();

                $response =['error_msg' => 'New password generate: '.$newPassword];

            }else {

                $response = ['error_code' => 0, 'error_msg' => 'User not found'];
            }
        }catch(\Exception $e){

            $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
        }   
        return response()->json($response);
    }

    public function buyCard(Request $req)
    {
        $response = ['error_code' => 1, 'error_msg' => ''];

        $data = json_decode($req -> getContent());

        $user = User::where('api_token', $req->api_token)->first();

        $card = Card::where('id', $data->card)->first();

        if ($user) {

            try {

                $buy = new Buys();
                $buy->id_card = $card->id;
                $buy->id_user = $user->id;
                $buy->save();

                $response =['error_msg' => 'Card whit id: '.$card->id.' bought'];

            } catch (\Exception $e) {

                $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
            }
        } else {

            $response = ['error_code' => 0, 'error_msg' => 'The card dont exist'];
        }
        return response()->json($response);
    }

    public function sellCard(Request $req)
    {
        $response = ['error_code' => 1, 'error_msg' => ''];

        $validator = Validator::make(json_decode($req->getContent(), true),
        [
           'id_card' => ['required', 'integer'],
           'amount' => ['required', 'integer'],
           'price' => ['required', 'numeric','min:0','not_in:0'],

       ]);

        if ($validator->fails()){

            $response = ['error_code' => 0, 'error_msg' => $validator->errors()];

        }else {

            $data = json_decode($req -> getContent());

            $user = User::where('api_token', $req->api_token)->first();

            $card = Card::select('id')
                        ->where('id', $data->id_card)
                        ->get();

            if ($card){

                try {

                    $sale = new Sales();
                    $sale->id_card = $data->id_card;
                    $sale->amount = $data->amount;
                    $sale->price = $data->price;
                    $sale->user = $user->id;
                    $sale->save();

                    $response =['error_msg' => 'Sale offer save whit id '.$sale->id];

                }catch (\Exception $e) {

                    $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
                }
            }else {

                $response = ['error_code' => 0, 'error_msg' => 'The card dont exist'];
            }
        }
        return response()->json($response);
    }
}
