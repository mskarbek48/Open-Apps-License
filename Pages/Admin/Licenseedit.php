
<!-- Content -->

<?php

if(isset($_GET['license_id']))
{
    $request = $pdo->prepare("SELECT * FROM `licenses` WHERE id=:id");
    $request->execute([':id' => $_GET['license_id']]);

    if($request->rowCount() > 0)
    {
        $license = $request->fetch(PDO::FETCH_ASSOC);

        $request = $pdo->prepare("SELECT * FROM `licenses_settings` WHERE license_id=:1");
        $request->execute([':1' => $_GET['license_id']]);
        $settings = $request->fetch(PDO::FETCH_ASSOC);

    } else {
        die(header("Location: " . URL . "/Admin/ProductList"));
    }
}else {
    die(header("Location: " . URL . "/Admin/ProductList"));
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">

        <div class="col-xl">
            <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edytowanie licencji</h5>
            </div>
            <div class="card-body">
                <form id="edit_license">

                    <div class="form-group mb-3">
                        <label class="form-label">Klient</label>
                        <select name="client" class="form-control">
                            <?php 
                            $request = $pdo->query("SELECT `id`, `role`, `login` FROM clients");
                            $clients = $request->fetchAll(PDO::FETCH_ASSOC);
                            foreach($clients as $client):
                            ?>
                            <option <?=$client['id']==$license['client_id']?"checked":""?> value="<?=$client['id']?>"><?=htmlspecialchars($client['login'])?> (<?=$client['role']?>)
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Aplikacja</label>
                        <select name="app" class="form-control">
                            <?php foreach(Products::getProducts() as $product): ?>
                                <option  <?=$product->getId()==$license['product_id']?"checked":""?> value="<?=$product->getId()?>"><?=$product->getName()?> (<?=$product->getCategoryName()?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="form-group mb-3">
                        <label class="form-label">IP VPS'a</label>
                        <input type="text" name="vps_ip" value="<?=isset($settings['vps_ip'])?$settings['vps_ip']:""?>" class="form-control">
                    </div>
                    
                    
                    <div class="form-group mb-3">
                        <label class="form-label">IP TS'a</label>
                        <input type="text" name="ts_ip" value="<?=isset($settings['ts_ip'])?$settings['ts_ip']:""?>" class="form-control">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label">PORT GŁOSOWY TS'a (UDP)</label>
                        <input type="text" name="ts_udp_port" value="<?=isset($settings['ts_udp_port'])?$settings['ts_udp_port']:""?>" class="form-control">
                    </div>
                    <div class="form-group mb-3">
                        <input name="beta" value="false" type="hidden">
                        <input name="beta" <?=$license['beta_tests'] == 1 ? "checked" : ""?> class="form-check-input" type="checkbox" id="beta">
                        <label class="form-check-label" for="beta"> Czy może używać wersji beta? </label>
                    </div>
                    <div class="form-group mb-3">
                        <input name="block" value="false" type="hidden">
                        <input name="block" <?=$license['blocked'] == 1 ? "checked" : ""?> class="form-check-input" type="checkbox" id="beta">
                        <label class="form-check-label" for="beta"> Czy licencja jest zablokowana? </label>
                    </div>
                    <div class="form-group mb-3">
                        <label>Powód blokady</label>
                        <input name="block_reason" class="form-control" placeholder="Łamanie zasad licencji">
                    </div>
                    <input type="hidden" name="license_id" value="<?=$_GET['license_id']?>">
                    <button type="submit" class="btn btn-primary">Zapisz</button>
                </form>
            </div>
            </div>
        </div>
    </div>
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
<!-- / Content -->
