use App\Events\ExportRfqOpened;
use App\Events\ExportContractSent;
use App\Events\ExportContractSigned;
use App\Events\ExportContractFunded;
use App\Events\ExportContractShipping;
use App\Events\ExportContractClosed;
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Contract;
use App\Models\ExportOffer;
use Illuminate\Http\Request;
use App\Services\Chat;
use App\Events\ExportRfqOpened;
use App\Events\ExportContractSent;
use App\Events\ExportContractSigned;
use App\Events\ExportContractFunded;
use App\Events\ExportContractShipping;
use App\Events\ExportContractClosed;

class ExportWorkflowController extends ApiController
{
    // ملاحظة: استخدم وسيط الصلاحيات بدلاً من التحقق اليدوي
    // EN: Use RoleAuthorization middleware instead of manual checks

    public function __construct()
    {
        $this->middleware('role:consumer,restaurant,exporter,admin')->only('rfq');
        $this->middleware('role:exporter,mill,packer,admin')->only('createContract');
        $this->middleware('role:admin')->only('fund');
    }

    public function rfq(Request $request, ExportOffer $offer)
    {
    $user = $request->user();
    $thread = Chat::ensureThread('export_offer', $offer->id, [$offer->seller_id, $user->id]);
    Chat::system($thread, '📄 RFQ opened by user #'.$user->id);
    event(new ExportRfqOpened($offer->id, $user->id)); // حدث فتح طلب عرض تصدير
    $this->audit('export.rfq', 'export_offer', $offer->id);
    return $this->ok(['offer_id' => $offer->id]);
    }

    public function createContract(Request $request)
    {
        $data = $request->validate([ 'export_offer_id' => ['required','integer','exists:export_offers,id'], 'buyer_id' => ['required','integer','exists:users,id'], 'payment_term' => ['required','in:lc,tt'] ]);
    $c = Contract::create([ 'export_offer_id' => $data['export_offer_id'], 'buyer_id' => $data['buyer_id'], 'payment_term' => $data['payment_term'], 'status' => 'sent' ]);
    $thread = Chat::ensureThread('export_offer', $data['export_offer_id'], []);
    Chat::system($thread, '📑 Contract sent #'.$c->id);
    event(new ExportContractSent($c->id)); // حدث إرسال عقد التصدير
    $this->audit('export.contract.sent', 'contract', $c->id);
    return $this->ok($c, 201);
    }

    public function sign(Request $request, Contract $contract)
    {
        $user = $request->user();
        if ((int)$user->id !== (int)$contract->buyer_id && $user->role !== 'admin') {
            // تعليق: رفض الوصول إذا لم يكن المشتري أو المدير
            // EN: Deny access if not buyer or admin
            abort(403, trans('auth.forbidden_action'));
        }
    $contract->status = 'signed'; $contract->signed_at = now(); $contract->save();
    $thread = Chat::ensureThread('export_offer', $contract->export_offer_id, []);
    Chat::system($thread, '✍️ Contract signed #'.$contract->id);
    event(new ExportContractSigned($contract->id, $contract->buyer_id)); // حدث توقيع عقد التصدير
    $this->audit('export.contract.signed', 'contract', $contract->id);
    return $this->ok($contract);
    }

    public function fund(Request $request, Contract $contract)
    {
    $contract->status = 'funded'; $contract->save();
    $thread = Chat::ensureThread('export_offer', $contract->export_offer_id, []);
    Chat::system($thread, '🏦 Contract funded #'.$contract->id);
    event(new ExportContractFunded($contract->id)); // حدث تمويل عقد التصدير
    $this->audit('export.contract.funded', 'contract', $contract->id);
    return $this->ok($contract);
    }

    public function ship(Request $request, Contract $contract)
    {
        $user = $request->user();
        $offer = ExportOffer::find($contract->export_offer_id);
        if ($user->role !== 'admin' && (int)$offer->seller_id !== (int)$user->id) {
            // تعليق: رفض الوصول إذا لم يكن المدير أو البائع
            // EN: Deny access if not admin or seller
            abort(403, trans('auth.forbidden_action'));
        }
    $contract->status = 'shipping'; $contract->save();
    $thread = Chat::ensureThread('export_offer', $contract->export_offer_id, []);
    Chat::system($thread, '🚢 Contract shipping #'.$contract->id);
    event(new ExportContractShipping($contract->id)); // حدث شحن عقد التصدير
    $this->audit('export.contract.shipping', 'contract', $contract->id);
    return $this->ok($contract);
    }

    public function close(Request $request, Contract $contract)
    {
        $user = $request->user();
        $offer = ExportOffer::find($contract->export_offer_id);
        if ($user->role !== 'admin' && (int)$offer->seller_id !== (int)$user->id) {
            // تعليق: رفض الوصول إذا لم يكن المدير أو البائع
            // EN: Deny access if not admin or seller
            abort(403, trans('auth.forbidden_action'));
        }
    $contract->status = 'closed'; $contract->save();
    $thread = Chat::ensureThread('export_offer', $contract->export_offer_id, []);
    Chat::system($thread, '✅ Contract closed #'.$contract->id);
    event(new ExportContractClosed($contract->id)); // حدث إغلاق عقد التصدير
    $this->audit('export.contract.closed', 'contract', $contract->id);
    return $this->ok($contract);
    }
}
