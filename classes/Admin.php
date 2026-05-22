<?php
require_once __DIR__ . '/User.php';

class Admin extends User {
    public function __construct($id, $name, $email, $password) {
        parent::__construct($id, $name, $email, 'admin', $password);
    }

    public function getAdminGreeting() {
        return "Mirësevini Administrator, " . $this->getName() . "!";
    }
}