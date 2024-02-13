<?php
$success = false;
if(isset($_GET['id']))
{ 
    $request = $pdo->prepare("SELECT `id`, `title`,`created`,`updated`,`priority`,`creator_id`,(SELECT `status` FROM tickets_status WHERE ticket_id=t.id) as `status` FROM `tickets` t WHERE t.id=:id");
    $request->execute([":id" => $_GET['id']]);
    $data = $request->fetch(PDO::FETCH_ASSOC);
    if($data['creator_id'] == Session::get("client_id"))
    {
        $success = true;
    }
}

if(!$success)
{ 
    die("<script>window.location.href='".URL."/Home'");
}
?>


<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">

        <div class="col-xxl-4 col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <h5 class="card-header">Informacje</h5>
                <hr class="m-0">
                <div class="card-body">
                    <h6>Data utworzenia</h6>
                    <?=date('d.m.Y H:i:s', $data['created'])?>
                </div>     
                <hr class="m-0">
                <div class="card-body">
                    <h6>Status</h6>
                    <?=$data['status']==1?'<span class="badge bg-dark">ZAMKNIĘTY</span>':'<span class="badge bg-success">OTWARTY</span>'?>
                </div>     
                <hr class="m-0">
                <div class="card-body">
                    <h6>Priorytet</h6>
                    <?php
                    if($data['priority'] == 0)
                    {
                        echo '<span class="badge bg-danger">WAŻNY</span>';
                    } elseif($data['priority'] == 1)
                    {
                        echo '<span class="badge bg-info">NORMALNY</span>';
                    } elseif($data['priority'] == 2)
                    {
                        echo '<span class="badge bg-secondary">NISKI</span>';
                    }
                    ?>
                </div>   
            </div>     
        </div>
        <div class="col-xxl-8 col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <h5 class="card-header">Wiadomości</h5>
                <div class="card-body">

                <?php
                $request = $pdo->prepare("SELECT * FROM `ticket_messages` WHERE ticket_id=:id ORDER BY id ASC");
                $request->execute([':id' => $_GET['id']]);
                $messages = $request->fetchAll(PDO::FETCH_ASSOC);
                foreach($messages as $message): ?>

                    <div class="row">

                        <?php
                        if($message['client_id'] == Session::get("client_id")): ?>
                        <div class="col-xl-6">
                        <div class="card bg-primary text-white mb-3">
                        <?php else: ?>
                        <div class="col-xl-6" style="float: right;">
                        <div class="card bg-warning text-white mb-3">
                        <?php endif; ?>
                                <div class="card-body">
                                    <?=htmlspecialchars($message['message'])?>
                                    <br><span class="text-muted"><?=date('d.m.Y H:i:s', $message['time'])?></span>
                                </div>
                            </div>
                        </div>

                    </div>

                <?php endforeach; ?>
                </div>
            </div>
        </div>
            
    </div>
</div>