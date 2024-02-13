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

    if($_POST['action'] == 'beta_tests')
    { 
        $request = $pdo->prepare("UPDATE `licenses` SET beta_tests=:1 WHERE id=:id");
        $request->execute([':1' => $status, ':id' => $_POST['id']]);
    } elseif($_POST['action'] == 'block')
    { 
        $request = $pdo->prepare("UPDATE `licenses` SET `blocked`=:1 WHERE id=:id");
        $request->execute([':1' => $status, ':id' => $_POST['id']]);
    }

    Api::_(true, "", $_POST);

}

