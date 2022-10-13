<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.13.01

namespace ProtocolLive\Mtproto;

class Mtproto extends Basics{

  public function __construct(
    Transport $Transport = Transport::Abridged,
    bool $Test = false
  ){
    parent::__construct($Transport, $Test);
  }

  /**
   * auth.importBotAuthorization flags:int api_id:int api_hash:string bot_auth_token:string = auth.Authorization
   * @link https://core.telegram.org/api/bots#login
   */
  public function BotImportAuth(
    int $Flags,
    int $ApiId,
    string $ApiHash,
    string $Token
  ):void{
    $Payload = Methods::BotImportAuth->value;
    
    $Payload .= bin2hex(pack('l', $Flags));

    $ApiId = dechex($ApiId);
    $ApiId = str_pad($ApiId, 8, 0, STR_PAD_LEFT);
    $ApiId = $this->InvertEndian($ApiId);
    $Payload .= $ApiId;

    $Payload .= $this->StringEncode($ApiHash);
    $Payload .= $this->StringEncode($Token);
  
    //$this->DebugHex($Payload);
    $count = $this->Count($Payload, true, true) or exit('Contagem errada');
    $msg = 'ef' . $count . $Payload;
    $this->PayloadParse($msg);
    $this->Send($msg);
    $this->Read();
  }

  public function PqMultiRequire(
    string $Nonce
    $auth_key_id = str_repeat(0, 8);
  ):void{
    $message_id = microtime(true) * pow(2, 32);
    $message_id = dechex($message_id);
    $message_id = $this->InvertEndian($message_id);
    $method = self::InvertEndian(Methods::PqMultiRequire->value);
    $nonce = self::InvertEndian($Nonce);
    $count = strlen($method . $nonce) / 2;
    $count = dechex($count);
    
    $Payload = $auth_key_id . $message_id . $count . $method . $nonce;
    $this->Send($Payload);
    $this->Read();
  }

  /**
   * getUsers Vector int = Vector User
   */
  public function UsersGet():void{}
}