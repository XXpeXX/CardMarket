<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Collection extends Controller
{
    public function create(Request $request) {
        $response = array('error_code' =>400, 'error_msg' => 'Error inserting info');
        $collection = new Collection();

        if(!$request->URL) {
            $response['error_msg'] = 'URL is required';

        }elseif(!$request->restaurante_id) {
            $response['error_msg'] = 'Restaurante_id is requiered';

        }else{
            try{
                $collection->restaurante_id = $request->restaurante_id;
                $collection->URL = $request->URL;
                $collection->save();
                $response = array('error_code'=>200, 'error_msg' => 'OK');
                Log::info('Collection '.$collection->URL.' from restaurant '.$collection->restaurante_id.' create');

            } catch (\Exception $e) {
                Log::alert('Function: Create Collection, Message: '.$e);
                $response = array('error_code' => 500, 'error_msg' => "Server connection error");
            }

            }
            return response()->json ($response);
        }

        public function update(Request $req, $id){
            $response = ['error_code'=> 404, 'error_msg'=> 'Collection '.$id.' not found'];
            $datos = $req->getContent();

            if (!$datos) {
                $datos = json_decode($datos);
            }

            $collection = Collection::find($id);

            if (isset($datos) && isset($id) && !empty($collection)) {
                try {
                    $collection->titulo = $datos->titulo ? ucfirst(strtolower($datos->titulo)) : $collection->titulo;
                    $collection->descripcion = $datos->descripcion ? $datos->descripcion : $collection->descripcion ;
                    $collection->foto = $datos->foto ? $datos->foto : $collection->foto;
                    $collection->save();
                    $response = array('error_code'=>200, 'error_msg'=> 'OK');
                    Log::info('Collection '.$collection->titulo.' update');

                } catch (\Exception $e) {
                    Log::alert('Function: Update Collection, Message: '.$e);
                    $response = array('error_code' => 500, 'error_msg' => "Server connection error");

                }
            }
            return response()->json($response);
        }

        public function delete($id) {
            $response = array('error_code'=>404, 'error_msg' => 'Collection '.$id.' not found');
            $collection = Collection::find($id);

            if (!empty($collection)) {
                try {
                    $collection->delete();
                    $response = array('error_code' => 200, 'error_msg' => 'OK');
                    Log::info('Collection delete');

                } catch (\Exception $e) {
                    Log::alert('Function: Delete Collection, Message: '.$e);
                    $response = array('error_code' => 500, 'error_msg' => "Server connection error");

                }
            }
            return response()->json($response);
        }
}
