<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row mb-3">
        <div class="col-xl-12">
            <a href="<?=URL?>/TicketCreate" class="btn btn-success">Utwórz zgłoszenie pomocy</a>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <h5 class="card-header">Tickety</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tytuł</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Priorytet</th>
                                <th>Aktualizacja</th>
                                <th>Opcje</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">

                        <?php
                        $request = $pdo->prepare("SELECT `id`, `title`,`created`,`updated`,`priority`,(SELECT `status` FROM tickets_status WHERE ticket_id=t.id) as `status` FROM `tickets` t WHERE creator_id=:id");
                        $request->execute([':id' => Session::get("client_id")]);
                        $tickets = $request->fetchAll(PDO::FETCH_ASSOC);
                        foreach($tickets as $ticket): ?>

                            <tr>
                                <th><?=htmlspecialchars($ticket['title'])?></th>
                                <td><?=date('d.m.Y H:i:s', $ticket['created'])?></td>
                                <td>
                                    <?=$ticket['status']==1?'<span class="badge bg-dark">ZAMKNIĘTY</span>':'<span class="badge bg-success">OTWARTY</span>'?>
                                </td>
                                <td>
                                    <?php
                                    if($ticket['priority'] == 0)
                                    {
                                        echo '<span class="badge bg-danger">WAŻNY</span>';
                                    } elseif($ticket['priority'] == 1)
                                    {
                                        echo '<span class="badge bg-info">NORMALNY</span>';
                                    } elseif($ticker['priority'] == 2)
                                    {
                                        echo '<span class="badge bg-secondary">NISKI</span>';
                                    }
                                    ?>
                                </td>
                                <td><?=date('d.m.Y H:i:s', $ticket['updated'])?></td>
                                <td><a href="<?=URL?>/Ticket?id=<?=$ticket['id']?>" class="btn-sm badge btn btn-primary">Wyświetl</a></td>
                            </tr>

                        
                        <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>