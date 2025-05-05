<?php
function base32_decode($b32) {
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $binary = '';
    $b32 = strtoupper($b32);
    foreach (str_split($b32) as $char) {
        $binary .= str_pad(base_convert(strpos($alphabet, $char), 10, 2), 5, '0', STR_PAD_LEFT);
    }
    $binary = str_split($binary, 8);
    $data = '';
    foreach ($binary as $byte) {
        if (strlen($byte) === 8) {
            $data .= chr(bindec($byte));
        }
    }
    return $data;
}

function verifyCode($secret, $code, $discrepancy = 1, $currentTimeSlice = null) {
    if ($currentTimeSlice === null) {
        $currentTimeSlice = floor(time() / 30);
    }

    $secretKey = base32_decode($secret);
    for ($i = -$discrepancy; $i <= $discrepancy; ++$i) {
        $calculatedCode = generateCode($secretKey, $currentTimeSlice + $i);
        if ($calculatedCode === $code) {
            return true;
        }
    }
    return false;
}

function generateCode($key, $timeSlice) {
    $time = pack('N*', 0) . pack('N*', $timeSlice);
    $hash = hash_hmac('sha1', $time, $key, true);
    $offset = ord(substr($hash, -1)) & 0x0F;
    $truncatedHash = substr($hash, $offset, 4);
    $value = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;
    return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
}