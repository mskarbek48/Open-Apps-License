<?php

class Api {


    public static function _($success = false, $message = 'No response', $data = array())
    {
        die(json_encode(array("success" => $success, "message" => $message, "data" => $data, "timestamp" => time())));
    }

}