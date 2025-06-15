<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // إذا لم يكن المستخدم مسجّل أو ليس من نوع admin
        if (! $request->user() || $request->user()->type !== 'admin') {
            // يمكنك إما ترجيع JSON أو إعادة توجيه
            return response()->json(['message' => 'Unauthorized.'], 403);
        }
        return $next($request);
    }
}
