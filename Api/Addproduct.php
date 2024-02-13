<?php

if($role == 'admin')
{
    foreach(["name", "category", "description"] as $key)
    { 
        if(!isset($_POST[$key]) || !strlen($_POST[$key]))
        { 
            die(Api::_(false, "Wypełnij wszystkie pola!"));
        }
    }

    $request = $pdo->prepare("SELECT `id` FROM `products_category` WHERE id=:id");
    $request->execute([':id' => $_POST['category']]);
    if($request->rowCount() != 0)
    {

        $request = $pdo->prepare("SELECT `id` FROM `products` WHERE name=:name");
        $request->execute([':name' => $_POST['name']]);
        if($request->rowCount() == 0)
        { 
            $request = $pdo->prepare("INSERT INTO `products` (`name`, `created`, `category`, `description`, `icon`, `color`) VALUES (:1, :2, :3, :4, :5, :6)");
            $request->execute([':1' => $_POST['name'], ':2' => time(), ':3' => $_POST['category'], ":4" => $_POST['description'], ":5" => $_POST['icon'], ':6' => $_POST['color']]);
            Api::_(true, "Utworzono produkt!");
        } else { 
            Api::_(false, "Produkt o takiej nazwie już istnieje!");
        }

    } else {
        Api::_(false, "Nie ma takiej kategorii!");
    }

} else {
    Api::_(false, "Brak uprawnień!");
}