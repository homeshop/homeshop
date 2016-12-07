<?php
/**
 * Created by IntelliJ IDEA.
 * User: mo
 * Date: 16-9-25
 * Time: 下午1:14
 */

namespace App\Core;

use Illuminate\Foundation\Application as App;


class Application extends App
{
    /**
     * Get the path to the database directory.
     *
     * @return string
     */
    public function databasePath()
    {
        return $this->databasePath ?: realpath($this->basePath.'/resources/database') ;
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @return string
     */
    public function path()
    {
        return realpath($this->basePath.'/module/App/Core') ;
    }
}