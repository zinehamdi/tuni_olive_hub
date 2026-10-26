import os
import docx
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import qn, nsdecls

COLOR_PRIMARY = RGBColor(27, 42, 27)      # #1B2A1B
COLOR_GOLD = RGBColor(200, 163, 86)       # #C8A356
COLOR_DARK_GOLD = RGBColor(140, 107, 18)  # #8C6B12
COLOR_GRAY_DARK = RGBColor(40, 40, 40)
COLOR_GRAY_MUTED = RGBColor(100, 116, 139)
COLOR_ALERT = RGBColor(185, 28, 28)

TC_PR_ORDER = [
    'cnfStyle', 'tcW', 'gridSpan', 'hMerge', 'vMerge', 
    'tcBorders', 'shd', 'noWrap', 'tcMar', 'textDirection', 
    'tcFitText', 'vAlign', 'hideMark', 'headers', 'cellIns', 
    'cellDel', 'cellMerge', 'tcPrChange'
]

P_PR_ORDER = [
    'pStyle', 'keepNext', 'keepLines', 'pageBreakBefore', 'framePr', 
    'widowControl', 'numPr', 'pBdr', 'shd', 'tabs', 
    'suppressAutoHyphens', 'kinsoku', 'wordWrap', 'overflowPunct', 
    'topLinePunct', 'autoSpaceDE', 'autoSpaceDN', 'bidi', 
    'adjustRightInd', 'snapToGrid', 'spacing', 'ind', 
    'contextualSpacing', 'mirrorIndents', 'suppressOverlap', 'jc', 
    'textDirection', 'textAlignment', 'textboxTightWrap', 
    'outlineLvl', 'divId', 'cnfStyle', 'rPr', 'sectPr', 'pPrChange'
]

R_PR_ORDER = [
    'rStyle', 'rFonts', 'b', 'bCs', 'i', 'iCs', 'caps', 'smallCaps', 
    'strike', 'dstrike', 'outline', 'shadow', 'emboss', 'imprint', 
    'noProof', 'snapToGrid', 'vanish', 'webHidden', 'color', 
    'spacing', 'w', 'kern', 'position', 'sz', 'szCs', 'highlight', 
    'u', 'effect', 'bdr', 'shd', 'fitText', 'vertAlign', 'rtl', 
    'cs', 'em', 'lang', 'eastAsianLayout', 'specVanish', 'oMath', 
    'rPrChange'
]

TBL_PR_ORDER = [
    'tblStyle', 'tblpPr', 'tblOverlap', 'bidiVisual', 'tblStyleRowBandSize', 
    'tblStyleColBandSize', 'tblW', 'jc', 'tblCellSpacing', 'tblInd', 
    'tblBorders', 'shd', 'tblLayout', 'tblCellMar', 'tblLook', 
    'tblCaption', 'tblDescription', 'tblPrChange'
]

def sort_xml_children(element, order_list):
    if element is None:
        return
    def get_order(child):
        tag = child.tag.split('}')[-1]
        try:
            return order_list.index(tag)
        except ValueError:
            return 999
    children = list(element)
    sorted_children = sorted(children, key=get_order)
    if children != sorted_children:
        for child in children:
            element.remove(child)
        for child in sorted_children:
            element.append(child)

def sanitize_document_schema(doc):
    for p in doc.paragraphs:
        if p._p.pPr is not None:
            sort_xml_children(p._p.pPr, P_PR_ORDER)
        for r in p.runs:
            if r._r.rPr is not None:
                sort_xml_children(r._r.rPr, R_PR_ORDER)
                
    for tbl in doc.tables:
        if tbl._tbl.tblPr is not None:
            sort_xml_children(tbl._tbl.tblPr, TBL_PR_ORDER)
        for r in tbl.rows:
            for c in r.cells:
                if c._tc.tcPr is not None:
                    sort_xml_children(c._tc.tcPr, TC_PR_ORDER)
                for p in c.paragraphs:
                    if p._p.pPr is not None:
                        sort_xml_children(p._p.pPr, P_PR_ORDER)
                    for run in p.runs:
                        if run._r.rPr is not None:
                            sort_xml_children(run._r.rPr, R_PR_ORDER)

def set_cell_background(cell, hex_color):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'''
        <w:tcMar {nsdecls("w")}>
            <w:top w:w="{top}" w:type="dxa"/>
            <w:bottom w:w="{bottom}" w:type="dxa"/>
            <w:left w:w="{left}" w:type="dxa"/>
            <w:right w:w="{right}" w:type="dxa"/>
        </w:tcMar>
    ''')
    tcPr.append(tcMar)

def set_cell_borders(cell, top=None, bottom=None, left=None, right=None):
    tcPr = cell._tc.get_or_add_tcPr()
    tcBorders = parse_xml(f'''
        <w:tcBorders {nsdecls("w")}>
            <w:top w:val="{top.get('val', 'single') if top else 'none'}" w:sz="{top.get('sz', '4') if top else '0'}" w:space="0" w:color="{top.get('color', 'auto') if top else 'auto'}"/>
            <w:left w:val="{left.get('val', 'single') if left else 'none'}" w:sz="{left.get('sz', '4') if left else '0'}" w:space="0" w:color="{left.get('color', 'auto') if left else 'auto'}"/>
            <w:bottom w:val="{bottom.get('val', 'single') if bottom else 'none'}" w:sz="{bottom.get('sz', '4') if bottom else '0'}" w:space="0" w:color="{bottom.get('color', 'auto') if bottom else 'auto'}"/>
            <w:right w:val="{right.get('val', 'single') if right else 'none'}" w:sz="{right.get('sz', '4') if right else '0'}" w:space="0" w:color="{right.get('color', 'auto') if right else 'auto'}"/>
        </w:tcBorders>
    ''')
    tcPr.append(tcBorders)

def make_p_rtl(p, font_name="Arial"):
    pPr = p._p.get_or_add_pPr()
    if pPr.find(qn('w:bidi')) is None:
        pPr.append(OxmlElement('w:bidi'))
    p.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    for r in p.runs:
        set_run_font(r, font_name)

