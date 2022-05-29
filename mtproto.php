<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.05.28.00

require(__DIR__ . '/requires.php');

class Mtproto extends MtprotoBasics{

  public function __construct(
    MtprotoTransport $Transport = MtprotoTransport::Abridged,
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
  ){
    $Payload = MtprotoMethods::BotImportAuth->value;
    
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

  /**
   * getUsers Vector int = Vector User
   */
  public function UsersGet(){}
}