<?php

if(isset($_GET['product_id']))
{
    $product = Products::getProductById($_GET['product_id']);
    if($product instanceof Product)
    {

        $versions = $product->getVersions();
        if(empty($versions))
        {
            $major = 1;
            $minor = 0;
            $release = 0;
        } else {
            $last = $versions[array_key_last($versions)];
            $major = $last['major'];
            $minor = $last['minor'];
            $release = $last['release'] + 1;
        }

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
                <h5 class="card-header">Dodaj nową wersje</h5>
                <div class="card-body">
                    <form id="add_version">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                    <label>Major</label>
                                    <input class="form-control" type="number" value="<?=$major?>" name="major">
                                </div>
                                <div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                    <label>Minor</label>
                                    <input class="form-control" type="number" value="<?=$minor?>" name="minor">
                                </div>
                                <div class="col-xl-1 col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                    <label>Release</label>
                                    <input class="form-control" type="number" value="<?=$release?>" name="release">
                                </div>
                                <div class="col-auto">
                                    <label>Cykl</label>
                                    <select class="form-control" type="select" name="cycle">
                                        <option value="stable" selected">Stabilna (Można pobierać)</option>
                                        <option value="rtm">Gotowy do wydania (Można pobierać, jednak mogą pojawić się liczne błędy które nie przeszkadzają znacząco w działaniu)</option>
                                        <option value="beta">Beta (Mogą pobierać beta-testerzy, aplikacja powinna działać z licznymi błędami)</option>
                                        <option value="alpha">Alpha (pre-beta) (Mogą pobierać alfa-testerzy, aplikacja powinna wstępnie działać z wieloma błędami)</option>
                                        <option value="pre-alpha">Pre-alpha (Mogę pobierać jedynie autorzy aplikacji, aplikacja nie została przygotowana do działania)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label>Wersja PHP</label>
                            <select class="form-control" type="select" name="php">
                                <option value="8.2">PHP 8.2</option>
                                <option value="8.1" selected>PHP 8.1 (zalecana)</option>
                                <option value="8.0">PHP 8.0</option>
                                <option value="7.4">PHP 7.4</option>
                            </select>
                        </div>
                        <div class="form-group mt-3">
                            <label>Komentarz</label>
                            <textarea class="form-control" name="comments" rows="4"></textarea>
                        </div>
                        <div class="form-group mt-3">
                            <div class="mb-3">
                                <label for="file" class="form-label">Załączony plik</label>
                                <input class="form-control" type="file" id="file" name="file">
                            </div>
                        </div>
                        <input type="hidden" name="id" value="<?=$_GET['product_id']?>">
                        <button type="submit" id="sb" class="btn btn-primary mt-2">Opublikuj</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

    $("#add_version").submit(function(e) {

    e.preventDefault(); // avoid to execute the actual submit of the form.

    $("#db").prop("disabled", true);

    var form = new FormData(this);

    $.ajax({
        type: "POST",
        url: "<?=URL?>/Api/AddVersion",
        data: form, // serializes the form's elements.
        cache: false,
        contentType: false,
        processData: false,
        success: function(data)
        {
            data = JSON.parse(data);
            if(data.success) {
            toastr.success(data.message);
            setTimeout(function(){
                window.location.href = '<?=URL?>/Admin/ProductList';
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
                <h5 class="card-header"><?=$product->getName()?> - Wersje</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>Wersja</th>
                        <th>Cykl</th>
                        <th>Utworzenie</th>
                        <th>Wersja PHP</th>
                        <th>Wspierane</th>
                        <th>Do używania</th>
                        <th>Komentarz</th>
                        <th>Raporty</th>
                        <th>Akcje</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">

                        <?php foreach($versions as $version): ?>


                            <tr>
                                <td><b><?=$version['major']?>.<?=$version['minor']?>.<?=$version['release']?></b></td>
                                <td>

                                <?php
                                $c = "?";
                                switch($version['cycle'])
                                {
                                    case "stable":
                                        $c = '<span class="badge bg-success">STABLE</span>';
                                        break;
                                    case "rtm":
                                        $c = '<span class="badge bg-primary">RTM</span>';
                                        break;
                                    case "beta":
                                        $c = '<span class="badge bg-warning">BETA</span>';
                                        break;
                                    case "alpha":
                                        $c = '<span class="badge bg-danger">ALPHA</span>';
                                        break;
                                    case "pre-alpha":
                                        $c = '<span class="badge bg-dark">PRE-ALPHA</span>';
                                        break;
                                }
                                echo $c;
                                ?>

                                </td>
                                <td><?=date('d.m.Y H:i:s', $version['created'])?></td>
                                <td><?=$version['php_version']?></td>
                                <td>
                                    <input class="form-check-input btn-supported" type="checkbox" id="<?=$version['id']?>" <?=$version['supported']==1?"checked":""?>>
                                </td>
                                <td>
                                    <input class="form-check-input btn-active" type="checkbox" id="<?=$version['id']?>" <?=$version['active']==1?"checked":""?>>
                                </td>
                                <td><?=substr($version['comments'],0,10)?>...</td>
                                <td>                              <a href="<?=URL?>/Admin/ProductLogs?product_id=<?=$product->getId()?>&version_id=<?=$version['id']?>" class="btn btn-sm btn-danger">
                                    Raporty
                                </a></td>
                                <td>
                                    <?php if(strlen($version['file_binary'])): ?>
                                        <button class="btn btn-xs btn-danger removefile" verid="<?=$version['id']?>">Usuń plik</button>
                                    <?php endif; ?>
                                </td>
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

   $(".removefile").on("click", function(e)
   {
        let verid = $(this).attr("verid");
        $.ajax({
            type: "POST",
            url: "<?=URL?>/Api/Removefile",
            data: {"id": verid},
            success: function(data)
            {
                window.location.reload();
                console.log(data);
            }
        });
   });

    $(".btn-supported").on("change", function()
    {
        $.ajax({
            type: "POST",
            url: "<?=URL?>/Api/EditVersion",
            data: {"id": this.id, "action": "supported", "status": this.checked},
            success: function(data)
            {
                console.log(data);
            }
        });
    });
    $(".btn-active").on("change", function()
    {
        $.ajax({
            type: "POST",
            url: "<?=URL?>/Api/EditVersion",
            data: {"id": this.id, "action": "active", "status": this.checked},
            success: function(data)
            {
                console.log(data);
            }
        });
    });
</script>

<!-- / Content -->
