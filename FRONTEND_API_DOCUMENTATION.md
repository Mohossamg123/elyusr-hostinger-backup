# Elyusr API — Frontend Documentation

> هذه الوثيقة مبنية على ملف `routes/api.php` وملفات التحقق/Controllers الموجودة في نسخة المشروع المرفوعة. أي تفاصيل خاصة بالحقول لم يوضحها الكود أو لم تُراجع هنا يجب تأكيدها من الـ Request/Resource قبل اعتمادها في الواجهة.

## 1. Base URL

- **Production API Base:** `https://api.elyusr.org/api/v1`
- كل المسارات في جدول الـ Endpoints نسبية إلى الـ Base URL أعلاه.
- صيغة البيانات الأساسية: JSON. عند رفع ملفات استخدم `multipart/form-data` إذا كان الـ endpoint يقبل ملفات.
- لا ترسل أسرار بوابات الدفع أو مفاتيح الخادم من الفرونت إند.

## 2. Authentication

الـ API يستخدم Laravel Sanctum. بعد تسجيل الدخول أو إنشاء حساب، خزّن `token` بأمان وأرسله للطلبات المحمية:

```http
Authorization: Bearer YOUR_TOKEN
Accept: application/json
Content-Type: application/json
```

- `POST /api/v1/register` إنشاء حساب عام.
- `POST /api/v1/login` تسجيل الدخول.
- `GET /api/v1/profile` بيانات الحساب الحالي — يتطلب Token.
- `POST /api/v1/logout` تسجيل الخروج — يتطلب Token.
- `POST /api/v1/change-password` تغيير كلمة المرور — يتطلب Token.
- المسارات الإدارية تتطلب Token وصلاحية مناسبة؛ بعض عمليات التعديل تتطلب أيضًا موافقة المحرر.
- التسجيل العام يقبل الدور `donor` أو `volunteer` فقط؛ لا يمكن اختيار دور إداري من واجهة التسجيل.

### Login body
```json
{
  "email": "user@example.com",
  "password": "your-password"
}
```

نجاح الدخول يرجع `message`, `token`, و`user`.

### Register body
```json
{
  "name": "Test User",
  "email": "user@example.com",
  "phone": "01000000000",
  "password": "StrongPass123",
  "password_confirmation": "StrongPass123",
  "role": "donor"
}
```

قواعد التحقق من الكود: الاسم مطلوب؛ البريد صحيح وغير مستخدم؛ الهاتف مطلوب وفريد؛ كلمة المرور 8 أحرف على الأقل ومؤكدة؛ الدور `donor` أو `volunteer`.

## 3. Common frontend behavior

- تعامل مع `401` على أنه جلسة غير مصرح بها/Token غير صالح، و`403` على أنه حساب غير نشط أو صلاحية غير كافية حسب الرسالة.
- أخطاء التحقق غالبًا تكون `422`؛ اعرض الأخطاء بجوار الحقول بدل الاكتفاء برسالة عامة.
- قوائم Laravel التي تستخدم `paginate(10)` قد ترجع بنية pagination (`data`, `links`, `meta`)؛ لا تفترض أن كل endpoint يرجع Array مباشرة.
- قد تختلف بنية الاستجابة حسب Resource أو Controller. راجع المثال الخاص بالـ endpoint قبل تثبيت types في TypeScript.
- مسارات الإحصائيات ليست كلها عامة: بعضها محمي بصلاحية حتى لو كانت قائمة البيانات العامة متاحة.

## 4. Public donation and payment flow

### 4.1 Payment methods
`GET /api/v1/payment-methods/active` يعرض وسائل الدفع النشطة فقط، مرتبة حسب `sort_order`.

### 4.2 Submit donation
`POST /api/v1/donations/submit` ينشئ تبرعًا بحالة أولية `pending` ويبدأ جلسة الدفع حسب مزود وسيلة الدفع. الكود يدعم `paymob` و`kashier`.

- عند اختيار Paymob، الاستجابة الناجحة تتضمن بيانات الدفع وبالأخص `client_secret` لاستخدام تكامل Paymob في الواجهة.
- عند اختيار Kashier، تتضمن بيانات الدفع `session_url`، وقد تتضمن `session_id`.
- احتفظ بـ `transaction_reference` لاستخدامه في التتبع.
- فشل تهيئة الدفع قد يرجع `422` ورسالة `message`، وأحيانًا `transaction_reference`.
- لا تعتبر التبرع مدفوعًا بمجرد إنشاء الطلب؛ استخدم نتيجة بوابة الدفع/التتبع.

