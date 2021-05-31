<?php
/**
 * Reagordi Framework
 *
 * @package reagordi
 * @author Sergej Rufov <support@freeun.ru>
 */

namespace Reagordi\Framework\Models;

use Phpfastcache\CacheManager;
use RedBeanPHP\SimpleModel;
use RedBeanPHP\R;

class Sites extends SimpleModel
{
    public function getSite( $host )
    {
        $cached_string = Reagordi::$app->context->cache->getItem('sites_' . $host );
        if ( is_null( $cached_string->get() ) ) {
            $site = R::findOne( DB_GLOBAL_PREF . 'sites', 'host = :host', [':host' => $host] );
            if ( $site->id ) {
                $number_of_seconds = 60; // The cached entry doesn't exist
                $cached_string->set( $site )->expiresAfter( $number_of_seconds );
                Reagordi::$app->context->cache->save( $cached_string );
            }
        } else {
            $site = $cached_string->get();
        }
        return $site;
    }
}
