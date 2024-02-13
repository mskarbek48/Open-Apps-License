<?php

if($role == 'admin' && isset($_POST['client']))
{
    foreach(["client", "app", "vps_ip", "ts_ip", "ts_udp_port", "beta", "block", "license_id", "block_reason"] as $key)
    {    
        if(!isset($_POST[$key]))
        { 
            Api::_(false, "Nie wypełniłeś/aś wszystkich pól!", $key);
        } else { 
            if(!strlen($_POST[$key]))
            { 
                $_POST[$key] = null;
            }
        }
    }

    $request = $pdo->prepare("SELECT `id` FROM `clients` WHERE id=:id");
    $request->execute([':id' => $_POST['client']]);
    if($request->rowCount() != 0)
    {

        $request = $pdo->prepare("SELECT `id` FROM licenses WHERE id=:id");
        $request->execute([':id' => $_POST['license_id']]);
        if($request->rowCount() != 0)
        {
            
            $request = $pdo->prepare("UPDATE `licenses` SET product_id=:1, client_id=:2, beta_tests=:3, modified=:4, blocked=:5, block_reason=:6 WHERE id=:7");
            $request->execute([
                ":1" => $_POST['app'],
                ":2" => $_POST['client'],
                ":3" => ($_POST['beta'] == 'on' ? '1' : '0'),
                ":4" => time(),
                ':5' => ($_POST['block'] == 'on' ? '1' : '0'),
                ":6" => (strlen($_POST['block_reason']) ? $_POST['block_reason'] : null),
                ":7" => $_POST['license_id'],
            ]);

            $request = $pdo->prepare("SELECT `id` FROM `licenses_settings` WHERE license_id=:id");
            $request->execute([':id' => $_POST['license_id']]);

            if($request->rowCount() == 0)
            {
                $request = $pdo->prepare("INSERT INTO `licenses_settings` (`license_id`, `vps_ip`, `ts_ip`, `ts_udp_port`, `modified`) VALUES (:1, :2, :3, :4, :5)");
                $request->execute([':1' => $_POST['license_id'], ':2' => str_replace(" ", "",$_POST['vps_ip']), ":3" => str_replace(" ", "",$_POST['ts_ip']), ":4" =>str_replace(" ", "", $_POST['ts_udp_port']), ':5' => time()]);
            } else {
                $request = $pdo->prepare("UPDATE `licenses_settings` SET vps_ip=:1, ts_ip=:2, ts_udp_port=:3, modified=:4 WHERE license_id=:5");
                $request->execute([':1' => str_replace(" ", "",$_POST['vps_ip']), ':2' => str_replace(" ", "",$_POST['ts_ip']), ':3' => str_replace(" ", "",$_POST['ts_udp_port']), ':4' => time(), ':5' => $_POST['license_id']]);
            }
            Api::_(true, "Licencja została zaaktualizowana!");

            Api::_(true, "Pomyślnie zaaktualizowano licencje!");

        } else { 
            Api::_(false, "Nie ma takiej licencji!");
        }
    } else { 
        Api::_(false, "Nie ma takiego klienta!");
    }
 
} elseif(!isset($_POST['client'])) { 

    foreach(["vps_ip", "ts_ip", "ts_udp_port","license_id"] as $key)
    {    
        if(!isset($_POST[$key]))
        { 
            Api::_(false, "Nie wypełniłeś/aś wszystkich pól!", $key);
        } else { 
            if(!strlen($_POST[$key]))
            { 
                $_POST[$key] = null;
            }
        }
    }

    $request = $pdo->prepare("SELECT * FROM `licenses` WHERE client_id=:1 AND id=:2");
    $request->execute([':1' => Session::get("client_id"), ":2" => $_POST['license_id']]);
    if($request->rowCount() != 0)
    {

        $request = $pdo->prepare("SELECT `id` FROM `licenses_settings` WHERE license_id=:id");
        $request->execute([':id' => $_POST['license_id']]);

        if($request->rowCount() == 0)
        {
            $request = $pdo->prepare("INSERT INTO `licenses_settings` (`license_id`, `vps_ip`, `ts_ip`, `ts_udp_port`, `modified`) VALUES (:1, :2, :3, :4, :5)");
            $request->execute([':1' => $_POST['license_id'], ':2' => str_replace(" ", "",$_POST['vps_ip']), ":3" => str_replace(" ", "",$_POST['ts_ip']), ":4" => str_replace(" ", "",$_POST['ts_udp_port']), ':5' => time()]);
        } else {
            $request = $pdo->prepare("UPDATE `licenses_settings` SET vps_ip=:1, ts_ip=:2, ts_udp_port=:3, modified=:4 WHERE license_id=:5");
            $request->execute([':1' => str_replace(" ", "",$_POST['vps_ip']), ':2' => str_replace(" ", "",$_POST['ts_ip']), ':3' => str_replace(" ", "",$_POST['ts_udp_port']), ':4' => time(), ':5' => $_POST['license_id']]);
        }
        Api::_(true, "Licencja została zaaktualizowana!");

    } else {
        Api::_(false, "Nie odnaleziono takiej licencji!");
    }

}

