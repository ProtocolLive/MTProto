<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.18.00

namespace ProtocolLive\Mtproto;

class PqResponse{
  use Helper;
  public readonly string $AuthKeyId;
  public readonly string $MessageId;
  public readonly string $Nonce;
  public readonly string $NonceServer;
  public readonly string $Pq;
  public readonly string $Fingerprint;

  /**
   * resPQ#05162463 nonce:int128 server_nonce:int128 pq:string server_public_key_fingerprints:Vector long = ResPQ
   * @link https://core.tlgr.org/mtproto/samples-auth_key#response-decomposition-using-the-following-formula
   */
  public function __construct(string $Data){
    //$Data = bin2hex($Data);
    $this->AuthKeyId = substr($Data, 0, 8 * 2);
    $this->MessageId = substr($Data, 8 * 2, 8 * 2);
    $count1 = substr($Data, 16 * 2, 4 * 2);
    $temp = substr($Data, 20 * 2, 4 * 2);
    $this->Nonce = substr($Data, 24 * 2, 16 * 2);
    $this->NonceServer = substr($Data, 40 * 2, 16 * 2);
    $this->Pq = substr($Data, 57 * 2, 8 * 2);
    $temp = substr($Data, 68 * 2, 4 * 2);
    $count2 = substr($Data, 72 * 2, 4 * 2);
    $this->Fingerprint = substr($Data, 76 * 2, 8 * 2);
  }
}