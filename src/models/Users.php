<?php
/**
 * Reagordi CMS
 *
 * @package reagordi
 * @author Sergej Rufov <support@freeun.ru>
 */

namespace Reagordi\CMS\Models;

use RedBeanPHP\R;
use RedBeanPHP\SimpleModel;

class Users  extends SimpleModel
{
    public static function isUserAuth($login, $password)
    {
        $data = R::findOne(DB_GLOBAL_PREF . 'users', '`login` = ?', [$login]);

        if ($data && \Reagordi::$app->security->validatePassword($password, $data->password)) return $data;

        return false;
    }
}
