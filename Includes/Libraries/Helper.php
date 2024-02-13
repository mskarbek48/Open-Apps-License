<?php

class Helper {

    public static function generateSerialNumber($up = false)
    {
        if(!$up)
        {
            $number = "D" . date('d') . "M" . date('m') . "Y" . date("Y") . "H" . date("H") . "I" . date("i") . "S" . date("s");
            $number .= "-" . substr(bin2hex(random_bytes(20)),0,20);
            $number .= "-" . substr(bin2hex(random_bytes(20)),0,20);
        } else {
            $number = "U_D" . date('d') . "M" . date('m') . "Y" . date("Y") . "H" . date("H") . "I" . date("i") . "S" . date("s");
            $number .= "-" . substr(bin2hex(random_bytes(20)),0,19);
            $number .= "-" . substr(bin2hex(random_bytes(20)),0,19);
        }
        


        return $number;
    }

    public static function licenseResponse($success, $message, $data)
    { 

        $test = json_encode(array("success" => $success, "message" => $message, "data" => $data));

        $cipher="AES-128-CBC";
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
    }

}
