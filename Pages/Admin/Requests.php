
<!-- Content -->


<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">

        <div class="col-xl">

            <div class="card">
                <div class="card-body">
                <h5 class="card-title">Zapytania</h5>

                <div class="table-responsive text-nowrap">
    <table class="table">
      <thead>
        <tr>
          <th>Czas</th>
          <th>Licencja</th>
          <th>Produkt</th>
          <th>Wersja</th>
          <th>Status</th>
          <th>Reason</th>
          <th>Adres IP</th>
          <th>Instancja</th>
          <th>Typ</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">


                <?php
                $request = $pdo->query("SELECT * FROM `licenses_requests` ORDER BY id DESC LIMIT 50");
                $request = $request->fetchAll(PDO::FETCH_ASSOC);
                $time = $request[0]['time'];
                $i = 0;
                foreach($request as $once)
                {

                    if($time - $once['time'] < 20)
                    {

                    } else {
                        $i++;
                    }
                    $time = $once['time'];

                    if(!isset($data[$once['license_id']][$i]))
                    {
                        $data[$once['license_id']][$i] = $once;
                    } else {
                        $data[$once['license_id']][$i]['instance'] .= ", " . $once['instance'];
                    }
                    
                }

                $products = Products::getProducts();

                foreach($data as $lic): ?>

                    <?php foreach($lic as $once): ?>

                    <tr>
                        <td><?=date('d.m.y H:i:s', $once['time'])?></td>
                        <td><?=$once['license_id']?></td>
                        <td>
                            <?php foreach($products as $product): ?>
                                <?php if($product->getId() == $once['product_id']): ?>
                                    <?=$product->getName()?>
                                    <?php if(!isset($vers[$once['product_id']]))
                                    {
                                        foreach($product->getVersions() as $ver):
                                            $vers[$once['product_id']][$ver['id']] = $ver;
                                        endforeach;
                                    }
                                    ?>
                                    
                                <?php endif; ?>
                            <?php endforeach; ?>    
                        </td>
                        <td><?=$vers[$once['product_id']][$once['version_id']]['major']?>.<?=$vers[$once['product_id']][$once['version_id']]['minor']?>.<?=$vers[$once['product_id']][$once['version_id']]['release']?> <?=$vers[$once['product_id']][$once['version_id']]['cycle']?></td>
                        <td><?=$once['success']==1?'<span class="badge bg-label-success me-1">TAK</span>':'<span class="badge bg-label-danger me-1">BŁĄD</span>'?></td>
                        <td><?=$once['reason']?></td>
                        <td><?=$once['address']?></td>
                        <td><?=$once['instance']?></td>
                        <td><?=$once['type']=='run'?'Włączenie aplikacji':'Sprawdzenie w trakcie'?></td>
                    </td>

                    <?php endforeach; ?>

                <?php endforeach; ?>

                </tbody>
    </table>
  </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

