<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Section;
use App\Models\Setting;
use App\Models\Topic;
use App\Models\TopicCategory;
use App\Models\WebmasterBanner;
use App\Models\WebmasterSection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MiddleEastUnionContentSeeder extends Seeder
{
    /**
     * Seed editable demo content for the Middle East Union website.
     *
     * Contact information is taken from the supplied official letterhead.
     * Descriptive, news, service, product, and opportunity copy is demo content.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedSiteSettings();

            $programs = $this->seedPrograms();
            $categories = $this->seedCategories($programs);

            $this->seedTopics($programs, $categories);
            $this->seedBanners();
        });
    }

    private function seedSiteSettings(): void
    {
        $settings = Setting::query()->first() ?? new Setting();

        $this->fillAndSave($settings, [
            'site_title_ar' => 'اتحاد الشرق الأوسط',
            'site_title_en' => 'Middle East Union',
            'site_desc_ar' => 'منصة إقليمية للأعمال والتجارة الدولية تربط الفرص بالخبرات والشركاء في أسواق الشرق الأوسط.',
            'site_desc_en' => 'A regional business and international trade platform connecting opportunities, expertise, and partners across Middle Eastern markets.',
            'site_keywords_ar' => 'اتحاد الشرق الأوسط، التجارة الدولية، فرص الأعمال، الاستثمار، الشراكات، اليمن، سلطنة عمان',
            'site_keywords_en' => 'Middle East Union, international trade, business opportunities, investment, partnerships, Yemen, Oman',
            'site_webmails' => 'info@middleeast-union.com',
            'site_url' => 'https://www.middleeast-union.com',
            'site_status' => 1,
            'whatsapp_no' => '+967777777797',
            'contact_t1_ar' => 'المركز الرئيسي: صنعاء - اليمن | المركز الإقليمي للعمليات والتجارة الدولية: سلطنة عُمان',
            'contact_t1_en' => 'Head Office: Sana’a, Yemen | Regional Center for Operations and International Trade: Sultanate of Oman',
            'contact_t3' => '+967 777 777 797',
            'contact_t4' => '+968 922 323 70',
            'contact_t6' => 'info@middleeast-union.com',
            'created_by' => $settings->exists ? $settings->created_by : 1,
            'updated_by' => 1,
        ]);
    }

    /**
     * @return array<string, WebmasterSection>
     */
    private function seedPrograms(): array
    {
        $programDefinitions = [
            'pages' => [
                'row_no' => 101,
                'title_ar' => 'صفحات الاتحاد',
                'title_en' => 'Union Pages',
                'slug_ar' => 'صفحات-الاتحاد',
                'slug_en' => 'meu-pages',
                'type' => 10,
                'sections_status' => 0,
                'date_status' => 0,
                'comments_status' => 0,
            ],
            'news' => [
                'row_no' => 102,
                'title_ar' => 'الأخبار والفعاليات',
                'title_en' => 'News & Events',
                'slug_ar' => 'الأخبار-والفعاليات',
                'slug_en' => 'meu-news',
                'type' => 0,
                'sections_status' => 1,
                'date_status' => 1,
                'comments_status' => 0,
            ],
            'services' => [
                'row_no' => 103,
                'title_ar' => 'الخدمات',
                'title_en' => 'Services',
                'slug_ar' => 'خدمات-الاتحاد',
                'slug_en' => 'meu-services',
                'type' => 0,
                'sections_status' => 1,
                'date_status' => 0,
                'comments_status' => 0,
            ],
            'products' => [
                'row_no' => 104,
                'title_ar' => 'دليل المنتجات',
                'title_en' => 'Products Directory',
                'slug_ar' => 'دليل-المنتجات',
                'slug_en' => 'meu-products',
                'type' => 0,
                'sections_status' => 1,
                'date_status' => 0,
                'comments_status' => 0,
            ],
            'opportunities' => [
                'row_no' => 105,
                'title_ar' => 'الفرص التجارية',
                'title_en' => 'Business Opportunities',
                'slug_ar' => 'الفرص-التجارية',
                'slug_en' => 'meu-opportunities',
                'type' => 0,
                'sections_status' => 1,
                'date_status' => 1,
                'comments_status' => 0,
            ],
        ];

        $programs = [];

        foreach ($programDefinitions as $key => $definition) {
            /** @var WebmasterSection $program */
            $program = $this->findOrNew(WebmasterSection::class, [
                'seo_url_slug_en' => $definition['slug_en'],
            ]);

            $this->fillAndSave($program, [
                'row_no' => $definition['row_no'],
                'title_ar' => $definition['title_ar'],
                'title_en' => $definition['title_en'],
                'seo_title_ar' => $definition['title_ar'].' | اتحاد الشرق الأوسط',
                'seo_title_en' => $definition['title_en'].' | Middle East Union',
                'seo_description_ar' => 'محتوى '.$definition['title_ar'].' في موقع اتحاد الشرق الأوسط.',
                'seo_description_en' => $definition['title_en'].' content on the Middle East Union website.',
                'seo_keywords_ar' => 'اتحاد الشرق الأوسط، '.$definition['title_ar'],
                'seo_keywords_en' => 'Middle East Union, '.$definition['title_en'],
                'seo_url_slug_ar' => $definition['slug_ar'],
                'seo_url_slug_en' => $definition['slug_en'],
                'photo' => 'nav-bg.png',
                'type' => $definition['type'],
                'title_status' => 1,
                'photo_status' => 1,
                'tags_status' => 1,
                'case_status' => 1,
                'featured_status' => 1,
                'index_status' => 1,
                'visits_status' => 1,
                'sections_status' => $definition['sections_status'],
                'comments_status' => $definition['comments_status'],
                'date_status' => $definition['date_status'],
                'expire_date_status' => $key === 'opportunities' ? 1 : 0,
                'longtext_status' => 1,
                'editor_status' => 1,
                'attach_file_status' => $key === 'opportunities' ? 1 : 0,
                'extra_attach_file_status' => 0,
                'multi_images_status' => 1,
                'section_icon_status' => 1,
                'icon_status' => 1,
                'maps_status' => 0,
                'order_status' => in_array($key, ['services', 'products'], true) ? 1 : 0,
                'related_status' => 1,
                'seo_status' => 1,
                'code_status' => 1,
                'no_status' => 1,
                'status' => 1,
                'created_by' => $program->exists ? $program->created_by : 1,
                'updated_by' => 1,
            ]);

            $programs[$key] = $program;
        }

        return $programs;
    }

    /**
     * @param  array<string, WebmasterSection>  $programs
     * @return array<string, Section>
     */
    private function seedCategories(array $programs): array
    {
        $definitions = [
            'news.union' => ['news', 1, 'أخبار الاتحاد', 'Union News', 'أخبار-الاتحاد', 'union-news', 'fa fa-newspaper'],
            'news.events' => ['news', 2, 'الفعاليات والملتقيات', 'Events & Forums', 'الفعاليات-والملتقيات', 'events-forums', 'fa fa-calendar'],
            'news.partnerships' => ['news', 3, 'الشراكات والاتفاقيات', 'Partnerships & Agreements', 'الشراكات-والاتفاقيات', 'partnerships-agreements', 'fa fa-handshake'],

            'services.trade' => ['services', 1, 'التجارة الدولية', 'International Trade', 'التجارة-الدولية', 'international-trade', 'fa fa-globe'],
            'services.development' => ['services', 2, 'تطوير الأعمال', 'Business Development', 'تطوير-الأعمال', 'business-development', 'fa fa-chart-line'],
            'services.research' => ['services', 3, 'دراسات السوق', 'Market Research', 'دراسات-السوق', 'market-research', 'fa fa-chart-pie'],
            'services.matchmaking' => ['services', 4, 'الربط والشراكات', 'Business Matchmaking', 'الربط-والشراكات', 'business-matchmaking', 'fa fa-people-arrows'],

            'products.food' => ['products', 1, 'المنتجات الغذائية والزراعية', 'Food & Agricultural Products', 'المنتجات-الغذائية-والزراعية', 'food-agriculture', 'fa fa-seedling'],
            'products.construction' => ['products', 2, 'مواد البناء والتشييد', 'Construction Materials', 'مواد-البناء-والتشييد', 'construction-materials', 'fa fa-building'],
            'products.industrial' => ['products', 3, 'المعدات والحلول الصناعية', 'Industrial Equipment & Solutions', 'المعدات-والحلول-الصناعية', 'industrial-solutions', 'fa fa-industry'],
            'products.consumer' => ['products', 4, 'المنتجات الاستهلاكية', 'Consumer Products', 'المنتجات-الاستهلاكية', 'consumer-products', 'fa fa-box-open'],

            'opportunities.import' => ['opportunities', 1, 'طلبات الاستيراد والتوريد', 'Import & Supply Requests', 'طلبات-الاستيراد-والتوريد', 'import-supply-requests', 'fa fa-truck-loading'],
            'opportunities.export' => ['opportunities', 2, 'عروض التصدير', 'Export Offers', 'عروض-التصدير', 'export-offers', 'fa fa-ship'],
            'opportunities.investment' => ['opportunities', 3, 'الشراكات والاستثمار', 'Partnerships & Investment', 'الشراكات-والاستثمار', 'partnerships-investment', 'fa fa-hand-holding-usd'],
        ];

        $categories = [];

        foreach ($definitions as $key => [$programKey, $rowNo, $titleAr, $titleEn, $slugAr, $slugEn, $icon]) {
            $program = $programs[$programKey];

            /** @var Section $category */
            $category = $this->findOrNew(Section::class, [
                'webmaster_id' => $program->id,
                'seo_url_slug_en' => $slugEn,
            ]);

            $this->fillAndSave($category, [
                'row_no' => $rowNo,
                'webmaster_id' => $program->id,
                'father_id' => 0,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'details_ar' => 'قسم '.$titleAr.' ضمن '.$program->title_ar.'.',
                'details_en' => $titleEn.' category within '.$program->title_en.'.',
                'seo_title_ar' => $titleAr.' | اتحاد الشرق الأوسط',
                'seo_title_en' => $titleEn.' | Middle East Union',
                'seo_description_ar' => 'تصفح محتوى '.$titleAr.' لدى اتحاد الشرق الأوسط.',
                'seo_description_en' => 'Browse '.$titleEn.' content from the Middle East Union.',
                'seo_url_slug_ar' => $slugAr,
                'seo_url_slug_en' => $slugEn,
                'icon' => $icon,
                'status' => 1,
                'visits' => $category->exists ? $category->visits : 0,
                'created_by' => $category->exists ? $category->created_by : 1,
                'updated_by' => 1,
            ]);

            $categories[$key] = $category;
        }

        return $categories;
    }

    /**
     * @param  array<string, WebmasterSection>  $programs
     * @param  array<string, Section>  $categories
     */
    private function seedTopics(array $programs, array $categories): void
    {
        $topics = [
            // Static pages
            ['pages', null, 1, 'من نحن', 'About the Union', 'من-نحن', 'about-the-union',
                '<p>اتحاد الشرق الأوسط منصة إقليمية تربط مجتمع الأعمال بالخبرات والفرص والأسواق، وتدعم بناء علاقات تجارية مستدامة بين المؤسسات ورواد الأعمال في المنطقة.</p><p><strong>تنبيه:</strong> هذا نص تعريفي افتراضي أُعد كبداية للموقع ويمكن استبداله بالنص الرسمي المعتمد.</p>',
                '<p>The Middle East Union is a regional platform connecting the business community with expertise, opportunities, and markets while supporting sustainable commercial relationships.</p><p><strong>Note:</strong> This is editable demo copy.</p>'],
            ['pages', null, 2, 'رؤيتنا', 'Our Vision', 'رؤيتنا', 'our-vision',
                '<p>أن يكون الاتحاد منصة موثوقة ومؤثرة في تنمية الروابط الاقتصادية والتجارية، وتحويل الفرص الإقليمية إلى شراكات عملية ذات أثر مستدام.</p>',
                '<p>To be a trusted and influential platform that develops economic and trade links and turns regional opportunities into sustainable partnerships.</p>'],
            ['pages', null, 3, 'رسالتنا', 'Our Mission', 'رسالتنا', 'our-mission',
                '<p>تيسير الوصول إلى المعلومات والأسواق والشركاء، وتقديم خدمات عملية تساعد الأعضاء على تطوير أعمالهم والتوسع بثقة في أسواق الشرق الأوسط.</p>',
                '<p>To facilitate access to information, markets, and partners, and provide practical services that help members grow confidently across Middle Eastern markets.</p>'],
            ['pages', null, 4, 'مراكزنا وبيانات التواصل', 'Our Offices & Contact Details', 'مراكزنا-وبيانات-التواصل', 'offices-contact',
                '<h3>المركز الرئيسي</h3><p>صنعاء - اليمن<br>هاتف: <a href="tel:+967777777797">+967 777 777 797</a></p><h3>المركز الإقليمي للعمليات والتجارة الدولية</h3><p>سلطنة عُمان<br>هاتف: <a href="tel:+96892232370">+968 922 323 70</a></p><p>البريد الإلكتروني: <a href="mailto:info@middleeast-union.com">info@middleeast-union.com</a><br>الموقع: <a href="https://www.middleeast-union.com">www.middleeast-union.com</a></p>',
                '<h3>Head Office</h3><p>Sana’a, Yemen<br>Phone: +967 777 777 797</p><h3>Regional Center for Operations and International Trade</h3><p>Sultanate of Oman<br>Phone: +968 922 323 70</p><p>Email: info@middleeast-union.com<br>Website: www.middleeast-union.com</p>'],
            ['pages', null, 5, 'العضوية والانضمام', 'Membership', 'العضوية-والانضمام', 'membership',
                '<p>تتيح العضوية للمنشآت ورواد الأعمال التعرف على الفرص، والتواصل مع شركاء محتملين، والاستفادة من الفعاليات والمحتوى المتخصص. أضف شروط العضوية ونموذج التقديم المعتمد قبل نشر الصفحة رسميًا.</p>',
                '<p>Membership helps businesses and entrepreneurs discover opportunities, meet potential partners, and access events and specialist content. Add the approved eligibility and application process before publishing.</p>'],

            // News and events
            ['news', 'news.union', 1, 'إطلاق البوابة الرقمية لاتحاد الشرق الأوسط', 'Middle East Union Digital Portal Launch', 'إطلاق-البوابة-الرقمية', 'digital-portal-launch',
                '<p>يقدم الاتحاد عبر بوابته الرقمية نافذة موحدة للأخبار والخدمات ودليل المنتجات والفرص التجارية، بما يسهل وصول مجتمع الأعمال إلى المعلومات والتواصل.</p><p><em>خبر تجريبي قابل للتحديث.</em></p>',
                '<p>The Union’s digital portal provides a unified gateway to news, services, products, and business opportunities.</p><p><em>Editable demo news item.</em></p>'],
            ['news', 'news.events', 2, 'ملتقى افتراضي للتجارة البينية في الشرق الأوسط', 'Virtual Forum on Intra-Regional Trade', 'ملتقى-التجارة-البينية', 'intra-regional-trade-forum',
                '<p>ملتقى أعمال افتراضي يجمع المصدرين والمستوردين ومقدمي الخدمات لمناقشة احتياجات الأسواق وتحديات سلاسل الإمداد وفرص التعاون.</p><p><em>فعالية افتراضية لأغراض العرض.</em></p>',
                '<p>A demo virtual business forum bringing exporters, importers, and service providers together to discuss market needs, supply chains, and collaboration.</p>'],
            ['news', 'news.partnerships', 3, 'برنامج لبناء الشراكات بين الأعضاء', 'Member Partnership Program', 'برنامج-بناء-الشراكات', 'member-partnership-program',
                '<p>برنامج تجريبي يهدف إلى مواءمة احتياجات الأعضاء مع القدرات المتاحة وتسهيل الاجتماعات الثنائية وبناء فرص التعاون المشترك.</p>',
                '<p>A demo program designed to match member needs with available capabilities and facilitate business meetings and collaboration.</p>'],
            ['news', 'news.events', 4, 'ورشة التعريف بمتطلبات دخول الأسواق', 'Market Entry Requirements Workshop', 'ورشة-دخول-الأسواق', 'market-entry-workshop',
                '<p>ورشة تعريفية حول الاستعداد للتصدير، وتوثيق المنتجات، وبناء ملف تجاري مناسب للتواصل مع المشترين والشركاء.</p>',
                '<p>An introductory workshop covering export readiness, product documentation, and business profiles for buyers and partners.</p>'],

            // Services
            ['services', 'services.trade', 1, 'خدمات تسهيل التجارة الدولية', 'International Trade Facilitation', 'تسهيل-التجارة-الدولية', 'international-trade-facilitation',
                '<p>مساندة الشركات في تنظيم متطلبات عروضها التجارية، وتحديد الأسواق المستهدفة، والوصول إلى جهات الأعمال ذات الصلة.</p>',
                '<p>Support for businesses in structuring commercial offers, identifying target markets, and reaching relevant business contacts.</p>'],
            ['services', 'services.development', 2, 'تطوير الملفات والعروض التجارية', 'Business Profile Development', 'تطوير-العروض-التجارية', 'business-profile-development',
                '<p>مراجعة وتطوير الملف التعريفي للشركة وعروض المنتجات والخدمات بما يجعلها أوضح وأكثر جاهزية للشراكات والتوسع.</p>',
                '<p>Review and development of company profiles and product or service offers to improve partnership and expansion readiness.</p>'],
            ['services', 'services.research', 3, 'دراسات أولية للأسواق والقطاعات', 'Market & Sector Briefs', 'دراسات-الأسواق-والقطاعات', 'market-sector-briefs',
                '<p>إعداد موجزات أولية تساعد على فهم حجم الفرصة، واتجاهات الطلب، والمنافسين، وقنوات الوصول المحتملة إلى السوق.</p>',
                '<p>Initial briefs covering opportunity size, demand trends, competitors, and potential routes to market.</p>'],
            ['services', 'services.matchmaking', 4, 'الربط مع الشركاء والموردين', 'Partner & Supplier Matchmaking', 'الربط-مع-الشركاء', 'partner-supplier-matchmaking',
                '<p>استقبال احتياج العضو، والبحث في شبكة العلاقات والدليل التجاري، وترتيب تواصل أولي مع الجهات المحتملة.</p>',
                '<p>Capturing member needs, searching the business network and directory, and arranging initial contact with suitable prospects.</p>'],

            // Products directory
            ['products', 'products.food', 1, 'عسل يمني طبيعي', 'Natural Yemeni Honey', 'عسل-يمني-طبيعي', 'natural-yemeni-honey',
                '<p>نموذج إدراج لمنتج عسل طبيعي مخصص للعرض التجاري. تُستكمل بيانات المورد، والمنشأ، والعبوة، والكمية المتاحة، وشهادات الجودة قبل النشر.</p>',
                '<p>Demo listing for natural honey. Supplier, origin, packaging, available volume, and certification details should be completed before publishing.</p>'],
            ['products', 'products.food', 2, 'بن عربي مختص', 'Specialty Arabian Coffee', 'بن-عربي-مختص', 'specialty-arabian-coffee',
                '<p>نموذج منتج للبن المختص، ويشمل عند اعتماده وصف المحصول ودرجة التحميص وخيارات التعبئة والطاقة التوريدية.</p>',
                '<p>Demo specialty coffee listing to be completed with crop, roast, packaging, and supply capacity information.</p>'],
            ['products', 'products.construction', 3, 'مواد تشطيب وبناء', 'Building & Finishing Materials', 'مواد-تشطيب-وبناء', 'building-finishing-materials',
                '<p>إدراج افتراضي لفئة مواد البناء والتشطيب. أضف المواصفات الفنية وبلد المنشأ والحد الأدنى للطلب وشروط التوريد.</p>',
                '<p>Demo listing for building and finishing materials. Add specifications, origin, minimum order, and supply terms.</p>'],
            ['products', 'products.construction', 4, 'حلول العزل الحراري والمائي', 'Thermal & Waterproofing Solutions', 'حلول-العزل', 'insulation-solutions',
                '<p>حلول افتراضية لمشروعات البناء تشمل مواد العزل الحراري والمائي، مع مساحة لإضافة المواصفات والاعتمادات.</p>',
                '<p>Demo construction solutions covering thermal and waterproofing materials, with space for specifications and approvals.</p>'],
            ['products', 'products.industrial', 5, 'معدات تعبئة وتغليف', 'Packaging Equipment', 'معدات-تعبئة-وتغليف', 'packaging-equipment',
                '<p>نموذج عرض لمعدات وخطوط التعبئة والتغليف المناسبة للمصانع والمنشآت الصغيرة والمتوسطة.</p>',
                '<p>Demo offer for packaging machinery and lines suitable for factories and small and medium enterprises.</p>'],
            ['products', 'products.industrial', 6, 'حلول الطاقة للمشروعات', 'Business Energy Solutions', 'حلول-الطاقة-للمشروعات', 'business-energy-solutions',
                '<p>حلول افتراضية للطاقة والأنظمة المساندة للمشروعات، تُستكمل ببيانات القدرة والضمان وخدمة ما بعد البيع.</p>',
                '<p>Demo energy and support systems listing to be completed with capacity, warranty, and after-sales details.</p>'],
            ['products', 'products.consumer', 7, 'منتجات العناية الشخصية', 'Personal Care Products', 'منتجات-العناية-الشخصية', 'personal-care-products',
                '<p>إدراج تجريبي لمنتجات العناية الشخصية مع حقول مقترحة للمكونات والتعبئة وشهادات المطابقة.</p>',
                '<p>Demo personal care listing with suggested fields for ingredients, packaging, and conformity certificates.</p>'],
            ['products', 'products.consumer', 8, 'منتجات منزلية متنوعة', 'Household Products', 'منتجات-منزلية', 'household-products',
                '<p>نموذج لفئة المنتجات المنزلية المتاحة للتوزيع أو التصدير، مع إمكانية إضافة معرض صور وملف مواصفات.</p>',
                '<p>Demo household products listing for distribution or export, with room for images and a specification sheet.</p>'],

            // Business opportunities
            ['opportunities', 'opportunities.import', 1, 'طلب موردين لمنتجات غذائية طويلة الأجل', 'Suppliers Wanted for Shelf-Stable Food', 'طلب-موردين-منتجات-غذائية', 'suppliers-wanted-shelf-stable-food',
                '<p>فرصة تجريبية: جهة شراء تبحث عن موردين لمنتجات غذائية مناسبة للتوزيع الإقليمي. يلزم تقديم نبذة الشركة وقائمة المنتجات والطاقة التوريدية.</p>',
                '<p>Demo opportunity: a buyer is seeking suppliers of shelf-stable food products for regional distribution.</p>'],
            ['opportunities', 'opportunities.import', 2, 'طلب عروض لمواد تعبئة وتغليف', 'Request for Packaging Material Offers', 'طلب-مواد-تعبئة-وتغليف', 'request-packaging-materials',
                '<p>طلب تجريبي لاستقبال عروض موردين لمواد التعبئة والتغليف، شاملاً المواصفات والحد الأدنى للطلب ومدة التوريد.</p>',
                '<p>Demo request for packaging material suppliers, including specifications, minimum order, and lead time.</p>'],
            ['opportunities', 'opportunities.export', 3, 'عرض تصدير منتجات زراعية موسمية', 'Seasonal Agricultural Export Offer', 'عرض-تصدير-منتجات-زراعية', 'seasonal-agricultural-export-offer',
                '<p>عرض تجريبي لمنتجات زراعية موسمية قابلة للتصدير. تُضاف بيانات المحصول والتعبئة والكميات والأسواق المستهدفة.</p>',
                '<p>Demo export offer for seasonal agricultural products. Add crop, packaging, quantity, and target market details.</p>'],
            ['opportunities', 'opportunities.export', 4, 'موزعون مطلوبون لمنتجات استهلاكية', 'Distributors Wanted for Consumer Products', 'موزعون-مطلوبون', 'distributors-wanted-consumer-products',
                '<p>فرصة تجريبية للبحث عن موزعين لديهم شبكة مبيعات محلية وخبرة في إطلاق المنتجات الاستهلاكية.</p>',
                '<p>Demo search for distributors with local sales networks and consumer product launch experience.</p>'],
            ['opportunities', 'opportunities.investment', 5, 'شريك تشغيلي لمشروع خدمات لوجستية', 'Operating Partner for a Logistics Project', 'شريك-مشروع-لوجستي', 'logistics-project-partner',
                '<p>فرصة شراكة افتراضية لمشروع خدمات لوجستية. يجب إضافة نطاق المشروع والمتطلبات المالية والتشغيلية وإجراءات التواصل الآمن.</p>',
                '<p>Demo partnership opportunity for a logistics services project. Add project scope, financial and operational requirements, and a secure contact process.</p>'],
        ];

        $programImages = [
            'pages' => 'meu-pages.svg',
            'news' => 'meu-news.svg',
            'services' => 'meu-services.svg',
            'products' => 'meu-products.svg',
            'opportunities' => 'meu-opportunities.svg',
        ];

        foreach ($topics as $index => [$programKey, $categoryKey, $rowNo, $titleAr, $titleEn, $slugAr, $slugEn, $detailsAr, $detailsEn]) {
            $program = $programs[$programKey];
            $category = $categoryKey ? $categories[$categoryKey] : null;

            /** @var Topic $topic */
            $topic = $this->findOrNew(Topic::class, [
                'webmaster_id' => $program->id,
                'seo_url_slug_en' => $slugEn,
            ]);

            $plainAr = trim(strip_tags($detailsAr));
            $plainEn = trim(strip_tags($detailsEn));

            $this->fillAndSave($topic, [
                'row_no' => $rowNo,
                'webmaster_id' => $program->id,
                'section_id' => $category?->id,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'details_ar' => $detailsAr,
                'details_en' => $detailsEn,
                'date' => in_array($programKey, ['news', 'opportunities'], true)
                    ? now()->subDays($index * 3)->toDateString()
                    : null,
                'expire_date' => $programKey === 'opportunities'
                    ? now()->addMonths(3)->toDateString()
                    : null,
                'photo_file' => $programImages[$programKey],
                'icon' => $category?->icon,
                'status' => 1,
                'featured' => $rowNo <= 2 ? 1 : 0,
                'visits' => $topic->exists ? $topic->visits : 0,
                'seo_title_ar' => $titleAr.' | اتحاد الشرق الأوسط',
                'seo_title_en' => $titleEn.' | Middle East Union',
                'seo_description_ar' => mb_substr($plainAr, 0, 155),
                'seo_description_en' => mb_substr($plainEn, 0, 155),
                'seo_keywords_ar' => 'اتحاد الشرق الأوسط، '.$titleAr,
                'seo_keywords_en' => 'Middle East Union, '.$titleEn,
                'seo_url_slug_ar' => $slugAr,
                'seo_url_slug_en' => $slugEn,
                'created_by' => $topic->exists ? $topic->created_by : 1,
                'updated_by' => 1,
            ]);

            if ($category) {
                /** @var TopicCategory $link */
                $link = $this->findOrNew(TopicCategory::class, [
                    'topic_id' => $topic->id,
                    'section_id' => $category->id,
                ]);
                $this->fillAndSave($link, [
                    'topic_id' => $topic->id,
                    'section_id' => $category->id,
                ]);
            }
        }
    }

    private function seedBanners(): void
    {
        /** @var WebmasterBanner $bannerGroup */
        $bannerGroup = $this->findOrNew(WebmasterBanner::class, [
            'title_en' => 'Middle East Union Home Banners',
        ]);

        $this->fillAndSave($bannerGroup, [
            'row_no' => 101,
            'title_ar' => 'بنرات اتحاد الشرق الأوسط الرئيسية',
            'title_en' => 'Middle East Union Home Banners',
            'width' => 1600,
            'height' => 600,
            'desc_status' => 1,
            'link_status' => 1,
            'icon_status' => 0,
            'type' => 1,
            'status' => 1,
            'created_by' => $bannerGroup->exists ? $bannerGroup->created_by : 1,
            'updated_by' => 1,
        ]);

        $banners = [
            [1, 'اتحاد الشرق الأوسط', 'Middle East Union',
                'نربط الأعمال بالفرص والأسواق والشركاء في المنطقة.',
                'Connecting businesses with opportunities, markets, and partners across the region.',
                'meu-banner-connect.svg', '/', '/'],
            [2, 'بوابتك إلى التجارة الدولية', 'Your Gateway to International Trade',
                'خدمات عملية تساعد المنشآت على الوصول إلى الأسواق وبناء الشراكات.',
                'Practical services that help businesses access markets and build partnerships.',
                'meu-banner-trade.svg', '/خدمات-الاتحاد', '/en/meu-services'],
            [3, 'اكتشف فرصًا جديدة لأعمالك', 'Discover New Business Opportunities',
                'تصفح دليل المنتجات وطلبات التوريد وعروض التصدير وفرص الاستثمار.',
                'Browse products, supply requests, export offers, and investment opportunities.',
                'meu-banner-opportunities.svg', '/الفرص-التجارية', '/en/meu-opportunities'],
        ];

        foreach ($banners as [$rowNo, $titleAr, $titleEn, $detailsAr, $detailsEn, $file, $linkAr, $linkEn]) {
            /** @var Banner $banner */
            $banner = $this->findOrNew(Banner::class, [
                'section_id' => $bannerGroup->id,
                'title_en' => $titleEn,
            ]);

            $this->fillAndSave($banner, [
                'row_no' => $rowNo,
                'section_id' => $bannerGroup->id,
                'title_ar' => $titleAr,
                'title_en' => $titleEn,
                'details_ar' => $detailsAr,
                'details_en' => $detailsEn,
                'file_ar' => $file,
                'file_en' => $file,
                'link_url' => $linkAr,
                'link_ar' => $linkAr,
                'link_en' => $linkEn,
                'status' => 1,
                'visits' => $banner->exists ? $banner->visits : 0,
                'created_by' => $banner->exists ? $banner->created_by : 1,
                'updated_by' => 1,
            ]);
        }
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $modelClass
     * @param  array<string, mixed>  $identity
     * @return TModel
     */
    private function findOrNew(string $modelClass, array $identity): Model
    {
        $query = $modelClass::query();

        foreach ($identity as $column => $value) {
            $query->where($column, $value);
        }

        $model = $query->first();

        if ($model) {
            return $model;
        }

        /** @var Model $model */
        $model = new $modelClass();

        foreach ($identity as $column => $value) {
            $model->{$column} = $value;
        }

        return $model;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function fillAndSave(Model $model, array $attributes): void
    {
        foreach ($attributes as $column => $value) {
            $model->{$column} = $value;
        }

        $model->save();
    }
}
