
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">

    

        <div class="col-xl">
        
            <div class="alert alert-primary alert-dismissible" role="alert">
            
                <b>Zanim cokolwiek tu zmienisz, przeczytaj mnie!</b>
                <ul>
                    <li>Licencja dodajemy jedynie <b>po zakupie aplikacji</b> przez klienta!</li>
                    <li>Ustawienia licencji (ip vpsa itp.) <b>zmienia tylko klient!</b> dostęp do tych ustawień może być wykorzystana tylko w przypadku zakupienia instalacji, lub też problemów z ustawieniem tych ustawień</li>
                    <li>Licencji <b>nie usuwamy</b> - jeśli jakaś licencja została zmieniona na inną/przestała być w użyciu - blokujemy ją za pomocą checkboxa</li>
                    <li>Dokładny powód blokady można wpisać po kliknięciu przycisku "Edycja ustawień"</li>
                    <li>Nie przypisujemy klientom bez potrzeby opcji "testy beta"</li>
                </ul>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <div class="card mb-4">
                <h5 class="card-header">Dodaj nową licencje</h5>
                <div class="card-body">
                    <form id="add_lic">
                        <div class="form-group">
                            <label class="form-label">Klient</label>
                            <select name="client" class="form-control">
                                <?php 
                                $request = $pdo->query("SELECT `id`, `role`, `login` FROM clients");
                                $clients = $request->fetchAll(PDO::FETCH_ASSOC);
                                foreach($clients as $client):
                                ?>
                                <option value="<?=$client['id']?>"><?=htmlspecialchars($client['login'])?> (<?=$client['role']?>)
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Aplikacja</label>
                            <select name="app" class="form-control">
                                <?php foreach(Products::getProducts() as $product): ?>
                                    <option value="<?=$product->getId()?>"><?=$product->getName()?> (<?=$product->getCategoryName()?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Klucz licencyjny</label>
                            <input type="text" name="serial" value="<?=Helper::generateSerialNumber()?>" class="form-control">
                        </div>
                        <div class="form-group  mt-3">
                            <div class="form-check">
                                <input name="beta" value="false" type="hidden">
                                <input name="beta" class="form-check-input" type="checkbox" id="beta">
                                <label class="form-check-label" for="beta"> Czy może używać wersji beta? </label>
                            </div>
                        </div>
                        <button type="submit" id="sb" class="btn btn-primary mt-2">Dodaj</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

    $("#add_lic").submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    $("#db").prop("disabled", true);

    var form = $("#add_lic").serialize();

    $.ajax({
        type: "POST",
        url: "<?=URL?>/Api/AddLicense",
        data: form, // serializes the form's elements.
        success: function(data)
        {
            data = JSON.parse(data);
            if(data.success) {
                toastr.success(data.message);
                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            } else {
                toastr.error(data.message);
            }
            console.log(data);
        }
    });

    });
    </script>


    <div class="row">

        <div class="col-xl">
            <div class="card">
                <h5 class="card-header">Licencje</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Klient</th>
                        <th>Aplikacja</th>
                        <th>Serial no.</th>
                        <th>Testy beta</th>
                        <th>Aktualizacja</th>
                        <th>Blokada</th>
                        <th>Edycja</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                    <?php
                    $request = $pdo->query("SELECT licenses.product_id, licenses.serial_number, licenses.beta_tests, licenses.blocked, licenses.id, licenses.modified, clients.login FROM `licenses` INNER JOIN `clients` ON licenses.client_id = clients.id");
                    $data = $request->fetchAll(PDO::FETCH_ASSOC);
                    foreach($data as $once):
                    ?>
                    <tr>
                        <td><?=$once['login']?></td>
                        <td><?=Products::getProductById($once['product_id'])->getName()?></td>
                        <td style="font-size: 6px;"><?=$once['serial_number']?></td>
                        <td>
                            <input class="form-check-input btn-beta_tests" type="checkbox" id="<?=$once['id']?>" <?=$once['beta_tests']==1?"checked":""?>>
                        </td>
                        <td><?=date('d.m.Y H:i:s', $once['modified'])?></td>
                        <td>
                            <input class="form-check-input btn-blocked" type="checkbox" id="<?=$once['id']?>" <?=$once['blocked']==1?"checked":""?>>
                        </td>
                        <td><a class="btn btn-primary btn-sm" href="<?=URL?>/Admin/LicenseEdit?license_id=<?=$once['id']?>">Edycja ustawień</a></td>
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
    $(".btn-beta_tests").on("change", function()
    {
        $.ajax({
            type: "POST",
            url: "<?=URL?>/Api/EditLicense",
            data: {"id": this.id, "action": "beta_tests", "status": this.checked},
            success: function(data)
            {
                console.log(data);
            }
        });
    });
    $(".btn-blocked").on("change", function()
    {
        $.ajax({
            type: "POST",
            url: "<?=URL?>/Api/EditLicense",
            data: {"id": this.id, "action": "block", "status": this.checked},
            success: function(data)
            {
                console.log(data);
            }
        });
    });
</script>

<!-- / Content -->
