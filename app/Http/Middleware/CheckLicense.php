<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;

class CheckLicense
{
    public function handle(
        Request $request,
        Closure $next
    ) {

        $expiresAt = Setting::getValue(
            'license_expires_at'
        );

        if (!$expiresAt) {

            return response()->json([
                'message' => 'Licence non configurée.'
            ], 403);

        }

        if (now()->gt($expiresAt)) {

            return response()->json([
                'message' => 'Licence expirée.'
            ], 403);

        }

        return $next($request);
    }
}