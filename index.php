<?php

use ProtocolLive\Mtproto\Mtproto;
use ProtocolLive\Mtproto\Transport;

require(__DIR__ . '/php.php');
require(__DIR__ . '/vendor/autoload.php');

$a = new Mtproto(Transport::Abridged, true);
$a->PqRequire(md5(uniqid()));