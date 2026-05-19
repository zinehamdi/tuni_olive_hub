<x-mail::message>
# مرحباً بك في منصة الزين! / Bienvenue sur ZinToop!

أهلاً وسهلاً بك يا **{{ $user->name }}** في عائلة منصة الزين لزيت الزيتون التونسي.

لقد تم تفعيل حسابك بنجاح، وأنت الآن جزء من أكبر شبكة رقمية تجمع الفلاحة، المعاصر، وتجار زيت الزيتون في تونس.
<br>
Votre compte a été créé avec succès. Vous faites désormais partie du plus grand réseau numérique d'huile d'olive en Tunisie.

<x-mail::button :url="route('dashboard')" color="success">
الذهاب إلى لوحة التحكم / Tableau de Bord
</x-mail::button>

إذا كان لديك أي استفسار، فريق الدعم الفني الخاص بنا في خدمتك.

مع خالص التحيات،<br>
فريق {{ config('app.name', 'ZinToop') }}
</x-mail::message>
