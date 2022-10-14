<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.14.01

namespace ProtocolLive\Mtproto;

class Mtproto extends Basics{

  public function __construct(
    Transport $Transport = Transport::Abridged,
    bool $Test = false
  ){
    parent::__construct($Transport, $Test);
  }

  public function PqRequire(
    string $Nonce
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
    $this->Send($Payload);
    $this->Read();
  }
}