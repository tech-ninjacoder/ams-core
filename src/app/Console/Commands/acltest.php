<?php

namespace App\Console\Commands;

use App\Models\Core\Auth\User;
use App\Models\Tenant\Project\Project;
use App\Models\Tenant\Project\ProjectUser;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class acltest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'acl:test';

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
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Loop until a valid user ID is provided
        while (true) {
            $userId = $this->ask('Please enter the user ID');

            // Validate user ID
            if (User::find($userId)) {
                break; // Exit the loop if the user ID is valid
            }

            $this->error('Invalid user ID. Please enter a valid user ID.');
        }

        // Loop until a valid date is provided
        while (true) {
            $dateInput = $this->ask('Please enter the date (YYYY-MM-DD)');

            // Validate the date format
            $date = DateTime::createFromFormat('Y-m-d', $dateInput);

            if ($date && $date->format('Y-m-d') === $dateInput) {
                // Use Carbon to ensure it’s a valid date
                $date = Carbon::parse($dateInput);
                break; // Exit the loop if the date is valid
            }

            $this->error('Invalid date format. Please enter a valid date (YYYY-MM-DD).');
        }

        // Log the user and date for debugging
        $this->info("Processing user with ID: {$userId} and date: {$date->format('Y-m-d')}");

        // Fetch projects where the user was assigned on the given date
        $projectUser = ProjectUser::where('user_id', $userId)
            ->where(function ($query) use ($date) {
                $query->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date);
            })
            ->get();

        // Log the result
        Log::info('projectUser ' . json_encode($projectUser));

        // Output the result
        if ($projectUser->isEmpty()) {
            $this->info("No projects assigned to user ID: {$userId} on date: {$date->format('Y-m-d')}");
        } else {
            $this->info("Projects assigned to user ID: {$userId} on date: {$date->format('Y-m-d')}:");
            foreach ($projectUser as $entry) {
                $this->line("Project ID: {$entry->project_id}, Start Date: {$entry->start_date}, End Date: {$entry->end_date}");
            }
        }

        return 0;
    }
}
