<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Traits\CommonTraits;
use App\Models\News;
use App\Models\NewsDetail;
use Carbon\Carbon;

class FetchNews extends Command
{
    use CommonTraits;
    public $apiUrl;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiUrl = config('services.cricket-champion.endpoint').'news/'.config('services.cricket-champion.token');
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
            if ($res && $res['status']) {
                foreach ($res['data'] as $key => $value) {
                    $news_id = (isset($value['news_id']) && !empty($value['news_id'])) ? $value['news_id'] : NULL;
                    if(!empty($news_id)){
                        $params = array(
                            "news_id" => $news_id,
                            "title" => (isset($value['title']) && !empty($value['title'])) ? $value['title'] : NULL,
                            "description" => (isset($value['description']) && !empty($value['description'])) ? $value['description'] : NULL,
                            "image" => (isset($value['image']) && !empty($value['image'])) ? $value['image'] : NULL,
                            "pub_date" => (isset($value['pub_date']) && !empty($value['pub_date'])) ? $value['pub_date'] : NULL,
                            "source" => "CricketChampion",
                            "status" => 1,
                            "created_at" => Carbon::now()->format("Y-m-d h:i:s"),
                            "updated_at" => Carbon::now()->format("Y-m-d h:i:s"),
                        );
                        $data = News::where('news_id', $news_id)->first();
                        if ($data && !empty($data)) {
                            // Update
                            // $data->update($params);
                        }
                        else{
                            News::insert($params);
                            $paramsDetail = array(
                                "news_id" => $news_id,
                                "news_content" => (isset($value['content']) && !empty($value['content'])) ? implode('|', $value['content']) : NULL,
                                "created_at" => Carbon::now()->format("Y-m-d h:i:s"),
                                "updated_at" => Carbon::now()->format("Y-m-d h:i:s"),
                            );
                            NewsDetail::insert($paramsDetail);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
            $this->captureExceptionLog($th, $this->signature);
        }
        return 0;
    }
}
