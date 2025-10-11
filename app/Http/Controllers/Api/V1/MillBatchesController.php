<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\MillStorageMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MillBatchesController extends ApiController
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'mill' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        $p = DB::table('mill_batches')->where('mill_id', $user->id)->orderByDesc('id')->paginate(max(1, min(100, (int)$request->input('per_page', 15))))->appends($request->query());
        $meta = [ 'current_page'=>$p->currentPage(),'per_page'=>$p->perPage(),'total'=>$p->total(),'last_page'=>$p->lastPage(),'from'=>$p->firstItem(),'to'=>$p->lastItem() ];
        return response()->json(['success'=>true,'data'=>$p->items(),'meta'=>$meta]);
    }

    public function show(Request $request, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'mill' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        $batch = DB::table('mill_batches')->where('id', $id)->where('mill_id', $user->id)->first();
        if (!$batch) abort(404);
        $movs = MillStorageMovement::query()->where('ref_object_type','batch')->where('ref_object_id',$id)->orderBy('created_at')->get();
        return $this->ok(['batch' => $batch, 'movements' => $movs]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'mill' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        $data = $request->validate(['olive_qty_kg' => ['required','numeric','min:0.001'], 'note' => ['nullable','string','max:200']]);
        $id = DB::table('mill_batches')->insertGetId([
            'mill_id' => $user->id,
            'olive_qty_kg' => $data['olive_qty_kg'],
            'note' => $data['note'] ?? null,
            'started_at' => now(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        MillStorageMovement::create([
            'mill_id' => $user->id,
            'product' => 'olive', 'type' => 'in', 'qty' => $data['olive_qty_kg'], 'unit' => 'kg',
            'ref_object_type' => 'batch', 'ref_object_id' => $id, 'note' => 'Batch start', 'created_at' => now(),
        ]);
        $this->audit('mill.batch.created', 'batch', $id);
        return $this->ok(['id' => $id], 201);
    }

    public function complete(Request $request, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'mill' && $user->role !== 'admin') abort(403, trans('auth.forbidden_action'));
        $data = $request->validate(['oil_qty_l' => ['required','numeric','min:0'], 'extraction_rate' => ['nullable','numeric','min:0','max:1']]);
        $batch = DB::table('mill_batches')->where('id', $id)->where('mill_id', $user->id)->first();
        if (!$batch) abort(404);
        $rate = $data['extraction_rate'] ?? ( (float)$data['oil_qty_l'] > 0 && (float)$batch->olive_qty_kg > 0 ? round(((float)$data['oil_qty_l'] / (float)$batch->olive_qty_kg), 4) : null );
        DB::table('mill_batches')->where('id', $id)->update([
            'oil_qty_l' => $data['oil_qty_l'], 'extraction_rate' => $rate, 'completed_at' => now(), 'updated_at' => now(),
        ]);
        // Movements
        MillStorageMovement::insert([
            [ 'mill_id'=>$user->id,'product'=>'olive','type'=>'out','qty'=>$batch->olive_qty_kg,'unit'=>'kg','ref_object_type'=>'batch','ref_object_id'=>$id,'note'=>'Batch complete','created_at'=>now() ],
            [ 'mill_id'=>$user->id,'product'=>'oil','type'=>'in','qty'=>$data['oil_qty_l'],'unit'=>'l','ref_object_type'=>'batch','ref_object_id'=>$id,'note'=>'Batch complete','created_at'=>now() ],
        ]);
        $this->audit('mill.batch.completed', 'batch', $id);
        return $this->ok(['id' => $id, 'oil_qty_l' => (float)$data['oil_qty_l'], 'extraction_rate' => $rate]);
    }
}
