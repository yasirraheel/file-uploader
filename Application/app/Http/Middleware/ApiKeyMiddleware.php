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
        $apiKey = $request->header('X-API-KEY') ?? $request->query('api_key');

        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Key is missing',
            ], 401);
        }

        $admin = Admin::where('api_key', $apiKey)->first();

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid API Key',
            ], 401);
        }

        // Optionally attach admin to request if needed
        // $request->merge(['admin' => $admin]);

        return $next($request);
    }
}
