<?php

if($role == 'admin')
{
    foreach(["id", "action", "status"] as $key)
    {    
        if(!isset($_POST[$key]) || !strlen($_POST[$key]))
        { 
            Api::_(false, "Nie wypełniłeś/aś wszystkich pól!");
        }
    }

    if($_POST['status'] == "true")
    {
        $status = "1";
    } else { 
        $status = "0";
    }

    if($_POST['action'] == 'active')
    { 
        $request = $pdo->prepare("UPDATE `products_version` SET active=:1 WHERE id=:id");
        $request->execute([':1' => $status, ':id' => $_POST['id']]);
    } elseif($_POST['action'] == 'supported')
    { 
        $request = $pdo->prepare("UPDATE `products_version` SET supported=:1 WHERE id=:id");
        $request->execute([':1' => $status, ':id' => $_POST['id']]);
    }

    Api::_(true, "", $_POST);

}

