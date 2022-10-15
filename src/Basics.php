<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.15.02

namespace ProtocolLive\Mtproto;
use \Exception;
use \Socket;

class Basics{
  use Helper;
  private Socket $Connection;
  private Transport $Transport;
  private bool $Test;

  public function __construct(
    Transport $Transport = Transport::Abridged,
    bool $Test = false
  ){
    $this->Transport = $Transport;
    $this->Test = $Test;
    $temp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if($temp === false):
      throw new Exception(socket_strerror(socket_last_error()), socket_last_error());
    endif;
    $this->Connection = $temp;
    
    if($Test):
      $temp = socket_connect($this->Connection, '149.154.167.40', 443);
    else:
      $temp = socket_connect($this->Connection, '149.154.167.50', 443);
    endif;
    if($temp === false):
      throw new Exception(socket_strerror(socket_last_error()), socket_last_error());
    endif;
    //socket_set_option($this->Connection, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 5, 'usec' => 0]);
    //var_dump(file_get_contents('http://meuip.com/api/meuip.php'));
  }

  protected function Read(
    bool $Dump = false
  ):string|TransportErrors{
    $response = socket_read($this->Connection, 1024);
    if($Dump):
      $this->HexDebug(bin2hex($response), 'Received:');
    endif;
    if($response[0] === chr(1)):
      $response = unpack('l', substr($response, 1));
      return TransportErrors::from($response);
    endif;
    $response = substr($response, 1);
  }

  /**
   * @link https://core.tlgr.org/mtproto/samples-auth_key
   */
  protected function Send(
    string $Msg,
    bool $Dump = false
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
    if($Dump):
      $this->HexDebug($Msg, 'Sending:');
    endif;
    $Msg = hex2bin($Msg);
    if($this->Test):
      $key = '-----BEGIN PUBLIC KEY-----' . PHP_EOL;
      $key .= 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyMEdY1aR+sCR3ZSJrtzt' . PHP_EOL;
      $key .= 'KTKqigvO/vBfqACJLZtS7QMgCGXJ6XIRyy7mx66W0/sOFa7/1mAZtEoIokDP3Sho' . PHP_EOL;
      $key .= 'qF4fVNb6XeqgQfaUHd8wJpDWHcR2OFwvplUUI1PLTktZ9uW2WE23b+ixNwJjJGwB' . PHP_EOL;
      $key .= 'DJPQEQFBE+vfmH0JP503wr5INS1poWg/j25sIWeYPHYeOrFp/eXaqhISP6G+q2Ie' . PHP_EOL;
      $key .= 'TaWTXpwZj4LzXq5YOpk4bYEQ6mvRq7D1aHWfYmlEGepfaYR8Q0YqvvhYtMte3ITn' . PHP_EOL;
      $key .= 'uSJs171+GDqpdKcSwHnd6FudwGO4pcCOj4WcDuXc2CTHgH8gFTNhp/Y8/SpDOhvn' . PHP_EOL;
      $key .= '9QIDAQAB' . PHP_EOL;
      $key .= '-----END PUBLIC KEY-----';
    else:
      $key = '-----BEGIN RSA PUBLIC KEY-----' . PHP_EOL;
      $key .= 'MIIBCgKCAQEA6LszBcC1LGzyr992NzE0ieY+BSaOW622Aa9Bd4ZHLl+TuFQ4lo4g' . PHP_EOL;
      $key .= '5nKaMBwK/BIb9xUfg0Q29/2mgIR6Zr9krM7HjuIcCzFvDtr+L0GQjae9H0pRB2OO' . PHP_EOL;
      $key .= '62cECs5HKhT5DZ98K33vmWiLowc621dQuwKWSQKjWf50XYFw42h21P2KXUGyp2y/' . PHP_EOL;
      $key .= '+aEyZ+uVgLLQbRA1dEjSDZ2iGRy12Mk5gpYc397aYp438fsJoHIgJ2lgMv5h7WY9' . PHP_EOL;
      $key .= 't6N/byY9Nw9p21Og3AoXSL2q/2IJ1WRUhebgAdGVMlV1fkuOQoEzR7EdpqtQD9Cs' . PHP_EOL;
      $key .= '5+bfo3Nhmcyvk5ftB0WkJ9z6bNZ7yxrP8wIDAQAB' . PHP_EOL;
      $key .= '-----END RSA PUBLIC KEY-----';
    endif;
    //openssl_public_encrypt($Msg, $Msg, $key);
    return socket_write($this->Connection, $Msg, strlen($Msg));
  }
}