<?php

namespace App\Http\Controllers\Admin;

use App\Models\AstrologyData;
use App\Models\MatchAstrology;
use App\Models\Pandits;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class AstrologyController extends Controller
{
    use CommonTraits;

    public function uploadMatchAstrology(Request $request)
    {
        try {
            $payload = $request->all();
            $panditId = $payload['pandit_id'];
            $matchId = $payload['match_id'];
            $astrologyData = json_decode($payload['astrology_data'], true);

            // Get the list of table fields
            $tableFields = Schema::getColumnListing((new AstrologyData())->getTable());

            // Update or create astrology data for each zodiac sign
            foreach ($astrologyData as $signData) {
                $sheetName = $signData['sheetName'];
                $signInfo = json_encode($signData['data']);

                // Check if the sheetName matches any table field
                if (in_array($sheetName, $tableFields)) {
                    // Find or create a record based on pandit_id, match_id, and sheetName
                    AstrologyData::updateOrCreate(
                        ['pandit_id' => $panditId, 'match_id' => $matchId],
                        [$sheetName => $signInfo]
                    );
                }
            }

            return response(['message' => 'Data uploaded successfully', 'status' => true, 'data' => $astrologyData], 200);
        } catch (\Exception $e) {
            return response(['error' => 'Internal Server Error', 'status' => false], 500);
        }
    }

    public function generateReport(Request $request) {
        try {
            $houses = [
                'aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo',
                'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces',
            ];
            
            $matchAstrologyCreate = null;

            if ($request->post('moon_sign')) {
                $moonSign = intval($request->post('moon_sign'));
                if (in_array($moonSign, range(1, 12))) {
                    $zodiacSign = $houses[$moonSign - 1];
                    if ($zodiacSign !== false) {
                        $fetchAstrologyData = AstrologyData::select($zodiacSign)
                        ->where('match_id', $request->post('match_id'))
                        ->first();

                        if($fetchAstrologyData) {
                            $data = json_decode($fetchAstrologyData, true);
                            if ($data !== null) {
                                $firstValue = $data[$zodiacSign];
                            } else {
                                $firstValue = "";
                            }
                            $matchAstrologyCreate = MatchAstrology::create([
                                'user_id' => $request->post('user_id'),
                                'match_id' => $request->post('match_id'),
                                'astrology_data' => $firstValue
                            ]); 
                        }
                    }
                }
            }
            
            return response()->json(['msg' => 'Match Astrology Generated Successfully', 'data' => $matchAstrologyCreate, 'success' => true]);
        } catch (\Exception $e) {
            // Handle any errors that occur during data processing
            return response(['error' => $e->getMessage(), 'status' => false], 500);
        }
    }

    public function uploadAstrology(Request $request)
    {
        try {
            $data = $request->input('astrology_data'); // Excel data
            $panditId = $request->input('pandit_id');
            
            // Get the current year and month
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;

            // Check if a record already exists for the current month and year
            $existingRecord = AstrologyData::where('pandit_id', $panditId)
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->first();
            
            if ($existingRecord) {
                // A record already exists for the current month and year
                return response(['error' => 'Astrology data already exists for this pandit for the current month and year', 'status' => false], 200);
            }

            foreach ($data as $row) {
                // Get the keys (column names) of the $row array
                $rowKeys = array_keys($row);

                // Find the column named "Date" regardless of case and spaces
                $filteredKeys = array_filter($rowKeys, function ($columnName) {
                    return strcasecmp(str_replace(' ', '', $columnName), 'date') === 0;
                });

                $dateColumn = array_shift($filteredKeys);

                if ($dateColumn !== false) {
                    // Get the date value from the row
                    $dateValue = $row[$dateColumn];
                    
                    // Append the current year and month to the date
                    $date = Carbon::create($currentYear, $currentMonth, $dateValue)->toDateString();
                    
                    // Initialize an array to store formatted data
                    $formattedData = [
                        'pandit_id' => $panditId,
                        'date' => $date,
                    ];
                    
                    // Iterate over the keys (column names) of the $row array
                    foreach ($rowKeys as $columnName) {
                        // Format the column name to lowercase and remove spaces
                        $formattedColumnName = str_replace(' ', '', strtolower($columnName));
                        
                        // Store the data with the formatted column name
                        $formattedData[$formattedColumnName] = $row[$columnName];
                    }

                    // Insert the formatted data into the database
                    AstrologyData::create([
                        'pandit_id' => $panditId,
                        'date' => $date,
                        'aries' => $formattedData['aries'],
                        'taurus' => $formattedData['taurus'],
                        'gemini' => $formattedData['gemini'],
                        'cancer' => $formattedData['cancer'],
                        'leo' => $formattedData['leo'],
                        'virgo' => $formattedData['virgo'],
                        'libra' => $formattedData['libra'],
                        'scorpio' => $formattedData['scorpio'],
                        'sagittarius' => $formattedData['sagittarius'],
                        'capricorn' => $formattedData['capricorn'],
                        'aquarius' => $formattedData['aquarius'],
                        'pisces' => $formattedData['pisces']
                    ]);
                } else {
                    // Handle the case where the "Date" column is not found in the row
                    return response(['error' => 'Date column not found in the data', 'status' => false], 200);
                }
            }

            // Return a success response if the data was processed successfully
            return response(['message' => 'Data uploaded successfully', 'status' => true], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during data processing
            return response(['error' => $e->getMessage(), 'status' => false], 500);
        }
    }

    public function uploadEditedAstrology(Request $request)
    {
        try {
            $data = $request->input('astrology_data'); // Excel data
            $panditId = $request->input('pandit_id');
            
            // Get the current year and month
            $currentYear = Carbon::now()->year;
            $currentMonth = Carbon::now()->month;

            // Check if records with the same panditId exist for the current month and year
            AstrologyData::where('pandit_id', $panditId)
            ->whereYear('date', $currentYear)
            ->whereMonth('date', $currentMonth)
            ->delete();

            foreach ($data as $row) {
                // Insert the data into the database
                AstrologyData::create([
                    'pandit_id' => $panditId,
                    'date' => $row['date'],
                    'aries' => $row['aries'],
                    'taurus' => $row['taurus'],
                    'gemini' => $row['gemini'],
                    'cancer' => $row['cancer'],
                    'leo' => $row['leo'],
                    'virgo' => $row['virgo'],
                    'libra' => $row['libra'],
                    'scorpio' => $row['scorpio'],
                    'sagittarius' => $row['sagittarius'],
                    'capricorn' => $row['capricorn'],
                    'aquarius' => $row['aquarius'],
                    'pisces' => $row['pisces']
                ]);
            }

            // Return a success response if the data was processed successfully
            return response(['message' => 'Data uploaded successfully', 'status' => true], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during data processing
            return response(['error' => $e->getMessage(), 'status' => false], 500);
        }
    }

    public function fetchUniqueYearsAndMonths()
    {
        try {
            $uniqueDates = AstrologyData::select(DB::raw('YEAR(date) as year'), DB::raw('MONTH(date) as month'))
                ->distinct()
                ->get();

            // Extract years and months from the result
            $years = $uniqueDates->pluck('year');
            $months = $uniqueDates->pluck('month');

            return response(['years' => $years, 'months' => $months, 'status' => true], 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage(), 'status' => false], 200);
        }
    }

    public function fetchByPanditAndMatch(Request $request)
    {
        try {
            $panditId = $request->input('pandit_id');
            $matchId = $request->input('match_id');

            // Assuming you have a model named Astrology
            $astrologyData = AstrologyData::where('pandit_id', $panditId)
            ->where('match_id', $matchId)
            ->get();

            return response()->json(['status' => true, 'astrology_data' => $astrologyData]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error($e);
            return response()->json(['status' => false, 'error' => 'Internal Server Error'], 200);
        }
    }

    public function fetchDataByYearMonthAndPanditId(Request $request)
    {
        try {
            $year = $request->input('year');
            $month = $request->input('month');
            $panditId = $request->input('pandit_id');

            $pandit = Pandits::select('name')
            ->where('id', $panditId)
            ->get();

            $data = AstrologyData::select('date', 'aries', 'taurus', 'gemini', 'cancer', 'leo', 'virgo', 'libra', 'scorpio', 'sagittarius', 'capricorn', 'aquarius', 'pisces')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('pandit_id', $panditId)
            ->get();

            return response(['data' => $data, 'pandit' =>  $pandit &&  $pandit[0] ? $pandit[0]->name : 'No Pandit Selected', 'status' => true], 200);
        } catch (\Exception $e) {
            return response(['error' => $e->getMessage(), 'status' => false], 200);
        }
    }

}
