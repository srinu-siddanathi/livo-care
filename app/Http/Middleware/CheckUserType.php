<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\DiagnosticCenter;

class CheckUserType
{
    public function handle(Request $request, Closure $next, $type)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $userType = null;
        if ($request->user() instanceof Doctor) {
            $userType = 'doctor';
        } elseif ($request->user() instanceof DiagnosticCenter) {
            $userType = 'diagnostic';
        }

        if ($userType !== $type) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
} 