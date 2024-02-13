
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">


    <div class="row">
        <div class="col-xl">
        <div class="alert alert-primary alert-dismissible" role="alert">
            
            <b>Zanim cokolwiek tu zmienisz, przeczytaj mnie!</b>
            <ul>
                <li>Aplikacje tworzą <b>jedynie autorzy aplikacji</b></li>
                <li>Aktualizacje mogą być robione jedynie przez autorów aplikacji</li>
                <li>Jeżeli dana wersja jest "wspierana" (czyli udzielamy support) zaznacz opcje wspierana</li>
                <li>Aplikacja która nie jest wspierana <b>dalej będzie działać,ale nie będzie można jej pobierać ze stosownym komunikatem</b></li>
                <li>Aplikacja która jest "do używania" <b>uruchamia się, jeśli wsparcie jest włączone, ta opcja ma być zaznaczona</b></li>
                <li>Przy wyłączeniu opcji "do używania" <b>danej wersji nie będzie dało się uruchomić!</b></li>
            </ul>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>

    <div class="row">




        <?php
        $request = $pdo->query("SELECT * FROM products_category");
        $data = $request->fetchAll(PDO::FETCH_ASSOC);
        foreach($data as $cat): ?>

            <h4 class="fw-bold py-3 mb-4"><?=$cat['name']?> <a href="<?=URL?>/Admin/ProductAdd?category=<?=$cat['id']?>" class="btn btn-success btn-sm">Dodaj</a></h4>

            <?php $products = Products::getProductsByCategoryId($cat['id']); ?>

            <?php if(empty($products)): ?>
                <div class="alert alert-danger" role="alert">Aktualnie nie ma produktów w tej kategorii!</div>
            <?php else: ?>

                <?php foreach($products as $product): ?>
                    <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-xs-12 mb-3">
                        <div class="card text-center">
                            <div class="card-header">Utworzona: <?=$product->getCreationDate()?></div>
                            <div class="card-body">
                                <h5 class="card-title"><?=$product->getName()?></h5>
                                <p class="card-text"><?=$product->getDescription()?></p>

                                <a href="<?=URL?>/Admin/ProductVersions?product_id=<?=$product->getId()?>" class="btn btn-sm btn-primary">
                                    Wersje
                                </a>
                                <a href="<?=URL?>/Admin/ProductEdit?product_id=<?=$product->getId()?>" class="btn btn-sm btn-warning">
                                    Edytuj
                                </a>

  
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>

        <?php endforeach; ?>

    </div>
</div>
<!-- / Content -->
