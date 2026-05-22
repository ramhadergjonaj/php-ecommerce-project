<?php

class User
{
    protected int $id;
    protected string $name;
    protected string $email;
    protected string $role;
    protected string $password;

    public function __construct(int $id, string $name, string $email, string $role, string $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->role = $role;
        $this->password = $password;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPasswordHash(): string
    {
        return $this->password;
    }

    public function isPasswordHashed(): bool
    {
        return preg_match('/^\$2[ayb]\$.{50,}$/', $this->password) === 1;
    }

    public function verifyPassword(string $password): bool
    {
        $stored = $this->password;

        if ($this->isPasswordHashed()) {
            return password_verify($password, $stored);
        }

        return hash_equals($stored, $password);
    }
}
