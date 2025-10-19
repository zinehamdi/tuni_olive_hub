<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\AuditLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

abstract class ApiController extends Controller
{
    use AuthorizesRequests;

    protected function ok($data = null, int $status = 200)
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }

    /**
     * Standard paginated envelope with meta information.
     */
    protected function paginate($query, string $resourceClass, ?int $perPage = null)
    {
        $per = $perPage ?? (int) request('per_page', 15);
        $per = max(1, min(100, $per));
        $p = $query->paginate($per)->appends(request()->query());

        // Build meta section explicitly
        $meta = [
            'current_page' => $p->currentPage(),
            'per_page' => $p->perPage(),
            'total' => $p->total(),
            'last_page' => $p->lastPage(),
            'from' => $p->firstItem(),
            'to' => $p->lastItem(),
        ];

        $data = $resourceClass::collection($p->items());

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => $meta,
        ]);
    }

    protected function audit(string $action, ?string $type = null, ?int $id = null): void
    {
        AuditLog::create([
            'actor_id' => Auth::id(),
            'action' => $action,
            'object_type' => $type,
            'object_id' => $id,
            'ip' => request()->ip(),
            'ua' => (string) request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
