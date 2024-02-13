<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">

        <div class="col-xl">
            <div class="card">
                <h5 class="card-header">Klienci</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Login</th>
                                <th>Utworzono</th>
                                <th>Zaaktualizowano</th>
                                <th>Ostatnie logowanie</th>
                                <th>Blokada</th>
                                <th>Rola</th>
                                <th>Reset hasła</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php
                            $request = $pdo->query("SELECT * FROM `clients`");
                            $data = $request->fetchAll(PDO::FETCH_ASSOC);
                            foreach($data as $once): ?>
                                <tr>
                                    <td><?=$once['login']?></td>
                                    <td><?=date('Y-m-d H:i:s', $once['created'])?></td>
                                    <td><?=date('Y-m-d H:i:s', $once['updated'])?></td>
                                    <td><?=date('Y-m-d H:i:s', $once['last_login'])?></td>
                                    <td>                            
                                        <input class="form-check-input btn-blocked" type="checkbox" id="<?=$once['id']?>" <?=$once['blocked']==1?"checked":""?>>
                                    </td>
                                    <td><?=$once['role']=='admin'?'<span class="badge bg-danger">ADMIN</span>':'<span class="badge bg-info">KLIENT</span>'?></td>
                                    <td><button type="button" title="Zresetuj hasło" class="btn btn-warning btn-xs btn-password" value="<?=$once['id']?>"><i class="fa-solid fa-lock"></i></button>
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
$(".btn-blocked").on("change", function()
{
    $.ajax({
        type: "POST",
        url: "<?=URL?>/Api/EditClient",
        data: {"id": this.id, "action": "block", "status": this.checked},
        success: function(data)
        {
            console.log(data);
        }
    });
});
$(".btn-password").on("click", function()
{
    $.ajax({
        type: "POST",
        url: "<?=URL?>/Api/EditClient",
        data: {"id": this.value, "action": "password"},
        success: function(data)
        {
            data = JSON.parse(data);
            swal("Hasło", data.message, "success");
        }
    });
});
</script>

<!-- / Content -->