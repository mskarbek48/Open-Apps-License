<?php

if(isset($_POST['action']))
{
    if($_POST['action'] == 'register')
    {    
        foreach(["username", "uid", "password", "password2"] as $key)
        { 
            if(!isset($_POST[$key]) || !strlen($_POST[$key]))
            { 
                die(Api::_(false, "Wypełnij wszystkie pola!"));
            }
        }
        if(strlen($_POST['username']) > 3 && strlen($_POST['username']) < 32)
        {
            if(strlen($_POST['password']) > 8 && strlen($_POST['password']) < 32)
            { 
                if($_POST['password'] == $_POST['password2'])
                {
                    if(strlen($_POST['uid']) == 28)
                    { 

                        $request = $pdo->prepare("SELECT `id` FROM `clients` WHERE login=:login");
                        $request->execute([':login' => $_POST['username']]);
                        if($request->rowCount() == 0)
                        {
                            $request = $pdo->prepare("INSERT INTO `clients` (`created`, `updated`, `login`, `hashed_password`, `blocked`, `block_reason`, `role`, `uid`) VALUES (:1, :2, :3, :4, :5, :6, :7, :8)");
                            $request->execute([':1' => time(), ':2' => time(), ':3' => $_POST['username'], ':4' => password_hash($_POST['password'], PASSWORD_BCRYPT), ':5' => 0, ':6' => NULL, ':7' => "client", ":8" => $_POST['uid']]);
                            Api::_(true, "Konto zostało założone. Zaloguj się!");
                        } else { 
                            Api::_(false, "Użytkownik o takim loginie już istnieje!");
                        }
                    } else { 
                        Api::_(false, "To nie jest uid z TS3!");
                    }
                } else { 
                    Api::_(false, "Podane hasła różnią się!");
                }
            } else { 
                Api::_(false, "Hasło musi posiadać od 3 do 32 znaków!");
            }
        } else {
            Api::_(false, "Login musi posiadać od 3 do 32 znaków!");
        }
    } elseif($_POST['action'] == 'login')
    {
        foreach(['username', 'password'] as $key)
        { 
            if(!isset($_POST[$key]) || !strlen($_POST[$key]))
            { 
                die(Api::_(false, "Wypełnij wszystkie pola!"));
            }
        }
        $request = $pdo->prepare("SELECT `hashed_password`, `blocked`, `id` FROM `clients` WHERE login=:login");
        $request->execute([':login' => $_POST['username']]);
        $data = $request->fetch(PDO::FETCH_ASSOC);
        if(isset($data['hashed_password']))
        {
            if(password_verify($_POST['password'], $data['hashed_password']))
            {

                if($data['blocked'] != 1)
                {
                    $request = $pdo->prepare("UPDATE `clients` SET `last_login`=:1 WHERE login=:login");
                    $request->execute([':1' => time(), ':login' => $_POST['username']]);
                    Session::set("login", $_POST['username']);
                    Session::set("client_id", $data['id']);
                    Session::login();
                    Api::_(true, "Zostałeś zalogowany!");

                } else {
                    Api::_(false, "Twoje konto jest zablokowane");
                }
            } else {
                Api::_(false, "Hasło lub login jest nieprawidłowe!");
            }
        } else {
            Api::_(false, "Hasło lub login jest nieprawidłowe!");
        }
    }
}