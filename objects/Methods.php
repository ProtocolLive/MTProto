<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.13.01

namespace ProtocolLive\Mtproto;

/**
 * Codes in little endian
 */
enum Methods:string{
  case BotImportAuth = '2cffa367';
  case Ping = '7abe77ec';
  case Pong = '347773c5';
  case PqRequire = '60469778'; //Deprecated
  case PqMultiRequire = 'be7e8ef1';
  case PqResponse = '05162463';
  case UsersGet = 'f5d5842d';
}