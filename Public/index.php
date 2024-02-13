<?php


$http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';


error_reporting(E_ALL);
ini_set('log_errors', TRUE); 
ini_set('error_log', "../Includes/Logs/error.log");
ini_set('display_errors', FALSE);

define("URL", "$http://" . $_SERVER['SERVER_NAME']);
define("DIR", __DIR__ . "/../");
$path = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = '/';
if(substr($path, 0, strlen($base)) == $base)
{
    $path = substr($path, strlen($base));
}
$path = rtrim($path, '/');
$path = explode('/', strtolower($path));
define("path", $path);
    

$config = require(DIR . "/Includes/Config.php");

try {
    $pdo = new PDO("mysql:host={$config['database']['host']};dbname={$config['database']['database']}", $config['database']['login'], $config['database']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e)
{
    error_log($e);
    die("Wystąpił problem podczas łączenia z bazą danych!");
}

require_once(DIR . "Includes/Libraries/Session.php");
require_once(DIR . "Includes/Libraries/Api.php");
require_once(DIR . "Includes/Libraries/Product/Products.php");
require_once(DIR . "Includes/Libraries/Product/Product.php");
require_once(DIR . "Includes/Libraries/Helper.php");
Session::init();

if(path[0] == 'api')
{
    if(path[1] == 'auth')
    {
        die(require_once(DIR . "Api/Auth.php"));
    } elseif(path[1] == 'checklicense'){
        die(require_once(DIR . "Api/Checklicense.php"));
    } else {
        if(Session::isLogged())
        {
            $request = $pdo->prepare("SELECT `role` FROM `clients` WHERE login=:login");
            $request->execute([':login' => Session::get("login")]);
            $role = $request->fetch(PDO::FETCH_ASSOC)['role'];
            if(file_exists(DIR . "Api/" . ucfirst(path[1]) . ".php"))
            {
                die(require_once(DIR . "Api/".ucfirst(path[1]).".php"));
            } else {
                Api::_(false, "No method with this name!");
            }
        } else {
            Api::_(false, "Please login first!");
        }
    }
}

if(Session::isLogged())
{

    $request = $pdo->prepare("SELECT `role` FROM `clients` WHERE login=:login");
    $request->execute([':login' => Session::get("login")]);
    $role = $request->fetch(PDO::FETCH_ASSOC)['role'];

    if(path[0] == 'login' || path[0] == 'register')
    {
        header("Location: " . URL . "/Licenses");
    } else {
        if(path[0] == 'admin')
        {
            if($role == 'admin')
            {
                if(file_exists(DIR . "Pages/Admin/" . ucfirst(path[1]) . ".php"))
                {
                    require_once(DIR . "Includes/Header.php");
                    require_once(DIR . "Pages/Admin/" . ucfirst(path[1]) . ".php");
                    require_once(DIR . "Includes/Footer.php");
                } else {
                    header("Location: " . URL . "/Licenses");
                }
            } else {
                header("Location: " . URL . "/Licenses");
            }
            die();
        }
        if(file_exists(DIR . "Pages/" . ucfirst(path[0]) . ".php"))
        {
            require_once(DIR . "Includes/Header.php");
            require_once(DIR . "Pages/" . ucfirst(path[0]) . ".php");
            require_once(DIR . "Includes/Footer.php");
        } else {
            header("Location: " . URL . "/Licenses");
        }
    }

} else {

    if(path[0] == 'login')
    {
        require_once(DIR . "Pages/Login.php");
    } elseif(path[0] == 'register')
    {
        require_once(DIR . "Pages/Register.php");
    } else {
        header("Location: " . URL . "/Login");
    }

    
}