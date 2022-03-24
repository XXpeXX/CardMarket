<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Buys;
use App\Models\Collection;
use App\Models\Card_Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Sales;

class CardsController extends Controller
{
    public function createCollection(Request $req){

        $response = ['error_code' => 1, 'error_msg' => ''];
    
        $validator = Validator::make(json_decode($req->getContent(), true), [
            'name' => ['required', 'max:50'],
            'image' => ['required', 'max:100'],
            'edition' => ['required', 'date'],
            'card' => ['required']
        ]);
    
        if ($validator->fails()) {

            $response = ['error_code' => 0, 'error_msg' => $validator->errors()];

        }else {

            $data = json_decode($req -> getContent());
            $cardsCollection =[];

            foreach ($data->card as $tempCard) {
                
                if (isset($tempCard->id)){

                    $card = Card::where('id', $tempCard->id)->first();

                    if ($card){

                        array_push($cardsCollection,$card->id);
                    }

                }elseif (isset($tempCard->name) && isset($tempCard->description)) {

                    $newCard = new Card();
                    $newCard->name = $tempCard->name;
                    $newCard->description = $tempCard->description;
    
                        try {

                            $newCard->save();
                            array_push($cardsCollection,$newCard->id);
                            $response =['error_msg' => 'Card saved with id ' .$newCard->id];
                                        
                        }catch (\Exception $e) {
                            
                            $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
                        }

                }else {

                    $response = ['error_code' => 0, 'error_msg' => 'The inserted data is wrong'];
                }  
            }
    
            if(!empty($cardsCollection)){

                $cardsId = implode(", ",$cardsCollection); 

                try{

                    $collection = new Collection();
                    $collection->name = $data->name;
                    $collection->image = $data->image;
                    $collection->edition = $data->edition;
                    $collection->save();
                    $response =['error_msg' => 'Collection saved with id ' .$collection->id];
                     
                    foreach($cardsCollection as $id){

                        $cardCollection = new Card_Collection();
                        $cardCollection->id_card = $id;
                        $cardCollection->id_collection = $collection->id;
                        $cardCollection->save();
                    }

                    $response =['error_msg' => 'Collection saved with id ' .$collection->id.' and the cards have been added with id: '.$cardsId];
                
                }catch (\Exception $e) {

                    $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
                }
            }
        }
        
        return response()->json($response);
    }

    public function createCard(Request $req)
    {
        $response = ['error_code' => 1, 'error_msg' => ''];

        $validator = Validator::make(json_decode($req->getContent(), true), [
            'name' => ['required', 'max:50'],
            'description' => ['required', 'max:400'],
            'collection' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {

            $response = ['error_code' => 0, 'error_msg' => $validator->errors()];

        }else {

            $data = json_decode($req -> getContent());

            $collection = Collection::where('id', $data->collection)->first();

            if ($collection) {

                try {
                    
                    $card = new Card();
                    $card->name = $data->name;
                    $card->description = $data->description;
                    $card->save();

                    try {
                        
                        $cardCollection = new Card_Collection();
                        $cardCollection->id_card = $card->$id;
                        $cardCollection->id_collection = $collection->id;
                        $cardCollection->save();

                        $response =['error_msg' => 'Card saved with id ' .$card->id.' and the collection with id '.$data->collection];
                        
                    }catch (\Exception $e) {

                        $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
                    }
                }catch (\Exception $e) {
                    
                    $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
                }

            }else {

                $response = ['error_code' => 0, 'error_msg' => 'The collection does not exist'];
            }
        }
        return response()->json($response);
    }

    public function linkCardCollection(Request $req)
    {
        $response = ['error_code' => 1, 'error_msg' => ''];

        $validator = Validator::make(json_decode($req->getContent(), true), [
            'card' => ['required'],
            'collection' => ['required']
        ]);
    
        if ($validator->fails()) {

            $response = ['error_code' => 0, 'error_msg' => $validator->errors()];

        }else {

            $data = json_decode($req -> getContent());

            try{

                $card = Card::where('id', $data->card)->first();
                $collection = Collection::where('id', $data->collection)->first();

                if($card && $collection){
                    $cardCollection = new Card_Collection();
                    $cardCollection->id_card = $data->card;
                    $cardCollection->id_collection = $data->collection;
                    $cardCollection->save();
                    
                    $response =['error_msg' => 'Collection saved with id ' .$data->collection.' and the card have been added with id: '.$data->card];
                }else {

                    $response = ['error_code' => 0, 'error_msg' => 'The collection or card does not exist'];
                }
            }catch (\Exception $e) {

                $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
            }
        }
        return response()->json($response);
    }

    public function searchNames(Request $req){

        $response = ['error_code' => 1, 'error_msg' => ''];

        try {

            if ($req->has('search')){

               $cards = Card::select(['name','description','id'])                           
                        ->where('name','like','%'. $req->input('search').'%')
                        ->get();

                    $response =['result' => $cards];
            }else {

                $response =['error_msg' => 'Enter a name to start the search'];
            }
        }catch (\Exception $e){

            $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
        }
        return response()->json($response);
    }

    public function searchSales(Request $req){

        $response = ['error_code' => 1, 'error_msg' => ''];

        try {

            if ($req -> has('search')){

               $cards = Sales::select(['id_card','amount','price','user'])
                        ->join('users', 'users.id', '=', 'sales.user')
                        ->join('cards', 'cards.id', '=', 'sales.id_card')
                        ->select('cards.name', 'sales.amount', 'sales.price', 'users.name as seller')
                        ->where('cards.name','like','%'. $req->input('search').'%')
                        ->orderBy('sales.price','ASC')
                        ->get();                           
                        
                    $response =['result' => $cards];
            }else {

                $response =['error_msg' => 'Enter a name to start the search'];
            }
        }catch (\Exception $e){

            $response = ['error_code' => 0, 'error_msg' => 'Error: '.$e->getMessage()];
        }
        return response()->json($response);
    }
}
