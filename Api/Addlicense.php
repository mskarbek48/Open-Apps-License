<?php

if($role == 'admin')
{
    foreach(["client", "app", "serial", "beta"] as $key)
    { 
        if(!isset($_POST[$key]) || !strlen($_POST[$key]))
        { 
            die(Api::_(false, "Wypełnij wszystkie pola!", $key));
        }
    }

    $request = $pdo->prepare("SELECT `id` FROM `licenses` WHERE serial_number=:1");
    $request->execute([':1' => $_POST['serial']]);
    if($request->rowCount() == 0)
    { 

        $request = $pdo->prepare("SELECT `id` FROM clients WHERE id=:1");
        $request->execute([':1' => $_POST['client']]);
        if($request->rowCount() == 1)
        { 

            if(!empty(Products::getProductById($_POST['app'])))
            {

                $request = $pdo->prepare("INSERT INTO `licenses` (`product_id`,`client_id`, `serial_number`, `beta_tests`, `created`, `modified`) VALUES (:1, :2, :3, :4, :5, :6)");
                $request->execute([
                    ":1" => $_POST['app'],
                    ":2" => $_POST['client'],
                    ":3" => $_POST['serial'],
                    ":4" => ($_POST['beta'] == "false" ? 0 : 1),
                    ":5" => time(),
                    ":6" => time(),
                ]);

                Api::_(true, "Pomyślnie dodano licencje!");

            } else { 
                Api::_(false, "Nie ma takiej aplikacji!");
            }
        } else { 
            Api::_(false, "Nie ma takiego klienta!");
        }
    } else { 
        Api::_(false, "Licencja z takim kluczem już istnieje!");
    }
} else {
    Api::_(false, "Brak uprawnień!");
}