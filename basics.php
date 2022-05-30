<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.05.30.03

/**
 * https://core.telegram.org/mtproto/mtproto-transports
 */
enum MtprotoTransport:string{
  case Abridged = 'ef';
  case Full = '';
  case Intermediate = 'eeeeeeee';
  case Padded = 'dddddddd';
}

/**
 * Codes in little endian
 */
const MtprotoObjects = [
  'd23c81a3' => 'User',
  'c67599d1' => 'UserNone'
];

class MtprotoBasics{
  private Socket $Connection;
  private MtprotoTransport $Transport;
  private bool $Test;

  public function __construct(
    MtprotoTransport $Transport = MtprotoTransport::Abridged,
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
    socket_set_option($this->Connection, SOL_SOCKET, SO_RCVTIMEO, ["sec" => 2, "usec" => 0]);
    //var_dump(file_get_contents('http://meuip.com/api/meuip.php'));
  }

  protected function Count(
    string $Msg,
    bool $AsHex = true,
    bool $TransportHeader = false
  ):string|false{
    if($this->Transport === MtprotoTransport::Abridged):
      $count = strlen($Msg) / 2;
      if($TransportHeader):
        if(($count % 4) !== 0):
          return false;
        endif;
        $count /= 4;
      endif;
      if($count >= 127):
        $count = 0x7f + ($count << 8);
      endif;
    endif;
    if($AsHex):
      return $this->SafeHex(dechex($count));
    else:
      return $count;
    endif;
  }

  public static function HexDebug(
    string $Hex,
    string $Msg = null
  ):void{
    $Hex = str_split($Hex, 16 * 2);
    foreach($Hex as $id => &$n):
      $n = str_split($n, 2);
      $n = str_pad(dechex($id * 16), 2, 0, STR_PAD_LEFT) .
      ' | ' . implode(' ', $n);
    endforeach;
    $Hex = implode(PHP_EOL, $Hex);
    echo $Msg . PHP_EOL .
      '   | 00 01 02 03 04 05 06 07 08 09 0a 0b 0c 0d 0e 0f' .
      PHP_EOL .
      '---+------------------------------------------------' .
      PHP_EOL .
      $Hex . PHP_EOL . PHP_EOL;
  }

  public static function InvertEndian(string $Hex):string{
    $Hex = str_split($Hex, 2);
    $Hex = array_reverse($Hex);
    return implode('', $Hex);
  }

  protected function PayloadParse(string $Hex):void{
    $temp = substr($Hex, 0, 2);
    $temp = MtprotoTransport::tryFrom($temp);
    $return = 'Transport: ' . $temp->name . PHP_EOL;

    $count = substr($Hex, 2, 2);
    $count = hexdec($count) * 4;
    $return .= 'Bytes: ' . $count . PHP_EOL;
    $count *= 2;

    $temp = substr($Hex, 4, 8);
    $temp = MtprotoMethods::tryFrom($temp);

    $temp = 'MtpMethod_' . $temp->name;
    $temp = new $temp;
    $temp->Parse(substr($Hex, 12));
    $return .= $temp;
    echo $return . PHP_EOL;
  }

  protected function Read():void{
    $return = '';
    while($response = socket_read($this->Connection, 1024)):
      $return .= $response;
    endwhile;
    if($return === ''):
      $return = $response;
    endif;
    if($return === false):
      exit('Nenhuma resposta');
    endif;
    $this->HexDebug(bin2hex($return), 'Received:');
    if($return === ''):
      return;
    endif;
    if($return[0] === chr(1)):
      $return = unpack('l', substr($return, 1));
      var_dump(~$return[1]);
    endif;
  }

  /**
   * @link https://core.tlgr.org/mtproto/samples-auth_key
   */
  protected function Send(string $Msg):int|false{
    $this->HexDebug($Msg, 'Sending:');
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
    endif;
    //openssl_public_encrypt($Msg, $Msg, $key);
    return socket_write($this->Connection, $Msg, strlen($Msg));
  }

  public static function SafeHex(string $Hex):string{
    if((strlen($Hex) % 2) === 1):
      $Hex = '0' . $Hex;
    endif;
    return $Hex;
  }

  public static function StringEncode(string $Data):string{
    $count = strlen($Data);
    $count = dechex($count);
    $count = self::SafeHex($count);
    $Data = $count . bin2hex($Data);
    $Data = str_split($Data, 8);
    foreach($Data as &$block):
      $block = self::InvertEndian($block);
      $block = str_pad($block, 8, 0, STR_PAD_LEFT);
    endforeach;
    return implode('', $Data);
  }

  public static function StringDecode(string $Data):string{
    $Data = str_split($Data, 8);
    $last = count($Data) - 1;
    foreach($Data as $id => &$block):
      if($id !== $last):
        $block = self::InvertEndian($block);
      endif;
    endforeach;
    $block = dechex(hexdec($block));//Remove left 0s
    $block = self::InvertEndian($block);
    $Data = implode('', $Data);
    $Data = substr($Data, 2);//Remove count
    return hex2bin($Data);
  }
}