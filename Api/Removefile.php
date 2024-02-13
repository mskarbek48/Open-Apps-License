<?php

if($role == 'admin')
{
    foreach(["id"] as $key)
    {    
        if(!isset($_POST[$key]) || !strlen($_POST[$key]))
        { 
            Api::_(false, "Nie wypełniłeś/aś wszystkich pól!");
        }
    }

    $request = $pdo->prepare("UPDATE `products_version` SET file_binary=null WHERE id=:id");
    $request->execute([':id' => $_POST['id']]);

    Api::_(true, "Plik został usunięty", $_POST);

}

