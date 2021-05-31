<?php
/**
 * Reagordi Framework
 *
 * @package reagordi
 * @author Sergej Rufov <support@freeun.ru>
 */

/**
 * Разрешает мультисайтовость
 *
 * @var bool
 */
defined('RG_ALLOW_MULTISITE') or define('RG__ALLOW_MULTISITE', false);

\Reagordi\Framework\Loader::registerAutoLoadClasses(
    'reagordi:framework',
    array(
        'Reagordi\\CMS\\Models\\Users' => __DIR__ . '/src/models/Users.php',
    )
);

if (RG_ALLOW_MULTISITE === false) define( 'DB_PREF', DB_GLOBAL_PREF . '1_' );
else define( 'DB_PREF', DB_GLOBAL_PREF . Reagordi::$app->config->get( 'id' ) );

$path = str_replace( ROOT_DIR . '/', '', APP_DIR );
define( 'TEMPLATE_URL', str_replace(ROOT_DIR, '', APP_DIR) . '/templates/' );

Reagordi::$app->context->view->setTemplateDir(__DIR__ . '/templates');

if (is_file(APP_DIR . '/php_interface/init/' . Reagordi::$app->context->server->getHttpHost() . '.php'))
    require_once APP_DIR . '/php_interface/init/' . Reagordi::$app->context->server->getHttpHost() . '.php';

// if (Reagordi::$app->config->get('site_online'))

    $collector->group(array('prefix' => Reagordi::$app->options->get('url', 'api_path')), function (\Phroute\Phroute\RouteCollector $collector) {
        if (strpos(Reagordi::$app->context->server->getRequestUri(), '/' . Reagordi::$app->options->get('url', 'api_path')) !== false) {
            defined('RESPONSE_API') or define('RESPONSE_API', true);
        }

        if (is_dir(VENDOR_DIR . '/reagordi/cms/endpoints/')) {
            $it = new RecursiveDirectoryIterator(VENDOR_DIR . '/reagordi/cms/endpoints/');
            foreach (new RecursiveIteratorIterator($it) as $endpoint) {
                if ($endpoint->getExtension() == 'php') {
                    include_once $endpoint;
                }
            }
        }

        foreach (Reagordi::$app->config->get('modules') as $model) {
            if (is_dir(APP_DIR . '/modules/' . $model . '/endpoints/')) {
                $it = new RecursiveDirectoryIterator(APP_DIR . '/modules/' . $model . '/endpoints/');
                foreach (new RecursiveIteratorIterator($it) as $endpoint) {
                    if ($endpoint->getExtension() == 'php') {
                        include_once $endpoint;
                    }
                }
            }
        }
    });

    $collector->group(array('prefix' => Reagordi::$app->options->get('url', 'admin_path')), function (\Phroute\Phroute\RouteCollector $collector) {
        if (strpos(Reagordi::$app->context->server->getRequestUri(), '/' . Reagordi::$app->options->get('url', 'admin_path')) !== false) {
            defined('RESPONSE_ADMIN') or define('RESPONSE_ADMIN', true);
            define('TEMPLATE_URL', str_replace(ROOT_DIR, '', APP_DIR) . '/templates/' . Reagordi::$app->config->get('theme', 'admin'));
            Reagordi::$app->context->view->setTemplateDir(APP_DIR . '/templates/' . Reagordi::$app->config->get('theme', 'admin') . '/');
        }

        if (is_dir(VENDOR_DIR . '/reagordi/cms/admin/')) {
            $it = new RecursiveDirectoryIterator(VENDOR_DIR . '/reagordi/cms/admin/');
            foreach (new RecursiveIteratorIterator($it) as $endpoint) {
                if ($endpoint->getExtension() == 'php') {
                    include_once $endpoint;
                }
            }
        }

        foreach (Reagordi::$app->config->get('modules') as $model) {
            if (is_dir(APP_DIR . '/modules/' . $model . '/admin/')) {
                $it = new RecursiveDirectoryIterator(APP_DIR . '/modules/' . $model . '/admin/');
                foreach (new RecursiveIteratorIterator($it) as $endpoint) {
                    if ($endpoint->getExtension() == 'php') {
                        include_once $endpoint;
                    }
                }
            }
        }
    });

    $collector->any(Reagordi::$app->options->get('components', 'user', 'loginUrl'), function () {
        Reagordi::$app->context->setTitle(Reagordi::$app->config->get('site_name') . ' - ' . t('Login to the site'));
        Reagordi::$app->context->setDescription(Reagordi::$app->config->get('site_name') . ' - ' . t('Login to the site'));

        Reagordi::$app->context->view->setTemplateDir(__DIR__ . '/template');

        Reagordi::$app->context->view->layout = 'auth';

        $error = false;
        $url = HOME_URL . '/' . Reagordi::$app->options->get('components', 'user', 'loginUrl');

        if (Reagordi::$app->context->request->get('act'))
            $url = add_query_arg('act', urlencode(Reagordi::$app->context->request->get('act')), $url);
        else
            $url = add_query_arg('act', 'login', $url);

        if (Reagordi::$app->context->request->get('redirect_to'))
            $url = add_query_arg('redirect_to', urlencode(Reagordi::$app->context->request->get('redirect_to')), $url);

        $request = Reagordi::$app->context->request;

        switch ($request->getPost('act')) {
            case 'login':
                if ($request->getPost('login') && $request->getPost('password')) {
                    if (mb_strlen($request->getPost('login')) <= 4) $error = true;

                    if (mb_strlen($request->getPost('password')) <= 7) $error = true;

                    if (!$error) {
                        Reagordi::$app->applicaiton->dbInit();
                        $user = Reagordi\CMS\Models\Users::isUserAuth($request->getPost('login'), $request->getPost('password'));
                        echo '<pre>';
                        print_r($user);
                        exit;
                    }
                }
                break;
            case 'restore':
                break;
            case 'reg':
                break;
        }

        Reagordi::$app->context->view->assign('error', $error);
        Reagordi::$app->context->view->assign('url', $url);

        return Reagordi::$app->context->view->fech();
    });

    if (strpos(Reagordi::$app->context->server->getRequestUri(), '/' . Reagordi::$app->options->get('url', 'admin_path')) === false) {
        $path = str_replace(ROOT_DIR . '/', '', APP_DIR);
        define('TEMPLATE_URL', str_replace(ROOT_DIR, '', APP_DIR) . '/templates/' . Reagordi::$app->config->get('theme', 'site'));
        Reagordi::$app->context->view->setTemplateDir(APP_DIR . '/templates/' . Reagordi::$app->config->get('theme', 'site'));
    }

    $dir_path = APP_DIR . '/pages/';
    if (RG_ALLOW_MULTISITE) $dir_path = APP_DIR . '/pages/' . Reagordi::$app->context->server->getHttpHost();
    $it = new RecursiveDirectoryIterator($dir_path);
    unset($dir_path);

    foreach (new RecursiveIteratorIterator($it) as $endpoint) {
        if ($endpoint->getExtension() == 'php') {
            include_once $endpoint;
        }
    }
