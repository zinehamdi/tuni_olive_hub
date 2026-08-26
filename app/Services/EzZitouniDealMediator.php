<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Load;
use App\Models\Thread;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class EzZitouniDealMediator
{
    /**
     * Post a specialized Ez-Zitouni AI mediation message in the chat.
     */
    public static function postMessage(Thread $thread, string $bodyAr, string $bodyFr, string $bodyEn): Message
    {
        $locale = app()->getLocale();
        $text = match ($locale) {
            'fr' => $bodyFr,
            'en' => $bodyEn,
            default => $bodyAr,
        };

        // Prefix with official Zitouni AI indicator
        $formattedBody = "🤖 **" . __('Ez-Zitouni') . "**:\n" . $text;

        return Message::create([
            'thread_id' => $thread->id,
            'sender_id' => Auth::id() ?? 1,
            'body' => $formattedBody,
            'attachments' => ['is_bot' => true, 'bot_name' => 'ezzitouni'],
            'is_flagged' => false,
            'is_deleted' => false,
        ]);
    }

    /**
     * 1. Milestone: Deal Proposed
     */
    public static function onDealCreated(Thread $thread, Order $order): void
    {
        $product = $order->listing?->product?->type === 'olive' ? 'زيتون' : 'زيت زيتون';
        $productFr = $order->listing?->product?->type === 'olive' ? 'Olives' : 'Huile d\'olive';
        $productEn = $order->listing?->product?->type === 'olive' ? 'Olives' : 'Olive Oil';

        $ar = "مرحباً بكما! تم اقتراح صفقة جديدة لـ **{$order->qty} {$order->unit}** من **{$product}** بسعر إجمالي **{$order->total} د.ت** ({$order->price_unit} د.ت / {$order->unit}).\n💡 يمكن للبائع قبول العرض أو اقتراح سعر جديد مباشرة.";
        $fr = "Bonjour ! Une nouvelle offre a été proposée pour **{$order->qty} {$order->unit}** de **{$productFr}** au montant total de **{$order->total} TND** ({$order->price_unit} TND / {$order->unit}).\n💡 Le vendeur peut accepter l'offre ou proposer un nouveau prix.";
        $en = "Hello! A new deal has been proposed for **{$order->qty} {$order->unit}** of **{$productEn}** for a total of **{$order->total} TND** ({$order->price_unit} TND / {$order->unit}).\n💡 The seller can accept the deal or propose a counter-offer.";

        self::postMessage($thread, $ar, $fr, $en);
    }

    /**
     * 2. Milestone: Price Counter-Offer (Renegotiation)
     */
    public static function onCounterOffer(Thread $thread, Order $order, string $proposerName): void
    {
        $ar = "🔄 اقترح **{$proposerName}** سعراً جديداً: **{$order->price_unit} د.ت / {$order->unit}** (الإجمالي الجديد: **{$order->total} د.ت**).\nيُرجى من الطرف الآخر مراجعة السعر الجديد وقبوله أو مواصلة التفاوض.";
        $fr = "🔄 **{$proposerName}** a proposé un nouveau prix : **{$order->price_unit} TND / {$order->unit}** (Nouveau total : **{$order->total} TND**).\nVeuillez vérifier le nouveau prix pour l'accepter ou négocier.";
        $en = "🔄 **{$proposerName}** proposed a counter-offer: **{$order->price_unit} TND / {$order->unit}** (New total: **{$order->total} TND**).\nPlease review the new price to accept or continue negotiating.";

        self::postMessage($thread, $ar, $fr, $en);
    }

    /**
     * 3. Milestone: Deal Confirmed / Accepted
     */
    public static function onDealConfirmed(Thread $thread, Order $order): void
    {
        $ar = "🎉 **تهانينا! تم تأكيد وقبول الصفقة بنجاح!**\n\n"
            . "💡 **إرشادات الزيتوني لإتمام العملية بأمان:**\n"
            . "1️⃣ **طريقة الخلاص:** اتفقا معاً على وسيلة الدفع الأنسب لكما (نقداً عند الاستلام للمبالغ البسيطة، أو تحويل بنكي / شيك مصادق عليه للمبالغ الكبرى مع التحقق من الحساب قبل التسليم).\n"
            . "2️⃣ **النقل والتوصيل:**\n"
            . "   • يمكنكما الضغط على **'استدعاء ناقل'** بالأعلى لاختيار ناقل موثوق من المنصة (مع ميزة التتبع المباشر ورمز الأمان PIN).\n"
            . "   • أو يمكن لأحدكما التكفل بالنقل مباشرة (بسيارته/شاحنته الخاصة أو ناقل معرفة).";

        $fr = "🎉 **Félicitations ! L'offre a été confirmée et acceptée !**\n\n"
            . "💡 **Conseils d'Ez-Zitouni pour finaliser votre transaction en toute sécurité :**\n"
            . "1️⃣ **Règlement :** Convenez ensemble du moyen de paiement (Espèces à la livraison pour les petits montants, ou Virement bancaire / Chèque certifié pour les montants importants avec vérification avant livraison).\n"
            . "2️⃣ **Transport :**\n"
            . "   • Vous pouvez cliquer sur **'Appeler un transporteur'** ci-dessus pour choisir un transporteur de la plateforme (avec suivi GPS et code PIN sécurisé).\n"
            . "   • Ou l'un de vous peut assurer lui-même le transport (véhicule personnel ou transporteur de votre choix).";

        $en = "🎉 **Congratulations! The deal has been confirmed and accepted!**\n\n"
            . "💡 **Ez-Zitouni's safety tips to complete your transaction:**\n"
            . "1️⃣ **Payment:** Agree between yourselves on the best payment method (Cash on delivery for small amounts, or Bank transfer / Certified check for large commercial amounts with verification before release).\n"
            . "2️⃣ **Transport & Logistics:**\n"
            . "   • Click **'Summon Transporter'** above to assign a verified platform carrier (with live GPS tracking and secure PIN verification).\n"
            . "   • Or arrange transportation directly between yourselves (own vehicle or preferred private transporter).";

        self::postMessage($thread, $ar, $fr, $en);
    }

    /**
     * 4. Milestone: Deal Rejected / Cancelled
     */
    public static function onDealRejected(Thread $thread, Order $order, string $cancellerName): void
    {
        $ar = "❌ تم إلغاء / رفض هذا العرض من قِبل **{$cancellerName}**. يمكنكم مناقشة تفاصيل أخرى في المحادثة أو تقديم عرض جديد في أي وقت.";
        $fr = "❌ Cette offre a été annulée / refusée par **{$cancellerName}**. Vous pouvez discuter dans le chat ou soumettre une nouvelle offre.";
        $en = "❌ This offer was cancelled / rejected by **{$cancellerName}**. You can continue chatting or submit a new proposal.";

        self::postMessage($thread, $ar, $fr, $en);
    }

    /**
     * 5. Milestone: Transporter Summoned with Secure PIN Code
     */
    public static function onTransporterSummoned(Thread $thread, Load $load, string $carrierName, string $pinCode, float $estimatedCost): void
    {
        $ar = "🚚 **تم استدعاء الناقل ({$carrierName}) بنجاح!**\nكلفة النقل التقديرية: **~{$estimatedCost} د.ت**.\n\n🔐 **رمز تأكيد الاستلام السري (PIN):** `{$pinCode}`\n⚠️ **تنبيه هام للمشتري:** هذا الرمز هو ضمانك، لا تسلمه للناقل إلا بعد وصول الشحنة وفحصها والتأكد من سلامتها.";
        $fr = "🚚 **Le transporteur ({$carrierName}) a été assigné avec succès !**\nCoût estimé du transport : **~{$estimatedCost} TND**.\n\n🔐 **Code PIN de livraison sécurisé :** `{$pinCode}`\n⚠️ **Rappel pour l'acheteur :** Ce code est votre garantie, ne le communiquez au transporteur qu'après réception et vérification de la marchandise.";
        $en = "🚚 **Transporter ({$carrierName}) assigned successfully!**\nEstimated transport cost: **~{$estimatedCost} TND**.\n\n🔐 **Secure Delivery PIN Code:** `{$pinCode}`\n⚠️ **Notice for Buyer:** This code is your delivery proof. Do not share it with the carrier until the shipment is received and inspected.";

        self::postMessage($thread, $ar, $fr, $en);
    }

    /**
     * 6. Milestone: Delivery Completed
     */
    public static function onDeliveryCompleted(Thread $thread, Load $load): void
    {
        $ar = "✅ **تم تأكيد استلام الشحنة بنجاح عبر رمز الـ PIN!**\nاكتملت جميع مراحل الصفقة والنقل بنجاح. نشكركم على ثقتكم في منصة ZinToop! 🫒✨";
        $fr = "✅ **Livraison confirmée avec succès via le code PIN !**\nToutes les étapes de la transaction et du transport sont terminées. Merci pour votre confiance en ZinToop ! 🫒✨";
        $en = "✅ **Delivery verified and confirmed successfully via PIN code!**\nAll stages of the transaction and transport are complete. Thank you for using ZinToop! 🫒✨";

        self::postMessage($thread, $ar, $fr, $en);
    }
}
