<?php

if($role == 'admin')
{

    if(isset($_POST['action']))
    {    
        if($_POST['action'] == 'block')
        {
            foreach(["id", "status"] as $key)
            {    
                if(!isset($_POST[$key]) || !strlen($_POST[$key]))
                { 
                    Api::_(false, "Nie wypełniłeś/aś wszystkich pól!");
                }
            }
            $status = 0;
            if($_POST['status'] == "true")
            {
                $status = 1;
            }

            $request = $pdo->prepare("UPDATE `clients` SET blocked=:1 WHERE id=:2");
            $request->execute([':1' => $status, ':2' => $_POST['id']]);
            Api::_(true, "Pomyslnie zablokowano klienta!");
        } elseif($_POST['action'] == 'password')
        { 
            foreach(["id"] as $key)
            {    
                if(!isset($_POST[$key]) || !strlen($_POST[$key]))
                { 
                    Api::_(false, "Nie wypełniłeś/aś wszystkich pól!");
                }
            }
            $password = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(16/strlen($x)) )),1,16);
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $request = $pdo->prepare("UPDATE `clients` SET hashed_password=:1 WHERE id=:2");
            $request->execute([':1' => $hash, ':2' => $_POST['id']]);
            Api::_(true, "Nowe hasło: " . $password);

        }
    } else { 
        Api::_(false, "Nie wypełniłeś wszystkich pól!");
    }
}

