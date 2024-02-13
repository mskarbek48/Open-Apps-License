<?php

if($role == 'admin')
{
    foreach(["name", "category", "description", "id", "color", "icon"] as $key)
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

        $request = $pdo->prepare("SELECT `id` FROM `products` WHERE id=:id");
        $request->execute([':id' => $_POST['id']]);
        if($request->rowCount() == 1)
        { 
            $request = $pdo->prepare("UPDATE `products` SET name=:name, description=:description, category=:category, color=:color, icon=:icon WHERE id=:id");
            $request->execute([':name' => $_POST['name'], ':description' => $_POST['description'], ':category' => $_POST['category'], ':color' => $_POST['color'], ':icon' => $_POST['icon'], ':id' => $_POST['id']]);
            Api::_(true, "Zaaktualizowano produkt!");
        } else { 
            Api::_(false, "Produkt o takim id nie istnieje!");
        }

    } else {
        Api::_(false, "Nie ma takiej kategorii!");
    }

} else {
    Api::_(false, "Brak uprawnień!");
}