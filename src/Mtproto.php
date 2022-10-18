<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.18.01

namespace ProtocolLive\Mtproto;

use Exception;
use ProtocolLive\Mtproto\Servers\Servers;

class Mtproto extends Basics{

  /**
   * @throws Exception
   */
  public function __construct(
    Servers $Server,
    Transport $Transport = Transport::Abridged
  ){
    parent::__construct($Server, $Transport);
  }

  public function PqRequire(
    string $Nonce,
    bool $Dump = false
  ):void{
    $auth_key_id = str_repeat(0, 16);
    $message_id = microtime(true) * pow(2, 32);
    $message_id = dechex($message_id);
    $message_id = self::InvertEndian($message_id);
    $method = self::InvertEndian(Methods::PqRequire->value);
    $nonce = self::InvertEndian($Nonce);
    $count = strlen($method . $nonce) / 2;
    $count = dechex($count);
    $count = str_pad($count, 8, 0, STR_PAD_LEFT);
    $count = self::InvertEndian($count);
    $Payload = $auth_key_id . $message_id . $count . $method . $nonce;

    $this->Send($Payload, $Dump);
  }
}