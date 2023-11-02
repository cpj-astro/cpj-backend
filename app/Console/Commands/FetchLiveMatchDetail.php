<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommonTraits;
use App\Models\Matches;

class FetchLiveMatchDetail extends Command
{
    use CommonTraits;
    public $apiUrl;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:live-match-details';

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
        $this->apiUrl = config('services.cricket-champion.endpoint') . 'liveMatch/' . config('services.cricket-champion.token');
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
            $data = Matches::where('status', 1)->where('match_category', '=', 'live')->get();
            if ($data) {
                foreach ($data as $key => $value) {
                    if (isset($value->match_id) && !empty($value->match_id)) {
                        $postData = [
                            'match_id' => $value->match_id
                        ];
                        $res = $this->pullData($this->apiUrl, 'POST', $postData);
                        $res = json_decode($res, true);
                        // $this->captureLog($res);
                        if (!empty($res) && $res['status']) {
                            if (isset($res['data']) && !empty($res['data'])) {
                                $resp = $res['data'];
                                $params = array(
                                    "match_over" => isset($resp['match_over']) && !empty($resp['match_over']) ? $resp['match_over'] : NULL,
                                    "min_rate" => isset($resp['min_rate']) && !empty($resp['min_rate']) ? $resp['min_rate'] : NULL,
                                    "max_rate" => isset($resp['max_rate']) && !empty($resp['max_rate']) ? $resp['max_rate'] : NULL,
                                    "fav_team" => isset($resp['fav_team']) && !empty($resp['fav_team']) ? $resp['fav_team'] : NULL,
                                    "toss" => isset($resp['toss']) && !empty($resp['toss']) ? $resp['toss'] : NULL,
                                    "result" => isset($resp['result']) && !empty($resp['result']) ? $resp['result'] : NULL,
                                    "second_circle" => isset($resp['second_circle']) && !empty($resp['second_circle']) ? $resp['second_circle'] : NULL,
                                    "s_ovr" => isset($resp['s_ovr']) && !empty($resp['s_ovr']) ? $resp['s_ovr'] : NULL,
                                    "s_min" => isset($resp['s_min']) && !empty($resp['s_min']) ? $resp['s_min'] : NULL,
                                    "s_max" => isset($resp['s_max']) && !empty($resp['s_max']) ? $resp['s_max'] : NULL,
                                    "current_inning" => isset($resp['current_inning']) && !empty($resp['current_inning']) ? $resp['current_inning'] : NULL,
                                    "batting_team" => isset($resp['batting_team']) && !empty($resp['batting_team']) ? $resp['batting_team'] : NULL,
                                    "balling_team" => isset($resp['balling_team']) && !empty($resp['balling_team']) ? $resp['balling_team'] : NULL,
                                    "session" => isset($resp['session']) && !empty($resp['session']) ? $resp['session'] : NULL,
                                    "first_circle" => isset($resp['first_circle']) && !empty($resp['first_circle']) ? $resp['first_circle'] : NULL,
                                    "team_a_score" => isset($resp['team_a_score']) && !empty($resp['team_a_score']) ? $resp['team_a_score'] : NULL,
                                    "curr_rate" => isset($resp['curr_rate']) && !empty($resp['curr_rate']) ? $resp['curr_rate'] : NULL,
                                    "rr_rate" => isset($resp['rr_rate']) && !empty($resp['rr_rate']) ? preg_replace("/[^0-9.]/", 0, $resp['rr_rate']) : NULL,
                                    "team_a_scores" => isset($resp['team_a_scores']) && !empty($resp['team_a_scores']) ? $resp['team_a_scores'] : NULL,
                                    "team_a_over" => isset($resp['team_a_over']) && !empty($resp['team_a_over']) ? $resp['team_a_over'] : NULL,
                                    "team_a_scores_over" => isset($resp['team_a_scores_over']) && !empty($resp['team_a_scores_over']) ? $resp['team_a_scores_over'] : NULL,
                                    "team_b_scores" => isset($resp['team_b_scores']) && !empty($resp['team_b_scores']) ? $resp['team_b_scores'] : NULL,
                                    "team_b_over" => isset($resp['team_b_over']) && !empty($resp['team_b_over']) ? $resp['team_b_over'] : NULL,
                                    "team_b_scores_over" => isset($resp['team_b_scores_over']) && !empty($resp['team_b_scores_over']) ? $resp['team_b_scores_over'] : NULL,
                                    "lastwicket" => isset($resp['lastwicket']) && !empty($resp['lastwicket']) ? $resp['lastwicket'] : NULL,
                                    "batsman" => isset($resp['batsman']) && !empty($resp['batsman']) ? $resp['batsman'] : NULL,
                                    "partnership" => isset($resp['partnership']) && !empty($resp['partnership']) ? $resp['partnership'] : NULL,
                                    "bowler" => isset($resp['bolwer']) && !empty($resp['bolwer']) ? $resp['bolwer'] : NULL,
                                    "last4overs" => isset($resp['last4overs']) && !empty($resp['last4overs']) ? $resp['last4overs'] : NULL,
                                    "yet_to_bat" => isset($resp['yet_to_bet']) && !empty($resp['yet_to_bet']) ? $resp['yet_to_bet'] : NULL,
                                    "target" => isset($resp['target']) && !empty($resp['target']) ? $resp['target'] : NULL,
                                );
                                // If the matched is moved by admin then do not get the match odds from API till the toss. After toss update the match odds from API.
                                if(!($value->source == 'admin' && $params['toss'] === NULL)){

                                    $params = array_merge($params, $this->prepareMatchData($value));
                                }
                                Matches::where('match_id', $value->match_id)->update($params);
                            }
                        } else {
                            // $this->captureLog($this->signature, 'error');
                            // $this->captureLog((isset($res['msg']) ? $res['msg'] : 'Something went wrong'), 'error');
                        }
                        usleep(1000000); // 1000000 = 1 second
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th, $this->signature);
        }
        return 0;
    }
}
