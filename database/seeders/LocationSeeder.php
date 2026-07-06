<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Region;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    public function run()
    {
        // Get all regions mapped by name for easy lookup
        $regions = Region::all()->keyBy('name');
        
        // Excel data processed and deduplicated
        $locationData = [
            // GREATER ACCRA
            ['name' => 'Accra', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Law Court Complex, Accra', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Tema', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Adentan', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Amasaman', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Sowutuom', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Gbetsele', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Kwabenya', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Weija', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Ashaiman', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Dorvsu, Police HQ', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Dansoman', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Achimota', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Ada', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Adjabeng', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Abeka', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Kaneshie', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Madina', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Dodowa', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'La', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Prampram', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Sege', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Teshie', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'TDC, Tema', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Asofan – Ofankor', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Ngleshie Amanfro', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Gbese', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Baatsonaa', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Kotobabi', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'SSNIT, Accra', 'region_id' => $regions['Greater Accra']->id],
            ['name' => 'Bills, Accra', 'region_id' => $regions['Greater Accra']->id],

            // EASTERN REGION
            ['name' => 'Koforidua', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Akim Oda', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Nkawkaw', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Nsawam', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Nsawam Prisons', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Somanya', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Kyebi', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Odumase Krobo', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Asamankese', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Akim Swedru', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Mpraeso', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Akropong-Akwapim', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Suhum', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Anyinam', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Nkwatia', 'region_id' => $regions['Eastern']->id],
            ['name' => 'New Abriem', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Begoro', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Akim Ofoase', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Abetifi', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Donkorkrom', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Mampong-Akwapim', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Kwabeng', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Asesewa', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Senchi Ferry', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Kade', 'region_id' => $regions['Eastern']->id],
            ['name' => 'New Tafo', 'region_id' => $regions['Eastern']->id],
            ['name' => 'New Abirim', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Aburi', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Osino', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Kra Aboa Coaltar', 'region_id' => $regions['Eastern']->id],
            ['name' => 'Akwatia', 'region_id' => $regions['Eastern']->id],

            // CENTRAL REGION
            ['name' => 'Cape Coast', 'region_id' => $regions['Central']->id],
            ['name' => 'Agona Swedru', 'region_id' => $regions['Central']->id],
            ['name' => 'Dunkwa-On-Offin', 'region_id' => $regions['Central']->id],
            ['name' => 'Winneba', 'region_id' => $regions['Central']->id],
            ['name' => 'Mankesim', 'region_id' => $regions['Central']->id],
            ['name' => 'Assin Fosu', 'region_id' => $regions['Central']->id],
            ['name' => 'Kasoa Odupong Kpehe', 'region_id' => $regions['Central']->id],
            ['name' => 'Ofaakor', 'region_id' => $regions['Central']->id],
            ['name' => 'Nsuaem Kyekyewere', 'region_id' => $regions['Central']->id],
            ['name' => 'Awutu', 'region_id' => $regions['Central']->id],
            ['name' => 'Saltpond', 'region_id' => $regions['Central']->id],
            ['name' => 'Apam', 'region_id' => $regions['Central']->id],
            ['name' => 'Dawurampong', 'region_id' => $regions['Central']->id],
            ['name' => 'Breman Asikuma', 'region_id' => $regions['Central']->id],
            ['name' => 'Ajumako', 'region_id' => $regions['Central']->id],
            ['name' => 'Twifo Praso', 'region_id' => $regions['Central']->id],
            ['name' => 'Elmina', 'region_id' => $regions['Central']->id],
            ['name' => 'Abura-Dunkwa', 'region_id' => $regions['Central']->id],
            ['name' => 'Essarkyir', 'region_id' => $regions['Central']->id],
            ['name' => 'Nyankomasi Ahenkro', 'region_id' => $regions['Central']->id],
            ['name' => 'Kasoa', 'region_id' => $regions['Central']->id],
            ['name' => 'Diaso', 'region_id' => $regions['Central']->id],
            ['name' => 'Assin Foso', 'region_id' => $regions['Central']->id],
            ['name' => 'Twifo Hemang', 'region_id' => $regions['Central']->id],
            ['name' => 'Gomoa Afransi', 'region_id' => $regions['Central']->id],
            ['name' => 'Agona Nsabaa', 'region_id' => $regions['Central']->id],

            // WESTERN REGION
            ['name' => 'Sekondi', 'region_id' => $regions['Western']->id],
            ['name' => 'Tarkwa', 'region_id' => $regions['Western']->id],
            ['name' => 'Takoradi', 'region_id' => $regions['Western']->id],
            ['name' => 'Agona-Nkwanta', 'region_id' => $regions['Western']->id],
            ['name' => 'Axim', 'region_id' => $regions['Western']->id],
            ['name' => 'Nkroful', 'region_id' => $regions['Western']->id],
            ['name' => 'Half Assini', 'region_id' => $regions['Western']->id],
            ['name' => 'Prestea', 'region_id' => $regions['Western']->id],
            ['name' => 'Shama', 'region_id' => $regions['Western']->id],
            ['name' => 'Daboase', 'region_id' => $regions['Western']->id],
            ['name' => 'Asankragwa', 'region_id' => $regions['Western']->id],
            ['name' => 'Wassa-Akropong', 'region_id' => $regions['Western']->id],
            ['name' => 'Kwesimintsim', 'region_id' => $regions['Western']->id],
            ['name' => 'Mpohor', 'region_id' => $regions['Western']->id],
            ['name' => 'Takoradi Market Circle', 'region_id' => $regions['Western']->id],
            ['name' => 'Takoradi Harbour Area', 'region_id' => $regions['Western']->id],

            // WESTERN NORTH
            ['name' => 'Sefwi-Wiawso', 'region_id' => $regions['Western North']->id],
            ['name' => 'Bibiani', 'region_id' => $regions['Western North']->id],
            ['name' => 'Enchi', 'region_id' => $regions['Western North']->id],
            ['name' => 'Bodi', 'region_id' => $regions['Western North']->id],
            ['name' => 'Debiso', 'region_id' => $regions['Western North']->id],
            ['name' => 'Juaboso', 'region_id' => $regions['Western North']->id],
            ['name' => 'Akotombra', 'region_id' => $regions['Western North']->id],
            ['name' => 'Adabokrom', 'region_id' => $regions['Western North']->id],

            // ASHANTI REGION
            ['name' => 'Kumasi', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Kumasi Prisons', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Mampong-Ashanti', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Obuasi', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Offinso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Adum - Kumasi', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'KMA - Kumasi', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Nkawie', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Kumawu', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Juaso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Nsuta', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Bekwai', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Tepa', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Juaben', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Akropong Ashanti', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Old Tafo', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Asokwa', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Atasemanso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Abuakwa', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Kwadaso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Donyina', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Bills, Asokwa', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Prempeh Assembly Hall - Kumasi', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'New Edubiase', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Kuntanase', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Ejisu', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Ejura', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Agona-Ashanti', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Fomena', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Agogo', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Effiduase', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Kodie', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Mankranso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Nyinahin', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Konongo', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Manso Nkwanta', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Mamponteng', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Asokore Mampong', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Jacobu', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Akomadan', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Twedie', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Kwaso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Asiwa', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Manso Adubia', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Toase', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Pakyi No. 2', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Bonwire', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Bompata', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Boamang', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Drobonso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Wiamoase', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Pokukrom', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Ntonso', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Suame', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Adansi Asokwa', 'region_id' => $regions['Ashanti']->id],
            ['name' => 'Bills, Kumasi', 'region_id' => $regions['Ashanti']->id],

            // BONO REGION
            ['name' => 'Sunyani', 'region_id' => $regions['Bono']->id],
            ['name' => 'Wenchi', 'region_id' => $regions['Bono']->id],
            ['name' => 'Fiapre', 'region_id' => $regions['Bono']->id],
            ['name' => 'Berekum', 'region_id' => $regions['Bono']->id],
            ['name' => 'Dormaa Ahenkro', 'region_id' => $regions['Bono']->id],
            ['name' => 'Drobo', 'region_id' => $regions['Bono']->id],
            ['name' => 'Wamfie', 'region_id' => $regions['Bono']->id],
            ['name' => 'Sampa', 'region_id' => $regions['Bono']->id],
            ['name' => 'Nsoatre', 'region_id' => $regions['Bono']->id],
            ['name' => 'Nkrankwanta', 'region_id' => $regions['Bono']->id],

            // AHAFO REGION
            ['name' => 'Duayaw Nkwanta', 'region_id' => $regions['Ahafo']->id],
            ['name' => 'Goaso', 'region_id' => $regions['Ahafo']->id],
            ['name' => 'Hwidiem', 'region_id' => $regions['Ahafo']->id],
            ['name' => 'Kukuom', 'region_id' => $regions['Ahafo']->id],
            ['name' => 'Bechem', 'region_id' => $regions['Ahafo']->id],
            ['name' => 'Kenyasi', 'region_id' => $regions['Ahafo']->id],

            // BONO EAST REGION
            ['name' => 'Atebubu', 'region_id' => $regions['Bono East']->id],
            ['name' => 'Kintampo', 'region_id' => $regions['Bono East']->id],
            ['name' => 'Techiman', 'region_id' => $regions['Bono East']->id],
            ['name' => 'Nkoranza', 'region_id' => $regions['Bono East']->id],
            ['name' => 'Kwame Danso', 'region_id' => $regions['Bono East']->id],
            ['name' => 'Tuobodom', 'region_id' => $regions['Bono East']->id],
            ['name' => 'Yeji', 'region_id' => $regions['Bono East']->id],
            ['name' => 'Jema', 'region_id' => $regions['Bono East']->id],

            // VOLTA REGION
            ['name' => 'Ho', 'region_id' => $regions['Volta']->id],
            ['name' => 'Denu', 'region_id' => $regions['Volta']->id],
            ['name' => 'Hohoe', 'region_id' => $regions['Volta']->id],
            ['name' => 'Sogakope', 'region_id' => $regions['Volta']->id],
            ['name' => 'Keta', 'region_id' => $regions['Volta']->id],
            ['name' => 'Kpando', 'region_id' => $regions['Volta']->id],
            ['name' => 'Juapong', 'region_id' => $regions['Volta']->id],
            ['name' => 'Agbozume', 'region_id' => $regions['Volta']->id],
            ['name' => 'Adidome', 'region_id' => $regions['Volta']->id],
            ['name' => 'Dzodze', 'region_id' => $regions['Volta']->id],
            ['name' => 'Anloga', 'region_id' => $regions['Volta']->id],
            ['name' => 'Abor', 'region_id' => $regions['Volta']->id],
            ['name' => 'Peki', 'region_id' => $regions['Volta']->id],
            ['name' => 'Akatsi', 'region_id' => $regions['Volta']->id],
            ['name' => 'Dabala', 'region_id' => $regions['Volta']->id],
            ['name' => 'Ave Dakpa', 'region_id' => $regions['Volta']->id],
            ['name' => 'Vakpo', 'region_id' => $regions['Volta']->id],
            ['name' => 'Dzolokpuita', 'region_id' => $regions['Volta']->id],
            ['name' => 'Kpetoe', 'region_id' => $regions['Volta']->id],
            ['name' => 'Adaklu', 'region_id' => $regions['Volta']->id],
            ['name' => 'Ve-Golokuati', 'region_id' => $regions['Volta']->id],
            ['name' => 'Nogokpo', 'region_id' => $regions['Volta']->id],
            ['name' => 'Battor', 'region_id' => $regions['Volta']->id],

            // OTI REGION
            ['name' => 'Dambai', 'region_id' => $regions['Oti']->id],
            ['name' => 'Jasikan', 'region_id' => $regions['Oti']->id],
            ['name' => 'New Ayoma', 'region_id' => $regions['Oti']->id],
            ['name' => 'Kadjebi', 'region_id' => $regions['Oti']->id],
            ['name' => 'Kete Krachi', 'region_id' => $regions['Oti']->id],
            ['name' => 'Nkwanta', 'region_id' => $regions['Oti']->id],
            ['name' => 'Kpassa', 'region_id' => $regions['Oti']->id],
            ['name' => 'Chinderi', 'region_id' => $regions['Oti']->id],
            ['name' => 'Bonwire Kwamekrom', 'region_id' => $regions['Oti']->id],

            // NORTHERN REGION
            ['name' => 'Tamale', 'region_id' => $regions['Northern']->id],
            ['name' => 'Tamale Prisons', 'region_id' => $regions['Northern']->id],
            ['name' => 'Yendi', 'region_id' => $regions['Northern']->id],
            ['name' => 'Bimbilla', 'region_id' => $regions['Northern']->id],
            ['name' => 'Wulensi', 'region_id' => $regions['Northern']->id],
            ['name' => 'Kpandai', 'region_id' => $regions['Northern']->id],

            // SAVANNAH REGION
            ['name' => 'Damango', 'region_id' => $regions['Savannah']->id],
            ['name' => 'Bole', 'region_id' => $regions['Savannah']->id],
            ['name' => 'Salaga', 'region_id' => $regions['Savannah']->id],

            // NORTH EAST REGION
            ['name' => 'Nalerigu', 'region_id' => $regions['North East']->id],
            ['name' => 'Walewale', 'region_id' => $regions['North East']->id],
            ['name' => 'Gambaga', 'region_id' => $regions['North East']->id],
            ['name' => 'Chereponi', 'region_id' => $regions['North East']->id],
            ['name' => 'Yagaba', 'region_id' => $regions['North East']->id],

            // UPPER EAST REGION
            ['name' => 'Bolgatanga', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Bawku', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Navrongo', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Bongo', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Sandema', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Zebilla', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Garu', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Pusiga', 'region_id' => $regions['Upper East']->id],
            ['name' => 'Zuarungu', 'region_id' => $regions['Upper East']->id],

            // UPPER WEST REGION
            ['name' => 'Wa', 'region_id' => $regions['Upper West']->id],
            ['name' => 'Lawra', 'region_id' => $regions['Upper West']->id],
            ['name' => 'Tumu', 'region_id' => $regions['Upper West']->id],
            ['name' => 'Jirapa', 'region_id' => $regions['Upper West']->id],
            ['name' => 'Nandom', 'region_id' => $regions['Upper West']->id],
            ['name' => 'Nadowli', 'region_id' => $regions['Upper West']->id],
        ];

        // Remove any potential duplicates based on name and region_id combination
        $uniqueLocations = collect($locationData)->unique(function ($item) {
            return $item['name'].$item['region_id'];
        })->toArray();

        // Create locations
        foreach ($uniqueLocations as $location) {
            Location::create([
                'name' => $location['name'],
                'region_id' => $location['region_id'],
                'is_active' => true
            ]);
        }
    }
}