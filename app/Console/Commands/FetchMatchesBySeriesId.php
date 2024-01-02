<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommonTraits;
use App\Models\Series;
use App\Models\Matches;
use Illuminate\Support\Facades\File;

class FetchMatchesBySeriesId extends Command
{
    use CommonTraits;
    public $apiUrl;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:matches-by-series-id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull matches by series id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUrl = config('services.cricket-champion.endpoint').'upcomingMatchesBySeriesId/'.config('services.cricket-champion.token');
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $seriesData = Series::where('status', 1)->orderBy('series_id', 'asc')->get();
            if($seriesData){
                foreach ($seriesData as $key => $value) {
                    $postData = [
                        'series_id' => $value->series_id
                    ];
                    $res = $this->pullData($this->apiUrl,'POST',$postData);
                    
                    // comment the below before live
                    // $path = public_path() . "/matchesBySeriesId.json";
                    // $res = File::get($path);
                    
                    $res = json_decode($res, true);
                    
                    if($res['status']){
                        foreach ($res['data'] as $iKey => $iVal) {
                            $data = Matches::where('series_id', $value->series_id)->where('match_id',$iVal['match_id'])->first();
                            $params = $iVal;
                            $params['series_id'] = $value->series_id;
                            $params['source'] = 'CricketChampion';
                            $params['status'] = 1;

                            if($data){
                                // Update
                                if($data->source == 'admin'){
                                    // Add the fields that you dont want to update from API.
                                    unset($params['match_category']);
                                    $params['source'] = 'admin';
                                }
                                $data->update($params);
                            }
                            else{
                                // Insert
                                Matches::create($params);
                            }
                        }
                    }
                    else{
                        // $this->captureLog($this->signature, 'error');
                        // $this->captureLog($res['msg'], 'error');
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th, $this->signature);
        }
        return 0;
    }
}
