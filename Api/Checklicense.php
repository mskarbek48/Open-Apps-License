<?php



$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$method = $_SERVER['REQUEST_METHOD'];

$rq = $pdo->prepare("INSERT INTO `licenses_requests` (`license_id`, `product_id`, `version_id`, `success`, `reason`, `address`, `time`, `instance`, `type`) VALUES (:1, :2, :3, :4, :5, :6, :7, :8, :9)");

if($method == "POST" && $user_agent == "Open-Apps Application")
{

    foreach(["ts_ip", "ts_udp_port", "application", "instance", "major", "minor", "release", "cycle", "serial_number", "time", "type", "core_fragment", "license_fragment", "logs"] as $key)
    {
        if(!isset($_POST[$key]))
        {
            response(false, "Niepoprawne argumenty wysyłane w POST! Skontakuj się z supportem aplikacji.");
        }
    }



    $fatal = false;
    if(str_contains($_POST['core_fragment'], "if(") || str_contains($_POST['license_fragment'], "function(")  || str_contains($_POST['license_fragment'], "if("))
    {
        if($ip != "***" && $ip != '***' && $ip != "***" && $ip != '***'  && $ip != '***')
        {
            $fatal = true;
            $request = $pdo->prepare("INSERT INTO `raports` (`ip`, `serial_number`, `data`, `time`) VALUES (:1, :2, :3, :4)");
            $request->execute([':1' => $ip, ':2' => $_POST['serial_number'], ':3' => json_encode(["license_lines" => $_POST["license_lines"], "core_fragment" => $_POST['core_fragment'], "core_lines" => $_POST['core_lines']]), ':4' => time()]);
            $request = $pdo->prepare("UPDATE `licenses` SET block_reason=:1, blocked=1 WHERE serial_number=:2");
            $request->execute([':1' => "Łamanie zasad licencji", ':2' => $_POST['serial_number']]);
        }

    }


    $request = $pdo->prepare("SELECT * FROM `licenses` WHERE serial_number=:serial");
    $request->execute([':serial' => $_POST['serial_number']]);
    $license = $request->fetch(PDO::FETCH_ASSOC);


    if(isset($license['id']))
    {

        if($license['blocked'])
        {
            if($license['unblock_time'] == null || $license['unblock_time'] == 0)
            {
                $t = "Nigdy";
            } else {
                $t = date('d.m.Y H:i:s', $license['unblock_time']);
            }
            $rq->execute([
                ":1" => $license['id'],
                ':2' => $license['product_id'],
                ':3' => 0,
                ':4' => 0,
                ':5' =>
                "Blokada licencji",
                ':6' => $ip,
                ':7' => time(),
                ':8' => $_POST['instance'],
                ":9" => $_POST['type'],
            ]);
            die(response(false, "\n\n(!) Twoja licencja została zablokowana, skontakuj się z supportem.\n(!) Powód blokady: ". $license['block_reason'] . "\n(!) Blokada wygasa: " . $t . "\n"));
        }
        

        $request = $pdo->prepare("SELECT `name` FROM products WHERE id=:id");
        $request->execute([':id' => $license['product_id']]);
        $product_name = $request->fetch(PDO::FETCH_ASSOC)['name'];
        if($product_name == $_POST['application'])
        {

            $request = $pdo->prepare("SELECT * FROM `licenses_settings` WHERE license_id=:id");
            $request->execute([':id' => $license['id']]);
            $data = $request->fetch(PDO::FETCH_ASSOC);
            if(isset($data['id']))
            {
                if($data['vps_ip'] == $ip)
                {
                    if($data['ts_ip'] == $_POST['ts_ip'])
                    {
                        if($data['ts_udp_port'] == $_POST['ts_udp_port'])
                        {

                            $request = $pdo->prepare("SELECT `cycle`, `id`,`release_time`, `supported`, `active` FROM products_version WHERE product_id=:id AND major=:major AND minor=:minor AND `release`=:release AND cycle=:cycle ORDER BY id DESC LIMIT 1");
                            $request->execute([':id' => $license['product_id'], ':major' => $_POST['major'], ':minor' => $_POST['minor'], "release" => $_POST['release'], ':cycle' => $_POST['cycle']]);
                            $ver = $request->fetch(PDO::FETCH_ASSOC);
                            if(isset($ver['id']))
                            {
                                
                                if($ver['supported'] == 0)
                                {
                                    $supported = false;
                                } else {
                                    $supported = true;
                                }

                                if($ver['active'] == 0)
                                {
                                    $rq->execute([
                                        ":1" => $license['id'],
                                        ':2' => $license['product_id'],
                                        ':3' => $ver['id'],
                                        ':4' => 0,
                                        ':5' =>
                                        "Wersja aplikacji z której próbujesz skorzystać jest już zbyt stara. Zaaktualizuj aplikacje, aby jej dalej używać!",
                                        ':6' => $ip,
                                        ':7' => time(),
                                        ':8' => $_POST['instance'],
                                        ":9" => $_POST['type'],
                                    ]);
                                    die(response(false, "Wersja aplikacji z której próbujesz skorzystać jest już zbyt stara. Zaaktualizuj aplikacje, aby jej dalej używać!"));
                                }

                                if($ver['cycle'] != "stable")
                                {
                                    if($license['beta_tests'] != 1)
                                    {
                                        $rq->execute([
                                            ":1" => $license['id'],
                                            ':2' => $license['product_id'],
                                            ':3' => $ver['id'],
                                            ':4' => 0,
                                            ':5' =>
                                            "Nie posiadasz dostępu do tej nieoficjalnej wersji aplikacji!",
                                            ':6' => $ip,
                                            ':7' => time(),
                                            ':8' => $_POST['instance'],
                                            ":9" => $_POST['type'],
                                        ]);
                                        die(response(false, "Nie posiadasz dostępu do tej nieoficjalnej wersji aplikacji!"));
                                    }
                                }



                                $request = $pdo->prepare("SELECT `major`, `minor`, `release` FROM `products_version` WHERE cycle='stable' AND product_id=:id ORDER BY id DESC LIMIT 1");
                                $request->execute([':id' => $license['product_id']]);
                                $v = $request->fetch(PDO::FETCH_ASSOC);

                                if(isset($v['major']))
                                {
                                    $new_version = $v['major'] . "." . $v['minor'] . "." . $v['release'];
                                } else {
                                    $new_version = $_POST['major'] . "." . $_POST['minor'] . "." . $_POST['release'];
                                }

                                


                                $rq->execute([
                                    ":1" => $license['id'],
                                    ':2' => $license['product_id'],
                                    ':3' => $ver['id'],
                                    ':4' => true,
                                    ':5' => "",
                                    ':6' => $ip,
                                    ':7' => time(),
                                    ':8' => $_POST['instance'],
                                    ":9" => $_POST['type'],
                                ]);

                                
                                foreach(json_decode($_POST['logs'], true) as $log)
                                {
                                    $request = $pdo->prepare("SELECT `id` FROM `logs` WHERE log=:1");
                                    $request->execute([":1" => $log]);
                                    if($request->rowCount() == 0)
                                    {
                                        $request = $pdo->prepare("INSERT INTO `logs` (`time`, `serial_number`, `product_id`, `version_id`, `log`) VALUES (:1, :2, :3, :4, :5)");
                                        $request->execute([":1" => time(), ':2' => $_POST['serial_number'], ':3' => $license['product_id'], ':4' => $ver['id'],  ':5' => $log]);
                                    }

                                }

                                $request = $pdo->prepare("SELECT `login`, `uid` FROM `clients` WHERE id=:id");
                                $request->execute([':id' => $license['client_id']]);

                                $client = $request->fetch(PDO::FETCH_ASSOC);
                                response(true, "Licencja jest prawidłowa", array(

                                    "license_id" => $license['id'],
                                    "client_id" => $license['client_id'],
                                    "client_login" => $client['login'],
                                    "client_uid" => $client['uid'],
                                    "time" => time(),
                                    "beta_warning" => ($ver['cycle']!='stable'?1:0),
                                    "new_version" => $new_version, 
                                    "release_time" => $ver['release_time'],
                                    "supported" => $supported,
                                    "authors" => array(
                                        "ggELfXbLqY1M30R7peUH6wmvhKk=",
                                        "1AVyQTjJ6HkOF34gGgQMH+GIjYc="
                                    ),
                                )
                                );

                            } else {
                                $rq->execute([
                                    ":1" => $license['id'],
                                    ':2' => $license['product_id'],
                                    ':3' => 0,
                                    ':4' => 0,
                                    ':5' => "Wersja którą próbujesz uruchomić nie istnieje w systemie licencyjnym!",
                                    ':6' => $ip,
                                    ':7' => time(),
                                    ':8' => $_POST['instance'],
                                    ":9" => $_POST['type'],
                                ]);
                                response(false, "Wersja którą próbujesz uruchomić nie istnieje w systemie licencyjnym!");
                            }
                        } else {
                            $rq->execute([
                                ":1" => $license['id'],
                                ':2' => $license['product_id'],
                                ':3' => 0,
                                ':4' => 0,
                                ':5' => "Port UDP serwera TS3 jest inny niż podany w konfiguracji ({$_POST['ts_udp_port']})",
                                ':6' => $ip,
                                ':7' => time(),
                                ':8' => $_POST['instance'],
                                ":9" => $_POST['type'],
                            ]);
                            response(false, "Port UDP serwera TS3 jest inny niż podany w konfiguracji ({$_POST['ts_udp_port']})");
                        }
                    } else {
                        $rq->execute([
                            ":1" => $license['id'],
                            ':2' => $license['product_id'],
                            ':3' => 0,
                            ':4' => 0,
                            ':5' => "Adres IP serwera TS3 jest inny niż podany w konfiguracji ({$_POST['ts_ip']})",
                            ':6' => $ip,
                            ':7' => time(),
                            ':8' => $_POST['instance'],
                            ":9" => $_POST['type'],
                        ]);
                        response(false, "Adres IP serwera TS3 jest inny niż podany w konfiguracji ({$_POST['ts_ip']})");
                    }
                } else {
                    $rq->execute([
                        ":1" => $license['id'],
                        ':2' => $license['product_id'],
                        ':3' => 0,
                        ':4' => 0,
                        ':5' => "Adres IP z którego wykonujesz zapytanie jest inny niż podany w konfiguracji ($ip)",
                        ':6' => $ip,
                        ':7' => time(),
                        ':8' => $_POST['instance'],
                        ":9" => $_POST['type'],
                    ]);
                    response(false, "Adres IP z którego wykonujesz zapytanie jest inny niż podany w konfiguracji ($ip)");
                }
            } else {
                $rq->execute([
                    ":1" => $license['id'],
                    ':2' => $license['product_id'],
                    ':3' => 0,
                    ':4' => 0,
                    ':5' => "Licencja nie została skonfigurowana. Skonfiguruj ją w panelu licencyjnym!",
                    ':6' => $ip,
                    ':7' => time(),
                    ':8' => $_POST['instance'],
                    ":9" => $_POST['type'],
                ]);
                response(false, "Licencja nie została skonfigurowana. Skonfiguruj ją w panelu licencyjnym!");
            }
        } else {
            $rq->execute([
                ":1" => $license['id'],
                ':2' => $license['product_id'],
                ':3' => 0,
                ':4' => 0,
                ':5' => "Ta licencja obejmuje inną aplikacje (" . $product_name . ")",
                ':6' => $ip,
                ':7' => time(),
                ':8' => $_POST['instance'],
                ":9" => $_POST['type'],
            ]);
            response(false, "Ta licencja obejmuje inną aplikacje (" . $product_name . ")");
        }
    } else {
        response(false, "Nie odnaleziono licencji o podanym kluczu!");
    }
    


}

function response($success, $msg, $data = array())
{

    global $pdo;


    echo encrypt(json_encode(array("success" => $success, "msg" => $msg, "data" => $data)));
}

function encrypt($string) {

    $string = gzcompress(str_replace(["<?php", "?>", "<?PHP"], ["", "", ""], preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n",$string)));
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = rs(16);
    $secret_iv = rs(16);

    $key = hash('sha256', "*****************" . $secret_key, );
    $iv = substr(hash('sha256', $secret_iv), 0, 16);


    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);


    $output = rs(38) . $secret_iv . rs(29) . $secret_key . rs(38) . $output;

    $output = base64_encode($output);

    $output_array = str_split($output, 90);

    $output = implode("\n", $output_array);

    return $output;
}


function rs($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}