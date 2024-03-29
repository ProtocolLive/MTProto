<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.19.01

namespace ProtocolLive\Mtproto;
use \Exception;
use ProtocolLive\Mtproto\Servers\Servers;
use \Socket;

class Basics{
  use Helper;
  private Socket $Connection;
  
  /**
   * @throws Exception
   */
  public function __construct(
    protected Servers $Server,
    private Transport $Transport = Transport::Abridged
  ){
    $temp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if($temp === false):
      throw new Exception(socket_strerror(socket_last_error()), socket_last_error());
    endif;
    $this->Connection = $temp;
    
    $temp = socket_connect($this->Connection, $Server->Class()::Ip, 443);
    if($temp === false):
      throw new Exception(socket_strerror(socket_last_error()), socket_last_error());
    endif;
  }

  protected function Read(
    string $Dump = null
  ):string|TransportErrors|null{
    $response = socket_read($this->Connection, 1024);
    if($Dump !== null):
      self::HexDebug(bin2hex($response), 'Received ' . $Dump);
    endif;
    if($response == ''):
      return null;
    endif;
    if($response[0] === chr(1)):
      $response = unpack('l', substr($response, 1));
      return TransportErrors::from($response);
    endif;
    return substr($response, 1);
  }

  /**
   * @link https://core.tlgr.org/mtproto/samples-auth_key
   */
  protected function Send(
    string $Msg,
    string $Dump = null
  ):int|false{
    if($this->Transport === Transport::Abridged):
      $count = strlen($Msg) / 2;
      if($count % 4 !== 0):
        exit('Size error');
      endif;
      $count /= 4;
      $count = dechex($count);
      $count = self::SafeHex($count);
      $Msg = Transport::Abridged->value . $count . $Msg;
    elseif($this->Transport === Transport::Intermediate):
      $count = strlen($Msg);
      $count = dechex($count);
      $count = str_pad($count, 8, 0, STR_PAD_LEFT);
      $count = self::InvertEndian($count);
      $Msg = Transport::Intermediate->value . $count . $Msg;
    endif;
    if($Dump !== null):
      self::HexDebug($Msg, 'Sending ' . $Dump);
    endif;
    $Msg = hex2bin($Msg);
    return socket_write($this->Connection, $Msg, strlen($Msg));
  }
}