<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2020/4/15 0015
 * Time: 10:44
 */
namespace App\Process;
use EasySwoole\Component\Process\AbstractProcess;
use Swoole\Process;

class TestProcess extends AbstractProcess {
    protected function run($arg)
    {
        //当进程启动后，会执行的回调
        var_dump($this->getProcessName()." run");
        var_dump($arg);
        // TODO: Implement run() method.
    }

    protected function onPipeReadable(Process $process)
    {
        /*
         * 该回调可选
         * 当有主进程对子进程发送消息的时候，会触发的回调，触发后，务必使用
         * $process->read()来读取消息
         */
    }

    protected function onShutDown()
    {
        /*
         * 该回调可选
         * 当该进程退出的时候，会执行该回调
         */
    }

    protected function onException(\Throwable $throwable, ...$args)
    {
        /*
         * 该回调可选
         * 当该进程出现异常的时候，会执行该回调
         */
    }
}