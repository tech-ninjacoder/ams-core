<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckAccessBehavior
{
    public function handle(Request $request, Closure $next)
    {
        $behavior = 'own_departments';

        if (auth()->user()->can('access_all_departments')){
            $behavior = 'all_departments';
        }elseif (auth()->user()->can('access_own_departments')){
            $behavior = 'own_departments';
        }

        $request->merge([
            'access_behavior' => $behavior
        ]);
        Log::info('CheckAccessBehavior');
        Log::info('access_behavior: '.$behavior);

        return $next($request);
    }
}
