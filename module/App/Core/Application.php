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
        return $this->databasePath ?: $this->basePath.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'database';
    }

    /**
     * Get the path to the application "app" directory.
     *
     * @return string
     */
    public function path()
    {
        return $this->basePath.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Core';
    }
}