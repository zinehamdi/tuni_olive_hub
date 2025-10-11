<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends ApiController
{
    public function show(Invoice $invoice)
    {
        $this->authorize('show', $invoice);
        return $this->ok(['invoice' => $invoice]);
    }

    public function issue(Request $request)
    {
        $data = Validator::make($request->all(), [
            'order_id' => 'nullable|exists:orders,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'seller_id' => 'required|exists:users,id',
            'buyer_id' => 'required|exists:users,id',
            'currency' => 'required|in:TND,USD,EUR',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'meta' => 'nullable|array',
        ])->validate();
        $this->authorize('issue', [Invoice::class, $data]);
        $data['status'] = 'issued';
        // Stub a non-empty PDF URL
        $invoice = Invoice::create($data);
        $invoice->pdf_url = url('/invoices/'.$invoice->id.'.pdf');
        $invoice->save();
        $this->audit('invoice_issue', 'invoice', $invoice->id);
        return $this->ok(['invoice' => $invoice], 201);
    }
}
