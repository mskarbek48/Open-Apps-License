<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">

        <div class="col-xl">
            <div class="card">
                <h5 class="card-header">Klienci</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped" id="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>IP</th>
                                <th>Klient Login</th>
                                <th>Klient UID</th>
                                <th>Fragment core'a</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php
                            $request = $pdo->query("SELECT * FROM `raports`");
                            $data = $request->fetchAll(PDO::FETCH_ASSOC);
                            foreach($data as $once): ?>

                                <?php $json = json_decode($once['data'], true); ?>

                                <?php
                                $request = $pdo->prepare("SELECT `login`, `uid` FROM `clients` WHERE id=(SELECT `client_id` FROM `licenses` WHERE serial_number=:1) LIMIT 1");
                                $request->execute([':1' => $once['serial_number']]);
                                $client = $request->fetch(PDO::FETCH_ASSOC);
                                ?>

                                <tr>
                                    <td><?=$once['id']?></td>
                                    <td><?=htmlspecialchars($once['ip'])?></td>
                                    <td><?=htmlspecialchars($client['login'])?></td>
                                    <td><?=htmlspecialchars($client['uid'])?></td>
                                    <td><?=htmlspecialchars($json['core_fragment'])?></td>
                                    <td><?=date('d.m.Y H:i:s', $once['time'])?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#table").dataTable({});
</script>



<!-- / Content -->