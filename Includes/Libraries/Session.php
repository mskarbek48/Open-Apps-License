<?php


class Session {

    public static function setTimer($name)
    {
        $_SESSION['timers'][$name] = time();
    }

    public static function checkTimer($name, $seconds)
    { 
        if(isset($_SESSION['timers'][$name]))
        { 
            if(time() - $_SESSION['timers'][$name] >= $seconds)
            { 
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    public static function getTimer($name, $seconds)
    { 
        if(isset($_SESSION['timers'][$name]))
        { 
            if(time() - $_SESSION['timers'][$name] >= $seconds)
            { 
                return 0;
            } else {
                return $seconds - (time() - $_SESSION['timers'][$name]);
            }
        }
        return 0;
    }

    public static function init() 
    {
        session_name("OA_AUTH");
        session_start();
        if(!isset($_SESSION['data']))
        {
            $_SESSION['data'] = array();
        }
        if(!isset($_SESSION['logged_in']))
        {
            $_SESSION['logged_in'] = false;
        }
        if(!isset($_SESSION['login_type']))
        {
            $_SESSION['login_type'] = 'online';
        }
        $_SESSION['refresh'] = time();
    }

    public static function set($key, $value)
    {
        return $_SESSION['data'][$key] = $value;
    }

    public static function del($key)
    {
        unset($_SESSION['data'][$key]);
    }

    public static function get($key)
    { 
        return isset($_SESSION['data'][$key]) ? $_SESSION['data'][$key] : null;
    }

    public static function login()
    {
        return $_SESSION['logged_in'] = true;
    }

    public static function isset($key)
    {
        return isset($_SESSION['data'][$key]);
    }

    public static function isLogged()
    {
        return $_SESSION['logged_in'];
    }

    public static function logout()
    { 
        $_SESSION['logged_in'] = false;
    }

    public static function destroy()
    { 
        $_SESSION = array();
        session_destroy();
    }

    public static function setLoginType($type)
    {
        $_SESSION['login_type'] = $type;
    }

    public static function getLoginType()
    {
        return $_SESSION['login_type'];
    }
    
}