<?php

use ProtocolLive\Mtproto\Mtproto;
use ProtocolLive\Mtproto\Transport;

require(__DIR__ . '/php.php');
require(__DIR__ . '/vendor/autoload.php');
echo "\e[H\e[J";

$a = new Mtproto(Transport::Abridged, true);