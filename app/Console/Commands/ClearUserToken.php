<?php

namespace App\Console\Commands;

use App\Models\PersonalAccessToken;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearUserToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users token from the database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        PersonalAccessToken::where('name', '!=', 'api_token')->whereNotNull('expires_at')->where('expires_at', '<', Carbon::now())->delete();
        return 0;
    }
}
