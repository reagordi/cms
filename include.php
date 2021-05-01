<?php
/**
 * Reagordi Framework
 *
 * @package reagordi
 * @author Sergej Rufov <support@freeun.ru>
 */

if (Reagordi::$app->config->get('site_online')) {

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

    $collector->any(Reagordi::$app->options->get('url', 'auth_path'), function () {
        Reagordi::$app->context->setTitle(Reagordi::$app->config->get('site_name') . t(' - Login to the site'));
        Reagordi::$app->context->setDescription(Reagordi::$app->config->get('site_name') . t(' - Login to the site'));

        Reagordi::$app->context->view->layout = 'auth';

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
}
