<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.13.00

namespace ProtocolLive\Mtproto;

/**
 * https://core.telegram.org/mtproto/mtproto-transports
 */
enum Transport:string{
  case Abridged = 'ef';
  case Full = '';
  case Intermediate = 'eeeeeeee';
  case Padded = 'dddddddd';
}