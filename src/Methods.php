<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.19.00

namespace ProtocolLive\Mtproto;

/**
 * Codes in little endian
 */
enum Methods:string{
  case BotImportAuth = '2cffa367';
  /**
   * req_DH_params#d712e4be nonce:int128 server_nonce:int128 p:string q:string public_key_fingerprint:long encrypted_data:string = Server_DH_Params
   * @link https://core.tlgr.org/mtproto/samples-auth_key#request-to-start-diffie-hellman-key-exchange
   */
  case DhRequire = 'd712e4be';
  /**
   * server_DH_params_fail#79cb045d nonce:int128 server_nonce:int128 new_nonce_hash:int128 = Server_DH_Params;
   * @link https://core.tlgr.org/mtproto/samples-auth_key#5-a-response-from-the-server-has-been-received-with-the-following-content
   */
  case DhServerFail = '79cb045d';
  /**
   * server_DH_params_ok#d0e8075c nonce:int128 server_nonce:int128 encrypted_answer:string = Server_DH_Params;
   * @link https://core.tlgr.org/mtproto/samples-auth_key#5-a-response-from-the-server-has-been-received-with-the-following-content
   */
  case DhServerOk = 'd0e8075c';
  /**
   * p_q_inner_data#83c95aec pq:string p:string q:string nonce:int128 server_nonce:int128 new_nonce:int256 = P_Q_inner_data
   * @link https://core.tlgr.org/mtproto/samples-auth_key#4-encrypted-data-generation
   */
  case PqInnerData = '83c95aec';
  /**
   * p_q_inner_data_dc#a9f55f95 pq:string p:string q:string nonce:int128 server_nonce:int128 new_nonce:int256 dc:int = P_Q_inner_data;
   * @link https://core.tlgr.org/mtproto/auth_key#presenting-proof-of-work-server-authentication
   */
  case PqInnerDataDc = 'a9f55f95';
  /**
   * p_q_inner_data_temp_dc#56fddf88 pq:string p:string q:string nonce:int128 server_nonce:int128 new_nonce:int256 dc:int expires_in:int = P_Q_inner_data;
   * @link https://core.tlgr.org/mtproto/auth_key#presenting-proof-of-work-server-authentication
   */
  case PqInnerDataDcTemp = '56fddf88';
  /**
   * req_pq#60469778 nonce:int128 = ResPQ
   * @link https://core.tlgr.org/mtproto/samples-auth_key#1-request-for-pq-authorization
   */
  case PqRequire = '60469778';
  /**
   * req_pq_multi#be7e8ef1 nonce:int128 = ResPQ;
   * @link https://core.tlgr.org/mtproto/auth_key#dh-exchange-initiation
   */
  case PqMultiRequire = 'be7e8ef1';
  /**
   * resPQ#05162463 nonce:int128 server_nonce:int128 pq:string server_public_key_fingerprints:Vector long = ResPQ 
   * @link https://core.tlgr.org/mtproto/samples-auth_key#response-decomposition-using-the-following-formula
   */
  case PqResponse = '05162463';
}