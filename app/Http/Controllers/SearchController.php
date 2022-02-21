<?php

namespace App\Http\Controllers;

use App\Models\Search;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class SearchController extends Controller
{
    //
    private function getHttP()
    {
        return Http::withHeaders([
            'x-api-key' => env('API_KEY')
        ]);
    }
    public function search()
    {

        $response = $this->getHttP()->get('https://api.thecatapi.com/v1/breeds');

        if ($response->successful()) {
            return $response->json();
        }
    }

    public function breed($id)
    {
        $response = $this->getHttP()->get("https://api.thecatapi.com/v1/breeds/search?q=".$id);
        if ($response->successful()) {
            return $response->json();
        }
        return $response;
    }

    public function searchByBreed(Request $request)
    {

        $data = $request->all();
        $validated = $request->validate([
            'breed_id' => "required"
        ]);

        $search = new Search();
        if (Auth::check()) {
            $search->user_id = $data['user_id'];
        }
        $search->breed_id = $data['breed_id'];
        $search->save();
        return response()->json(['' => ""], 200);
    }

    public function mostRecent($count)
    {
        $mostRecent = Search::select('breed_id')
            ->groupBy('breed_id')
            ->orderByRaw('COUNT(*) DESC')
            ->limit($count)
            ->get()->toArray();
        return response()->json(Arr::flatten($mostRecent), 200);
    }

    public function imagesById($id){
        $response = $this->getHttP()->get('https://api.thecatapi.com/v1/images/search?limit=8&breed_ids='.$id);
        if($response->successful()){
            return $response->json();
        }
        return $response();
    }
}
