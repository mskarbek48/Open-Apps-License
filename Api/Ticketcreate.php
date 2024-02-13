<?php



foreach(["message", "priority", "topic", "agree"] as $once)
{    
    if(!isset($_POST[$once]) || !strlen($_POST[$once]))
    {
        die(Api::_(false, "Nie wypełniłeś wszystkich wymaganych pól!"));
    }
}

if(!in_array($_POST['priority'], [0,1,2]))
{
    die(Api::_(false, "Niepoprawny priorytet!"));
}

$request = $pdo->prepare("INSERT INTO `tickets` (`creator_id`, `title`, `created`, `updated`, `priority`) VALUES (:1, :2, :3, :4, :5)");
$request->execute([':1' => Session::get("client_id"), ':2' => $_POST['topic'], ':3' => time(), ':4' => time(), ":5" => $_POST['priority']]);

$id_ticket = $pdo->lastInsertId();

try {
    $request = $pdo->prepare("INSERT INTO `ticket_messages` (`ticket_id`, `client_id`, `time`, `message`) VALUES (:1, :2, :3, :4)");
    $request->execute([":1" => $id_ticket, ':2' => Session::get("client_id"), ":3" => time(), ':4' => $_POST['message']]);
} catch (Exception $e)
{
    $pdo->query("DELETE FROM `tickets` WHERE id=" . $id_ticket);
    Api::_(false, "Błąd bazy danych (1)!");
}

try {
    $request = $pdo->prepare("INSERT INTO `tickets_status` (`ticket_id`, `time`, `status`, `client_id`) VALUES (:1, :2, :3, :4)");
    $request->execute([':1' => $id_ticket, ':2' => time(), ':3' => 0, ':4' => Session::get("client_id")]);
} catch(Exception $e)
{
    $pdo->query("DELETE FROM `tickets` WHERE id=" . $id_ticket);
    $pdo->query("DELETE FROM `ticket_messages` WHERE ticket_id=" . $id_ticket);
    Api::_(false, "Błąd bazy danych (2)!");
}


Api::_(true, "Pomyślnie utworzono zgłoszenie!");