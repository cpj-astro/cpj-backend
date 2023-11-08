<?php

namespace App\Http\Controllers\Admin;

use App\Models\AstrologyData;
use App\Models\Pandits;
use Illuminate\Http\Request;
use App\Traits\CommonTraits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class AstrologyController extends Controller
{
    use CommonTraits;

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

}
