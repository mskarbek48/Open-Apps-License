<?php

if(isset($_GET['product_id']))
{
    $product = Products::getProductById($_GET['product_id']);
    if($product instanceof Product)
    {

    } else {
        die(header("Location: " . URL . "/Home"));
    }
}else {
    die(header("Location: " . URL . "/Home"));
}
?>

<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">


    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                
                <div class="card-body">
                    <h4 class="card-title">Historia wersji</h4>

                    <?php if(!empty($product->getVersions())): ?>

                    <div class="demo-inline-spacing mt-3">
                        <div class="list-group">

                            <?php foreach($product->getVersions() as $version):?>

                            <a href="javascript:void(0);" class="list-group-item list-group-item-action flex-column align-items-start">
                            <div class="d-flex justify-content-between w-100">
                                <h6><?=$version['major'] . "." . $version['minor'] . "." . $version['release'] . " " . $version['cycle']?></h6>
                                <small><?=date('d.m.Y H:i:s', $version['release_time'])?></small>
                            </div>
                            <p class="mb-3"><?=$version['comments']?></p>
                            <ul>
                                <li>Wersja PHP: <b><?=$version['php_version']?></b></li>
                                <li>Wspierana: <b><?=$version['supported']==1?'Tak':'Nie'?></b></li>
                                <li>Aktywna: <b><?=$version['active']==1?'Tak':'Nie'?></b></li>
                            </ul>
                            </a>

                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php else: ?>

                        <div class="alert alert-danger" role="alert">
                            Ta aplikacja nie posiada jeszcze żadnej wersji.
                        </div>

                    <?php endif; ?>



                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Content -->
