<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.15.01

namespace ProtocolLive\Mtproto;
use GMP;
use stdClass;

trait Helper{
  public static function FactorWolfram(
    int|GMP $Number
  ):stdClass|null{
    if($Number instanceof GMP):
      $Number = gmp_strval($Number, 10);
    endif;
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
    if($Number instanceof GMP):
      $return->P = gmp_init($divs[0], 10);
      $return->Q = gmp_init($divs[1], 10);
    else:
      $return->P = $divs[0];
      $return->Q = $divs[1];
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

  public static function SafeHex(string $Hex):string{
    if((strlen($Hex) % 2) === 1):
      $Hex = '0' . $Hex;
    endif;
    return $Hex;
  }
}