<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealtyOffer extends Model
{
    use HasFactory;
    protected $table = 'realty_offers';
    protected $guarded = [];

    public static function getOfferByOfferId($offerId){
        return RealtyOffer::where('offerId',$offerId)->first();
    }
    public static function getAllOffers(){
        return RealtyOffer::orderBy('date','DESC')->get();
    }
    public static function getOffersByPrice($price){
        return RealtyOffer::where('price',$price)->orderBy('date','DESC')->get();
    }

    public static function parseRealty($request)
    {
        $errors = [];
        $price = $request->price;
        $parsYR = YandexRealty::parseByUrl($price);
        $parsCN = CianRealty::parseByUrl($price);

        $offers = self::getOffersByPrice($price);
        if (count($offers) == 0) {
            $result = ['success'=>false, 'error' => 'No offer with this price'];
        }elseif($parsCN['success'] && $parsYR['success']){
            $result = ['success'=>true,'offers' => self::getOffersByPrice($price)];
        }else{
            $result = ['success'=>false, 'error' => 'Something went wrong'];
        }
        return $result;
    }
}
