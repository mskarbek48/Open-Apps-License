<?php
header('Content-Type: application/json; charset=utf-8');
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$method = $_SERVER['REQUEST_METHOD'];


if($method == "GET" && isset($_GET['serial_number']))
{

    
    $request = $pdo->prepare("SELECT * FROM `licenses` WHERE serial_number=:serial");
    $request->execute([':serial' => $_GET['serial_number']]);
    $license = $request->fetch(PDO::FETCH_ASSOC);


    if(isset($license['id']))
    {
        if(!$license['blocked'])
        {

            if(isset($path[2]))
            {

                $request = $pdo->prepare("SELECT * FROM `vpn` WHERE ip=:1");
                $request->execute([':1' => $path[2]]);
                $vpn = $request->fetch(PDO::FETCH_ASSOC);
                if(isset($vpn['id']))
                {
                    die(json_encode(array(
                        "success" => true,
                        "results" => json_decode($vpn['results'], true),
                        "comment" => $vpn['comment'],
                    )));
                } else {
                    $check = file_get_contents("http://ip-api.com/json/{$path[2]}?fields=status,message,country,countryCode,region,regionName,city,zip,isp,org,as,query,proxy,hosting,mobile");
                    $success = false;
                    $results = [];
                    $comments = [];
                    if($check)
                    {
                        $data = json_decode($check, true);

                        $results = [
                            "info" => [
                                "country" => $data['country'],
                                "cc" => $data['countryCode'],
                                "city" => $data['city'],
                                "region" => $data['regionName'],
                                "isp" => $data['isp'],
                                "org" => $data['org'],
                            ],
                            "is_proxy" => $data['proxy'],
                            "is_hosting" => $data['hosting'],
                            "is_mobile" => $data['mobile'],
                        ];

                        $request = $pdo->prepare("INSERT INTO `vpn` (`ip`, `results`) VALUES (:1, :2)");
                        $request->execute([':1' => $path[2], ':2' => json_encode($results)]);

                        die(json_encode(array(
                            "success" => true,
                            "results" => $results,
                            "comment" => "",
                        )));

                    }

                }

            } else {
                die(json_encode(array(
                    "success" => false,
                    "message" => "Missing ip argument",
                )));
            }
        } else {
            die(json_encode(array(
                "success" => false,
                "message" => "License is banned.",
            )));
        }
    } else {
        die(json_encode(array(
            "success" => false,
            "message" => "License not found",
        )));
    }
} else {
    die(json_encode(array(
        "success" => false,
        "message" => "Invalid request",
    )));
}

function ip_in_range( $ip, $range ) {
    if ( strpos( $range, '/' ) === false ) {
        $range .= '/32';
    }
    // $range is in IP/CIDR format eg 127.0.0.1/24
    list( $range, $netmask ) = explode( '/', $range, 2 );
    $range_decimal = ip2long( $range );
    $ip_decimal = ip2long( $ip );
    $wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
    $netmask_decimal = ~ $wildcard_decimal;
    return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
}