def make_table_rtl(tbl):
    tblPr = tbl._tbl.tblPr
    if tblPr.find(qn('w:bidiVisual')) is None:
        tblPr.append(OxmlElement('w:bidiVisual'))
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER

def set_run_font(run, font_name="Arial", is_rtl=True):
    rPr = run._r.get_or_add_rPr()
    rFonts = rPr.find(qn('w:rFonts'))
    if rFonts is None:
        rFonts = OxmlElement('w:rFonts')
        rPr.append(rFonts)
    rFonts.set(qn('w:ascii'), font_name)
    rFonts.set(qn('w:hAnsi'), font_name)
    if is_rtl:
        rFonts.set(qn('w:cs'), font_name)
        if rPr.find(qn('w:rtl')) is None:
            rPr.append(OxmlElement('w:rtl'))

# ─────────────────────────────────────────────────────────────────────────────
# 1. BUILD GENERAL HARVESTING CONTRACT (ARABIC)
# ─────────────────────────────────────────────────────────────────────────────
def build_contract_recolte_ar(output_path):
    doc = docx.Document()
    
    # Page setup (A4, balanced margins 15mm)
    for section in doc.sections:
        section.page_width = Inches(8.27)
        section.page_height = Inches(11.69)
        section.top_margin = Inches(0.5)
        section.bottom_margin = Inches(0.5)
        section.left_margin = Inches(0.55)
        section.right_margin = Inches(0.55)

    # Header Box
    h_tbl = doc.add_table(rows=1, cols=1)
    make_table_rtl(h_tbl)
    h_cell = h_tbl.cell(0, 0)
    set_cell_background(h_cell, "1B2A1B")
    set_cell_margins(h_cell, top=140, bottom=140, left=200, right=200)
    
    hp = h_cell.paragraphs[0]
    make_p_rtl(hp)
    hp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr1 = hp.add_run("شركة منصة الزين لزيت الزيتون التونسي (ZinToop SUARL)")
    hr1.font.bold = True
    hr1.font.size = Pt(13)
    hr1.font.color.rgb = RGBColor(255, 255, 255)
    set_run_font(hr1, "Arial", True)
    
    hp2 = h_cell.add_paragraph()
    make_p_rtl(hp2)
    hp2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr2 = hp2.add_run("EZZINE TUNISIAN OLIVE OIL PLATFORM • وحدة الخدمات الرقمية والتكنولوجيا الفلاحية (AgriTech)")
    hr2.font.size = Pt(9.5)
    hr2.font.color.rgb = RGBColor(200, 163, 86)
    set_run_font(hr2, "Arial", False)

    hp3 = h_cell.add_paragraph()
    make_p_rtl(hp3)
    hp3.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr3 = hp3.add_run("المعرف الجبائي (MF): 1969552S  •  المقر: دار أمان، حدائق الخضراوي، 3140 القيروان  •  الهاتف: 25 777 926 (+216)")
    hr3.font.size = Pt(8.5)
    hr3.font.color.rgb = RGBColor(220, 220, 220)
    set_run_font(hr3, "Arial", True)

    doc.add_paragraph() # Small spacer

    # Title Box
    tp = doc.add_paragraph()
    make_p_rtl(tp)
    tp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    tr1 = tp.add_run("عقد تقديم خدمات الجني الميكانيكي لمحصول الزيتون")
    tr1.font.bold = True
    tr1.font.size = Pt(15)
    tr1.font.color.rgb = COLOR_PRIMARY
    set_run_font(tr1, "Arial", True)

    tmp = doc.add_paragraph()
    make_p_rtl(tmp)
    tmp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    tm_run = tmp.add_run("الموسم الفلاحي: 202... / 202...  |  رقم العقد: REC-202.../.....  |  تاريخ التحرير: ...... / ...... / 202...")
    tm_run.font.bold = True
    tm_run.font.size = Pt(10)
    tm_run.font.color.rgb = COLOR_DARK_GOLD
    set_run_font(tm_run, "Arial", True)

    # Parties Table (2 Columns)
    ptbl = doc.add_table(rows=1, cols=2)
    make_table_rtl(ptbl)
    ptbl.autofit = False
    
    col1 = ptbl.cell(0, 0)
    col2 = ptbl.cell(0, 1)
    
    for c in (col1, col2):
        set_cell_background(c, "F8FAF8")
        set_cell_margins(c, top=100, bottom=100, left=120, right=120)
        set_cell_borders(c, 
            top={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            bottom={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            left={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            right={'sz': '8', 'val': 'single', 'color': 'C8A356'}
        )

    p1 = col1.paragraphs[0]
    make_p_rtl(p1)
    r = p1.add_run("الطرف الأول (مقدم الخدمة والمنفذ):")
    r.font.bold = True
    r.font.size = Pt(10)
    r.font.color.rgb = COLOR_PRIMARY
    set_run_font(r, "Arial", True)
    
    p1_desc = col1.add_paragraph()
    make_p_rtl(p1_desc)
    r = p1_desc.add_run("شركة منصة الزين لزيت الزيتون التونسي (ZinToop SUARL) المذكورة كامل بياناتها ومقرها بالترويسة القانونية أعلاه، ويمثلها قانونياً السيد حمدي الزين (Hamdi Ezzine) بصفته المدير والممثل القانوني.")
    r.font.size = Pt(9)
    set_run_font(r, "Arial", True)

    p2 = col2.paragraphs[0]
    make_p_rtl(p2)
    r = p2.add_run("الطرف الثاني (المستفيد / صاحب الضيعة):")
    r.font.bold = True
    r.font.size = Pt(10)
    r.font.color.rgb = COLOR_PRIMARY
    set_run_font(r, "Arial", True)
    
    p2_desc = col2.add_paragraph()
    make_p_rtl(p2_desc)
    r = p2_desc.add_run("السيد(ة) / المجمع: [ ................................................ ]\nب.ت.و / المعرف: [ ........................ ] • الهاتف: [ ........................ ]\nمقر السكنى: [ ................................................ ]")
    r.font.size = Pt(9)
    set_run_font(r, "Arial", True)

    # Farm info banner
    doc.add_paragraph()
    f_tbl = doc.add_table(rows=1, cols=1)
    make_table_rtl(f_tbl)
    fc = f_tbl.cell(0, 0)
    set_cell_background(fc, "EAF4E8")
    set_cell_margins(fc, top=70, bottom=70, left=120, right=120)
    set_cell_borders(fc, 
        top={'sz': '4', 'val': 'single', 'color': 'A3CCA0'},
        bottom={'sz': '4', 'val': 'single', 'color': 'A3CCA0'},
        left={'sz': '4', 'val': 'single', 'color': 'A3CCA0'},
        right={'sz': '4', 'val': 'single', 'color': 'A3CCA0'}
    )
    fp = fc.paragraphs[0]
    make_p_rtl(fp)
    r = fp.add_run("🌿 بيانات الضيعة والإنتاج:   موقع الضيعة: [ ................................ ]  •  صنف الزيتون: [ ................ ]  •  عدد الأصول: [ ............ ] شجرة  •  المحصول التقديري: [ ............ ] كغ")
    r.font.bold = True
    r.font.size = Pt(9)
    r.font.color.rgb = RGBColor(22, 101, 52)
    set_run_font(r, "Arial", True)

    # Clauses
    clauses = [
        ("البند 1: ميثاق الجودة وحماية الأشجار (التزام تقني صارم)",
         "يلتزم الطرف الأول (ZinToop) بجني المحصول بواسطة الآلات والمشاطات الكهربائية الحديثة حصراً، مع المنع البات والقطعي لاستعمال العصا أو الضرب لحماية أصول وبراعم الأشجار، والتعهد بـ الجني الكامل للشجرة دون ترك ثمار متبقية، والتجميع الفوري للزيتون في صناديق بلاستيكية مهواة مخصصة لحفظ الجودة."),
        ("البند 2: التسعير وعملية الوزن بالضيعة",
         "حُدّد سعر الجني الصافي بمبلغ: [ ........................ ] مليم/دينار دون احتساب الأداء (HT) عن كل كيلوغرام مجني. ويتم وزن كميات الزيتون المجموع بواسطة باسكول / ميزان الطرف الأول (ZinToop) بالضيعة بحضور الطرف الثاني (أو من ينوبه) مع تسليم وصل وزن رسمي (Bon de pesée)، مع احتفاظ الفلاح بحق التثبت والمطابقة عند تفريغ المحصول بالمعصرة."),
        ("البند 3: العربون وموعد انطلاق الأشغال (بعد المعاينة الميدانية)",
         "يدفع الطرف الثاني عند إبرام هذا العقد عربوناً بنسبة [ .......... % ] من القيمة التقديرية للمحصول قدره [ ........................ د.ت ] لتثبيت الموعد وحجز المعدات وفريق العمل إثر المعاينة الميدانية للضيعة. وقد تم الاتفاق على تحديد تاريخ بداية الأشغال يوم: [ ...... / ...... / 202... ]."),
        ("البند 4: شروط الخلاص المالي وإدارة الصناديق",
         "• الخلاص: يكون الدفع فورياً عند انتهاء الجني في نفس اليوم للأشغال قصيرة المدة (أقل من أسبوع)؛ بينما يكون الخلاص أسبوعياً دورياً للضيعات الكبرى (أكثر من أسبوع) بحسب أوزان الأسبوع المنقضي، مع تسوية العربون بالحساب النهائي.\n• الصناديق: الصناديق ملكية خاصة وحصرية لـ ZinToop؛ يلتزم الفلاح بتفريغها فوراً بالمعصرة، ويحجر تحجيراً باتاً تركها أو إبقاؤها بالضيعة، ويلتزم بتعويض أي صندوق تالف أو مفقود.")
    ]

    for title, text in clauses:
        doc.add_paragraph()
        cp = doc.add_paragraph()
        make_p_rtl(cp)
        cr1 = cp.add_run(f"🔸 {title}")
        cr1.font.bold = True
        cr1.font.size = Pt(10)
        cr1.font.color.rgb = COLOR_PRIMARY
        set_run_font(cr1, "Arial", True)

        ctp = doc.add_paragraph()
        make_p_rtl(ctp)
        cr2 = ctp.add_run(text)
        cr2.font.size = Pt(9)
        cr2.font.color.rgb = COLOR_GRAY_DARK
        set_run_font(cr2, "Arial", True)

    # Notes section
    doc.add_paragraph()
    np = doc.add_paragraph()
    make_p_rtl(np)
    nr = np.add_run("📝 ملاحظات أو اتفاقات تكميلية خاصة (سطران لتدوين أي اتفاق إضافي):")
    nr.font.bold = True
    nr.font.size = Pt(9.5)
    nr.font.color.rgb = COLOR_DARK_GOLD
    set_run_font(nr, "Arial", True)

    np_line1 = doc.add_paragraph()
    make_p_rtl(np_line1)
    nr1 = np_line1.add_run("1) ............................................................................................................................................................................")
    nr1.font.color.rgb = RGBColor(160, 160, 160)
    set_run_font(nr1, "Arial", True)

    np_line2 = doc.add_paragraph()
    make_p_rtl(np_line2)
    nr2 = np_line2.add_run("2) ............................................................................................................................................................................")
    nr2.font.color.rgb = RGBColor(160, 160, 160)
    set_run_font(nr2, "Arial", True)

    # Signatures Table (2 columns)
    doc.add_paragraph()
    stbl = doc.add_table(rows=1, cols=2)
    make_table_rtl(stbl)
    s1 = stbl.cell(0, 0)
    s2 = stbl.cell(0, 1)

    for sc in (s1, s2):
        set_cell_background(sc, "FFFFFF")
        set_cell_margins(sc, top=80, bottom=100, left=100, right=100)
        set_cell_borders(sc, 
            top={'sz': '6', 'val': 'single', 'color': 'C8A356'},
            bottom={'sz': '6', 'val': 'single', 'color': 'C8A356'},
            left={'sz': '6', 'val': 'single', 'color': 'C8A356'},
            right={'sz': '6', 'val': 'single', 'color': 'C8A356'}
        )

    sp1 = s1.paragraphs[0]
    make_p_rtl(sp1)
    sr = sp1.add_run("عن الطرف الأول (ZinToop SUARL)\nمقدم الخدمة والمنفذ\n\nالممثل القانوني: حمدي الزين (Hamdi Ezzine)\nالصفة: المدير والممثل القانوني\n\nالتوقيع والختم: .......................................")
    sr.font.size = Pt(8.5)
    sr.font.bold = True
    set_run_font(sr, "Arial", True)

    sp2 = s2.paragraphs[0]
    make_p_rtl(sp2)
    sr2 = sp2.add_run("عن الطرف الثاني (المستفيد)\nصاحب الضيعة / الفلاح\n\nالاسم واللقب: [ ................................................ ]\nالصفة: الفلاح / مالك الضيعة الفلاحية\n\nإمضاء الفلاح: .......................................")
    sr2.font.size = Pt(8.5)
    sr2.font.bold = True
    set_run_font(sr2, "Arial", True)

    # Footer note
    doc.add_paragraph()
    ft_p = doc.add_paragraph()
    make_p_rtl(ft_p)
    ft_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    ft_r = ft_p.add_run("EZZINE TUNISIAN OLIVE OIL PLATFORM (ZinToop SUARL) • MF: 1969552S • وثيقة تعاقدية رسمية قابلة للتعديل والطباعة")
    ft_r.font.size = Pt(8)
    ft_r.font.color.rgb = RGBColor(140, 140, 140)
    set_run_font(ft_r, "Arial", True)

    sanitize_document_schema(doc)
    doc.save(output_path)
    print(f"✅ Generated: {output_path}")

# ─────────────────────────────────────────────────────────────────────────────
# 2. BUILD GENERAL HARVESTING CONTRACT (FRENCH)
# ─────────────────────────────────────────────────────────────────────────────
def build_contract_recolte_fr(output_path):
    doc = docx.Document()
    
    for section in doc.sections:
        section.page_width = Inches(8.27)
        section.page_height = Inches(11.69)
        section.top_margin = Inches(0.5)
        section.bottom_margin = Inches(0.5)
        section.left_margin = Inches(0.55)
        section.right_margin = Inches(0.55)

    # Header Box
    h_tbl = doc.add_table(rows=1, cols=1)
    h_cell = h_tbl.cell(0, 0)
    set_cell_background(h_cell, "1B2A1B")
    set_cell_margins(h_cell, top=140, bottom=140, left=200, right=200)
    
    hp = h_cell.paragraphs[0]
    hp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr1 = hp.add_run("EZZINE TUNISIAN OLIVE OIL PLATFORM (ZinToop SUARL)")
    hr1.font.bold = True
    hr1.font.size = Pt(13)
    hr1.font.color.rgb = RGBColor(255, 255, 255)
    set_run_font(hr1, "Arial", False)
    
    hp2 = h_cell.add_paragraph()
    hp2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr2 = hp2.add_run("Unité des Services Digitaux & AgriTech • Kairouan, Tunisie")
    hr2.font.size = Pt(9.5)
    hr2.font.color.rgb = RGBColor(200, 163, 86)
    set_run_font(hr2, "Arial", False)

    hp3 = h_cell.add_paragraph()
    hp3.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr3 = hp3.add_run("Matricule Fiscal: 1969552S  •  Siège: Dar Amen, Jardins Khadhraoui, Kairouan  •  Contact: +216 25 777 926")
    hr3.font.size = Pt(8.5)
    hr3.font.color.rgb = RGBColor(220, 220, 220)
    set_run_font(hr3, "Arial", False)

    doc.add_paragraph()

    # Title Box
    tp = doc.add_paragraph()
    tp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    tr1 = tp.add_run("Contrat de Prestation de Récolte Mécanisée des Olives")
    tr1.font.bold = True
    tr1.font.size = Pt(15)
    tr1.font.color.rgb = COLOR_PRIMARY
    set_run_font(tr1, "Arial", False)

    tmp = doc.add_paragraph()
    tmp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    tm_run = tmp.add_run("Campagne: 202... / 202...  |  N° Contrat: REC-202.../.....  |  Date: ...... / ...... / 202...")
    tm_run.font.bold = True
    tm_run.font.size = Pt(10)
    tm_run.font.color.rgb = COLOR_DARK_GOLD
    set_run_font(tm_run, "Arial", False)

    # Parties Table
    ptbl = doc.add_table(rows=1, cols=2)
    ptbl.autofit = False
    col1 = ptbl.cell(0, 0)
    col2 = ptbl.cell(0, 1)
    
    for c in (col1, col2):
        set_cell_background(c, "F8FAF8")
        set_cell_margins(c, top=100, bottom=100, left=120, right=120)
        set_cell_borders(c, 
            top={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            bottom={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            left={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            right={'sz': '8', 'val': 'single', 'color': 'C8A356'}
        )

    p1 = col1.paragraphs[0]
    r = p1.add_run("Première Partie (Prestataire ZinToop):")
    r.font.bold = True
    r.font.size = Pt(10)
    r.font.color.rgb = COLOR_PRIMARY
    set_run_font(r, "Arial", False)
    
    p1_desc = col1.add_paragraph()
    r = p1_desc.add_run("ZinToop SUARL dont les coordonnées légales complètes figurent à l'en-tête ci-dessus, valablement représentée par son Gérant, M. Hamdi Ezzine.")
    r.font.size = Pt(9)
    set_run_font(r, "Arial", False)

    p2 = col2.paragraphs[0]
    r = p2.add_run("Deuxième Partie (L'Agriculteur / Client):")
    r.font.bold = True
    r.font.size = Pt(10)
    r.font.color.rgb = COLOR_PRIMARY
    set_run_font(r, "Arial", False)
    
    p2_desc = col2.add_paragraph()
    r = p2_desc.add_run("M./Mme: [ ................................................ ]\nCIN / Identifiant: [ ........................ ] • Tél: [ ........................ ]\nAdresse: [ ................................................ ]")
    r.font.size = Pt(9)
    set_run_font(r, "Arial", False)

    # Farm info banner
    doc.add_paragraph()
    f_tbl = doc.add_table(rows=1, cols=1)
    fc = f_tbl.cell(0, 0)
    set_cell_background(fc, "EAF4E8")
    set_cell_margins(fc, top=70, bottom=70, left=120, right=120)
    set_cell_borders(fc, 
        top={'sz': '4', 'val': 'single', 'color': 'A3CCA0'},
        bottom={'sz': '4', 'val': 'single', 'color': 'A3CCA0'},
        left={'sz': '4', 'val': 'single', 'color': 'A3CCA0'},
        right={'sz': '4', 'val': 'single', 'color': 'A3CCA0'}
    )
    fp = fc.paragraphs[0]
    r = fp.add_run("🌿 Données du Verger:   Lieu: [ ................................ ]  •  Variété: [ ................ ]  •  Nb d'arbres: [ ............ ] pieds  •  Quantité estimée: [ ............ ] Kg")
    r.font.bold = True
    r.font.size = Pt(9)
    r.font.color.rgb = RGBColor(22, 101, 52)
    set_run_font(r, "Arial", False)

    clauses_fr = [
        ("Art. 1 : Charte Qualité & Protection du Verger (Engagement Technique)",
         "Récolte exécutée exclusivement par peignes vibreurs électriques modernes, avec interdiction formelle du gaulage au bâton. Engagement de récolte intégrale de l'arbre sans abandon de fruits, avec ramassage immédiat dans des caisses ajourées en plastique garantissant la fraîcheur."),
        ("Art. 2 : Tarification au Kilo & Opération de Pesée sur l'Exploitation",
         "Tarif net convenu: [ ........................ ] DT/Millimes HT par Kg récolté. La pesée des olives est effectuée au moyen de la bascule de ZinToop directement sur l'exploitation et en présence de l'agriculteur (ou son représentant) avec délivrance d'un bon de pesée officiel, l'agriculteur conservant la faculté de vérification lors du déchargement au moulin."),
        ("Art. 3 : Acompte de Réservation & Date de Début des Travaux",
         "Versement à la signature d'un acompte de [ .......... % ] (soit [ ........................ DT ]) validant le planning suite à la visite technique du verger. Date convenue pour le début des travaux: [ ...... / ...... / 202... ]."),
        ("Art. 4 : Modalités de Paiement & Gestion Stricte des Caisses",
         "• Paiement: Règlement intégral le jour de clôture pour les chantiers courts (<= 1 semaine) ; ou hebdomadaire pour les grands vergers (> 1 semaine) au prorata des pesées avec régularisation de l'acompte.\n• Caisses: Propriété exclusive de ZinToop déchargées immédiatement au moulin. Interdiction formelle de conserver les caisses sur la propriété après le travail journalier. Dédommagement obligatoire en cas de perte/casse.")
    ]

    for title, text in clauses_fr:
        doc.add_paragraph()
        cp = doc.add_paragraph()
        cr1 = cp.add_run(f"🔸 {title}")
        cr1.font.bold = True
        cr1.font.size = Pt(10)
        cr1.font.color.rgb = COLOR_PRIMARY
        set_run_font(cr1, "Arial", False)

        ctp = doc.add_paragraph()
        cr2 = ctp.add_run(text)
        cr2.font.size = Pt(9)
        cr2.font.color.rgb = COLOR_GRAY_DARK
        set_run_font(cr2, "Arial", False)

    # Notes section
    doc.add_paragraph()
    np = doc.add_paragraph()
    nr = np.add_run("📝 Remarques ou conventions particulières complémentaires (2 lignes libres):")
    nr.font.bold = True
    nr.font.size = Pt(9.5)
    nr.font.color.rgb = COLOR_DARK_GOLD
    set_run_font(nr, "Arial", False)

    np_line1 = doc.add_paragraph()
    nr1 = np_line1.add_run("1) ............................................................................................................................................................................")
    nr1.font.color.rgb = RGBColor(160, 160, 160)

    np_line2 = doc.add_paragraph()
    nr2 = np_line2.add_run("2) ............................................................................................................................................................................")
    nr2.font.color.rgb = RGBColor(160, 160, 160)

    # Signatures
    doc.add_paragraph()
    stbl = doc.add_table(rows=1, cols=2)
    s1 = stbl.cell(0, 0)
    s2 = stbl.cell(0, 1)

    for sc in (s1, s2):
        set_cell_background(sc, "FFFFFF")
        set_cell_margins(sc, top=80, bottom=100, left=100, right=100)
        set_cell_borders(sc, 
            top={'sz': '6', 'val': 'single', 'color': 'C8A356'},
            bottom={'sz': '6', 'val': 'single', 'color': 'C8A356'},
            left={'sz': '6', 'val': 'single', 'color': 'C8A356'},
            right={'sz': '6', 'val': 'single', 'color': 'C8A356'}
        )

    sp1 = s1.paragraphs[0]
    sr = sp1.add_run("Pour la Première Partie (ZinToop SUARL)\nPrestataire de services\n\nReprésentant Légal: Hamdi Ezzine\nQualité: Gérant Mandataire\n\nSignature & Cachet: .......................................")
    sr.font.size = Pt(8.5)
    sr.font.bold = True

    sp2 = s2.paragraphs[0]
    sr2 = sp2.add_run("Pour la Deuxième Partie (Le Client)\nExploitant Agricole\n\nNom & Prénom: [ ................................................ ]\nQualité: Propriétaire / Exploitant\n\nÉmargement Client: .......................................")
    sr2.font.size = Pt(8.5)
    sr2.font.bold = True

    # Footer
    doc.add_paragraph()
    ft_p = doc.add_paragraph()
    ft_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    ft_r = ft_p.add_run("EZZINE TUNISIAN OLIVE OIL PLATFORM (ZinToop SUARL) • MF: 1969552S • Document contractuel modifiable et imprimable")
    ft_r.font.size = Pt(8)
    ft_r.font.color.rgb = RGBColor(140, 140, 140)

    sanitize_document_schema(doc)
    doc.save(output_path)
    print(f"✅ Generated: {output_path}")

# ─────────────────────────────────────────────────────────────────────────────
# 3. BUILD FAS AGRI HARVESTING CONTRACT (DETAILED ARABIC PARTNERSHIP CONTRACT)
# ─────────────────────────────────────────────────────────────────────────────
def build_contract_fas_agri(output_path):
    doc = docx.Document()
    
    for section in doc.sections:
        section.page_width = Inches(8.27)
        section.page_height = Inches(11.69)
        section.top_margin = Inches(0.6)
        section.bottom_margin = Inches(0.6)
        section.left_margin = Inches(0.65)
        section.right_margin = Inches(0.65)

    # Header Box
    h_tbl = doc.add_table(rows=1, cols=1)
    make_table_rtl(h_tbl)
    h_cell = h_tbl.cell(0, 0)
    set_cell_background(h_cell, "1B2A1B")
    set_cell_margins(h_cell, top=140, bottom=140, left=200, right=200)
    
    hp = h_cell.paragraphs[0]
    make_p_rtl(hp)
    hp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr1 = hp.add_run("شركة منصة الزين لزيت الزيتون التونسي (SUARL) - ZinToop")
    hr1.font.bold = True
    hr1.font.size = Pt(13)
    hr1.font.color.rgb = RGBColor(255, 255, 255)
    set_run_font(hr1, "Arial", True)
    
    hp2 = h_cell.add_paragraph()
    make_p_rtl(hp2)
    hp2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr2 = hp2.add_run("المعرف الجبائي (RNE): 1969552S  •  عدد التصرف الداخلي: B5094322026")
    hr2.font.size = Pt(9.5)
    hr2.font.color.rgb = RGBColor(200, 163, 86)
    set_run_font(hr2, "Arial", True)

    hp3 = h_cell.add_paragraph()
    make_p_rtl(hp3)
    hp3.alignment = WD_ALIGN_PARAGRAPH.CENTER
    hr3 = hp3.add_run("المقر: فيلا HUB، حدائق الخضراوي، حي ابن الجزار، س1، دار الأمان، 3140 القيروان  •  الهاتف: 25 777 926  •  contact@zintoop.com")
    hr3.font.size = Pt(8.5)
    hr3.font.color.rgb = RGBColor(220, 220, 220)
    set_run_font(hr3, "Arial", True)

    doc.add_paragraph()

    # Title Box
    tp = doc.add_paragraph()
    make_p_rtl(tp)
    tp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    tr1 = tp.add_run("عقد تقديم خدمات جني صابة الزيتون")
    tr1.font.bold = True
    tr1.font.size = Pt(16)
    tr1.font.color.rgb = COLOR_PRIMARY
    set_run_font(tr1, "Arial", True)

    tmp = doc.add_paragraph()
    make_p_rtl(tmp)
    tmp.alignment = WD_ALIGN_PARAGRAPH.CENTER
    tm_run = tmp.add_run("الموسم الفلاحي: 2026 / 2027  •  ضيعات ولاية زغوان (500 طن زيتون)")
    tm_run.font.bold = True
    tm_run.font.size = Pt(10.5)
    tm_run.font.color.rgb = COLOR_DARK_GOLD
    set_run_font(tm_run, "Arial", True)

    date_p = doc.add_paragraph()
    make_p_rtl(date_p)
    date_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    date_r = date_p.add_run("تونس / القيروان في: ...... / ...... / 2026")
    date_r.font.bold = True
    date_r.font.size = Pt(9.5)
    date_r.font.color.rgb = COLOR_GRAY_MUTED
    set_run_font(date_r, "Arial", True)

    # Parties Section
    intro_p = doc.add_paragraph()
    make_p_rtl(intro_p)
    intro_r = intro_p.add_run("وقع الاتفاق والتراضي التام بين الممضيين أسفله:")
    intro_r.font.bold = True
    intro_r.font.size = Pt(10.5)
    set_run_font(intro_r, "Arial", True)

    ptbl = doc.add_table(rows=2, cols=1)
    make_table_rtl(ptbl)
    
    cell1 = ptbl.cell(0, 0)
    set_cell_background(cell1, "F8FAF8")
    set_cell_margins(cell1, top=80, bottom=80, left=120, right=120)
    set_cell_borders(cell1, 
        top={'sz': '6', 'val': 'single', 'color': 'C8A356'},
        bottom={'sz': '4', 'val': 'single', 'color': 'E2E8F0'},
        left={'sz': '6', 'val': 'single', 'color': 'C8A356'},
        right={'sz': '6', 'val': 'single', 'color': 'C8A356'}
    )
    p_t1 = cell1.paragraphs[0]
    make_p_rtl(p_t1)
    r = p_t1.add_run("الطرف الأول: مسدي الخدمات (ZinToop)")
    r.font.bold = True
    r.font.size = Pt(10)
    r.font.color.rgb = COLOR_PRIMARY
    set_run_font(r, "Arial", True)
    p_d1 = cell1.add_paragraph()
    make_p_rtl(p_d1)
    r = p_d1.add_run("شركة منصة الزين لزيت الزيتون التونسي (ZinToop SUARL)، شركة الشخص الواحد ذات المسؤولية المحدودة، المعرف الجبائي عدد: 1969552S، ومقرها الاجتماعي الكائن بـ دار الأمان، حدائق الخضراوي، 3140 القيروان، ويمثلها بخصوص هذا العقد وكيلها وممثلها القانوني السيد حمدي الزين (Ezzine Hamdi)، صاحب بطاقة التعريف الوطنية عدد: 07712882.")
    r.font.size = Pt(9.5)
    set_run_font(r, "Arial", True)

    cell2 = ptbl.cell(1, 0)
    set_cell_background(cell2, "F8FAF8")
    set_cell_margins(cell2, top=80, bottom=80, left=120, right=120)
    set_cell_borders(cell2, 
        top={'sz': '4', 'val': 'single', 'color': 'E2E8F0'},
        bottom={'sz': '6', 'val': 'single', 'color': 'C8A356'},
        left={'sz': '6', 'val': 'single', 'color': 'C8A356'},
        right={'sz': '6', 'val': 'single', 'color': 'C8A356'}
    )
    p_t2 = cell2.paragraphs[0]
    make_p_rtl(p_t2)
    r = p_t2.add_run("الطرف الثاني: المستفيد - صاحب الضيعة (FAS AGRI)")
    r.font.bold = True
    r.font.size = Pt(10)
    r.font.color.rgb = COLOR_PRIMARY
    set_run_font(r, "Arial", True)
    p_d2 = cell2.add_paragraph()
    make_p_rtl(p_d2)
    r = p_d2.add_run("شركة فاس أقري (FAS AGRI SARL)، شركة ذات المسؤولية المحدودة، المعرف الجبائي عدد: 1772025K، ومقرها الاجتماعي الكائن بـ: 100 نهج الحسين بوزيان – باب البحر – تونس 1001، ويمثلها قانونياً وكيلها السيد محمد حسين صفر.")
    r.font.size = Pt(9.5)
    set_run_font(r, "Arial", True)

    articles = [
        ("الفصل 1: موضوع العقد والكميات ونسق الجني اليومي التصاعدي",
         "يتمثل هذا العقد في تعهد والتزام الطرف الأول (شركة منصة الزين - ZinToop) بالقيام بأشغال الجني الميكانيكي لمحصول الزيتون بالضيعات الفلاحية التابعة لـ الطرف الثاني (شركة فاس أقري) الكائنة بولاية زغوان، وذلك خلال الموسم الفلاحي 2026 / 2027.\n\n"
         "يُقدّر المحصول الجملي المتعاقد عليه بحوالي خمسمائة ألف كيلوغرام (500.000 كغ)، أي ما يعادل 500 طناً، متأتية من حوالي 8,200 شجرة زيتون من صنف \"شملالي\" و\"شتوي\".\n\n"
         "📌 وتيرة ونسق الجني اليومي التصاعدي (Cadence progressive):\n"
         "اتفق الطرفان على أن تنطلق وتيرة الجني اليومية بداية من خمسة أطنان (5 طن) يومياً كنسق أولي تشغيلي، على أن يتولى الطرف الأول تعزيز الفريق الميداني تدريجياً باليد العاملة المختصة والآلات الميكانيكية للوصول بالنسق تدريجياً إلى طاقة جني يومية تتراوح بين عشرة أطنان (10 طن) وأربعة عشر طناً (14 طن) يومياً، بما يضمن استكمال جمع المحصول في أفضل الآجال."),
        
        ("الفصل 2: مدة العقد، أيام العمل والراحة الأسبوعية، واستثناء الظروف الجوية",
         "يباشر الطرف الأول تنفيذ أعمال الجني الميدانية بداية من يوم: [ ...... / ...... / 2026 ]، وتستمر الأشغال حتى الانتهاء التام من جمع كامل محصول صابة الزيتون المتفق عليه.\n\n"
         "• يوم الراحة الأسبوعي: يُخصص يوم راحة أسبوعي لفريق العمل الميداني (يوم الأحد من كل أسبوع، أو أي يوم آخر يتم التنسيق والاتفاق المسبق حوله بين الطرفين)، وذلك لإراحة اليد العاملة وإجراء الصيانة الدورية وتفقد المشاطات والتجهيزات الميكانيكية.\n\n"
         "• توقف العمل بفعل الأمطار والعوامل المناخية: يتوقف العمل بصفة آلية ومبررة عند هطول الأمطار أو وجود بلل ورطوبة شديدة بالأشجار والثمار، وذلك حفاظاً على سلامة العمالة الميدانية من مخاطر التكهرب والتزحلق، وحماية للأشجار من تكسر الأغصان، وحفظاً لجودة الثمار قبل تحويلها. ولا يُعد هذا التوقف الاضطراري إخلالاً بالعقد، وتُمدد مدة إنجاز العقد تلقائياً بما يعادل أيام التوقف المناخي الفعلية."),

        ("الفصل 3: التزامات الطرفين بخصوص الآلات والمعدات والتجهيزات",
         "نظراً لتوفر جانب هام من المعدات لدى صاحب الضيعة وحرصاً على التكامل التقني بين الطرفين، تم ضبط الالتزامات والمعدات كما يلي:\n\n"
         "1. التزامات الطرف الثاني (المستفيد - شركة فاس أقري):\n"
         "  - توفير آلة كبيرة لجني الزيتون (هزاز ميكانيكي / Vibreur mécanisé) في حالة عمل جيدة وجاهزة للتشغيل.\n"
         "  - توفير عدد من المشاطات والآلات الكهربائية الحديثة المتوفرة بالضيعة مع كابلاتها وتوابعها لمعاضدة أعمال الجني.\n"
         "  - توفير الصناديق البلاستيكية المهواة المخصصة لحفظ جودة الزيتون وكذلك الأكياس القماشية بالعدد الكافي حسب عرف النشاط وفي حالة حسنة.\n"
         "  - توفير نقاط تزويد الطاقة (شاحنات طاقة / مولدات أو بطاريات) لتشغيل وشحن الآلات بالضيعة.\n\n"
         "2. التزامات الطرف الأول (مسدي الخدمات - شركة ZinToop):\n"
         "  - توفير اليد العاملة المختصة والمدربة (سائقي ومشغلي المشاطات، فرق الجمع واللمّ، والمشرفين الميدانيين) وتعزيز العدد تصاعدياً حسب وتيرة الإنتاج.\n"
         "  - توفير مشاطات كهربائية حديثة مكملة تابعة له لدعم الإنتاجية اليومية.\n"
         "  - الحفاظ التام على المعدات والآلات والصناديق المسلمة له من الطرف الثاني، وتشغيلها بحرفية وأمانة، وإرجاعها عند انتهاء الأشغال بالحالة التي تسلمها عليها (باستثناء الاستهلاك الطبيعي الناتج عن الاستعمال العادي).\n"
         "  - الامتناع البات والقطعي عن استعمال العصا أو الضرب لحماية أصول وبراعم الأشجار والأغصان الفتية.\n"
         "  - الجمع الكامل والأمين للثمار دون ترك زيتون متبقٍ بالأشجار."),

        ("الفصل 4: المقابل المالي، الأوزان بالضيعة، وجدول الخلاص",
         "• سعر الجني الصافي: حُدد سعر الجني الصافي بمبلغ ثلاثمائة وأربعون مليماً (0.340 د) عن الكيلوغرام الواحد الصافي المجني دون احتساب الأداء.\n\n"
         "• عملية الوزن: يتم وزن كميات الزيتون المجموع بواسطة \"باسكول\" / ميزان الطرف الأول بالضيعة وبحضور ممثل الطرف الثاني الذي يوقع على وصل وزن يومي رسمي (Bon de pesée)، مع احتفاظ الطرف الثاني بحق التثبت ومطابقة الوزن عند تفريغ المحصول بالمعصرة.\n\n"
         "• التسبقة المالية (العربون): دفع الطرف الثاني للطرف الأول مبلغاً قدره ألفا دينار (2.000,000 د) بعنوان تسبقة مالية مباشرة عبر تحويل بنكي عند إمضاء هذا العقد، وتُخصم هذه التسبقة بالحساب من الفاتورة النهائية للموسم.\n\n"
         "• دورية الخلاص: يقوم الطرف الثاني بخلاص أجرة العمل المنجزة للطرف الأول كل خمسة عشر (15) يوماً بواسطة تحويل بنكي مباشر لحساب مسدي الخدمات، وذلك بالاعتماد على مجموع وصولات الوزن المعتمدة لتلك الفترة."),

        ("الفصل 5: المسؤولية والسلامة المهنية",
         "يلتزم الطرف الأول بتطبيق قواعد السلامة المهنية لعملته داخل الضيعة، ويتعهد بعدم إحداث أي ضرر بأشجار أو تجهيزات ضيعات الطرف الثاني خارج إطار الاستغلال الفلاحي العادي، وإلا كان مسؤولاً مدنياً طبقاً لأحكام الفصول 82 و83 من مجلة الالتزامات والعقود التونسية."),

        ("الفصل 6: فسخ العقد",
         "يجوز فسخ هذا العقد باتفاق الطرفين كتابياً بالتراضي. وفي صورة إخلال أحد الطرفين بأحد التزاماته الجوهرية (كتأخر الطرف الثاني عن خلاص المستحقات بعد انقضاء 15 يوماً، أو توقف الطرف الأول غير المبرر)، يتم التنبيه على الطرف المخل بمراسلة مضمونة الوصول مع الإعلام بالبلوغ، وإذا لم يُرفع الإخلال خلال أجل خمسة (5) أيام، يُفسخ العقد وجوباً مع حفظ حق الطرف المتضرر في التعويض."),

        ("الفصل 7: المحكمة المختصة والقانون المنطبق",
         "يخضع هذا العقد ويفسر طبقاً لأحكام القانون التونسي. وفي حال نشوء أي نزاع يتعذر تسويته ودياً بين الطرفين، تختص محاكم تونس 1 حصرياً بالنظر فيه والبت فيه.\n\n"
         "حرر هذا العقد في نظيرين أصليين بتاريخ: [ ...... / ...... / 2026 ] (نظير لكل طرف للعمل بمقتضاه عن حسن نية).")
    ]

    for title, text in articles:
        doc.add_paragraph()
        ap = doc.add_paragraph()
        make_p_rtl(ap)
        ar1 = ap.add_run(f"📋 {title}")
        ar1.font.bold = True
        ar1.font.size = Pt(11)
        ar1.font.color.rgb = COLOR_PRIMARY
        set_run_font(ar1, "Arial", True)

        atp = doc.add_paragraph()
        make_p_rtl(atp)
        ar2 = atp.add_run(text)
        ar2.font.size = Pt(9.5)
        ar2.font.color.rgb = COLOR_GRAY_DARK
        set_run_font(ar2, "Arial", True)

    # Signatures Table
    doc.add_paragraph()
    stbl = doc.add_table(rows=1, cols=2)
    make_table_rtl(stbl)
    s1 = stbl.cell(0, 0)
    s2 = stbl.cell(0, 1)

    for sc in (s1, s2):
        set_cell_background(sc, "FFFFFF")
        set_cell_margins(sc, top=100, bottom=120, left=120, right=120)
        set_cell_borders(sc, 
            top={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            bottom={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            left={'sz': '8', 'val': 'single', 'color': 'C8A356'},
            right={'sz': '8', 'val': 'single', 'color': 'C8A356'}
        )

    sp1 = s1.paragraphs[0]
    make_p_rtl(sp1)
    sr1 = sp1.add_run("إمضاء وختم الطرف الأول (مسدي الخدمات):\n\nشركة منصة الزين لزيت الزيتون التونسي (ZinToop SUARL)\nالوكيل والممثل القانوني: حمدي الزين (Ezzine Hamdi)\nالمعرف الجبائي: 1969552S\n\nالتوقيع والختم: .......................................")
    sr1.font.size = Pt(9)
    sr1.font.bold = True
    set_run_font(sr1, "Arial", True)

    sp2 = s2.paragraphs[0]
    make_p_rtl(sp2)
    sr2 = sp2.add_run("إمضاء وختم الطرف الثاني (صاحب الضيعة):\n\nشركة فاس أقري (FAS AGRI SARL)\nالممثل القانوني والوكيل: محمد حسين صفر\nالمعرف الجبائي: 1772025K\n\nالتوقيع والختم: .......................................")
    sr2.font.size = Pt(9)
    sr2.font.bold = True
    set_run_font(sr2, "Arial", True)

    # Footer
    doc.add_paragraph()
    ft_p = doc.add_paragraph()
    make_p_rtl(ft_p)
    ft_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    ft_r = ft_p.add_run("وثيقة تعاقدية رسمية — شركة منصة الزين لزيت الزيتون التونسي (ZinToop SUARL) • تونس / القيروان")
    ft_r.font.size = Pt(8.5)
    ft_r.font.color.rgb = RGBColor(140, 140, 140)
    set_run_font(ft_r, "Arial", True)

    sanitize_document_schema(doc)
    doc.save(output_path)
    print(f"✅ Generated: {output_path}")

# Run generation
if __name__ == '__main__':
    base_dirs = ['public/contracts', 'docs/contracts']
    for d in base_dirs:
        os.makedirs(d, exist_ok=True)
        build_contract_recolte_ar(os.path.join(d, 'contrat_recolte_zintoop_ar.docx'))
        build_contract_recolte_fr(os.path.join(d, 'contrat_recolte_zintoop_fr.docx'))
        build_contract_fas_agri(os.path.join(d, 'contrat_recolte_fas_agri.docx'))
