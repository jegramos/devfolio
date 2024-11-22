<?php

namespace Database\Seeders;

use DB;
use Exception;
use Illuminate\Database\Seeder;
use Log;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->delete();

        $filePath = base_path('database/seeders/dumps/countries.csv');
        try {
            $csvFile = fopen($filePath, 'r');
        } catch (Exception $e) {
            Log::channel('stderr')->error($e->getMessage());
        }

        $isInFirstRow = true;
        $countriesArray = [];
        while (($data = fgetcsv($csvFile, 555)) !== false) {
            if (! $isInFirstRow) {
                $countriesArray[] = [
                    'iso' => $data['1'],
                    'name' => $data['2'],
                    'iso3' => $data['3'],
                    'num_code' => $data['4'],
                ];
            }
            $isInFirstRow = false;
        }
        fclose($csvFile);

        DB::table('countries')->insert($countriesArray);
    }
}
