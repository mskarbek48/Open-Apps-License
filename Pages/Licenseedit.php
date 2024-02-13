<!-- Content -->
<?php

if (isset($_GET['license_id'])) {
    $request = $pdo->prepare("SELECT * FROM `licenses` WHERE id=:id AND client_id=:1");
    $request->execute([':id' => $_GET['license_id'], ':1' => Session::get("client_id")]);

    if ($request->rowCount() > 0) {
        $license = $request->fetch(PDO::FETCH_ASSOC);

        $request = $pdo->prepare("SELECT * FROM `licenses_settings` WHERE license_id=:1");
        $request->execute([':1' => $_GET['license_id']]);
        $settings = $request->fetch(PDO::FETCH_ASSOC);

        $product = Products::getProductById($license['product_id']);

    } else {
        die(header("Location: " . URL . "/Licenses"));
    }
} else {
    die(header("Location: " . URL . "/Licenses"));
}
?>
<style>
    .new-version
    {
        font-weight: bold;
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y">


    <div class="row">

        <div class="col-lg-12">

            <?php if ($license['blocked'] == 1): ?>
            <div class="alert alert-danger" role="alert">
                <b>Twoja licencja jest zablokowana!</b>
                <p style="">Skontakuj się z nami, aby poznać szczegóły!</p>
                <p style="margin: 0;">Powód blokady: <b><?= strlen($license['block_reason']) ? $license['block_reason'] : "Nieznany" ?></b></p>
                <p style="margin: 0;">Blokada wygasa: <b><?= strlen($license['unblock_time']) ? date('d.m.Y H:i:s', $license['unblock_time']) : "Nigdy" ?></b></p>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">

                    <h6 class="text-muted">Edycja licencji <?= $license['id'] ?></h6>

                    <div class="nav-align-top mb-4">
                        <div class="col-xl-6">
                            <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-justified-home"
                                        aria-controls="navs-pills-justified-home" aria-selected="true"><i
                                            class="tf-icons bx bx-home"></i> Informacje</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-justified-profile"
                                        aria-controls="navs-pills-justified-profile" aria-selected="false"><i
                                            class="tf-icons bx bx-user"></i> Ustawienia</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-justified-messages"
                                        aria-controls="navs-pills-justified-messages" aria-selected="false"><i
                                            class="tf-icons bx bx-message-square"></i> Zapytania</button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                                        data-bs-target="#navs-pills-download"
                                        aria-controls="navs-pills-download" aria-selected="false"><i
                                            class="tf-icons fa-solid fa-download"></i> Pobierz</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="navs-pills-justified-home" role="tabpanel">
                                <div class="alert alert-primary" role="alert">
                                    <b>Hej! Dobrze że jesteś!</b>
                                    <p style="margin: 0;">Twoja aplikacja ma w konfiguracji miejsce na podanie kluczu
                                        licencyjnego. To właśnie ten klucz, który znajduje się poniżej musisz wpisać w
                                        konfiguracji!</p>
                                </div>
                                <h5 class="" style="margin-bottom: 0;">Klucz licencyjny</h5>
                                <p style="margin-top: 0; margin-bottom: 10px;"><?= $license['serial_number'] ?></p>

                                <h6 class="mt-3" style="margin-bottom: 0;">Aplikacja</h6>
                                <span><?= $product->getName() ?></span>

                                <h6 class="mt-2" style="margin-bottom: 0;">Data utworzenia/zakupu</h6>
                                <span><?= date('d.m.Y H:i:s', $license['created']) ?></span>

                                <h6 class="mt-2" style="margin-bottom: 0;">Data modyfikacji</h6>
                                <span><?= date('d.m.Y H:i:s', $license['modified']) ?></span>

                                <h6 class="mt-2" style="margin-bottom: 0;">Dostęp do beta-testów</h6>
                                <span><?= $license['beta_tests'] == 1 ? 'Tak' : 'Nie' ?></span>

                                <h6 class="mt-2" style="margin-bottom: 0;">Zablokowana</h6>
                                <span><?= $license['blocked'] == 1 ? 'Tak - ' . $license['block_reason'] : 'Nie' ?></span>
                            </div>
                            <div class="tab-pane fade" id="navs-pills-justified-profile" role="tabpanel">

                                <div class="alert alert-primary" role="alert">
                                    <b>Przeczytaj mnie!</b>
                                    <p style="margin: 0;">Przyszedł czas na skonfigurowanie Twojej licencji. Tutaj dużo
                                        klientów popełnia błąd. Musisz pamiętać, że IP twojej maszyny może mieć format
                                        ipv6 (np. 0000:2222:204ed) lub ipv4 (np. 1.1.1.1)
                                        Dlatego, po wpisaniu kluczu licencyjnego <b>warto uruchomić testowo aplikacje i
                                            zajrzeć w zakładke "zapytania", aby sprawdzić w jaki sposób nasz system
                                            widzi adres IP Twojej maszyny, następnie wpisać go tutaj.</b>
                                        Pamiętaj! Gdy masz aplikacje na tym samym IP co serwer TeamSpeak, w konfiguracji
                                        i tak MUSISZ wpisać ip numeryczne serwera TS3, tak samo jak tutaj.
                                    </p>
                                </div>

                                <form id="edit_license">
                                    <div class="form-group mb-3">
                                        <label class="form-label">IP VPS'a</label>
                                        <input type="text" name="vps_ip"
                                            value="<?= isset($settings['vps_ip']) ? $settings['vps_ip'] : "" ?>"
                                            class="form-control">
                                    </div>


                                    <div class="form-group mb-3">
                                        <label class="form-label">IP TS'a</label>
                                        <input type="text" name="ts_ip"
                                            value="<?= isset($settings['ts_ip']) ? $settings['ts_ip'] : "" ?>"
                                            class="form-control">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">PORT GŁOSOWY TS'a (UDP)</label>
                                        <input type="text" name="ts_udp_port"
                                            value="<?= isset($settings['ts_udp_port']) ? $settings['ts_udp_port'] : "" ?>"
                                            class="form-control">
                                    </div>
                                    <input type="hidden" name="license_id" value="<?= $_GET['license_id'] ?>">
                                    <button type="submit" class="btn btn-primary">Zapisz</button>
                                </form>
                            </div>
                            <script>

                            $("#edit_license").submit(function(e) {

                            e.preventDefault(); // avoid to execute the actual submit of the form.

                            var form = $(this);

                            $.ajax({
                                type: "POST",
                                url: "<?=URL?>/Api/EditLicenseSettings",
                                data: form.serialize(), // serializes the form's elements.
                                success: function(data)
                                {
                                    data = JSON.parse(data);
                                    if(data.success) {
                                    toastr.success(data.message);
                                    } else {
                                    toastr.error(data.message);
                                    }
                                    console.log(data);
                                }
                            });

                            });
                            </script>
                            <div class="tab-pane fade" id="navs-pills-justified-messages" role="tabpanel">
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>CZAS</th>
                                                <th>WERSJA</th>
                                                <th>STATUS</th>
                                                <th>POWÓD</th>
                                                <th>ADRES IP</th>
                                                <th>INSTANCJA</th>
                                                <th>POWÓD SPRAWDZENIA</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <?php
                                            $versions = $product->getVersions();
                                            foreach($versions as $version) {
                                                $vers[$version['id']] = $version;
                                            }
                                            $request = $pdo->prepare("SELECT * FROM `licenses_requests` WHERE license_id=:1 ORDER BY id DESC LIMIT 100");
                                            $request->execute([':1' => $license['id']]);
                                            $data = $request->fetchAll(PDO::FETCH_ASSOC);




                                            $keys = [];
                                            foreach($data as $key => $once): ?>

                                                <tr>
                                                    <td><?=date('d.m.Y H:i:s', $once['time'])?></td>
                                                    <td><?=isset($vers[$once['version_id']]) ? $vers[$once['version_id']]['major'] . "." . $vers[$once['version_id']]['minor'] . "." . $vers[$once['version_id']]['release'] . " " . $vers[$once['version_id']]['cycle']  : "?"?></td>
                                                    <td><?=$once['success']==1?'<span class="badge bg-label-success me-1">TAK</span>':'<span class="badge bg-label-danger me-1">BŁĄD</span>'?></td>
                                                    <td><?=$once['reason']?></td>
                                                    <td><?=$once['address']?></td>
                                                    <td><?=$once['instance']?></td>
                                                    <td><?=$once['type']=='run'?'Włączenie aplikacji':'Sprawdzenie w trakcie'?></td>
                                                </tr>
                                            <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="navs-pills-download" role="tabpanel">
                                <div class="row">
                                    <div class="col-xl-6">
                                        <div class="alert alert-danger" role="alert">
                                            <b>UWAGA!</b>
                                            Przypominamy, że udostępnianie komukolwiek plików aplikacji
                                            jest <b>SUROWO</b> zabronione! Do plików aplikacji może mieć
                                            dostęp jedynie <b>osoba która zakupiła aplikacje</b>. Nieprzestrzeganie
                                            tej zasady może wiązać się z <b>całkowitym odebraniem</b> licencji!
                                            <p class="mt-2">Informujemy, iż przed pobraniem system licencyjny <b>umieszcza szyfrowany klucz</b>
                                            który jest przypisany osobno do każdego klienta i nie ma możliwości jego usunięcia.
                                            W przypadku udostępnienia aplikacji, wycieku - będziemy wiedzieć który klient udostępnił aplikacje.</p>
                                            <p>Rozszyfrowanie plików aplikacji skutkuje <b>utratą licencji</b></p>
                                        </div>
                                    </div>
                                    <div class="col-xl-6">
                                        <h5>Stabilne wydania</h5>

       
                                        <ul>
                                        
                                        <?php $i = 0; foreach($vers as $ver): ?>
                                            <?php if($ver['cycle'] == 'stable'): ?>
                                                <?php $i++; ?>
                                                <li <?=$i==1?'class="new-version"':''?>>Wersja <?=$ver['major'] . "." . $ver['minor'] . "." . $ver['release'] . " " . $ver['cycle']?> (<?=date('d.m.Y H:i:s', $ver['release_time'])?>) 
                                                
                                                <?=$ver['active']==1?'<a href="#" class="download" value="'.$_GET['license_id'].':'.$product->getId().':'.$ver['id'].'">POBIERZ</a>':''?>

                                                <?=$i==1?'<span class="badge bg-primary">NAJNOWSZA</span>':''?>

                                                <?=$ver['supported']==1&&$ver['active']==1?'<span class="badge bg-success">WSPIERANA</span>':''?>
                                                <?=$ver['supported']==0&&$ver['active']==1?'<span class="badge bg-danger">NIEWSPIERANA</span>':''?>
                                                <?=$ver['active']==0?'<span class="badge bg-danger">NIE AKTYWNA</span>':''?>

                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        </ul>
                                        <?php if($i == 0): ?>
                                            <div class="alert alert-primary mb-0" role="alert">
                                               Niestety, ale aktualnie nie opublikowano żadnych wersji stabilnych. Zapraszamy później!
                                            </div>
                                        <?php endif; ?>

                                        <hr>
                                        <h5>Testy beta</h5>
                                        <?php if($license['beta_tests']): ?>
                                            <ul>
                                        
                                        <?php $i = 0; foreach($vers as $ver): ?>
                                            <?php if($ver['cycle'] !== 'stable' && $ver['cycle'] != 'pre-alpha'): ?>
                                                <?php $i++; ?>
                                                <li <?=$i==1?'class="new-version"':''?>>Wersja <?=$ver['major'] . "." . $ver['minor'] . "." . $ver['release'] . " " . $ver['cycle']?> (<?=date('d.m.Y H:i:s', $ver['release_time'])?>) 
                                                
                                                <?=$ver['active']==1?'<a href="#" class="download" value="'.$_GET['license_id'].':'.$product->getId().':'.$ver['id'].'">POBIERZ</a>':''?>

                                                <?=$i==1?'<span class="badge bg-primary">NAJNOWSZA</span>':''?>

                                                <?=$ver['active']==0?'<span class="badge bg-danger">NIE AKTYWNA</span>':'<span class="badge bg-danger">NIEWSPIERANA</span>'?>

                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
   
                                        </ul>
                                        <?php if($i == 0): ?>
                                            <div class="alert alert-primary mb-0" role="alert">
                                               Niestety, ale aktualnie nie opublikowano żadnych wersji testowych. Zapraszamy później!
                                            </div>
                                        <?php endif; ?>
                                        <?php else: ?>
                                            <div class="alert alert-dark mb-0" role="alert">
                                               Aktualnie nie masz dostępu do testowych wersji aplikacji.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div id="download" class="text-center" style="transition: all 0.4s ease-in-out; opacity:0;">

                                            <img src="<?=URL?>/assets/img/1484.gif">

                                            <h4><b>Proszę czekać...</b></h3>
                                            <p>Jesteśmy w trakcie przygotowywania pakietu do pobrania. Ze względu na bezpieczeństwo aplikacji, może to chwilkę potrwać!</p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

let started = false;

$(".download").click(function () {

    let v = $(this).attr("value");
    if(started)
    {
        toastr["error"]("Jedna z wersji jest w trakcie przygotowania. Prosimy o cierpliwość.", "Błąd!");
    } else {
        started = true;
        $("#download").css("opacity", 1);

        $.ajax({
            data: {},
            url: "<?=URL?>/Api/Build?data=" + v,
            type: "GET",
            success: function(data)
            {
                try {
                    JSON.parse(data);
                } catch (e) {

                    window.open("<?=URL?>/Api/Build?data=" + v);
                    $("#download").css("opacity", 0);
                    started = false;
                    return false;
                }
                data = JSON.parse(data);
                if(data.success) {
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message);
                }
                started = false;
            }
        })

    }

    

});


</script>