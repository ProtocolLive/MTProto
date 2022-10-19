<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.19.00

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
    //https://core.tlgr.org/mtproto/samples-auth_key#1-request-for-pq-authorization
    $auth_key_id = str_repeat(0, 8 * 2);
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
    $this->Send($Payload, 'PqRequire');

    //https://core.tlgr.org/mtproto/samples-auth_key#2-a-response-from-the-server-has-been-received-with-the-following-content
    $temp = $this->Read('PqResponse');
    $pqresponse = new PqResponse($temp);

    //https://core.tlgr.org/mtproto/samples-auth_key#3-pq--17ed48941a08f981-decomposed-into-2-prime-cofactors
    $pq = self::FactorWolfram($pqresponse->Pq, $Dump);

    //https://core.tlgr.org/mtproto/samples-auth_key#4-encrypted-data-generation
    $data = self::InvertEndian(Methods::PqInnerData->value);
    $data .= '08' . $pqresponse->Pq . '000000';
    $data .= '04' . $pq->P . '000000';
    $data .= '04' . $pq->Q . '000000';
    $data .= $nonce;
    $data .= $pqresponse->NonceServer;
    $data .= bin2hex(random_bytes(32));
    $data_hash = sha1(hex2bin($data));
    $rsapad = self::RsaPad($data, $this->Server);

    //https://core.tlgr.org/mtproto/samples-auth_key#request-to-start-diffie-hellman-key-exchange
    $message_id = microtime(true) * pow(2, 32);
    $message_id = dechex($message_id);
    $message_id = self::InvertEndian($message_id);
    $payload = self::InvertEndian(Methods::DhRequire->value);
    $payload .= $nonce;
    $payload .= $pqresponse->NonceServer;
    $payload .= '04' . $pq->P . '000000';
    $payload .= '04' . $pq->Q . '000000';
    $payload .= $pqresponse->Fingerprint;
    $count = strlen($rsapad->EncryptedData) / 2;
    $count = dechex($count);
    $count = 'fe00' . self::SafeHex($count);
    $payload .= $count . $rsapad->EncryptedData;
    $count = strlen($payload) / 2;
    $count = dechex($count);
    $count = '0000' . self::SafeHex($count);
    $count = self::InvertEndian($count);
    $payload = $auth_key_id . $message_id . $count . $payload;
    self::HexDebug($payload);
    self::Send($payload, 'DhRequire');
    self::Read('DhServer');
  }
}