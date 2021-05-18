id: 20
name: Sentry
properties: 'a:0:{}'
disabled: 1

-----

switch ($modx->event->name) {
    case 'OnMODXInit':
        $file = MODX_CORE_PATH.'components/sentry/vendor/autoload.php';
        if (file_exists($file)) {
            require_once $file;
        }
        break;
    case 'OnWebPageInit':
    case 'OnManagerPageInit':
        try {
            Sentry\init([
                'dsn' => 'https://@sentry.stik.pro/14',
                'error_types' => E_ERROR & ~E_WARNING & ~E_PARSE
            ]);
        } catch (Exception $e) {
            $modx->log(MODX_LOG_LEVEL_ERROR, 'Sentry Exception: ',  $e->getMessage(), "\n");
        }
        break;
}