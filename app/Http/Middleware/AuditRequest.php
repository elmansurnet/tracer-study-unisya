<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditRequest
{
    /**
     * Metode HTTP yang dicatat — exclude GET/HEAD untuk mengurangi volume log.
     */
    private array $loggedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            in_array($request->method(), $this->loggedMethods, true)
            && $request->user()
            && $response->getStatusCode() < 400
        ) {
            ActivityLog::create([
                'user_id'     => $request->user()->id,
                'description' => $request->method() . ' ' . $request->path(),
                'properties'  => [
                    'method'      => $request->method(),
                    'url'         => $request->fullUrl(),
                    'status_code' => $response->getStatusCode(),
                ],
                'ip_address' => $request->ip(),
            ]);
        }

        return $response;
    }
}