<?php
//Protocol Corporation Ltda.
//https://github.com/ProtocolLive
//2022.10.18.00

namespace ProtocolLive\Mtproto\Servers;

enum Servers{
  case Dc2;
  case Dc2Test;

  public function Class():string{
    return match($this){
      self::Dc2 => Dc2::class,
      self::Dc2Test => Dc2Test::class,
    };
  }
}