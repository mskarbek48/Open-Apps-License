<?php



class Products {

    public static function getProductByName(string $name)
    {

    }

    public static  function getProductById(int $id)
    {
        global $pdo;

        $request = $pdo->query("SELECT * FROM `products` WHERE id=$id");
        $product = $request->fetch(PDO::FETCH_ASSOC);
        if(isset($product['id']))
        { 
            $product = new Product($product['id'], $product['name'], $product['category'], $product['created'], $product['description'], $product['color'], $product['icon']);
        } else { 
            $product = array();
        }
        


        return $product;
    }

    public static  function getProducts()
    {
        global $pdo;

        $request = $pdo->query("SELECT * FROM `products`");
        $products = $request->fetchAll(PDO::FETCH_ASSOC);
        $pr = array();
        foreach($products as $key => $product)
        {
            $pr[$product['id']] =new Product($product['id'], $product['name'], $product['category'], $product['created'], $product['description']);
        }

        return $pr;
    }

    public static  function getProductsByCategoryId(int $id)
    { 

        global $pdo;

        $request = $pdo->query("SELECT * FROM `products` WHERE category=$id");
        $data = $request->fetchAll(PDO::FETCH_ASSOC);
        foreach($data as $product)
        { 
            $products[$product['id']] = new Product($product['id'], $product['name'], $product['category'], $product['created'], $product['description'], $product['color'], $product['icon']);
        }

        return $products;

    }

    public static  function getProductsByCategoryName(string $name)
    { 
        
    }

}