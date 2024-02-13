
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">

<div class="alert alert-warning" role="alert">
            Szanowni Użytkownicy, sprzedaż aplikacji została zakończona, nie wpływa to aktualnych klientów. <a href="https://www.facebook.com/openappspl/posts/pfbid02zuGfgp85j6R3mybH2YKSnUGpqJpTCeKc8idju6CZSCwu6r4BpgqurknYFN9GnKaDl">Szczegóły tutaj</a>
          </div>

    <h4 class="fw-bold py-3 mb-4">
        Zakupione licencje
    </h4>

    <div class="row">



        <?php

        $request = $pdo->prepare("SELECT licenses.id, licenses.product_id,licenses.client_id,licenses.serial_number,licenses.beta_tests,licenses.created,licenses.modified,licenses.blocked, licenses_settings.vps_ip, licenses_settings.ts_ip,licenses_settings.ts_udp_port FROM `licenses` LEFT JOIN licenses_settings ON licenses.id=licenses_settings.license_id WHERE client_id=:client_id");
        $request->execute([':client_id' => Session::get("client_id")]);
        if($request->rowCount() == 0): ?>


        <div class="alert alert-danger" role="alert">
            Nie posiadasz żadnej licencji. Możesz ją zakupić kontakując się z nami za pośrednictwem chatu na facebooku!
        </div>


        <?php else: ?>


            <?php foreach($request->fetchAll(PDO::FETCH_ASSOC) as $license): ?>


                <?php 
                $product = Products::getProductById($license['product_id']); ?>

                <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6">

                    <div class="card mb-3">

                        <div class="card-body text-center" style="padding: 20px;">
                            <i class="<?=$product->getIcon()?>" style="font-size: 64px; color: <?=$product->getColor()?>;"></i>
                            <small style="display: block; margin-top: 15px;">ID Licencji: <?=$license['id']?></small>
                            <h3><b><?=$product->getName()?></b></h3>

                            <?php if(strlen($license['ts_ip'])): ?>
                                <div class="badge bg-success mb-3"><i class="fa-brands fa-teamspeak"></i> <?=$license['ts_ip']?></div>
                            <?php else: ?>
                                <div class="badge bg-secondary mb-3"><i class="fa-brands fa-teamspeak"></i> ???</div>
                            <?php endif; ?>
                            <div style="display: block;"> 
                            <a href="<?=URL?>/LicenseEdit?license_id=<?=$license['id']?>" class="btn btn-sm btn-primary">Informacje/Ustawienia</a>
                            </div>
                        </div>

                    </div>

                </div>

            <?php endforeach; ?>


        <?php endif; ?>


    </div>
</div>
<!-- / Content -->