> **تنبيه:** راجع `StoreDonationRequest.php` لمعرفة الحقول الإلزامية الدقيقة في نموذج التبرع؛ لا ترسل حقولًا تخمينية. الكود يحذف حقول بيانات المتبرع المؤقتة بعد إنشاء/ربط سجل المتبرع.

### 4.3 Track donation
`GET /api/v1/donations/track/{transactionReference}` لمتابعة حالة التبرع باستخدام مرجع العملية.

### 4.4 Payment callbacks
هذه المسارات مخصصة لتواصل بوابة الدفع مع الخادم وليست لاستدعاء الواجهة كجزء من التدفق العادي:
- `POST /api/v1/paymob/callback`
- `POST /api/v1/kashier/webhook`
- `GET /api/v1/kashier/redirect`

## 5. Endpoint inventory

يوضح الجدول كل المسارات المكتشفة في `routes/api.php`. عمود الصلاحية يذكر الـ middleware الصريح إن وجد؛ الصلاحيات الفعلية تعتمد كذلك على مجموعة المسار.

| Method | Endpoint | Access | Permission / notes |
|---|---|---|---|
| `GET` | `/api/v1/payment-methods/active` | مفتوح | حسب صلاحية المجموعة |
| `POST` | `/api/v1/donations/submit` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/donations/track/{transactionReference}` | مفتوح | حسب صلاحية المجموعة |
| `POST` | `/api/v1/paymob/callback` | مفتوح | حسب صلاحية المجموعة |
| `POST` | `/api/v1/kashier/webhook` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/kashier/redirect` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/faq-categories` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/faq-categories/{faqCategory}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/faqs` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/faqs/{faq}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/donation-campaigns` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/donation-campaigns/{donationCampaign}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/donation-campaign-categories` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/donation-campaign-categories/{donationCampaignCategory}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/programs` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/programs/{program}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/projects` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/projects/{project}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/cases` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/cases/statistics` | يتطلب Token | cases.view |
| `GET` | `/api/v1/cases/{case}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/case-categories` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/case-categories/{caseCategory}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/service-categories` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/service-categories/{serviceCategory}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/services` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/services/{service}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/programs/{program}/donation-packages` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/programs/{program}/donation-packages/{donationPackage}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/projects/{project}/donation-packages` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/projects/{project}/donation-packages/{donationPackage}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/donation-campaigns/{donationCampaign}/donation-packages` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/donation-campaigns/{donationCampaign}/donation-packages/{donationPackage}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/volunteer-campaigns` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/volunteer-campaigns/statistics` | يتطلب Token | volunteer_campaigns.view |
| `GET` | `/api/v1/volunteer-campaigns/{volunteer_campaign}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/content` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/content/{content}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/gallery` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/gallery/statistics` | يتطلب Token | gallery.view |
| `GET` | `/api/v1/gallery/{gallery}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/stories` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/stories/statistics` | يتطلب Token | stories.view |
| `GET` | `/api/v1/stories/{story}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/home-numbers` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/home-numbers/statistics` | يتطلب Token | home_numbers.view |
| `GET` | `/api/v1/home-numbers/{homeNumber}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/achievements` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/achievements/statistics` | يتطلب Token | achievements.view |
| `GET` | `/api/v1/achievements/{achievement}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/blog-seo` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/blog-seo/statistics` | يتطلب Token | blog_seo.view |
| `GET` | `/api/v1/blog-seo/{blogSeo}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/homepage-content` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/homepage/hero` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/homepage/content` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/hero` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/stats` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/programs` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/trust` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/why-us` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/values` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/story` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/about-us/vision-2030` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/axes` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/axes/{slug}` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/homepage/carousel` | مفتوح | حسب صلاحية المجموعة |
| `POST` | `/api/v1/register` | مفتوح | حسب صلاحية المجموعة |
| `POST` | `/api/v1/login` | مفتوح | حسب صلاحية المجموعة |
| `GET` | `/api/v1/profile` | يتطلب Token | حسب صلاحية المجموعة |
| `POST` | `/api/v1/logout` | يتطلب Token | حسب صلاحية المجموعة |
| `POST` | `/api/v1/change-password` | يتطلب Token | حسب صلاحية المجموعة |
| `GET` | `/api/v1/dashboard` | يتطلب Token | dashboard.view |
| `GET` | `/api/v1/settings` | يتطلب Token | settings.view |
| `POST` | `/api/v1/settings` | يتطلب Token | settings.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/settings/{setting}` | يتطلب Token | settings.view |
| `PUT` | `/api/v1/settings/{setting}` | يتطلب Token | settings.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/settings/{setting}` | يتطلب Token | settings.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/settings/{setting}` | يتطلب Token | settings.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/faq-categories` | يتطلب Token | faq_categories.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/faq-categories/{faqCategory}` | يتطلب Token | faq_categories.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/faq-categories/{faqCategory}` | يتطلب Token | faq_categories.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/faq-categories/{faqCategory}` | يتطلب Token | faq_categories.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/faqs` | يتطلب Token | faqs.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/faqs/{faq}` | يتطلب Token | faqs.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/faqs/{faq}` | يتطلب Token | faqs.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/faqs/{faq}` | يتطلب Token | faqs.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/donation-campaigns` | يتطلب Token | donation_campaigns.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/donation-campaigns/{donationCampaign}` | يتطلب Token | donation_campaigns.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/donation-campaigns/{donationCampaign}` | يتطلب Token | donation_campaigns.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/donation-campaigns/{donationCampaign}` | يتطلب Token | donation_campaigns.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/donation-campaign-categories` | يتطلب Token | donation_campaign_categories.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/donation-campaign-categories/{donationCampaignCategory}` | يتطلب Token | donation_campaign_categories.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/donation-campaign-categories/{donationCampaignCategory}` | يتطلب Token | donation_campaign_categories.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/donation-campaign-categories/{donationCampaignCategory}` | يتطلب Token | donation_campaign_categories.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/service-categories` | يتطلب Token | service_categories.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/service-categories/{serviceCategory}` | يتطلب Token | service_categories.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/service-categories/{serviceCategory}` | يتطلب Token | service_categories.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/service-categories/{serviceCategory}` | يتطلب Token | service_categories.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/services` | يتطلب Token | services.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/services/{service}` | يتطلب Token | services.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/services/{service}` | يتطلب Token | services.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/services/{service}` | يتطلب Token | services.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/cases` | يتطلب Token | cases.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/cases/{case}` | يتطلب Token | cases.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/cases/{case}` | يتطلب Token | cases.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/cases/{case}` | يتطلب Token | cases.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/case-categories` | يتطلب Token | case_categories.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/case-categories/{caseCategory}` | يتطلب Token | case_categories.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/case-categories/{caseCategory}` | يتطلب Token | case_categories.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/case-categories/{caseCategory}` | يتطلب Token | case_categories.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/programs` | يتطلب Token | programs.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/programs/{program}` | يتطلب Token | programs.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/programs/{program}` | يتطلب Token | programs.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/programs/{program}` | يتطلب Token | programs.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/programs/{program}/donation-packages` | يتطلب Token | donation_packages.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/programs/{program}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/programs/{program}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/programs/{program}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/projects` | يتطلب Token | projects.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/projects/{project}` | يتطلب Token | projects.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/projects/{project}` | يتطلب Token | projects.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/projects/{project}` | يتطلب Token | projects.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/projects/{project}/donation-packages` | يتطلب Token | donation_packages.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/projects/{project}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/projects/{project}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/projects/{project}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/donation-campaigns/{donationCampaign}/donation-packages` | يتطلب Token | donation_packages.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/donation-campaigns/{donationCampaign}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/donation-campaigns/{donationCampaign}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/donation-campaigns/{donationCampaign}/donation-packages/{donationPackage}` | يتطلب Token | donation_packages.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/users` | يتطلب Token | users.view |
| `POST` | `/api/v1/users` | يتطلب Token | users.create |
| `GET` | `/api/v1/users/{user}` | يتطلب Token | users.view |
| `PUT` | `/api/v1/users/{user}` | يتطلب Token | users.update |
| `PATCH` | `/api/v1/users/{user}` | يتطلب Token | users.update |
| `DELETE` | `/api/v1/users/{user}` | يتطلب Token | users.delete |
| `POST` | `/api/v1/users/super-admin` | يتطلب Token | users.create |
| `GET` | `/api/v1/roles` | يتطلب Token | roles.view |
| `POST` | `/api/v1/roles` | يتطلب Token | roles.create |
| `GET` | `/api/v1/roles/{role}` | يتطلب Token | roles.view |
| `PUT` | `/api/v1/roles/{role}` | يتطلب Token | roles.update |
| `PATCH` | `/api/v1/roles/{role}` | يتطلب Token | roles.update |
| `DELETE` | `/api/v1/roles/{role}` | يتطلب Token | roles.delete |
| `GET` | `/api/v1/permissions` | يتطلب Token | permissions.view |
| `POST` | `/api/v1/permissions` | يتطلب Token | permissions.create |
| `GET` | `/api/v1/permissions/{permission}` | يتطلب Token | permissions.view |
| `PUT` | `/api/v1/permissions/{permission}` | يتطلب Token | permissions.update |
| `PATCH` | `/api/v1/permissions/{permission}` | يتطلب Token | permissions.update |
| `DELETE` | `/api/v1/permissions/{permission}` | يتطلب Token | permissions.delete |
| `GET` | `/api/v1/donors` | يتطلب Token | donors.view |
| `POST` | `/api/v1/donors` | يتطلب Token | donors.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/donors/{donor}` | يتطلب Token | donors.view |
| `PUT` | `/api/v1/donors/{donor}` | يتطلب Token | donors.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/donors/{donor}` | يتطلب Token | donors.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/donors/{donor}` | يتطلب Token | donors.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/donations` | يتطلب Token | donations.view |
| `POST` | `/api/v1/donations` | يتطلب Token | donations.create |
| `GET` | `/api/v1/donations/{donation}` | يتطلب Token | donations.view |
| `PUT` | `/api/v1/donations/{donation}` | يتطلب Token | donations.update |
| `PATCH` | `/api/v1/donations/{donation}` | يتطلب Token | donations.update |
| `DELETE` | `/api/v1/donations/{donation}` | يتطلب Token | donations.delete |
| `GET` | `/api/v1/payment-methods` | يتطلب Token | payment_methods.view |
| `POST` | `/api/v1/payment-methods` | يتطلب Token | payment_methods.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/payment-methods/{paymentMethod}` | يتطلب Token | payment_methods.view |
| `PUT` | `/api/v1/payment-methods/{paymentMethod}` | يتطلب Token | payment_methods.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/payment-methods/{paymentMethod}` | يتطلب Token | payment_methods.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/payment-methods/{paymentMethod}` | يتطلب Token | payment_methods.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/payment-gateways` | يتطلب Token | payment_methods.view |
| `GET` | `/api/v1/payment-gateways/{gateway}` | يتطلب Token | payment_methods.view |
| `PUT` | `/api/v1/payment-gateways/{gateway}` | يتطلب Token | payment_methods.update |
| `PATCH` | `/api/v1/payment-gateways/{gateway}` | يتطلب Token | payment_methods.update |
| `PATCH` | `/api/v1/payment-gateways/{gateway}/toggle` | يتطلب Token | payment_methods.update |
| `POST` | `/api/v1/payment-gateways/{gateway}/test` | يتطلب Token | payment_methods.view |
| `GET` | `/api/v1/activity-logs/statistics` | يتطلب Token | reports.view |
| `GET` | `/api/v1/activity-logs` | يتطلب Token | reports.view |
| `GET` | `/api/v1/volunteers/statistics` | يتطلب Token | volunteers.view |
| `GET` | `/api/v1/volunteers` | يتطلب Token | volunteers.view |
| `POST` | `/api/v1/volunteers` | يتطلب Token | volunteers.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/volunteers/{volunteer}` | يتطلب Token | volunteers.view |
| `PUT` | `/api/v1/volunteers/{volunteer}` | يتطلب Token | volunteers.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/volunteers/{volunteer}` | يتطلب Token | volunteers.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/volunteers/{volunteer}` | يتطلب Token | volunteers.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/volunteer-campaigns/{volunteer_campaign}/applications` | يتطلب Token | volunteer_applications.view |
| `POST` | `/api/v1/volunteer-campaigns/{volunteer_campaign}/invite` | يتطلب Token | volunteer_applications.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/volunteer-campaigns/{volunteer_campaign}/applications/{application}` | يتطلب Token | volunteer_applications.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/volunteer-campaigns/{volunteer_campaign}/applications/{application}` | يتطلب Token | volunteer_applications.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/volunteer-campaigns` | يتطلب Token | volunteer_campaigns.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/volunteer-campaigns/{volunteer_campaign}` | يتطلب Token | volunteer_campaigns.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/volunteer-campaigns/{volunteer_campaign}` | يتطلب Token | volunteer_campaigns.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/volunteer-campaigns/{volunteer_campaign}` | يتطلب Token | volunteer_campaigns.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/notifications` | يتطلب Token | حسب صلاحية المجموعة |
| `GET` | `/api/v1/notifications/unread-count` | يتطلب Token | حسب صلاحية المجموعة |
| `PATCH` | `/api/v1/notifications/{id}/read` | يتطلب Token | حسب صلاحية المجموعة |
| `PATCH` | `/api/v1/notifications/read-all` | يتطلب Token | حسب صلاحية المجموعة |
| `GET` | `/api/v1/content-change-requests` | يتطلب Token | content_change_requests.view |
| `GET` | `/api/v1/content-change-requests/{contentChangeRequest}` | يتطلب Token | content_change_requests.view |
| `POST` | `/api/v1/content-change-requests/{contentChangeRequest}/approve` | يتطلب Token | content_change_requests.approve |
| `POST` | `/api/v1/content-change-requests/{contentChangeRequest}/reject` | يتطلب Token | content_change_requests.reject |
| `GET` | `/api/v1/content/statistics` | يتطلب Token | content.view |
| `POST` | `/api/v1/content` | يتطلب Token | content.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/content/{content}` | يتطلب Token | content.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/content/{content}` | يتطلب Token | content.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/content/{content}` | يتطلب Token | content.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/admin/content/statistics` | يتطلب Token | content.view |
| `GET` | `/api/v1/admin/content` | يتطلب Token | content.view |
| `POST` | `/api/v1/admin/content` | يتطلب Token | content.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/admin/content/{content}` | يتطلب Token | content.view |
| `PUT` | `/api/v1/admin/content/{content}` | يتطلب Token | content.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/admin/content/{content}` | يتطلب Token | content.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/admin/content/{content}` | يتطلب Token | content.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/gallery` | يتطلب Token | gallery.create |
| `PUT` | `/api/v1/gallery/{gallery}` | يتطلب Token | gallery.update |
| `PATCH` | `/api/v1/gallery/{gallery}` | يتطلب Token | gallery.update |
| `DELETE` | `/api/v1/gallery/{gallery}` | يتطلب Token | gallery.delete |
| `POST` | `/api/v1/stories` | يتطلب Token | stories.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/stories/{story}` | يتطلب Token | stories.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/stories/{story}` | يتطلب Token | stories.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/stories/{story}` | يتطلب Token | stories.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/home-numbers` | يتطلب Token | home_numbers.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/home-numbers/{homeNumber}` | يتطلب Token | home_numbers.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/home-numbers/{homeNumber}` | يتطلب Token | home_numbers.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/home-numbers/{homeNumber}` | يتطلب Token | home_numbers.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/achievements` | يتطلب Token | achievements.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/achievements/{achievement}` | يتطلب Token | achievements.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/achievements/{achievement}` | يتطلب Token | achievements.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/achievements/{achievement}` | يتطلب Token | achievements.delete؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/blog-seo` | يتطلب Token | blog_seo.create؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/blog-seo/{blogSeo}` | يتطلب Token | blog_seo.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/blog-seo/{blogSeo}` | يتطلب Token | blog_seo.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/blog-seo/{blogSeo}` | يتطلب Token | blog_seo.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/newsletter/statistics` | يتطلب Token | newsletter.view |
| `GET` | `/api/v1/newsletter` | يتطلب Token | newsletter.view |
| `POST` | `/api/v1/newsletter` | يتطلب Token | newsletter.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/newsletter/{newsletter_subscriber}` | يتطلب Token | newsletter.view |
| `PUT` | `/api/v1/newsletter/{newsletter_subscriber}` | يتطلب Token | newsletter.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/newsletter/{newsletter_subscriber}` | يتطلب Token | newsletter.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/newsletter/{newsletter_subscriber}` | يتطلب Token | newsletter.delete؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/homepage-content` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/homepage-content` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/homepage/hero` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/homepage/hero` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/homepage/hero` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/homepage/content` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `POST` | `/api/v1/homepage/content` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/homepage/content` | يتطلب Token | homepage.update؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/admin/about-us` | يتطلب Token | about_us.view |
| `PUT` | `/api/v1/admin/about-us` | يتطلب Token | about_us.update؛ قد يتطلب موافقة المحرر |
| `PUT` | `/api/v1/admin/about-us/{$section}` | يتطلب Token | about_us.update؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/admin/axes` | يتطلب Token | axes.view |
| `POST` | `/api/v1/admin/axes` | يتطلب Token | axes.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/admin/axes/{axis}` | يتطلب Token | axes.view |
| `PUT` | `/api/v1/admin/axes/{axis}` | يتطلب Token | axes.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/admin/axes/{axis}` | يتطلب Token | axes.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/admin/axes/{axis}` | يتطلب Token | axes.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/seasons` | يتطلب Token | seasons.view |
| `POST` | `/api/v1/seasons` | يتطلب Token | seasons.create؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/seasons/{season}` | يتطلب Token | seasons.view |
| `PUT` | `/api/v1/seasons/{season}` | يتطلب Token | seasons.update؛ قد يتطلب موافقة المحرر |
| `PATCH` | `/api/v1/seasons/{season}` | يتطلب Token | seasons.update؛ قد يتطلب موافقة المحرر |
| `DELETE` | `/api/v1/seasons/{season}` | يتطلب Token | seasons.delete؛ قد يتطلب موافقة المحرر |
| `GET` | `/api/v1/admin/homepage/carousel` | يتطلب Token | homepage.view |
| `PUT` | `/api/v1/admin/homepage/carousel/settings` | يتطلب Token | homepage.update |
| `GET` | `/api/v1/admin/homepage/carousel/items` | يتطلب Token | homepage.view |
| `POST` | `/api/v1/admin/homepage/carousel/items` | يتطلب Token | homepage.update |
| `GET` | `/api/v1/admin/homepage/carousel/items/{item}` | يتطلب Token | homepage.view |
| `PUT` | `/api/v1/admin/homepage/carousel/items/{item}` | يتطلب Token | homepage.update |
| `PATCH` | `/api/v1/admin/homepage/carousel/items/{item}` | يتطلب Token | homepage.update |
| `DELETE` | `/api/v1/admin/homepage/carousel/items/{item}` | يتطلب Token | homepage.update |
| `PATCH` | `/api/v1/admin/homepage/carousel/items/{item}/toggle` | يتطلب Token | homepage.update |
| `POST` | `/api/v1/admin/homepage/carousel/reorder` | يتطلب Token | homepage.update |

