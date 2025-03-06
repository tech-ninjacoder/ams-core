<?php

namespace App\Console\Commands;

use App\Models\Tenant\Project\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeletedProjectMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:deleted_projects';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'one time usage script to restore deleted projects into sub groups of main projects';

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
        $projects = DB::table('projects')->whereNotNull('deleted_at')->get();
        $count = 0;
        foreach ($projects as $project) {
            DB::table('projects')
                ->where('id','=',$project->id)
                ->update(['type' => 0,'deleted_at' => NULL]);
        }
        $project_current = Project::whereNull('type')->get();
        $this->line('current projects => '.$project_current->count());
        foreach ($project_current as $project) {
            $project = Project::where('id','=',$project->id)->first();
            $project->type = 1;
            $project->save();
        }

        $this->line('count all =>'. $count);
        return 0;
    }
}
