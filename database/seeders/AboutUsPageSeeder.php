<?php

namespace Database\Seeders;

use App\Models\AboutUsPage;
use Illuminate\Database\Seeder;

class AboutUsPageSeeder extends Seeder
{
    public function run(): void
    {
        AboutUsPage::updateOrCreate(
            ['id' => 1],
            [
                'is_active' => true,
                'content' => [
                    'hero' => [
                        'badge_ar' => 'مؤسسة مشهرة برقم 2665 سنة 2019 – وزارة التضامن الاجتماعي',
                        'badge_en' => null,
                        'title_ar' => 'مؤسسة اليسر الخيرية',
                        'title_en' => null,
                        'description_ar' => 'مؤسسة غير ربحية تم إنشاؤها عام 2019، تابعة لوزارة التضامن الاجتماعي. هدفنا أن تصل الصدقة لمستحقيها – في المنصورة والدقهلية وكل محافظات مصر.',
                        'description_en' => null,
                        'quote_ar' => 'تبرعك يترك أثرًا ويغير حياة',
                        'quote_en' => null,
                        'image' => null,
                        'donate_label_ar' => 'تبرع الآن',
                        'donate_label_en' => null,
                        'donate_url' => '/donate',
                        'story_label_ar' => 'اعرف قصتنا',
                        'story_label_en' => null,
                    ],

                    'stats' => [
                        [
                            'number' => '1400+',
                            'label_ar' => 'كرتونة رمضان',
                            'label_en' => null,
                        ],
                        [
                            'number' => '3000+',
                            'label_ar' => 'كرتونة غذائية',
                            'label_en' => null,
                        ],
                        [
                            'number' => '122',
                            'label_ar' => 'سقف منزل',
                            'label_en' => null,
                        ],
                        [
                            'number' => '385',
                            'label_ar' => 'وصلة مياه نقية',
                            'label_en' => null,
                        ],
                        [
                            'number' => '9',
                            'label_ar' => 'عرائس تم تيسير زواجهن',
                            'label_en' => null,
                        ],
                        [
                            'number' => '354',
                            'label_ar' => 'طالب – مساعدات تعليم',
                            'label_en' => null,
                        ],
                        [
                            'number' => '365',
                            'label_ar' => 'يوم عطاء بدون توقف',
                            'label_en' => null,
                        ],
                        [
                            'number' => '4 طن',
                            'label_ar' => 'لحوم ودواجن موزعة',
                            'label_en' => null,
                        ],
                    ],

                    'programs' => [
                        ['title_ar' => 'حملة جاز (سقيا وأسقها)', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'حملة دفء الشتاء', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'باب الجنة (كفالة اليتيم)', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'مبادرة عروس خير', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'غذاء شهري (كراتين وسلال)', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'كفالات ومساعدات شهرية', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'صكوك الأضاحي ومزارع الوقف', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'زكاة المال والصدقات', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'مساعدات طبية طارئة وقوافل', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'دور وحلقات تحفيظ القرآن', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'حملة مزرعون', 'title_en' => null, 'icon' => null],
                        ['title_ar' => 'تيسير زواج الفتيات اليتيمات', 'title_en' => null, 'icon' => null],
                    ],

                    'trust' => [
                        [
                            'title_ar' => 'ترخيص وزارة التضامن',
                            'description_ar' => 'إشهار المؤسسة برقم 2665 لسنة 2019.',
                            'icon' => null,
                        ],
                        [
                            'title_ar' => 'مراجعة محاسبية مستقلة',
                            'description_ar' => 'تقارير مالية سنوية معتمدة وإيصالات رسمية.',
                            'icon' => null,
                        ],
                        [
                            'title_ar' => 'تغطية شاملة في مصر',
                            'description_ar' => 'مقر رئيسي بالمنصورة وقوافل خيرية في محافظات مصر.',
                            'icon' => null,
                        ],
                    ],

                    'why_us' => [
                        [
                            'title_ar' => 'الحضور بنفسك',
                            'description_ar' => 'يمكنك الحضور ومتابعة التبرع بنفسك والتأكد من تنفيذ العمل.',
                            'icon' => null,
                        ],
                        [
                            'title_ar' => 'التوثيق الفوري',
                            'description_ar' => 'نوثق التبرعات بالصور والفيديو وإشعار رسمي يوضح الأثر المحقق في نفس اليوم.',
                            'icon' => null,
                        ],
                        [
                            'title_ar' => 'تطوع عائلتك',
                            'description_ar' => 'أفراد عائلتك يمكنهم التطوع معنا ويكونون جزءًا من فرقنا والمبادرات.',
                            'icon' => null,
                        ],
                        [
                            'title_ar' => 'الكيان والحسابات الرسمية',
                            'description_ar' => 'كيان مشهر رسميًا بوزارة التضامن الاجتماعي مع حسابات بنكية رسمية.',
                            'icon' => null,
                        ],
                    ],

                    'values' => [
                        ['title_ar' => 'التمكين', 'description_ar' => 'بناء الإنسان: تعليم وعلاج وفرص أفضل.', 'icon' => null],
                        ['title_ar' => 'الإحسان', 'description_ar' => 'نسعى لباب البيت بكرامة.', 'icon' => null],
                        ['title_ar' => 'الأمانة', 'description_ar' => 'كل تبرع موثق بالصور والفيديو.', 'icon' => null],
                        ['title_ar' => 'القوة', 'description_ar' => 'برامج تنموية متجددة ومستمرة.', 'icon' => null],
                        ['title_ar' => 'الثقة', 'description_ar' => 'كيان مشهر رسميًا.', 'icon' => null],
                    ],

                    'story' => [
                        'title_ar' => 'قصتنا',
                        'description_ar' => 'بعد العمل لأكثر من عشر سنوات في العمل الخيري كان لابد من كيان منظم ومرخص يجمع أعمالنا ويكبر ويساعدنا في بناء العمل الخيري في جمع تبرعات وتوزيعها لمؤسسة قادرة تغير مفهوم العمل الخيري وتنمو بالأثر من خلال مبادراتنا.',
                        'registration_ar' => 'إشهار رقم 2665 لسنة 2019 – وزارة التضامن الاجتماعي',
                        'account_ar' => 'حساب بنك CIB: 100043605733',
                        'mission' => [
                            'title_ar' => 'رسالتنا',
                            'description_ar' => 'بناء إنسان واعٍ جسديًا، نفسيًا وبصريًا.',
                        ],
                        'goals' => [
                            'title_ar' => 'هدفنا',
                            'items' => [
                                'أن تصل الصدقة لمن يستحقها بأعلى معايير الأمانة.',
                                'تغيير مفهوم العمل الخيري في مصر من إعطاء صدقة لحظية إلى تغيير حياة كاملة.',
                                'الحفاظ على أموال المتبرعين وتوفير طرق تبرع آمنة وموثقة.',
                            ],
                        ],
                    ],

                    'vision_2030' => [
                        'title_ar' => 'رؤيتنا لعام 2030',
                        'description_ar' => 'مستهدفات طموحة للانتقال بالمجتمعات الأكثر احتياجًا إلى دائرة الاكتفاء الذاتي والإنتاج.',
                        'items' => [
                            [
                                'title_ar' => 'مشاريع استثمارية وقف لله',
                                'description_ar' => 'بناء مشاريع استثمارية وقف لله تستمر في خدمة الأسر وتوفير دخل مستدام.',
                                'icon' => null,
                            ],
                            [
                                'title_ar' => 'مستشفى طبية متكاملة',
                                'description_ar' => 'بناء مستشفى طبي يخدم أكبر عدد ممكن من الأسر الأولى بالرعاية.',
                                'icon' => null,
                            ],
                            [
                                'title_ar' => 'كفالة 50 ألف طالب',
                                'description_ar' => 'كفالة تعليمية شاملة لأكثر من 50,000 طالب من الأسر الأولى بالرعاية.',
                                'icon' => null,
                            ],
                            [
                                'title_ar' => 'مشاريع صغيرة للمستحقين',
                                'description_ar' => 'تمويل وتأسيس مشاريع إنتاجية صغيرة للمستحقين للتحول من الاحتياج إلى الإنتاج.',
                                'icon' => null,
                            ],
                        ],
                        'progress' => 22.7,
                        'progress_label_ar' => 'ما تم تحقيقه حتى الآن من مستهدفات 2030',
                    ],

                    'closing' => [
                        'title_ar' => 'ازرع اليوم... ليُثمر غدًا في كل بيت في مصر',
                        'subtitle_ar' => 'مؤسسة اليسر الخيرية – المنصورة والدقهلية وكل محافظات مصر',
                    ],
                ],
            ]
        );
    }
}
