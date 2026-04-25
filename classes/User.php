<?php

class User {
    protected $id;
    protected $name;
    protected $email;
    protected $role;
    protected $password;

    public function __construct($id, $name, $email, $role, $password) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getRole() {
        return $this->role;
    }

    public function verifyPassword($password) {
        return $this->password === $password;
    }
}