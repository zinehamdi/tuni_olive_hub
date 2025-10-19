<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\LoadStoreRequest;
use App\Http\Requests\V1\LoadUpdateRequest;
use App\Http\Resources\LoadResource;
use App\Events\LoadCreated;
use App\Services\Chat;
use App\Models\Load;
use Illuminate\Http\Request;

class LoadController extends ApiController
{
    // Guarded by route middleware in routes/api.php

    public function index(Request $request)
    {
        $this->authorize('viewAny', Load::class);
    $q = Load::query()->with(['owner','pickup','dropoff'])->latest();
    return $this->paginate($q, LoadResource::class);
    }

    public function show(Load $load)
    {
        $this->authorize('view', $load);
    return $this->ok(new LoadResource($load->load(['owner','pickup','dropoff'])));
    }

    public function store(LoadStoreRequest $request)
    {
        $this->authorize('create', Load::class);
        $load = Load::create($request->validated());
        $this->audit('load.created', 'load', $load->id);
        event(new LoadCreated($load->id, $load->owner_id));
        // chat thread for load
        $thread = Chat::ensureThread('load', $load->id, [$load->owner_id]);
        Chat::system($thread, 'تم إنشاء حمولة جديدة.');
    return $this->ok(new LoadResource($load->load(['owner','pickup','dropoff'])), 201);
    }

    public function update(LoadUpdateRequest $request, Load $load)
    {
        $this->authorize('update', $load);
        $load->update($request->validated());
        $this->audit('load.updated', 'load', $load->id);
    return $this->ok(new LoadResource($load->load(['owner','pickup','dropoff'])));
    }

    public function destroy(Load $load)
    {
        $this->authorize('delete', $load);
        $load->delete();
        $this->audit('load.deleted', 'load', $load->id);
        return $this->ok(null, 204);
    }
}
