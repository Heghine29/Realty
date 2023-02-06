<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class YandexRealty extends Model
{
    use HasFactory;

    public static function parseByUrl($price)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://realty.ya.ru/gate/react-page/get/?priceMin=' . $price . '&priceMax=' . $price . '&areaMin=15&rgid=587795&type=SELL&category=APARTMENT&roomsTotal=2&_pageType=search&_providers=seo&_providers=queryId&_providers=forms&_providers=filters&_providers=filtersParams&_providers=mapsPromo&_providers=newbuildingPromo&_providers=refinements&_providers=search&_providers=react-search-data&_providers=searchHistoryParams&_providers=searchParams&_providers=searchPresets&_providers=showSurveyBanner&_providers=seo-data-offers-count&_providers=related-newbuildings&_providers=breadcrumbs&_providers=ads&_providers=cache-footer-links&_providers=site-special-projects&_providers=offers-stats&_providers=seo-texts&_providers=samolet-plus-serp-snippets&crc=y7a63ce9abcb87574f964c184c99e1a06');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept' => 'application/json',
        ]);
        curl_setopt($ch, CURLOPT_COOKIE, 'spravka=dD0xNjc1NTI3NjE1O2k9MTg4LjExMy4xNi4yMTE7RD1DNDY5OUUwN0NGQzc5RjY5RENDQzYwQ0RDMDJDMjA1NkRGNDQwRTRBRjFGNDk0MTMyNzAwMEZGNEUwMEY4ODY0MDQ2RkRFMUNENjQ0Qzc3OTFGOTQ3Qjk2RjYwRUZCNTk3MkExO3U9MTY3NTUyNzYxNTg1MTM4MjExNztoPTZhZTg4MzdjMGQxZmE2NWFkOTkxMThmNTRkNWIyNjI1; Cookie_check=checked; podbor_popup_shown=YES; yandex_csyr=1675645303; Session_id=noauth:1675645303; ys=c_chck.4217821824; mda2_beacon=1675645303457; sso_status=sso.passport.yandex.ru:synchronized; from=other; prev_uaas_data=9417947781675456590%23699561%23669241%23674840%23694443%23708626%23213159%23361531%23610827%23695235; prev_uaas_expcrypted=iZ0yEa0CZl8g1UzDxCMDpsYfCRGHaQXH_5nVGT89CXAMAfxQnPQ0Tp52OaoKfIwk-C1f4jJgKn1YZown3dJSjLBbfFDsdCLyHRd8qYVdtqmelAHmuIOHAHfTRhHvqRQQ6jjmb-jxde1Yq25kWGf6cw%2C%2C; _ym_isad=2; rgid=587795; from_lifetime=1675645464197');
        $response = curl_exec($ch);
        $respArr = json_decode($response);
        $resp = self::getOffers($respArr);
        curl_close($ch);
        return $resp;

        //----file approach-----
       /* $response = file_get_contents(base_path('storage/json/yandex.json'));
        $resp = self::getOffers(json_decode($response));
        return $resp;*/
    }

    public static function getOffers($respArr)
    {
        try {
            $entities = $respArr->response->search->offers->entities;
            $yandex = '';
            for ($i = 0; $i < count($entities); $i++) {

                $offer_date = $entities[$i]->updateDate ?? $entities[$i]->creationDate;
                $date = date('Y-m-d H:i:s', strtotime($offer_date));
                $price = $entities[$i]->price->priceForWhole->value ?? '' . $entities[$i]->price->priceForWhole->currency ?? '';
                $builtYear = $entities[$i]->building->builtYear ?? '';
                $area = $entities[$i]->area->value ?? '';
                $dealStatus = $entities[$i]->dealStatus ?? '';
                $roomsTotal = $entities[$i]->roomsTotal ?? '';
                $offerId = $entities[$i]->offerId ?? '';
                $url = $entities[$i]->shareUrl ?? '';
                $description = $entities[$i]->description ?? '';
                $address = $entities[$i]->location->address ?? '';
                $image = $entities[$i]->mainImages[0] ?? '';
                $latitude = $entities[$i]->location->point->latitude ?? '';
                $longitude = $entities[$i]->location->point->longitude ?? '';

                $title = '';
                if (!empty($area) && !empty($roomsTotal)) {
                    $title .= $area . '㎡, ' . $roomsTotal . '-roomed flat';
                } else {
                    $title = 'Flat';
                }
                DB::beginTransaction();
                $yandex = RealtyOffer::updateOrCreate(
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
                        'from' => 'yandex',
                    ]
                );
                DB::commit();
                $yandex->save();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success'=>false];
        }
        return ['success'=>true];
    }
}
