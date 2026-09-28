import json

translations = {
    "B2B Sourcing & Wholesale Export": {
        "ar": "توريد وشراء B2B وتصدير بالجملة",
        "fr": "Sourcing B2B & Exportation en Gros",
        "en": "B2B Sourcing & Wholesale Export"
    },
    "Lab Tested & Certified": {
        "ar": "مفحوص ومطابق مخبرياً",
        "fr": "Analysé & Certifié en Laboratoire",
        "en": "Lab Tested & Certified"
    },
    "FOB / CIF Shipping Ready": {
        "ar": "جاهز للشحن بعقود FOB / CIF",
        "fr": "Prêt pour l'Expédition FOB / CIF",
        "en": "FOB / CIF Shipping Ready"
    },
    "Zero Broker Fees": {
        "ar": "دون عمولات وسطاء",
        "fr": "Zéro Commission Intermédiaire",
        "en": "Zero Broker Fees"
    },
    "Filter by Variety:": {
        "ar": "تصفية حسب الصنف:",
        "fr": "Filtrer par Variété :",
        "en": "Filter by Variety:"
    },
    "Chemlali": {
        "ar": "الشملالي",
        "fr": "Chemlali",
        "en": "Chemlali"
    },
    "Chetoui": {
        "ar": "الشتوي",
        "fr": "Chétoui",
        "en": "Chetoui"
    },
    "Organic (Bio)": {
        "ar": "عضوي (بيولوجي)",
        "fr": "Biologique (Bio)",
        "en": "Organic (Bio)"
    },
    "Request Custom B2B Quote": {
        "ar": "طلب عرض سعر B2B مخصص",
        "fr": "Demander un Devis B2B Personnalisé",
        "en": "Request Custom B2B Quote"
    },
    "Live Bulk & Wholesale Listings in Tunisia": {
        "ar": "عروض الزيت السائب والجملة المباشرة في تونس",
        "fr": "Offres en Vrac & Gros Directes en Tunisie",
        "en": "Live Bulk & Wholesale Listings in Tunisia"
    },
    "No bulk listings matching this filter currently. Connect with our export desk for direct sourcing.": {
        "ar": "لا توجد عروض سائبة تطابق هذا الفلتر حالياً. تواصل مع مكتب التصدير للشراء المباشر.",
        "fr": "Aucune offre en vrac ne correspond à ce filtre actuellement. Contactez notre bureau d'export pour un sourcing direct.",
        "en": "No bulk listings matching this filter currently. Connect with our export desk for direct sourcing."
    },
    "Why Source Bulk Extra Virgin Olive Oil from Tunisia?": {
        "ar": "لماذا تشتري زيت الزيتون البكر الممتاز السائب من تونس؟",
        "fr": "Pourquoi Acheter de l'Huile d'Olive Extra Vierge en Vrac de Tunisie ?",
        "en": "Why Source Bulk Extra Virgin Olive Oil from Tunisia?"
    },
    "World-Class Polyphenols": {
        "ar": "نسب بوليفينول عالمية",
        "fr": "Teneur Exceptionnelle en Polyphénols",
        "en": "World-Class Polyphenols"
    },
    "Flexible Export Packaging": {
        "ar": "خيارات تعبئة وتصدير مرنة",
        "fr": "Emballages d'Exportation Flexibles",
        "en": "Flexible Export Packaging"
    },
    "Direct Mill Verification": {
        "ar": "تحقق مباشر من المعاصر",
        "fr": "Vérification Directe des Moulins",
        "en": "Direct Mill Verification"
    },
    "View Lot": {
        "ar": "عرض الدفعة",
        "fr": "Voir le Lot",
        "en": "View Lot"
    },
    "Price on request": {
        "ar": "السعر عند الطلب",
        "fr": "Prix sur demande",
        "en": "Price on request"
    },
    "Bulk Olive Oil": {
        "ar": "زيت الزيتون بالجملة",
        "fr": "Huile d'Olive en Vrac",
        "en": "Bulk Olive Oil"
    },
    "Verified producer lot with direct mill sourcing.": {
        "ar": "دفعة موثوقة من المنتج مع توريد مباشر من المعصرة.",
        "fr": "Lot certifié du producteur avec approvisionnement direct du moulin.",
        "en": "Verified producer lot with direct mill sourcing."
    },
    "Olives": {
        "ar": "زيتون",
        "fr": "Olives",
        "en": "Olives"
    },
    "Extra Virgin": {
        "ar": "بكر ممتاز",
        "fr": "Extra Vierge",
        "en": "Extra Virgin"
    },
    "Bio / Organic": {
        "ar": "بيولوجي / عضوي",
        "fr": "Bio / Organique",
        "en": "Bio / Organic"
    },
    "Turnkey B2B Private Label & Co-Packing": {
        "ar": "تعبئة وتغليف متكامل بالعلامة الخاصة",
        "fr": "Solutions Clé en Main de Marque Privée & Co-Packing",
        "en": "Turnkey B2B Private Label & Co-Packing"
    },
    "Start Your Private Label Project": {
        "ar": "ابدأ مشروع علامتك الخاصة الآن",
        "fr": "Lancez Votre Projet de Marque Privée",
        "en": "Start Your Private Label Project"
    },
    "How Our Private Label Manufacturing Works": {
        "ar": "كيف تعمل منظومة التعبئة بالعلامة الخاصة",
        "fr": "Comment Fonctionne Notre Service de Marque Privée",
        "en": "How Our Private Label Manufacturing Works"
    },
    "Select Cultivar & Quality": {
        "ar": "اختيار الصنف والجودة",
        "fr": "Sélection de la Variété & Qualité",
        "en": "Select Cultivar & Quality"
    },
    "Choose between delicate Chemlali, robust high-polyphenol Chetoui, or certified Organic EVOO.": {
        "ar": "اختر بين الشملالي الناعم، الشتوي الغني بالبوليفينول، أو الزيت البيولوجي المعتمد.",
        "fr": "Choisissez entre le Chemlali doux, le Chétoui riche en polyphénols ou l'extra vierge bio certifié.",
        "en": "Choose between delicate Chemlali, robust high-polyphenol Chetoui, or certified Organic EVOO."
    },
    "Bottle & Packaging Selection": {
        "ar": "اختيار العبوات ومواد التغليف",
        "fr": "Choix des Bouteilles & Emballages",
        "en": "Bottle & Packaging Selection"
    },
    "Dark UV-resistant glass (Marasca/Dorica 250ml/500ml/750ml), premium ceramic, or 5L metallic tins.": {
        "ar": "زجاج داكن مضاد للأشعة فوق البنفسجية (ماراسكا/دوريكا)، سيراميك فاخر، أو صفائح 5 لتر.",
        "fr": "Verre sombre anti-UV (Marasca/Dorica), céramique haut de gamme ou bidons métalliques 5L.",
        "en": "Dark UV-resistant glass (Marasca/Dorica 250ml/500ml/750ml), premium ceramic, or 5L metallic tins."
    },
    "Branding & Label Compliance": {
        "ar": "تصميم الهوية ومطابقة الملصقات",
        "fr": "Design de Marque & Conformité de l'Étiquetage",
        "en": "Branding & Label Compliance"
    },
    "Custom label design, international barcode generation (619 / EAN-13), and FDA / EU regulatory compliance.": {
        "ar": "تصميم الملصق، توليد الباركود الدولي (619 / EAN-13)، والمطابقة لمعايير FDA والاتحاد الأوروبي.",
        "fr": "Création d'étiquettes, génération de codes-barres GS1 et conformité réglementaire UE / FDA.",
        "en": "Custom label design, international barcode generation (619 / EAN-13), and FDA / EU regulatory compliance."
    },
    "Export Logistics & Delivery": {
        "ar": "اللوجستيك والتصدير والشحن",
        "fr": "Logistique d'Exportation & Livraison",
        "en": "Export Logistics & Delivery"
    },
    "Palletized container loading, phytosanitary certificates, EUR.1, and customs clearance directly to your port.": {
        "ar": "تحميل الحاويات على منصات نقالة، الشهادات الصحية، EUR.1، والديوانة مباشرة حتى ميناء وصولك.",
        "fr": "Chargement conteneurisé sur palettes, certificats phytosanitaires, EUR.1 et dédouanement portuaire.",
        "en": "Palletized container loading, phytosanitary certificates, EUR.1, and customs clearance directly to your port."
    },
    "Accredited Bottling Partners in Tunisia": {
        "ar": "وحدات التعبئة والتغليف المعتمدة في تونس",
        "fr": "Unités d'Embouteillage et de Conditionnement Agréées",
        "en": "Accredited Bottling Partners in Tunisia"
    },
    "Co-Packing Accredited": {
        "ar": "معتمد للتعبئة والتغليف",
        "fr": "Agréé pour le Conditionnement",
        "en": "Co-Packing Accredited"
    },
    "Suppliers Directory": {
        "ar": "دليل الموردين والمنتجين",
        "fr": "Annuaire des Fournisseurs",
        "en": "Suppliers Directory"
    },
    "National Ecosystem Directory": {
        "ar": "دليل منظومة الزيتون الوطنية",
        "fr": "Annuaire de l'Écosystème National",
        "en": "National Ecosystem Directory"
    },
    "Farmers & Producers": {
        "ar": "فلاحين ومنتجين",
        "fr": "Agriculteurs & Producteurs",
        "en": "Farmers & Producers"
    },
    "Olive Mills (Pressoirs)": {
        "ar": "معاصر زيتون",
        "fr": "Moulins & Huileries",
        "en": "Olive Mills (Pressoirs)"
    },
    "Packaging & Exporters": {
        "ar": "وحدات تعبئة ومصدرين",
        "fr": "Unités de Conditionnement & Exportateurs",
        "en": "Packaging & Exporters"
    },
    "Join Directory as a Supplier": {
        "ar": "انضم للدليل كمزود خدمة أو مورد",
        "fr": "Rejoindre l'Annuaire comme Fournisseur",
        "en": "Join Directory as a Supplier"
    },
    "Category:": {
        "ar": "الفئة:",
        "fr": "Catégorie :",
        "en": "Category:"
    },
    "All": {
        "ar": "الكل",
        "fr": "Tous",
        "en": "All"
    },
    "Producers": {
        "ar": "منتجون",
        "fr": "Producteurs",
        "en": "Producers"
    },
    "Mills": {
        "ar": "معاصر",
        "fr": "Moulins",
        "en": "Mills"
    },
    "Packers": {
        "ar": "مجمعات تعبئة",
        "fr": "Conditionneurs",
        "en": "Packers"
    },
    "Olive Mills": {
        "ar": "معاصر زيت الزيتون",
        "fr": "Moulins à Huile d'Olive",
        "en": "Olive Mills"
    },
    "Milling & Cold Extraction Infrastructure": {
        "ar": "البنية التحتية للعصر والاستخراج على البارد",
        "fr": "Infrastructure de Trituration & Extraction à Froid",
        "en": "Milling & Cold Extraction Infrastructure"
    },
    "Bottling & Packaging Units": {
        "ar": "وحدات التعليب والتعبئة",
        "fr": "Unités d'Embouteillage & Conditionnement",
        "en": "Bottling & Packaging Units"
    },
    "Contract Bottling & Private Label Facilities": {
        "ar": "منشآت التعبئة بالعلامة الخاصة والتعاقد",
        "fr": "Installations de Conditionnement sous Marque Privée",
        "en": "Contract Bottling & Private Label Facilities"
    },
    "Guides & Market Insights": {
        "ar": "أدلة وتحليلات السوق",
        "fr": "Guides & Analyses de Marché",
        "en": "Guides & Market Insights"
    },
    "Read Article": {
        "ar": "قراءة المقال",
        "fr": "Lire l'Article",
        "en": "Read Article"
    },
    "100% Free Marketplace": {
        "ar": "سوق مجاني 100%",
        "fr": "Marketplace 100% Gratuite",
        "en": "100% Free Marketplace"
    },
    "Your free catalog and guide of Tunisian olive oil producers and brands": {
        "ar": "دليلك وكتالوجك المجاني لمنتجي وعلامات زيت الزيتون التونسي",
        "fr": "Votre catalogue et guide gratuit des producteurs et marques d'huile d'olive tunisienne",
        "en": "Your free catalog and guide of Tunisian olive oil producers and brands"
    }
}

files = {
    'ar': 'resources/lang/ar.json',
    'fr': 'resources/lang/fr.json',
    'en': 'resources/lang/en.json',
}

for lang, path in files.items():
    try:
        with open(path, 'r', encoding='utf-8') as f:
            data = json.load(f)
    except Exception:
        data = {}
    
    count = 0
    for key, val in translations.items():
        if key not in data or data[key] != val.get(lang, key):
            data[key] = val.get(lang, key)
            count += 1
            
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=4)
        
    print(f'Updated {count} keys in {path}')
