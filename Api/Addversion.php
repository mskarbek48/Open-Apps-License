<?php

if($role == 'admin')
{
    foreach(["major", "minor", "release", "cycle", "php", "comments", "id"] as $key)
    {    
        if(!isset($_POST[$key]) || !strlen($_POST[$key]))
        { 
            Api::_(false, "Nie wypełniłeś/aś wszystkich pól!", $_POST);
        }
    }

    $request = $pdo->prepare("SELECT `id` FROM `products` WHERE id=:id");
    $request->execute([':id' => $_POST['id']]);
    if($request->rowCount() > 0)
    { 
        $file = "";
        if(isset($_FILES['file']['tmp_name']) && strlen(($_FILES['file']['tmp_name'])))
        {
            $file = file_get_contents($_FILES['file']['tmp_name']);
        }

        $request = $pdo->prepare("INSERT INTO `products_version` 
        (`product_id`, `major`, `minor`, `release`, `release_time`, `cycle`, `php_version`, `file_binary`, `supported`, `active`, `comments`)
        VALUES
        (:0,:1, :2, :3, :4, :5, :6, :7, :8, :9, :10)
        ");
        $request->execute([
            ":0" => $_POST['id'],
            ":1" => $_POST['major'],
            ":2" => $_POST['minor'],
            ":3" => $_POST['release'],
            ":4" => time(),
            ":5" => $_POST['cycle'],
            ":6" => $_POST['php'],
            ":7" => $file,
            ":8" => 1,
            ":9" => 1,
            ":10" => $_POST['comments'],
        ]);
        Api::_(true, "Pomyślnie dodano wersje!");

    } else { 
        Api::_(false, "Nie ma takiego produktu!");
    }
}

