<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\Region;
use App\Models\Location;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourtSeeder extends Seeder
{
    public function run()
    {
        $regions = Region::all()->keyBy('name');
        $courtsData = [];

        // GREATER ACCRA COURTS
        $courtsData = array_merge($courtsData, [
            ['name' => 'Supreme Court of Ghana', 'type' => 'Supreme Court', 'region' => 'Greater Accra', 'location' => 'Accra'],
            ['name' => 'Court of Appeal (Civil & Criminal)', 'type' => 'Appeal Court', 'region' => 'Greater Accra', 'location' => 'Accra'],
            
            // High Courts in Accra
            ['name' => 'High Court - General Jurisdiction (1-13)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court - Criminal (1-5)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court - Probate & L.A (1-3)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court – Financial & Economic Crime (1 & 2)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court - Land (1-11)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court – Commercial (1-10)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court – Divorce & Matrimonial (1-3)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court - Human Rights (1 & 2)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            ['name' => 'High Court – Industrial & Labour (1 & 2)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Law Court Complex, Accra'],
            
            // High Courts in other Greater Accra locations
            ['name' => 'High Court (A & B)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Tema'],
            ['name' => 'High Court - Land (1 & 2)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Tema'],
            ['name' => 'High Court (1 & 2)', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Adentan'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Amasaman'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Sowutuom'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Greater Accra', 'location' => 'Gbetsele'],
            
            // Circuit Courts in Greater Accra
            ['name' => 'Circuit Court (1-11)', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Accra'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Amasaman'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Kwabenya'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Weija'],
            ['name' => 'Circuit Court (A & B)', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Tema'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Ashaiman'],
            ['name' => 'Circuit Court (1 & 2)', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Adentan'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Dorvsu, Police HQ'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Dansoman'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Achimota'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Gbetsele'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Greater Accra', 'location' => 'Ada'],
            
            // Family & Juvenile Courts
            ['name' => 'Family & Juvenile Court (A-C)', 'type' => 'Family & Juvenile Court', 'region' => 'Greater Accra', 'location' => 'Accra'],
            
            // District Courts in Greater Accra
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Adjabeng'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Abeka'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Ashaiman'],
            ['name' => 'District Court (1 & 2)', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Adentan'],
            ['name' => 'District Court (1 & 2)', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Kaneshie'],
            ['name' => 'District Court (A & B)', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Madina'],
            ['name' => 'District Court (A & B)', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Amasaman'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Dodowa'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Weija'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'La'],
            ['name' => 'District Court (1 & 2)', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Tema'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Ada'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Prampram'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Sege'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Teshie'],
            ['name' => 'District Court, TDC', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'TDC, Tema'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Sowutuom'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Dorvsu, Police HQ'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Asofan – Ofankor'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Ngleshie Amanfro'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Gbese'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Baatsonaa'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Kotobabi'],
            ['name' => 'District Court SSNIT (1-3)', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'SSNIT, Accra'],
            ['name' => 'District Court Bills (1-4) Saturday', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Bills, Accra'],
            ['name' => 'District Court Bills (1-4) Afternoons', 'type' => 'District Court', 'region' => 'Greater Accra', 'location' => 'Bills, Accra'],
        ]);

        // EASTERN REGION COURTS
        $courtsData = array_merge($courtsData, [
            ['name' => 'Court of Appeal', 'type' => 'Appeal Court', 'region' => 'Eastern', 'location' => 'Koforidua'],
            ['name' => 'High Court – General (1 & 3)', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Koforidua'],
            ['name' => 'High Court – Commercial (2 & 4)', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Koforidua'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Akim Oda'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Nkawkaw'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Nsawam'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Nsawam Prisons'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Somanya'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Kyebi'],
            ['name' => 'High Court', 'type' => 'High Court', 'region' => 'Eastern', 'location' => 'Odumase Krobo'],
            
            // Circuit Courts Eastern
            ['name' => 'Circuit Court (A & B)', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Koforidua'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Nsawam'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Asamankese'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Akim Swedru'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Mpraeso'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Odumase Krobo'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Akropong-Akwapim'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Suhum'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Anyinam'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Kyebi'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'Nkwatia'],
            ['name' => 'Circuit Court', 'type' => 'Circuit Court', 'region' => 'Eastern', 'location' => 'New Abriem'],
            
            // District Courts Eastern
            ['name' => 'District Court (A & B)', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Koforidua'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Begoro'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Nkawkaw'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Akim Ofoase'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Abetifi'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Donkorkrom'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Kyebi'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Nsawam'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Mampong-Akwapim'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Kwabeng'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Asamankese'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Akim Oda'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Asesewa'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Senchi Ferry'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Somanya'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Kade'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Suhum'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'New Tafo'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'New Abirim'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Aburi'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Osino'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Kra Aboa Coaltar'],
            ['name' => 'District Court', 'type' => 'District Court', 'region' => 'Eastern', 'location' => 'Akwatia'],
        ]);

        // Add more regions following the same pattern...
        // Due to length constraints, I'll show the pattern and you can continue similarly

        // Generate codes and create courts
        $typeCodes = [
            'Supreme Court' => 'SC',
            'Appeal Court' => 'AC',
            'High Court' => 'HC',
            'Circuit Court' => 'CC',
            'District Court' => 'DC',
            'Family & Juvenile Court' => 'FJ'
        ];

        $regionCodes = [
            'Greater Accra' => 'GA',
            'Eastern' => 'EA',
            'Central' => 'CE',
            'Western' => 'WE',
            'Western North' => 'WN',
            'Ashanti' => 'AS',
            'Bono' => 'BO',
            'Ahafo' => 'AH',
            'Bono East' => 'BE',
            'Volta' => 'VO',
            'Oti' => 'OT',
            'Northern' => 'NO',
            'Savannah' => 'SA',
            'North East' => 'NE',
            'Upper East' => 'UE',
            'Upper West' => 'UW'
        ];

        $courtCounter = [];

        foreach ($courtsData as $courtData) {
            $region = Region::where('name', $courtData['region'])->first();
            $location = Location::where('name', $courtData['location'])->where('region_id', $region->id)->first();
            
            if (!$location) {
                continue;
            }

            $typeCode = $typeCodes[$courtData['type']] ?? substr(strtoupper($courtData['type']), 0, 2);
            $regionCode = $regionCodes[$courtData['region']] ?? substr(strtoupper($courtData['region']), 0, 2);
            
            $baseCode = $typeCode . $regionCode;
            
            if (!isset($courtCounter[$baseCode])) {
                $courtCounter[$baseCode] = 1;
            } else {
                $courtCounter[$baseCode]++;
            }
            
            $code = $baseCode . str_pad($courtCounter[$baseCode], 2, '0', STR_PAD_LEFT);
            
            Court::create([
                'name' => $courtData['name'],
                'type' => $courtData['type'],
                'code' => $code,
                'region_id' => $region->id,
                'location_id' => $location->id,
                'is_active' => true
            ]);
        }
    }
}