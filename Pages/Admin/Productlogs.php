
<!-- Content -->

<?php

if(isset($_GET['product_id']) && isset($_GET['version_id']))
{
    $product = Products::getProductById($_GET['product_id']);
    if($product instanceof Product)
    {
        $versions = $product->getVersions();
        foreach($versions as $ver)
        {
            $vers[$ver['id']] = $ver;
        }
        if(!isset($vers[$_GET['version_id']]))
        {
            header("Location: " . URL . "/Admin/ProductList");
            die("<script> window.location.href = '".url."/Admin' </script>");
        }
    } else {
        header("Location: " . URL . "/Admin/ProductList");
        die("<script> window.location.href = '".url."/Admin' </script>");
    }
}else {
    header("Location: " . URL . "/Admin/ProductList");
    die("<script> window.location.href = '".url."/Admin' </script>");
}
?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">

        <div class="col-xl">

            <div class="card">
                <div class="card-body">
                <h5 class="card-title">Raporty - <?=$product->getName()?></h5>

                <?php
                $request = $pdo->prepare("SELECT * FROM `logs` WHERE product_id=:id AND version_id=:1 ORDER BY id DESC");
                $request->execute([':id' => $_GET['product_id'], ':1' => $_GET['version_id']]);


                foreach($request->fetchAll(PDO::FETCH_ASSOC) as $log): ?>
                    <?php
                    $version = "?";
                    if(isset($vers[$log['version_id']]))
                    {
                        $version = $vers[$log['version_id']]['major'] . "." . $vers[$log['version_id']]['minor'] . "." . $vers[$log['version_id']]['release'] . " " . $vers[$log['version_id']]['cycle'];
                    }
                    ?>
                    <p>[<?=$version?>]    <?=date('d.m.Y H:i:s', $log['time'])?> <?=htmlspecialchars($log['log']);?></p>
                <?php endforeach; ?>

                    
                </div>
            </div>
        </div>
    </div>
</div>

