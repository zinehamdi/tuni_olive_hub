<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Contract;
use App\Models\ExportShipment;
use App\Models\ExportDocument;
use App\Services\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class ExportShipmentsController extends ApiController
{
    // List shipments for a contract
    public function index(Request $request)
    {
        $contractId = (int) $request->query('contract_id');
        $q = ExportShipment::query();
        if ($contractId) $q->where('contract_id', $contractId);
        $items = $q->orderByDesc('id')->get();
        return $this->ok(['shipments' => $items]);
    }

    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'contract_id' => 'required|exists:contracts,id',
            'incoterm' => 'required|in:fob,cif',
            'port_from' => 'required|string',
            'port_to' => 'required|string',
            'vessel' => 'nullable|string',
            'etd_at' => 'nullable|date',
            'eta_at' => 'nullable|date|after_or_equal:etd_at',
        ])->validate();
        $this->authorize('create', ExportShipment::class);
        $shipment = ExportShipment::create($data);
        $this->audit('shipment_create', 'export_shipment', $shipment->id);
        // Chat system log
        $thread = Chat::ensureThread('contract', $shipment->contract_id, []);
        Chat::system($thread, '🚢 Shipment created for contract #'.$shipment->contract_id);
        return $this->ok(['shipment' => $shipment], 201);
    }

    public function update(Request $request, ExportShipment $shipment)
    {
        $data = Validator::make($request->all(), [
            'port_from' => 'sometimes|string',
            'port_to' => 'sometimes|string',
            'vessel' => 'nullable|string',
            'etd_at' => 'nullable|date',
            'eta_at' => 'nullable|date|after_or_equal:etd_at',
            'meta' => 'nullable|array',
        ])->validate();
        $this->authorize('update', $shipment);
        $shipment->fill($data);
        $shipment->save();
        $this->audit('shipment_update', 'export_shipment', $shipment->id);
        return $this->ok(['shipment' => $shipment]);
    }

    public function attachDocument(Request $request, ExportShipment $shipment)
    {
        $data = Validator::make($request->all(), [
            'type' => 'required|in:commercial_invoice,packing_list,coa,origin,phytosanitary,insurance,bl,other',
            'url' => 'required|url',
            'meta' => 'nullable|array',
        ])->validate();
        $this->authorize('attachDocument', $shipment);
        $doc = $shipment->documents()->create($data);
        $this->audit('shipment_attach_document', 'export_document', $doc->id);
        $thread = Chat::ensureThread('contract', $shipment->contract_id, []);
        Chat::system($thread, '📄 Document added: '.$doc->type);
        // Auto-bump status to docs_pending if draft
        if ($shipment->status === 'draft') {
            $shipment->status = 'docs_pending';
            $shipment->save();
        }
        return $this->ok(['document' => $doc, 'shipment' => $shipment], 201);
    }

    public function verifyDocument(Request $request, ExportShipment $shipment, ExportDocument $document)
    {
        if ($document->shipment_id !== $shipment->id) return $this->ok(['error' => 'mismatch'], 404);
        $this->authorize('verifyDocument', $shipment);
        $document->verified_at = now();
        $document->save();
        $this->audit('shipment_verify_document', 'export_document', $document->id);
        $thread = Chat::ensureThread('contract', $shipment->contract_id, []);
        Chat::system($thread, '✅ Document verified: '.$document->type);
        // If all have verified_at, mark shipment ready
        $allVerified = $shipment->documents()->count() > 0 && $shipment->documents()->whereNull('verified_at')->count() === 0;
        if ($allVerified && in_array($shipment->status, ['docs_pending','draft'])) {
            $shipment->status = 'ready';
            $shipment->save();
        }
        return $this->ok(['document' => $document, 'shipment' => $shipment]);
    }

    public function transition(Request $request, ExportShipment $shipment)
    {
        $data = Validator::make($request->all(), [
            'to' => 'required|in:ready,shipped,arrived,cleared,closed',
        ])->validate();
        $this->authorize('transition', $shipment);
        $to = $data['to'];
        $allowed = [
            'ready' => ['docs_pending','draft','ready'],
            'shipped' => ['ready','shipped'],
            'arrived' => ['shipped','arrived'],
            'cleared' => ['arrived','cleared'],
            'closed' => ['cleared','closed'],
        ];
        if (!in_array($shipment->status, $allowed[$to] ?? [], true)) {
            return $this->ok(['error' => 'transition_not_allowed'], 422);
        }
        $shipment->status = $to;
        $shipment->save();
        $this->audit('shipment_transition:'.$to, 'export_shipment', $shipment->id);
        $thread = Chat::ensureThread('contract', $shipment->contract_id, []);
        Chat::system($thread, '🔁 Shipment status -> '.$to);
        return $this->ok(['shipment' => $shipment]);
    }
}