## 6. Frontend implementation checklist

1. عرّف `API_BASE_URL` مرة واحدة: `https://api.elyusr.org/api/v1`.
2. أنشئ API client موحدًا يضيف `Accept: application/json` وBearer Token عند الحاجة.
3. استخدم `FormData` للحقول التي تتضمن ملفات، ولا تضبط `Content-Type` يدويًا مع `FormData` في المتصفح حتى يضيف الـ boundary.
4. اعرض حالات loading / empty / error في كل صفحة.
5. للصفحات الإدارية، أخفِ الأزرار حسب الصلاحيات التي يرجعها نظام المستخدم، لكن لا تعتمد على إخفاء الواجهة كحماية؛ الخادم هو مصدر الصلاحيات النهائي.
6. في التبرعات، اعرض حالة `pending` بوضوح، ثم حدّث الحالة من endpoint التتبع أو نتيجة الدفع الموثوقة.
7. لا تضع بيانات `.env` أو مفاتيح Paymob/Kashier السرية في كود المتصفح أو مستودع عام.

## 7. Files used to build this documentation

- `routes/api.php` — المسارات والـ middleware.
- `app/Http/Controllers/Api/V1/Auth/AuthController.php` — التسجيل والدخول والملف الشخصي.
- `app/Http/Controllers/Api/V1/Donation/DonationController.php` — بدء التبرع والدفع والتتبع.
- `app/Http/Controllers/Api/V1/PaymentMethod/PaymentMethodController.php` — وسائل الدفع العامة.
- `app/Http/Requests/Auth/RegisterRequest.php` و`LoginRequest.php` — قواعد تحقق الدخول والتسجيل.

