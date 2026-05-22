<?php

class Product
{
    private int $id;
    private string $name;
    private float $price;
    private string $category;
    private string $image;
    private string $description;
    private ?int $categoryId;

    public function __construct(
        int $id,
        string $name,
        float $price,
        string $category,
        string $image,
        string $description,
        ?int $categoryId = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
        $this->image = $image;
        $this->description = $description;
        $this->categoryId = $categoryId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getFormattedPrice(): string
    {
        return '$' . number_format($this->price, 2);
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
