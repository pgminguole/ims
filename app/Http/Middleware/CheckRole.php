<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        \Illuminate\Support\Facades\Log::info('CheckRole executing', [
            'user' => $user->email,
            'required_roles' => $roles,
            'user_role' => $user->assignedRole->name ?? 'None'
        ]);
        
        // Load role relationship if not loaded
        if (!$user->relationLoaded('assignedRole')) {
            $user->load('assignedRole');
        }

        // Allow super_admin to bypass all role checks
        if ($user->assignedRole && $user->assignedRole->name === 'super_admin') {
            return $next($request);
        }

        // Check if user has a role and if it matches any of the required roles
        if (!$user->assignedRole || !in_array($user->assignedRole->name, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}