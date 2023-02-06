<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CianRealty extends Model
{
    use HasFactory;

    public static function parseByUrl($price)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.cian.ru/search-offers/v2/search-offers-desktop/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept-language' => 'en-US,en;q=0.9,ru;q=0.8,hy;q=0.7,de-DE;q=0.6,de;q=0.5,ru-RU;q=0.4',
            'content-type' => 'application/json',
        ]);
        curl_setopt($ch, CURLOPT_COOKIE, '_CIAN_GK=c161e605-4426-4bd7-a905-4c59513d53e4; _gcl_au=1.1.1485923081.1675424748; _ga=GA1.2.125913679.1675424748; uxfb_usertype=searcher; sopr_utm=%7B%22utm_source%22%3A+%22direct%22%2C+%22utm_medium%22%3A+%22None%22%7D; tmr_lvid=404e9fe40f4878e7c4c13798963787dd; tmr_lvidTS=1675424748633; _ym_uid=1675424749972641882; _ym_d=1675424749; uxs_uid=4dbc9d30-a3b8-11ed-a6d6-7d6980596570; adrcid=AszzUlftArevMFLu1Qr_gZg; afUserId=52037f60-9582-4a52-b806-db370f7e90c7-p; AF_SYNC=1675424749590; __cf_bm=ZPC_EFoGqHyXni6JBDEMf.ra55q4aQQ7mjp.9mj.p2o-1675608613-0-AbsgDV9JJvQX/KBdH5gNfeNL78IGBfYONVjdQvNtlHZQNbyHfpKEDiYL07zgK7aw8+TdMrFUhMzYXcRqUoUm1Hg=; login_mro_popup=1; _gid=GA1.2.320106624.1675608617; sopr_session=846176c6638f4bc9; _ym_isad=2; _ym_visorc=b; _dc_gtm_UA-30374201-1=1; session_region_id=1; session_main_town_region_id=1');
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"jsonQuery":{"_type":"flatsale","engine_version":{"type":"term","value":2},"region":{"type":"terms","value":[1]},"price":{"type":"range","value":{"gte":'.$price.',"lte":'.$price.'}},"currency":{"type":"term","value":2},"room":{"type":"terms","value":[2]},"total_area":{"type":"range","value":{"gte":15}}}}');

        $response = curl_exec($ch);
        $respArr = json_decode($response);
        $resp = self::getOffers($respArr);
        curl_close($ch);
        return $resp;

        //----file approach-----
       /* $json = file_get_contents(base_path('storage/json/cian.json'));
        $respArr = json_decode($json);
        $resp = self::getOffers($respArr);
        return $resp;*/

    }

    public static function getOffers($respArr)
    {
        try {
            $cian = '';
            $entities = $respArr->data->offersSerialized;
            for ($i = 0; $i < count($entities); $i++) {
                $date = date('Y-m-d H:i:s', strtotime($entities[$i]->creationDate)) ?? '';
                $price = $entities[$i]->bargainTerms->priceRur ?? '' . $entities[$i]->bargainTerms->currency ?? '';
                $builtYear = $entities[$i]->building->buildYear ?? '';
                $area = $entities[$i]->totalArea ?? '';
                $dealStatus = $entities[$i]->dealType ?? '';
                $roomsTotal = $entities[$i]->roomsCount ?? '';
                $offerId = $entities[$i]->id ?? '';
                $url = $entities[$i]->fullUrl ?? '';
                $description = $entities[$i]->description ?? '';
                $address = $entities[$i]->geo->userInput ?? '';
                $image = $entities[$i]->photos[0]->fullUrl ?? '';
                $latitude = $entities[$i]->geo->coordinates->lat;
                $longitude = $entities[$i]->geo->coordinates->lng;

                $title = '';
                if (!empty($area) && !empty($roomsTotal)) {
                    $title .= $area . '㎡, ' . $roomsTotal . '-roomed flat';
                } else {
                    $title = 'Flat';
                }

                $cian = RealtyOffer::updateOrCreate(
                    ['offerId' => $offerId],
                    [
                        'date' => $date,
                        'price' => $price,
                        'builtYear' => $builtYear,
                        'area' => $area,
                        'dealStatus' => $dealStatus,
                        'roomsTotal' => $roomsTotal,
                        'url' => $url,
                        'description' => $description,
                        'address' => $address,
                        'title' => $title,
                        'image' => $image,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'from' => 'cian',
                    ]
                );
                $cian->save();
            }
        }catch (\Exception $e){
            DB::rollBack();
            return ['success'=>false];
        }
        return ['success'=>true];
    }
}
