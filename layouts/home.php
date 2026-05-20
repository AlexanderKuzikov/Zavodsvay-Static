<?php
/**
 * Layout для главной страницы
 */
if (!isset($title))            $title            = '';
if (!isset($meta_description)) $meta_description = '';
if (!isset($canonical))        $canonical        = '';
if (!isset($content))          $content          = '';
// SEO page-level опционалы — дефолты внутри head-seo.php
// $og_image, $og_type, $schema_type, $article_published, $article_modified
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/assets/css/template.css">
    <link rel="stylesheet" href="/assets/css/home-d.css">
    <?php if (!empty($canonical)): ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
    <?php endif; ?>
    <?php if (!empty($meta_description)): ?>
    <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <?php endif; ?>
    <?php include __DIR__ . '/../partials/head-favicon.php'; ?>
    <?php include __DIR__ . '/../partials/head-seo.php'; ?>
</head>
<body>

<?php include __DIR__ . '/../partials/components/icons-svg.php'; ?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="main-layout-container">
    <?php include __DIR__ . '/../partials/sidebar.php'; ?>
    <main class="content-area hd-page">
        <?= $content ?>
    </main>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<?php include __DIR__ . '/../partials/back-to-top.php'; ?>

<script src="/assets/js/template.js"></script>
</body>
</html>
