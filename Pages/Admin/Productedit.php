
<!-- Content -->

<?php

if(isset($_GET['product_id']))
{
    $product = Products::getProductById($_GET['product_id']);
    if($product instanceof Product)
    {

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
                <h5 class="mb-0">Edytowanie produktu</h5>
            </div>
            <div class="card-body">
                <form id="add_product">
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Nazwa</label>
                        <input type="text" class="form-control" value="<?=$product->getName()?>" name="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Kategoria</label>
                        <select class="form-control" name="category">
                        
                        <?php
                        $request = $pdo->query("SELECT * FROM products_category");
                        $data =$request->fetchAll(PDO::FETCH_ASSOC);
                        foreach($data as $cat): ?>
                            <option value="<?=$cat['id']?>" <?=$cat['id']==$product->getCategoryId()?'selected':''?>><?=$cat['name']?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Ikonka</label>
                        <select class="form-control" name="icon">
                            <option <?=$product->getIcon()=="fa-regular fa-window-restore"?"selected":""?> value="fa-regular fa-window-restore">fa-regular fa-window-restore</option>
                            <option <?=$product->getIcon()=="fa-solid fa-terminal"?"selected":""?> value="fa-solid fa-terminal">fa-solid fa-terminal</option>
                            <option <?=$product->getIcon()=="fa-solid fa-robot"?"selected":""?> value="fa-solid fa-robot">fa-solid fa-robot</option>
                            <option <?=$product->getIcon()=="fa-solid fa-network-wired"?"selected":""?> value="fa-solid fa-network-wired">fa-solid fa-network-wired</option>
                            <option <?=$product->getIcon()=="fa-regular fa-floppy-disk"?"selected":""?> value="fa-regular fa-floppy-disk">fa-regular fa-floppy-disk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-company">Kolor</label>
                        <input type="color"  class="form-control" name="color" value="<?=$product->getColor()?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-message">Opis</label>
                        <textarea id="basic-default-message" class="form-control" name="description"><?=$product->getDescription()?></textarea>
                    </div>
                    <input type="hidden" name="id" value="<?=$_GET['product_id']?>">
                        <button type="submit" class="btn btn-primary">Zapisz</button>
                </form>
            </div>
            </div>
        </div>
    </div>
</div>
<script>

$("#add_product").submit(function(e) {

  e.preventDefault(); // avoid to execute the actual submit of the form.

  var form = $(this);

  $.ajax({
      type: "POST",
      url: "<?=URL?>/Api/EditProduct",
      data: form.serialize(), // serializes the form's elements.
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
<!-- / Content -->
