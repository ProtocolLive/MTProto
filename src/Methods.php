<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.17.00

namespace ProtocolLive\Mtproto;

/**
 * Codes in little endian
 */
enum Methods:string{
  case BotImportAuth = '2cffa367';
  case DhRequire = 'd712e4be';
  case Ping = '7abe77ec';
  case Pong = '347773c5';
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
  case PqRequire = '60469778'; //Deprecated
  case PqMultiRequire = 'be7e8ef1';
  case PqResponse = '05162463';
  case UsersGet = 'f5d5842d';
}