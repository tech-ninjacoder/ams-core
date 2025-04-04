<?php

namespace App\Console\Commands;

use App\Models\Tenant\Project\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class non_children_projects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:main';

    /**
     * Get Projects that doesn't have parents
     *
     * @var string
     */
    protected $description = 'Get Projects that doesnt have parents';

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
        $geofences = Project::whereDoesntHave('parent')->get();
        Log::info(json_encode($geofences));
        return 0;
    }
}
