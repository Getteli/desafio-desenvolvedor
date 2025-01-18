<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class GraphqlResponseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try
        {
            $req = request()->all()["query"] ?? "";
            $response = $next($request);

            if ($response->isSuccessful())
            {
                if(isset($response->original['errors'][0]['status_code']))
                {
                    $response->setStatusCode((int)$response->original['errors'][0]['status_code']);
                }
                else
                {
                    if ((strpos($req, 'update') !== false) || (strpos($req, 'delete') !== false))
                    {
                        $response->setStatusCode(HttpResponse::HTTP_OK);
                    }
                    elseif (strpos($req, 'create') !== false)
                    {
                        $response->setStatusCode(HttpResponse::HTTP_CREATED);
                    }
                    elseif (strpos($req, 'requestPasswordReset') !== false)
                    {
                        $response->setStatusCode(HttpResponse::HTTP_ACCEPTED);
                    }
                    else
                    {
                        $response->setStatusCode(HttpResponse::HTTP_OK);
                    }
                }
            }
            elseif ($response->isClientError())
            {
                $response->setStatusCode((int)$response->getStatusCode());
            }
            elseif ($response->isServerError())
            {
                $response->setStatusCode(HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $response;
        }
        catch (\Exception $e)
        {
            // Captura exceções e define o status como 500
            return response()->json([
                'error' => $e->getMessage()
            ], HttpResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
