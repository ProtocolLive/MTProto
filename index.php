<?php
require(__DIR__ . '/php.php');
echo "\e[H\e[J";
require(__DIR__ . '/mtproto.php');

$a = new Mtproto(MtprotoTransport::Abridged, true);
