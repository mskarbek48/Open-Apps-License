<?php


class Product {

    private $category_id;
    private $category_name;
    private $created;
    private $name;
    private $description;
    private $id;
    private $versions;
    private $color;
    private $icon;

    public function __construct(int $id, string $name, int $category_id, int $created, string $description, $color = "", $icon = "") 
    {    
        $this->id = $id;
        $this->category_id = $category_id;
        $this->created = $created;
        $this->name = $name;
        $this->description = $description;
        $this->color = $color;
        $this->icon = $icon;
    }

    public function getVersions()
    {

        global $pdo;

        if(!isset($this->versions))
        { 
            $request = $pdo->query("SELECT * FROM products_version WHERE product_id={$this->id} ORDER BY id DESC");
            $this->versions = $request->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->versions;
    }

    public function getVersionById(int $id)
    {

        global $pdo;


        $request = $pdo->query("SELECT * FROM products_version WHERE product_id={$this->id} AND id={$id} ORDER BY id DESC");
        return $request->fetch(PDO::FETCH_ASSOC);
        

    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCreationDate()
    {
        return date('d.m.Y H:i:s', $this->created);
    }

    public function getName()
    {
        return str_replace("_", " ", ucfirst($this->name));
    }

    public function getCategoryId()
    {
        return $this->category_id;
    }

    public function getCategoryName()
    {
        global $pdo;

        if(!isset($this->category_name))
        { 
            $request = $pdo->query("SELECT `name` FROM `products_category` WHERE id={$this->category_id}");
            $data = $request->fetch(PDO::FETCH_ASSOC);
            if(isset($data['name']))
            { 
                $this->category_name = $data['name'];
            } else { 
                $this->category_name = "Brak/Nieznana";
            }
        }

        return $this->category_name;
    }

}