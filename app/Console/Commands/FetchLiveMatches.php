<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommonTraits;
use App\Models\Matches;
use Illuminate\Support\Facades\File;

class FetchLiveMatches extends Command
{
    use CommonTraits;
    public $apiUrl;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:live-matches';

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
        $this->apiUrl = config('services.cricket-champion.endpoint') . 'liveMatchList/' . config('services.cricket-champion.token');
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
            $res = $this->pullData($this->apiUrl, 'GET');
            $res = json_decode($res, true);
            
            if (!empty($res) && $res['status']) {
                foreach ($res['data'] as $key => $value) {
                    $params = array(
                        "series" => (isset($value['series']) && !empty($value['series'])) ? $value['series'] : NULL,
                        "match_date" => (isset($value['match_date']) && !empty($value['match_date'])) ? $value['match_date'] : NULL,
                        "match_time" => (isset($value['match_time']) && !empty($value['match_time'])) ? $value['match_time'] : NULL,
                        "matchs" => (isset($value['matchs']) && !empty($value['matchs'])) ? $value['matchs'] : NULL,
                        "venue" => (isset($value['venue']) && !empty($value['venue'])) ? $value['venue'] : NULL,
                        "match_type" => (isset($value['match_type']) && !empty($value['match_type'])) ? $value['match_type'] : NULL,
                        "fav_team" => (isset($value['fav_team']) && !empty($value['fav_team'])) ? $value['fav_team'] : NULL,
                        "min_rate" => (isset($value['min_rate']) && !empty($value['min_rate'])) ? $value['min_rate'] : NULL,
                        "max_rate" => (isset($value['max_rate']) && !empty($value['max_rate'])) ? $value['max_rate'] : NULL,
                        "s_ovr" => (isset($value['s_ovr']) && !empty($value['s_ovr'])) ? $value['s_ovr'] : NULL,
                        "s_min" => (isset($value['s_min']) && !empty($value['s_min'])) ? $value['s_min'] : NULL,
                        "s_max" => (isset($value['s_max']) && !empty($value['s_max'])) ? $value['s_max'] : NULL,
                        "session" => (isset($value['session']) && !empty($value['session'])) ? $value['session'] : NULL,
                        "team_a_id" => (isset($value['team_a_id']) && !empty($value['team_a_id'])) ? $value['team_a_id'] : NULL,
                        "team_a" => (isset($value['team_a']) && !empty($value['team_a'])) ? $value['team_a'] : NULL,
                        "team_a_short" => (isset($value['team_a_short']) && !empty($value['team_a_short'])) ? $value['team_a_short'] : NULL,
                        "team_a_img" => (isset($value['team_a_img']) && !empty($value['team_a_img'])) ? $value['team_a_img'] : NULL,
                        "team_a_score" => (isset($value['team_a_score']) && !empty($value['team_a_score'])) ? json_encode($value['team_a_score']) : NULL,
                        "team_a_scores_over" => (isset($value['team_a_scores_over']) && !empty($value['team_a_scores_over'])) ? json_encode($value['team_a_scores_over']) : NULL,
                        "team_b_id" => (isset($value['team_b_id']) && !empty($value['team_b_id'])) ? $value['team_b_id'] : NULL,
                        "team_b" => (isset($value['team_b']) && !empty($value['team_b'])) ? $value['team_b'] : NULL,
                        "team_b_short" => (isset($value['team_b_short']) && !empty($value['team_b_short'])) ? $value['team_b_short'] : NULL,
                        "team_b_img" => (isset($value['team_b_img']) && !empty($value['team_b_img'])) ? $value['team_b_img'] : NULL,
                        "team_b_score" => (isset($value['team_b_score']) && !empty($value['team_b_score'])) ? json_encode($value['team_b_score']) : NULL,
                        "team_b_scores_over" => (isset($value['team_b_scores_over']) && !empty($value['team_b_scores_over'])) ? json_encode($value['team_b_scores_over']) : NULL,
                    );
                    $params['match_category'] = 'live';
                    $params['source'] = 'CricketChampion';
                    $params['status'] = 1;
                    $params = array_merge($params, $this->prepareMatchData($value));
                    // $this->captureLog($params);
                    $data = Matches::where('match_id', $value['match_id'])->first();
                    if ($data) {
                        $data->update($params);
                    } else {
                        $params['match_id'] = $value['match_id'];
                        $data = Matches::create($params);
                    }
                }
            } else {
                // $this->captureLog($this->signature, 'error');
                // $this->captureLog($res['msg'], 'error');
            }
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th, $this->signature);
        }
        return 0;
    }
}
