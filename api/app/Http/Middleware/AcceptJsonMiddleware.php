<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptJsonMiddleware
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
        // Force Accept header to application/json
        $request->headers->set('Accept', 'application/json');
        
        // If this is a POST/PUT/PATCH and doesn't have a Content-Type, set it
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH']) && 
            !$request->headers->has('Content-Type')) {
            $request->headers->set('Content-Type', 'application/json');
        }
        
        // Try to fix common JSON formatting issues
        if ($request->isJson() || $request->headers->get('Content-Type') === 'application/json') {
            $content = $request->getContent();
            if (!empty($content)) {
                // Remove trailing commas in objects and arrays
                $fixedContent = preg_replace('/,\s*([\]}])/m', '$1', $content);
                
                // Only replace if we actually fixed something and it parses as valid JSON
                if ($fixedContent !== $content && json_decode($fixedContent) !== null) {
                    // Create a new Request with the fixed content
                    $newRequest = clone $request;
                    $reflection = new \ReflectionObject($newRequest);
                    $contentProperty = $reflection->getProperty('content');
                    $contentProperty->setAccessible(true);
                    $contentProperty->setValue($newRequest, $fixedContent);
                    
                    // Replace request instance
                    $request = $newRequest;
                }
            }
        }
        
        return $next($request);
    }
}
