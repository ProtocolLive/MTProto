<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.15.00

namespace ProtocolLive\Mtproto;

/**
 * @link https://core.tlgr.org/mtproto/mtproto-transports#transport-errors
 */
enum TransportErrors:int{
  case AuthKey = -404;
  case DC = -444;
  case Flood = -429;
  case Http = -403;
}