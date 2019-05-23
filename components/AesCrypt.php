<?php
namespace app\components;

class AesCrypt
{
  const AES_xtime = [];
  const AES_Sbox = [99,124,119,123,242,107,111,197,48,1,103,43,254,215,171,
  118,202,130,201,125,250,89,71,240,173,212,162,175,156,164,114,192,183,253,
  147,38,54,63,247,204,52,165,229,241,113,216,49,21,4,199,35,195,24,150,5,154,
  7,18,128,226,235,39,178,117,9,131,44,26,27,110,90,160,82,59,214,179,41,227,
  47,132,83,209,0,237,32,252,177,91,106,203,190,57,74,76,88,207,208,239,170,
  251,67,77,51,133,69,249,2,127,80,60,159,168,81,163,64,143,146,157,56,245,
  188,182,218,33,16,255,243,210,205,12,19,236,95,151,68,23,196,167,126,61,
  100,93,25,115,96,129,79,220,34,42,144,136,70,238,184,20,222,94,11,219,224,
  50,58,10,73,6,36,92,194,211,172,98,145,149,228,121,231,200,55,109,141,213,
  78,169,108,86,244,234,101,122,174,8,186,120,37,46,28,166,180,198,232,221,
  116,31,75,189,139,138,112,62,181,102,72,3,246,14,97,53,87,185,134,193,29,
  158,225,248,152,17,105,217,142,148,155,30,135,233,206,85,40,223,140,161,
  137,13,191,230,66,104,65,153,45,15,176,84,187,22];
  const AES_ShiftRowTab = [0,5,10,15,4,9,14,3,8,13,2,7,12,1,6,11];

  private static function AES_xtime(){
    for($i = 0; $i < 128; $i++) {
      $AES_xtime[$i] = $i << 1;
      $AES_xtime[128 + $i] = ($i << 1) ^ 0x1b;
    }
    return $AES_xtime;
  }
  private static function AES_ExpandKey($key, $AES_Sbox) {
    $kl = count($key);
    $ks;
    $Rcon = 1;
    switch ($kl) {
      case 16: $ks = 16 * (10 + 1); break;
      case 24: $ks = 16 * (12 + 1); break;
      case 32: $ks = 16 * (14 + 1); break;
      default:
        alert("AES_ExpandKey: Only key lengths of 16, 24 or 32 bytes allowed!");
    }
    for($i = $kl; $i < $ks; $i += 4) {
      $temp = array_slice($key, $i - 4, $i);
      if ($i % $kl == 0) {
        $temp = [$AES_Sbox[$temp[1]] ^ $Rcon, $AES_Sbox[$temp[2]], $AES_Sbox[$temp[3]], $AES_Sbox[$temp[0]]];
        if (($Rcon <<= 1) >= 256) {
          $Rcon ^= 0x11b;
        }
      } else if (($kl > 24) && ($i % $kl == 16)) {
        $temp = [$AES_Sbox[$temp[0]], $AES_Sbox[$temp[1]], $AES_Sbox[$temp[2]], $AES_Sbox[$temp[3]]];
      }
      for($j = 0; $j < 4; $j++) {
        $key[$i + $j] = $key[$i + $j - $kl] ^ $temp[$j];
      }
    }
    return $key;
  }
  private static function AES_AddRoundKey($state, $rkey) {
    for($i = 0; $i < 16; $i++) {
      $state[$i] ^= $rkey[$i];
    }
    return $state;
  }
  private static function AES_SubBytes($state, $sbox) {
    for($i = 0; $i < 16; $i++) {
      $state[$i] = $sbox[$state[$i]];
    }
    return $state;
  }
  private static function AES_ShiftRows($state, $shifttab) {
    $h = $state;
    for($i = 0; $i < 16; $i++) {
      $state[$i] = $h[$shifttab[$i]];
    }
    return $state;
  }
  private static function AES_MixColumns($state, $AES_xtime) {
    for($i = 0; $i < 16; $i += 4) {
      $s0 = $state[$i + 0];
      $s1 = $state[$i + 1];
      $s2 = $state[$i + 2];
      $s3 = $state[$i + 3];
      $h = $s0 ^ $s1 ^ $s2 ^ $s3;
      $state[$i + 0] ^= $h ^ $AES_xtime[$s0 ^ $s1];
      $state[$i + 1] ^= $h ^ $AES_xtime[$s1 ^ $s2];
      $state[$i + 2] ^= $h ^ $AES_xtime[$s2 ^ $s3];
      $state[$i + 3] ^= $h ^ $AES_xtime[$s3 ^ $s0];
    }
    return $state;
  }
  public static function AES_Encrypt($block, $key) {
    $data = [];
    $block_to_byte = [];
    foreach (str_split($block) as $b) {
      $block_to_byte[] = ord($b);
    }
    $data[1] = count($block_to_byte);
    if (count($block_to_byte) < 16) {
      for ($i = count($block_to_byte); $i < 16; $i++) {
        $block_to_byte[] = null;
      }
    }
    $key_to_byte = [];
    foreach (str_split($key) as $b) {
      $key_to_byte[] = ord($b);
    }
    $key_to_byte = self::AES_ExpandKey($key_to_byte, self::AES_Sbox);
    $AES_xtime = self::AES_xtime();
    $l = count($key_to_byte);
    $data[0] = self::AES_AddRoundKey($block_to_byte, array_slice($key_to_byte, 0, 16));
    for($i = 16; $i < $l - 16; $i += 16) {
      $data[0] = self::AES_SubBytes($data[0], self::AES_Sbox);
      $data[0] = self::AES_ShiftRows($data[0], self::AES_ShiftRowTab);
      $data[0] = self::AES_MixColumns($data[0], $AES_xtime);
      $data[0] = self::AES_AddRoundKey($data[0], array_slice($key_to_byte, $i, $i + 16));
    }
    $data[0] = self::AES_SubBytes($data[0], self::AES_Sbox);
    $data[0] = self::AES_ShiftRows($data[0], self::AES_ShiftRowTab);
    $data[0] = self::AES_AddRoundKey($data[0], array_slice($key_to_byte, $i, $l));
    return $data;
  }
  public static function getPublicKey($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return hash('md5', $randomString);
  }
}
