<?php


/**
 * Class Ws ===> Template from php-swoole lessons ---> Just for reference, do not instantiate!
 */
class Ws
{

    const HOST = "127.0.0.1";
    const PORT = 6001;

    public $server = null;

    public function __construct()
    {
        $this->server = new swoole_websocket_server(self::HOST, self::PORT);

        $this->server->set(
            [
                'worker_num' => 2,
                'task_worker_num' => 2,
            ]
        );
        $this->server->on("open", [$this, 'onOpen']);
        $this->server->on("message", [$this, 'onMessage']);
        $this->server->on("task", [$this, 'onTask']);
        $this->server->on("finish", [$this, 'onFinish']);
        $this->server->on("close", [$this, 'onClose']);

        $this->server->start();
    }


    /**
     * 监听ws连接事件
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request)
    {
        var_dump($request->fd);
        if ($request->fd == 1) {
            // 每2秒执行
            swoole_timer_tick(2000, function ($timer_id) {
                echo "2s: timerId:{$timer_id}\n";
            });
        }
    }

    /**
     * 监听server消息事件
     * @param $ws
     * @param $frame
     */
    public function onMessage($ws, $frame)
    {
        echo "ser-push-message:{$frame->data}\n";
        // todo 10s
        $data = [
            'task' => 1,
            'fd' => $frame->fd,
        ];
        //$ws->task($data);

        swoole_timer_after(5000, function () use ($ws, $frame) {
            echo "5s-after\n";
            $ws->push($frame->fd, "server-time-after:");
        });
        $ws->push($frame->fd, "server-push:" . date("Y-m-d H:i:s"));
    }

    public function onTask($serv, $taskId, $workerId, $data)
    {
        print_r($data);
        // 耗时场景 10s
        sleep(10);
        return "on task finish"; // 告诉worker
    }

    public function onFinish($serv, $taskId, $data)
    {
        echo "taskId:{$taskId}\n";
        echo "finish-data-sucess:{$data}\n";
    }

    public function onClose($ws, $fd)
    {
        echo "clientid:{$fd}\n";
    }
}

//$obj !!!=!!! new Ws();--->Do not instantiate!!! DO not open this!!!


/**
 * My own type using php-swoole in a simple way
 */
// $server = new swoole_websocket_server("127.0.0.1", 6001);//test locally
$server = new swoole_websocket_server("172.26.5.78", 6001);//In order to let others use

$server->on("open", function (swoole_websocket_server $server, $request) {
    echo "Server: handshake success with fd:{$request->fd}.\n";
});

$server->on("message", function (swoole_websocket_server $server, $frame) {
    echo "Receive from {$frame->fd}.\n";
    $data = $frame->data;

    foreach ($server->connections as $fd) {
        $server->push($fd, $data);
    }
});

$server->on("close", function ($server, $fd) {
    echo "Client {$fd} closed.\n";
});

$server->start();