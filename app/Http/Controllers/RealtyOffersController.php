<?php

namespace App\Http\Controllers;

use App\Models\RealtyOffer;
use Illuminate\Http\Request;

class RealtyOffersController extends Controller
{
    public function parseRealty(Request $request){
        $request->validate([
            'price' => 'required|numeric|min:1|max:9999999999'
        ]);

        $result = RealtyOffer::parseRealty($request);
        return response()->json(['result'=>$result]);
    }
    public function getOffer($offerId){
        $data = RealtyOffer::getOfferByOfferId($offerId);
        return response()->json(['success'=> true,'data'=>json_encode($data)]);
    }
}
