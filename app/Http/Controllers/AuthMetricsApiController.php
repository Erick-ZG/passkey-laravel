<?php

namespace App\Http\Controllers;

use App\Models\AuthMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthMetricsApiController extends Controller
{
    public function start(Request $request)
    {
        $kind = $request->string('kind')->value() ?: 'password_login';
        $state = (string) Str::uuid();

        $metric = AuthMetric::create([
            'kind'       => $kind,
            'flow_state' => $state,
            'started_at' => now(),
            'context'    => [
                'ip'        => $request->ip(),
                'userAgent' => $request->userAgent(),
                'from'      => 'api/metrics/start',
            ],
        ]);

        return response()->json([
            'metricId' => $metric->id,
            'state'    => $state,
        ]);
    }

    public function finish(Request $request)
    {
        $metric = AuthMetric::findOrFail($request->string('metricId'));
        $method = $request->string('method')->value() ?: 'unknown';
        $success = (bool) $request->boolean('success', true);

        $metric->fill([
            'method'      => $method,
            'success'     => $success,
            'ended_at'    => now(),
        ]);
        if ($metric->started_at) {
            $metric->duration_ms = $metric->started_at->diffInMilliseconds($metric->ended_at);
        }
        $metric->save();

        return response()->json(['ok' => true]);
    }
}
