<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckProjectAccessBehavior
{
    public function handle(Request $request, Closure $next)
    {
        // Determine the default behavior
        $behavior = 'access_all_projects';

        // Check if the user is an admin
        if (auth()->user()->isAdmin()) {
            $behavior = 'access_all_projects';
        } else {
            // Check if the user has the permission 'access_own_projects'
            if (auth()->user()->can('access_own_projects')) {
                $behavior = 'access_own_projects';
            }

            // Check if the user has the role 'department manager'
            if (auth()->user()->hasRole('Project Manager')) {
                $behavior = 'access_dep_projects';
            }
            if (auth()->user()->hasRole('Area Manager')) {
                $behavior = 'access_dep_projects';
            }
        }

        // Merge the behavior into the request
        $request->merge([
            'project_access_behavior' => $behavior
        ]);
        Log::info('role: '.auth()->user());

        Log::info('project_access_behavior: '.$behavior);

        return $next($request);
    }
}
