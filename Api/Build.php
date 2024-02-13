<?php

if(isset($_GET['data']))
{
    $data = explode(":", $_GET['data']);
    if(isset($data[2]))
    {
        $lic_id = $data[0];
        $app_id = $data[1];
        $ver_id = $data[2];

        $request = $pdo->prepare("SELECT * FROM `licenses` WHERE client_id=:1 AND id=:2");
        $request->execute([':1' => Session::get("client_id"), ":2" => $lic_id]);
        $data = $request->fetch(PDO::FETCH_ASSOC);
        $license = $data;
        if(isset($data['product_id']))
        {
            $request = $pdo->prepare("SELECT * FROM `products` WHERE id=:1");
            $request->execute([':1' => $data['product_id']]);
            $data = $request->fetch(PDO::FETCH_ASSOC);
            if(isset($data['id']))
            {
                if($data['id'] == $app_id)
                {
                    $request = $pdo->prepare("SELECT * FROM `products_version` WHERE product_id=:1 AND id=:2");
                    $request->execute([':1' => $data['id'], ':2' => $ver_id]);
                    $data = $request->fetch(PDO::FETCH_ASSOC);

                    if($data['cycle'] == 'stable')
                    {
                        if(isset($data['file_binary']))
                        {
                            header('Content-Type: application/octet-stream');
                            header('Content-Disposition: attachment; filename="build_' . rand(1000,9999999) . '.zip"');
                            echo $data['file_binary'];
                        } else {
                            Api::_(false, "Nie odnaleziono pliku!");
                        }
                    } elseif($data['cycle'] != 'pre-alpha')
                    {
                        if($license['beta_tests'])
                        {
                            if(isset($data['file_binary']))
                            {
                                sleep(3);
                                header('Content-Type: application/octet-stream');
                                header('Content-Disposition: attachment; filename="build_' . rand(1000,9999999) . '.zip"');
                                echo $data['file_binary'];
                            } else {
                                Api::_(false, "Nie odnaleziono pliku!");
                            }
                        } else {
                            Api::_(false, "Nie masz dostępu do tej wersji!");
                        }
                    } else {
                        Api::_(false, "Nie masz dostępu do tej wersji!");
                    }


                } else {
                    Api::_(false, "Niepoprawne argumenty!");
                }
            } else {
                Api::_(false, "Nie odnaleziono takiego produktu!");
            }
        } else {
            Api::_(false, "Nie odnaleziono takiej licencji!");
        }
    } else {
        Api::_(false, "Niepoprawne argumenty!");
    }
} else {
    Api::_(false, "Brakujące argumenty!");
}