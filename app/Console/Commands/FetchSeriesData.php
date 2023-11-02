<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommonTraits;
use App\Models\Series;
use Illuminate\Support\Facades\File;

class FetchSeriesData extends Command
{
    use CommonTraits;
    public $apiUrl;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:series';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch data from series API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUrl = config('services.cricket-champion.endpoint').'seriesList/'.config('services.cricket-champion.token');
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
            $path = public_path() . "/series.json";
            // $res = File::get($path);
            $res = $this->pullData($this->apiUrl,'GET');
            $res = json_decode($res, true);
            // dd($res);
            // if($res['status'] && isset($res['response'])){
                // $resp = is_string($res['response']) ? json_decode($res['response'], true) : $res['response'];
                if($res['status']){
                    Series::where('source', 'CricketChampion')->delete();
                    foreach ($res['data'] as $key => $value) {
                        $data = Series::where('series_id', $value['series_id'])->first();
                        $params = array(
                            'series_id' => $value['series_id'],
                            'series_name' => $value['series'],
                            'series_date' => $value['series_date'],
                            'total_matches' => $value['total_matches'],
                            'start_date' => $value['start_date'],
                            'end_date' => $value['end_date'],
                            'image' => $value['image'],
                            'month_wise' => $value['month_wise'],
                            'source' => 'CricketChampion',
                            'status' => 1,
                        );
                        if($data){
                            // Update
                            $data->update($params);
                        }
                        else{
                            // Insert
                            Series::create($params);
                        }
                    }
                }
                else{
                    $this->captureLog('Series command', 'error');
                    $this->captureLog($res['msg'], 'error');
                }
            // } else{
            //     $this->captureLog($res['msg'], 'error');
            // }
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th, $this->signature);
        }
        return 0;
    }
}
