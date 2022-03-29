<?php

namespace MyApp\Listeners;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream;
use Phalcon\Db\Adapter\Pdo\Mysql as AdapterInterface;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;

class MyDbListener extends Injectable
{
    public function beforeQuery(Event $event, AdapterInterface $connection)
    {
        
        $adapter = new Stream('../app/logs/db.log');
        $logger  = new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );
        $logger->error("Before Query ".$connection->getSQLStatement());        
    }

    public function afterQuery(Event $event, AdapterInterface $connection)
    {
        
        $adapter = new Stream('../app/logs/db.log');
        $logger  = new Logger(
            'messages',
            [
                'main' => $adapter,
            ]
        );
        $logger->error("After Query ".$connection->getSQLStatement());        
    }

   
}