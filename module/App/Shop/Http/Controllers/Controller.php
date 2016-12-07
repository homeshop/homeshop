<?php namespace App\Shop\Http\Controllers;
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-29
 * Time: 上午8:56
 */

use Homeshop\Modules\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function route(){
        kd('test');
        define('APP_ID', 'shop');
        define('BASE_PATH', \Module::get('shop'));
        define('APP_SITE_URL', SHOP_SITE_URL);
        define('TPL_NAME', TPL_SHOP_NAME);
        define('SHOP_RESOURCE_SITE_URL', SHOP_SITE_URL . DS . 'resource');
        define('SHOP_TEMPLATES_URL', SHOP_SITE_URL . '/templates/' . TPL_NAME);
        define('BASE_TPL_PATH', __DIR__ . '/Resources/views/' . TPL_NAME);
        Base::run();
    }
}