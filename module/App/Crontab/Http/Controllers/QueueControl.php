<?php namespace App\Crontab\Http\Controllers;



/**
 * 队列
 * 任务计划执行，执行频率5分钟
 */
ini_set('default_socket_timeout', -1);

class  QueueControl extends BaseCronControl {
    
    public function __construct() { }
    
    public function indexOp() {
        if(!C('queue.open')){
            return;
        }
        $timer = microtime(true);
        $logic_queue = Logic('queue');
        $model = Model();
        $worker = new QueueServer();
        $queues = $worker->scan();
        while(true) {
            $content = $worker->pop($queues, $keeptimer ? $keeptimer : 290);
            //          echo ceil(microtime(TRUE)-$timer),PHP_EOL;ob_flush();
            if(is_array($content)){
                $method = key($content);
                $arg = current($content);
                $result = $logic_queue->$method($arg);
                if(!$result['state']){
                    $this->log($result['msg'], false);
                }
                //				echo $method,PHP_EOL;ob_flush();
            }
            $keeptimer = 300 - intval(ceil(microtime(true) - $timer));
            //			echo var_dump($keeptimer),PHP_EOL;ob_flush();
            //			echo 'real timer: '.ceil(microtime(TRUE) - $timer),PHP_EOL;ob_flush();
            if($keeptimer <= 10){
                break;
            }
        }
    }
}
