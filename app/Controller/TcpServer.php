<?php


namespace App\Controller;

use Exception;
use Hyperf\Contract\OnReceiveInterface;
use Psr\Log\LoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Command\Command as HyperfCommand;
use Throwable;

class TcpServer implements OnReceiveInterface
{
    protected LoggerInterface $logger;
    protected CaculatorController $caculator;

    public function __construct(LoggerFactory $loggerFactory, CaculatorController $caculator)
    {
        $this->logger = $loggerFactory->get('log', 'default');
        $this->caculator = $caculator;
    }

    public function onReceive($server, int $fd, int $reactorId, string $data): void
    {
        try {
            $data = trim($data, "\n");
            $arr = explode(' ', $data);
            $op = $arr[0];
            if (count($arr) < 1) {
                $server->send($fd, "unknown command.\n");
                return;
            }

            
            $params = array_slice($arr, 1);
            $this->logger->info("op: $op params:".json_encode($params)." count:". count($arr));
            $res = call_user_func_array([$this->caculator, $op], $params);
        } catch (Throwable $e) {
            $res = $e->getMessage();
        }
        
        $res = json_encode($res, JSON_UNESCAPED_UNICODE);
        $server->send($fd, $res . "\n");
    }
}
