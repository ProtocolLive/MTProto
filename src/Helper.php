<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.18.03

namespace ProtocolLive\Mtproto;
use stdClass;

trait Helper{
  private static function Aes256Ige(string $Data, string $Key):string{
    $iv1 = $iv2 = str_repeat(chr(0), 16);
    $cripted = '';
    $count = strlen($Data);
    for($i = 0; $i < $count; $i += 16):
      $part = substr($Data, $i, 16);
      $temp = openssl_encrypt(
        $part ^ $iv1,
        'aes-256-ecb',
        $Key,
        OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING
      );
      $temp ^= $iv2;
      $cripted .= $temp;
      $iv1 = $temp;
      $iv2 = $part;
    endfor;
    return $cripted;
  }

  public function Factor(
    string $N,
    bool $Dump = false
  ):StdClass{
    $start = microtime(true);
    $N = hexdec($N);
    $i = floor(sqrt($N));
    while ($N % $i !== 0):
      $i += 1;
    endwhile;
    $return = new stdClass;
    $return->P = dechex($N / $i);
    $return->Q = dechex($i);
    if($Dump):
      echo 'Factor time: ' . microtime(true) - $start . PHP_EOL . PHP_EOL;
    endif;
    return $return;
  }

  public static function FactorWolfram(
    string $Number,
    bool $Dump = false
  ):stdClass|null{
    $start = microtime(true);
    $Number = hexdec($Number);
    $code = file_get_contents('http://www.wolframalpha.com/api/v1/code');
    $code = json_decode($code, true);
    $code = $code['code'];
    $query = 'Do prime factorization of ' . $Number;
    $params = [
      'async' => true,
      'banners' => 'raw',
      'debuggingdata' => false,
      'format' => 'moutput',
      'formattimeout' => 8,
      'input' => $query,
      'output' => 'JSON',
      'proxycode' => $code
    ];
    $url = 'https://www.wolframalpha.com/input/json.jsp?' . http_build_query($params);
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      'referer: https://www.wolframalpha.com/input/?i=' . urlencode($query)
    ]);
    $return = curl_exec($curl);
    $return = json_decode($return, true);
    if(isset($return['queryresult']['pods']) === false):
      return null;
    endif;
    foreach($return['queryresult']['pods'] as $pod):
      if($pod['id'] === 'Divisors'):
        $divs = substr($pod['subpods'][0]['moutput'], 4, -1);
        $divs = explode(', ', $divs);
      endif;
    endforeach;
    if(is_array($divs) === false):
      return null;
    endif;
    $divs[0] = intval($divs[0]);
    if(is_int($divs[0]) === false):
      return null;
    endif;
    $return = new stdClass;
    $return->P = self::SafeHex(dechex($divs[0]));
    $return->Q = self::SafeHex(dechex($divs[1]));
    if($Dump):
      echo 'Factor time: ' . microtime(true) - $start . PHP_EOL . PHP_EOL;
    endif;
    return $return;
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

  /**
   * @link https://core.tlgr.org/mtproto/auth_key#presenting-proof-of-work-server-authentication
   */
  protected function RsaPad(
    string $Data,
    string $Pubkey
  ):stdClass|null{
    $Data = hex2bin($Data);
    $count = strlen($Data);
    if($count > 144):
      return null;
    endif;
    $n = gmp_init($this->Server::Pubkey_N, 16);
    do{
      $data_with_padding = $Data . random_bytes(192 - $count);
      $data_pad_reversed = self::InvertEndian($data_with_padding);
      $temp_key = random_bytes(32);
      $data_with_hash = $data_pad_reversed . hash('sha256', $temp_key . $data_with_padding, true);
      $aes_encrypted = self::Aes256Ige($data_with_hash, $temp_key);
      $temp_key_xor = $temp_key ^ hash('sha256', $aes_encrypted, true);
      $key_aes_encrypted = $temp_key_xor + $aes_encrypted;

      $temp = bin2hex($temp_key_xor . $aes_encrypted);
      $temp = gmp_init($temp, 16);
    }while($temp >= $n);
    openssl_public_encrypt($key_aes_encrypted, $encrypted_data, $Pubkey);
    $return = new stdClass;
    $return->DataWithHash = $data_with_hash;
    $return->EncryptedData = $encrypted_data;
  }

  public static function SafeHex(string $Hex):string{
    if((strlen($Hex) % 2) === 1):
      $Hex = '0' . $Hex;
    endif;
    return $Hex;
  }

  public static function StringEncode(string $Data):string{
    $count = strlen($Data) / 2;
    $count = dechex($count);
    $count = self::SafeHex($count);
    $Data = $count . $Data;
    $Data = str_split($Data, 8);
    foreach($Data as &$block):
      $block = self::InvertEndian($block);
      $block = str_pad($block, 8, 0, STR_PAD_LEFT);
    endforeach;
    return implode('', $Data);
  }
}