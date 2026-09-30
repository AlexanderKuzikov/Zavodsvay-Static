<?php
/**
 * head-seo.php — SEO-мета, Open Graph, Twitter Cards, JSON-LD Schema.org, geo-теги
 *
 * Подключается во все layouts ПОСЛЕ head-favicon.php.
 * Page-level переменные (опциональные, задаются в pages/{page}/index.php):
 *   $og_image    — абс. URL картинки (дефолт: og-home.jpg)
 *   $og_type     — 'website' | 'article' (дефолт: 'website')
 *   $schema_type — 'LocalBusiness' | 'Article' | 'FAQPage' (дефолт: 'LocalBusiness')
 *   $article_published — ISO date, только для og_type=article
 *   $article_modified  — ISO date, только для og_type=article
 */

// ─── Глобальные данные компании (SSOT) ───────────────────────────────────────
$_site_url      = 'https://zavodsvay.ru';
$_company_name  = 'Завод винтовых свай «Гефест»';
$_legal_name    = 'ООО «Завод Винтовых Свай «Гефест»»';
$_phone         = '+73422099800';
$_email         = 'info@zavodsvay.ru';
$_address       = 'ул. Монастырская, 14, офис 502, Пермь, 614000';
$_city          = 'Пермь';
$_region        = 'RU-PER';
$_postal        = '614000';
$_lat           = '58.014746';
$_lng           = '56.228500';
$_vk            = 'https://vk.com/club236711949';
$_max           = 'https://max.ru/id5902229415_bot';
$_founded       = '2012';
// ─────────────────────────────────────────────────────────────────────────────

// Page-level дефолты
if (!isset($og_image))    $og_image    = $_site_url . '/assets/img/og/og-home.jpg';
if (!isset($og_type))     $og_type     = 'website';
if (!isset($schema_type)) $schema_type = 'LocalBusiness';

/**
 * Экранирование значения для вставки в JSON-LD.
 * Собирать JSON интерполяцией строк нельзя: кавычка в значении рвёт JSON,
 * а «</script>» закрывает тег разметки. HTML-экранирование здесь не помогает —
 * json.loads не понимает &quot;.
 */
if (!function_exists('_j')) {
    function _j($value): string {
        return json_encode((string) $value, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG
            | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}

// Текущий URL
$_current_url = !empty($canonical) ? $canonical : $_site_url . '/';

// og:image должен быть абсолютным
if (strpos($og_image, 'http') !== 0) {
    $og_image = $_site_url . $og_image;
}
?>

    <!-- Open Graph / Facebook / VK / Telegram -->
    <meta property="og:type"        content="<?= htmlspecialchars($og_type) ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($_current_url) ?>">
    <meta property="og:site_name"   content="<?= htmlspecialchars($_company_name) ?>">
    <meta property="og:title"       content="<?= htmlspecialchars($title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($og_image) ?>">
    <meta property="og:image:width"  content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type"   content="image/jpeg">
    <meta property="og:locale"      content="ru_RU">
<?php if ($og_type === 'article'): ?>
    <meta property="article:publisher" content="<?= htmlspecialchars($_vk) ?>">
<?php if (!empty($article_published)): ?>
    <meta property="article:published_time" content="<?= htmlspecialchars($article_published) ?>">
<?php endif; ?>
<?php if (!empty($article_modified)): ?>
    <meta property="article:modified_time" content="<?= htmlspecialchars($article_modified) ?>">
<?php endif; ?>
<?php endif; ?>

    <!-- Twitter Cards -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= htmlspecialchars($title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($meta_description) ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($og_image) ?>">

    <!-- Яндекс geo-теги -->
    <meta name="geo.region"   content="<?= $_region ?>">
    <meta name="geo.placename" content="<?= $_city ?>">
    <meta name="geo.position" content="<?= $_lat ?>;<?= $_lng ?>">
    <meta name="ICBM"         content="<?= $_lat ?>, <?= $_lng ?>">

    <!-- JSON-LD Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "Organization",
                "@id": <?= _j($_site_url . '/#organization') ?>,
                "name": <?= _j($_company_name) ?>,
                "legalName": <?= _j($_legal_name) ?>,
                "url": <?= _j($_site_url) ?>,
                "logo": {
                    "@type": "ImageObject",
                    "url": <?= _j($_site_url . '/assets/img/og/og-home.jpg') ?>
                },
                "telephone": <?= _j($_phone) ?>,
                "email": <?= _j($_email) ?>,
                "foundingDate": <?= _j($_founded) ?>,
                "sameAs": [<?= _j($_vk) ?>, <?= _j($_max) ?>]
            },
            {
                "@type": ["LocalBusiness", "HomeAndConstructionBusiness"],
                "@id": <?= _j($_site_url . '/#localbusiness') ?>,
                "name": <?= _j($_company_name) ?>,
                "url": <?= _j($_site_url) ?>,
                "telephone": <?= _j($_phone) ?>,
                "email": <?= _j($_email) ?>,
                "image": <?= _j($_site_url . '/assets/img/og/og-home.jpg') ?>,
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "ул. Монастырская, 14, офис 502",
                    "addressLocality": <?= _j($_city) ?>,
                    "addressRegion": "Пермский край",
                    "postalCode": <?= _j($_postal) ?>,
                    "addressCountry": "RU"
                },
                "geo": {
                    "@type": "GeoCoordinates",
                    "latitude": <?= _j($_lat) ?>,
                    "longitude": <?= _j($_lng) ?>
                },
                "openingHoursSpecification": [
                    {
                        "@type": "OpeningHoursSpecification",
                        "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
                        "opens": "09:00",
                        "closes": "18:00"
                    }
                ],
                "priceRange": "₽₽",
                "parentOrganization": {"@id": <?= _j($_site_url . '/#organization') ?>}
            },
            {
                "@type": "WebSite",
                "@id": <?= _j($_site_url . '/#website') ?>,
                "url": <?= _j($_site_url) ?>,
                "name": <?= _j($_company_name) ?>,
                "publisher": {"@id": <?= _j($_site_url . '/#organization') ?>},
                "inLanguage": "ru-RU"
            },
            {
                "@type": "WebPage",
                "@id": <?= _j($_current_url . '#webpage') ?>,
                "url": <?= _j($_current_url) ?>,
                "name": <?= _j($title) ?>,
                "description": <?= _j($meta_description) ?>,
                "isPartOf": {"@id": <?= _j($_site_url . '/#website') ?>},
                "about": {"@id": <?= _j($_site_url . '/#localbusiness') ?>},
                "inLanguage": "ru-RU"
            }<?php if ($schema_type === 'Article'): ?>,
            {
                "@type": "Article",
                "@id": <?= _j($_current_url . '#article') ?>,
                "headline": <?= _j($title) ?>,
                "description": <?= _j($meta_description) ?>,
                "url": <?= _j($_current_url) ?>,
                <?php if (!empty($article_published)): ?>"datePublished": <?= _j($article_published) ?>,<?php endif; ?>
                <?php if (!empty($article_modified)): ?>"dateModified": <?= _j($article_modified) ?>,<?php endif; ?>
                "publisher": {"@id": <?= _j($_site_url . '/#organization') ?>},
                "author": {"@id": <?= _j($_site_url . '/#organization') ?>},
                "isPartOf": {"@id": <?= _j($_site_url . '/#website') ?>},
                "inLanguage": "ru-RU"
            }<?php endif; ?>
        ]
    }
    </script>
