<?php

require_once __DIR__ . '/../models/User.php';

class UserController {
    public function index() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        $search = $userModel->searchUser($search);

        // Pass data to the view
        require __DIR__ . '/../view/usersView.php';
    }
}
