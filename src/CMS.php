<?php
/**
 * Reagordi CMS
 *
 * @package reagordi
 * @author Sergej Rufov <support@freeun.ru>
 */

namespace Reagordi\CMS;

use Reagordi\Framework\Web\View;
use Reagordi;

class CMS
{
    private static $obj = null;

    public static function getInstance()
    {
        if (self::$obj === null) {
            self::$obj = new CMS();
        }
        return self::$obj;
    }

    private function __construct()
    {

    }

    public function getHead()
    {
        $content = '';
        if (!isset(Reagordi::$app->context->document['title']))
            Reagordi::$app->context->document['title'] = '';
        if (!isset(Reagordi::$app->context->document['robots']))
            Reagordi::$app->context->document['robots'] = 'index,follow';
        $content .= '<meta charset="utf-8" />' . "\n";
        $content .= '<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, width=device-width" />' . "\n";
        if (isset(Reagordi::$app->context->document['description']))
            $content .= '<meta name="description" content="' . trim(strip_tags(Reagordi::$app->context->document['description'])) . '" />' . "\n";

        // Meta google
        $content .= '<meta itemprop="name" content="' . Reagordi::$app->context->document['title'] . '"/>' . "\n";
        if (isset(Reagordi::$app->context->document['description']))
            $content .= '<meta itemprop="description" content="' . trim(strip_tags(Reagordi::$app->context->document['description'])) . '"/>' . "\n";
        if (isset(Reagordi::$app->context->document['seo_image']))
            $content .= '<meta itemprop="image" content="' . Reagordi::$app->context->document['seo_image'] . '"/>' . "\n";

        // Twitter meta
        //$content .= '<meta name="twitter:site" content="' . Config::getInstance()->get( 'site_name' ) . '"/>'."\n";
        $content .= '<meta name="twitter:title" content="' . Reagordi::$app->context->document['title'] . '"/>' . "\n";
        if (isset(Reagordi::$app->context->document['description']))
            $content .= '<meta name="twitter:description" content="' . Reagordi::$app->context->document['description'] . '"/>' . "\n";
        if (isset(Reagordi::$app->context->document['seo_image']))
            $content .= '<meta name="twitter:image:src" content="' . Reagordi::$app->context->document['seo_image'] . '"/>' . "\n";
        $content .= '<meta name="twitter:domain" content="' . Reagordi::$app->context->server->getHttpHost() . '"/>' . "\n";

        // Meta Og
        //$content .= '<meta property="og:site_name" content="' . Reagordi::$app->config->get( 'site_name' ) . '"/>'."\n";
        $content .= '<meta property="og:title" content="' . Reagordi::$app->context->document['title'] . '" />' . "\n";
        if (isset(Reagordi::$app->context->document['description']))
            $content .= '<meta property="og:description" content="' . trim(strip_tags(Reagordi::$app->context->document['description'])) . '" />' . "\n";
        if (isset(Reagordi::$app->context->document['seo_image']))
            $content .= '<meta property="og:image" content="' . Reagordi::$app->context->document['seo_image'] . '"/>' . "\n";

        // Robots
        if (isset(Reagordi::$app->context->document['robots']) && Reagordi::$app->context->document['robots'] != 'index,follow')
            $content .= '<meta name="robots" content="' . Reagordi::$app->context->document['robots'] . '" />' . "\n";

        // Author info
        //if ( Config::getInstance()->get( 'show_author' ) ) {
        //    $content .= '<meta name="generator" content="Reagordi Framework" />'."\n";
        //    $content .= '<meta name="author" content="Reagordi Framework" />'."\n";
        //    $content .= '<meta name="copyright" content="Reagordi Framework (c) '.date('Y').'" />'."\n";
        //    $content .= '<meta http-equiv="reply-to" content="support@reagordi.com" />'."\n";
        //}
        $content .= '<title>' . Reagordi::$app->context->document['title'] . '</title>' . "\n";
        $content .= '<!--[Reagordi Style]-->';
        $lang = LANGUAGE_ID;
        $api_url = HOME_URL . '/' . Reagordi::$app->options->get('url', 'api_path');
        $sid = isset($_COOKIE[RG_COOKIE_SID]) ? $_COOKIE[RG_COOKIE_SID] : Reagordi::$app->context->session->sid;
        $content .= <<<_HTML
<script>
var reagordi = {
    lang: '{$lang}',
    sid: '{$sid}',
    api_url: '{$api_url}'
};
</script>
_HTML;

        return $content;
    }
}
