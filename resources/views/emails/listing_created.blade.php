<x-mail::message>
# تم نشر عرضك بنجاح! / Annonce publiée avec succès!

أهلاً بك، لقد تم نشر عرضك بنجاح على منصة الزين.

**تفاصيل العرض / Détails de l'annonce :**
- **المنتج / Produit :** {{ $listing->product->variety }} - {{ $listing->product->type === 'oil' ? 'زيت زيتون' : 'زيتون' }}
- **الكمية / Quantité :** {{ $listing->quantity }} {{ $listing->unit }}
- **السعر / Prix :** {{ $listing->price > 0 ? $listing->price . ' ' . $listing->currency : 'عند الطلب / Sur demande' }}

<x-mail::button :url="route('listings.show', $listing->id)" color="success">
عرض الإعلان / Voir l'annonce
</x-mail::button>

إذا كان لديك أي استفسار، فريق الدعم الفني الخاص بنا في خدمتك.

مع خالص التحيات،<br>
فريق {{ config('app.name', 'ZinToop') }}
</x-mail::message>
