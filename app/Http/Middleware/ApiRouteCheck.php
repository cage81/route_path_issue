<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
// use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;
use Carbon\Carbon;
use App\Models\AllowedIp;

class ApiRouteCheck
{
    protected const HOSTS_CALLER = ['192.168.1.215'];

    protected $ip_list = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next) // : Response
    {
        // var_dump($request);

        Log::debug(__CLASS__ . " " . __FUNCTION__ . " company " . $request->route('company'));

        $request->dt_req = Carbon::now('Europe/Rome')->format('Y-m-d H:i:s');

        Log::debug(__CLASS__ . " " . __FUNCTION__ . " request " . $request->getMethod() . " " . $request->getPathInfo() . " host " . $request->header('host') . " x-real-ip " . $request->header('x-real-ip') . " headers->all " . json_encode($request->headers->all()));

        try {
            $aip = AllowedIp::whereCompany($request->route('company'))->first();
            if (empty($aip))
                throw new \Exception('Unable to find');

        } catch (\Exception $e) {
            Log::debug(__CLASS__ . " " . __FUNCTION__ . " HTTP_SERVICE_UNAVAILABLE " . $e->getMessage());
            return response()->json(
                [
                    'data' => [],
                    'error_code' => Response::HTTP_SERVICE_UNAVAILABLE,
                    'error_message' => 'SERVICE UNAVAILABLE',
                ],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        Log::debug(__CLASS__ . " " . __FUNCTION__ . " ip " . $aip->ip);

        if (empty($aip->ip))
            return response()->json(
                [
                    'data' => [],
                    'error_code' => Response::HTTP_BAD_REQUEST,
                    'error_message' => 'BAD REQUEST',
                ],
                Response::HTTP_BAD_REQUEST
            );

        $ip_list = json_decode($aip->ip);

        if (in_array($request->header('host'), self::HOSTS_CALLER) || (in_array($request->header('x-real-ip'), $ip_list))) {
            return $next($request);
        }

        return response()->json(
            [
                'data' => [],
                'error_code' => Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED,
                'error_message' => 'Authentication required',
            ],
            Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED
        );
    }
}
