<?php

class Product {
    private $id;
    private $name;
    private $price;
    private $category;
    private $image;
    private $description;

    public function __construct($id, $name, $price, $category, $image, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
        $this->image = $image;
        $this->description = $description;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getFormattedPrice() {
        return "$" . number_format($this->price, 2);
    }

    public function getCategory() {
        return $this->category;
    }

    public function getImage() {
        return $this->image;
    }

    public function getDescription() {
        return $this->description;
    }
}