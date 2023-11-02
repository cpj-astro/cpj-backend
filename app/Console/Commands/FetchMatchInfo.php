<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommonTraits;
use App\Models\Matches;
use Illuminate\Support\Facades\File;

class FetchMatchInfo extends Command
{
    use CommonTraits;
    public $apiUrl;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:match-info';

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
        $this->apiUrl = config('services.cricket-champion.endpoint') . 'matchInfo/' . config('services.cricket-champion.token');
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
            $data = Matches::where('status', 1)->where('match_category', '!=', 'recent')->get();
            if ($data) {
                foreach ($data as $key => $value) {
                    if (isset($value->match_id) && !empty($value->match_id)) {
                        $postData = [
                            'match_id' => $value->match_id
                        ];
                        $res = $this->pullData($this->apiUrl, 'POST', $postData);

                        // comment the below before live
                        // $path = public_path() . "/matchInfo.json";
                        // $res = File::get($path);

                        $res = json_decode($res, true);

                        if (!empty($res) && $res['status']) {
                            if (isset($res['data']) && !empty($res['data'])) {
                                $resp = $res['data'];
                                // $this->captureLog($resp, 'debug');
                                $params = array(
                                    "matchs" => $resp['matchs'],
                                    "match_date" => $resp['match_date'],
                                    "match_time" => $resp['match_time'],
                                    "venue" => $resp['venue'],
                                    "toss" => $resp['toss'],
                                    "umpire" => $resp['umpire'],
                                    "third_umpire" => $resp['third_umpire'],
                                    "referee" => $resp['referee'],
                                    "man_of_match" => $resp['man_of_match'],
                                    "match_type" => $resp['match_type'],
                                    "team_a_id" => $resp['team_a_id'],
                                    "team_a" => $resp['team_a'],
                                    "team_a_short" => $resp['team_a_short'],
                                    "team_a_img" => $resp['team_a_img'],
                                    "team_b_id" => $resp['team_b_id'],
                                    "team_b" => $resp['team_b'],
                                    "team_b_short" => $resp['team_b_short'],
                                    "team_b_img" => $resp['team_b_img'],

                                );
                                Matches::where('match_id', $value->match_id)->update($params);
                            }
                        } else {
                            // $this->captureLog($this->signature, 'error');
                            // $this->captureLog((isset($res['msg']) ? $res['msg'] : 'Something went wrong'), 'error');
                        }
                        usleep(500000); // 1000000 = 1 second
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->captureExceptionLog($th, $this->signature);
        }
        return 0;
    }

}
