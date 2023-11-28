<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommonTraits;
use App\Models\Matches;
use Illuminate\Support\Facades\File;

class FetchRecentMatches extends Command
{
    use CommonTraits;
    public $apiUrl;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:recent-matches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUrl = config('services.cricket-champion.endpoint').'recentMatches/'.config('services.cricket-champion.token');
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
            $res = $this->pullData($this->apiUrl,'GET');

            // comment the below before live
            // $path = public_path() . "/recentMatches.json";
            // $res = File::get($path);
                    
            $res = json_decode($res, true);
            if(isset($res['status']) && !empty($res['status'])){
                foreach ($res['data'] as $key => $value) {
                    $params = array(
                        "match_id" => (isset($value['match_id']) && !empty($value['match_id'])) ? $value['match_id'] : NULL,
                        "series_id" => (isset($value['series_id']) && !empty($value['series_id'])) ? $value['series_id'] : NULL,
                        "series" => (isset($value['series']) && !empty($value['series'])) ? $value['series'] : NULL,
                        "date_wise" => (isset($value['date_wise']) && !empty($value['date_wise'])) ? $value['date_wise'] : NULL,
                        "match_date" => (isset($value['match_date']) && !empty($value['match_date'])) ? $value['match_date'] : NULL,
                        "match_time" => (isset($value['match_time']) && !empty($value['match_time'])) ? $value['match_time'] : NULL,
                        "matchs" => (isset($value['matchs']) && !empty($value['matchs'])) ? $value['matchs'] : NULL,
                        "venue" => (isset($value['venue']) && !empty($value['venue'])) ? $value['venue'] : NULL,
                        "match_type" => (isset($value['match_type']) && !empty($value['match_type'])) ? $value['match_type'] : NULL,
                        "result" => (isset($value['result']) && !empty($value['result'])) ? $value['result'] : NULL,
                        "team_a_id" => (isset($value['team_a_id']) && !empty($value['team_a_id'])) ? $value['team_a_id'] : NULL,
                        "team_a" => (isset($value['team_a']) && !empty($value['team_a'])) ? $value['team_a'] : NULL,
                        "team_a_short" => (isset($value['team_a_short']) && !empty($value['team_a_short'])) ? $value['team_a_short'] : NULL,
                        "team_a_img" => (isset($value['team_a_img']) && !empty($value['team_a_img'])) ? $value['team_a_img'] : NULL,
                        "team_a_scores" => (isset($value['team_a_scores']) && !empty($value['team_a_scores'])) ? $value['team_a_scores'] : NULL,
                        "team_a_scores_over" => (isset($value['team_a_scores_over']) && !empty($value['team_a_scores_over'])) ? $value['team_a_scores_over'] : NULL,
                        "team_a_over" => (isset($value['team_a_over']) && !empty($value['team_a_over'])) ? $value['team_a_over'] : NULL,
                        "team_b_id" => (isset($value['team_b_id']) && !empty($value['team_b_id'])) ? $value['team_b_id'] : NULL,
                        "team_b" => (isset($value['team_b']) && !empty($value['team_b'])) ? $value['team_b'] : NULL,
                        "team_b_short" => (isset($value['team_b_short']) && !empty($value['team_b_short'])) ? $value['team_b_short'] : NULL,
                        "team_b_img" => (isset($value['team_b_img']) && !empty($value['team_b_img'])) ? $value['team_b_img'] : NULL,
                        "team_b_scores" => (isset($value['team_b_scores']) && !empty($value['team_b_scores'])) ? $value['team_b_scores'] : NULL,
                        "team_b_over" => (isset($value['team_b_over']) && !empty($value['team_b_over'])) ? $value['team_b_over'] : NULL,
                        "team_b_scores_over" => (isset($value['team_b_scores_over']) && !empty($value['team_b_scores_over'])) ? $value['team_b_scores_over'] : NULL,
                    );
                    $params['match_category'] = 'recent';
                    $params['source'] = 'CricketChampion';
                    $params['status'] = 1;
                    $data = Matches::where('match_id', $value['match_id'])->first();
                    if($data){
                        // Update
                        $data->update($params);
                    }
                    else{
                        // Insert
                        Matches::create($params);
                    }
                }
            }
            else{
                $this->captureLog($this->signature, 'error');
                $this->captureLog($res['msg'], 'error');
            }
        }catch (\Throwable $th) {
            $this->captureExceptionLog($th, $this->signature);
        }
        return 0;
    }
}