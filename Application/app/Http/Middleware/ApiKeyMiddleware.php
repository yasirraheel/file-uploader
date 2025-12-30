<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for key in Header, Query Param, OR Request Body (Input)
        $apiKey = $request->header('X-API-KEY') ?? $request->input('api_key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Key is missing. Sent: ' . json_encode($request->all()), // Temporary Debug
            ], 401);
        }

        // TRIM whitespace just in case
        $apiKey = trim($apiKey);

        $admin = \DB::table('admins')->where('api_key', $apiKey)->first();

        if (!$admin) {
            // Check if ANY admin has a key set (Debugging purpose)
            $count = \DB::table('admins')->whereNotNull('api_key')->count();

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API Key. (Admin Key Count: ' . $count . ')',
            ], 401);
        }

        return $next($request);
    }
}
