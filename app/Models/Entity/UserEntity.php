<?php
namespace App\Models\Entity;

use CoffeeCode\DataLayer\DataLayer;

class UserEntity extends DataLayer {
  public function __construct() {
    parent::__construct(
      "users",
      [
        "username",
        "email",
        "password",
        "role",
        "auto_login_token"
      ],
      "id",
      false
    );
  }

  public function getUser($id) {
    return $this->find("id = :id", "id=$id")->fetch();
  }
  
  public function getUsers() {
    return $this->find()->fetch(true);
  }

  public function getUserByEmail($email) {
    return $this->find("email = :email", "email=$email")->fetch();
  }
}