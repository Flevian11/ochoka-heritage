<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title inertia><?php echo e(config('app.name', 'Ochoka Heritage')); ?></title>
    <meta name="description" content="Ochoka Heritage is a modern community platform for membership, welfare, contributions, governance, elections, meetings, and preserving institutional heritage.">
    <meta name="keywords" content="Ochoka Heritage, community management, community welfare, member contributions, community governance, elections, meetings, membership management, welfare fund, institutional heritage">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Ochoka Heritage">
    <link rel="canonical" href="<?php echo e(url('/')); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo e(config('app.name', 'Ochoka Heritage')); ?>">
    <meta property="og:title" content="Ochoka Heritage | Community, Welfare & Governance">
    <meta property="og:description" content="A modern digital platform connecting membership, welfare, contributions, governance, elections, meetings, and institutional heritage.">
    <meta property="og:url" content="<?php echo e(url('/')); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Ochoka Heritage | Community, Welfare & Governance">
    <meta name="twitter:description" content="A modern digital platform for community membership, welfare, contributions, governance, elections, meetings, and institutional heritage.">
    <meta name="theme-color" content="#ffffff">
    <?php echo app('Illuminate\Foundation\Vite')->reactRefresh(); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.tsx']); ?>
    <?php $__inertiaSsrResponse = app(\Inertia\Ssr\SsrState::class)->setPage($page)->dispatch();  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>
    <script type="application/ld+json">
        <?php echo json_encode([
            '<?php $__contextArgs = [];
if (context()->has($__contextArgs[0])) :
if (isset($value)) { $__contextPrevious[] = $value; }
$value = context()->get($__contextArgs[0]); ?>' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name', 'Ochoka Heritage'),
            'url' => url('/'),
            'description' => 'A modern community platform for membership, welfare, contributions, governance, elections, meetings, and institutional heritage.',
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>

    </script>
</head>
<body class="bg-white antialiased text-slate-900">
    <?php $__inertiaSsrResponse = app(\Inertia\Ssr\SsrState::class)->setPage($page)->dispatch();  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } else { ?><script data-page="app" type="application/json"><?php echo json_encode($page, JSON_HEX_TAG); ?></script><div id="app"></div><?php } ?>
</body>
</html>
<?php /**PATH D:\ochoka-heritage\resources\views/app.blade.php ENDPATH**/ ?>