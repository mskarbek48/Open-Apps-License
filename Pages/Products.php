
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
<div class="alert alert-warning" role="alert">
            Szanowni Użytkownicy, sprzedaż aplikacji została zakończona, nie wpływa to aktualnych klientów. <a href="https://www.facebook.com/openappspl/posts/pfbid02zuGfgp85j6R3mybH2YKSnUGpqJpTCeKc8idju6CZSCwu6r4BpgqurknYFN9GnKaDl">Szczegóły tutaj</a>
          </div>

    <div class="row">




        <?php
        $request = $pdo->query("SELECT * FROM products_category");
        $data = $request->fetchAll(PDO::FETCH_ASSOC);
        foreach($data as $cat): ?>

            <h4 class="fw-bold py-3 mb-4"><?=$cat['name']?></h4>

            <?php $products = Products::getProductsByCategoryId($cat['id']); ?>

            <?php if(empty($products)): ?>
                <div class="alert alert-danger" role="alert">Aktualnie nie ma produktów w tej kategorii!</div>
            <?php else: ?>

                <?php foreach($products as $product): ?>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="<?=$product->getIcon()?> mb-2" style="font-size: 64px; color: <?=$product->getColor()?>;"></i>
                                <small style="display: block; margin-top: 15px;">Utworzona: <?=$product->getCreationDate()?></small>
                                <h4 class="mt-3 mb-1"><b><?=$product->getName()?></b></h4>
                                <p class="card-text"><?=$product->getDescription()?></p>
                                <a href="https://www.facebook.com/openappspl" class="btn-primary btn btn-sm">Zakup</a>
                                <a href="<?=URL?>/ProductVersions?product_id=<?=$product->getId()?>" class="btn-secondary btn btn-sm">Historia wersji</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>
</div>
<!-- / Content -->
