<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.15.00

namespace ProtocolLive\Mtproto;

/**
 * Codes in little endian
 */
enum Objects:string{
  case User = 'd23c81a3';
  case UserNone = 'c67599d1';
  case VectorLong = '1cb5c415';
};