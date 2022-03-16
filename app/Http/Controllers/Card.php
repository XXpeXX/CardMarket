<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Card extends Controller
{
    public function create(Request $request) {
        $response = array('error_code' =>400, 'error_msg' => 'Error inserting info');
        $card = new Card();

        if(!$request->URL) {
            $response['error_msg'] = 'URL is required';

        }elseif(!$request->restaurante_id) {
            $response['error_msg'] = 'Restaurante_id is requiered';

        }else{
            try{
                $card->restaurante_id = $request->restaurante_id;
                $card->URL = $request->URL;
                $card->save();
                $response = array('error_code'=>200, 'error_msg' => 'OK');
                Log::info('Card '.$card->URL.' from restaurant '.$card->restaurante_id.' create');

            } catch (\Exception $e) {
                Log::alert('Function: Create Card, Message: '.$e);
                $response = array('error_code' => 500, 'error_msg' => "Server connection error");
            }

            }
            return response()->json ($response);
        }

        public function update(Request $req, $id){
            $response = ['error_code'=> 404, 'error_msg'=> 'Card '.$id.' not found'];
            $datos = $req->getContent();

            if (!$datos) {
                $datos = json_decode($datos);
            }

            $card = Card::find($id);

            if (isset($datos) && isset($id) && !empty($card)) {
                try {
                    $card->titulo = $datos->titulo ? ucfirst(strtolower($datos->titulo)) : $card->titulo;
                    $card->descripcion = $datos->descripcion ? $datos->descripcion : $card->descripcion ;
                    $card->foto = $datos->foto ? $datos->foto : $card->foto;
                    $card->save();
                    $response = array('error_code'=>200, 'error_msg'=> 'OK');
                    Log::info('Card '.$card->titulo.' update');

                } catch (\Exception $e) {
                    Log::alert('Function: Update card, Message: '.$e);
                    $response = array('error_code' => 500, 'error_msg' => "Server connection error");

                }
            }
            return response()->json($response);
        }

        public function delete($id) {
            $response = array('error_code'=>404, 'error_msg' => 'Card '.$id.' not found');
            $card = Card::find($id);

            if (!empty($card)) {
                try {
                    $card->delete();
                    $response = array('error_code' => 200, 'error_msg' => 'OK');
                    Log::info('Card delete');

                } catch (\Exception $e) {
                    Log::alert('Function: Delete Card, Message: '.$e);
                    $response = array('error_code' => 500, 'error_msg' => "Server connection error");

                }
            }
            return response()->json($response);
        }
}
