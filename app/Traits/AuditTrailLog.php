<?php

namespace App\Traits;

use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

trait AuditTrailLog
{
    public $message;

    public function createAuditTrail($message)
    {
        $agent = new Agent();
        $browser = $agent->browser();
        $browser_version = $agent->version($browser);

        $platform = $agent->platform();
        $platform_version = $agent->version($platform);


        return AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => $message,
            'username' => Auth::user()->username,
            'ip_address' => request()->ip(),
            'browser' => $browser.' '.$browser_version,
            'platform' => $platform .' '. $platform_version,
            'device' => $agent->device(),
        ]);
    }

    public function logUser()
    {
        if (Auth::check()) {

            $agent = new Agent();
            $browser = $agent->browser();
            $browser_version = $agent->version($browser);

            $platform = $agent->platform();
            $platform_version = $agent->version($platform);


            return AuditTrail::create([
                'user_id' => Auth::id(),
                'action' => $message,
                'username' => Auth::user()->username,
                'ip_address' => request()->ip(),
                'browser' => $browser.' '.$browser_version,
                'platform' => $platform .' '. $platform_version,
                'device' => $agent->device(),
            ]);
        }
    }
}
