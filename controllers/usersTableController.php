<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/userModel.php';

class UsersTableController {
    private PDO $pdo;
    private UserModel $userModel;

    private array $errors = [];
    private array $users = [];
    private string $search = "";
    private array $pagination = [];
    private int $totalUsers = 0;

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
        $this->userModel = new UserModel($this->pdo);
    }

    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->handlePostRequest();
            // PRG pattern: redirect back to GET to avoid resubmission
            $params = $_GET; // preserve current filters like ?search=
            $qs = $params ? ('?' . http_build_query($params)) : '';
            header('Location: ' . $_SERVER['PHP_SELF'] . $qs);
            exit;
        }

        // GET request
        $this->handleGetRequest();
        $this->showUsersTable();
    }

    private function handleGetRequest() {
        $this->search = isset($_GET['search']) ? trim($_GET['search']) : "";

        // 1) Count AFTER applying search
        $this->totalUsers = $this->userModel->countUsers($this->search);

        // 2) Build pagination
        $this->pagination = paginate($this->totalUsers, 8); // 5 users per page

        // 3) Fetch current page slice using SAME search
        $this->users = $this->userModel->getAllUsers(
            $this->pagination['limit'],
            $this->pagination['offset'],
            $this->search
        );
    }

    private function handlePostRequest() {
        // Register New User
        if (isset($_POST["registerNewUser"])) {
            require_once __DIR__ . '/../includes/registerUser.php';
            registerUserService($this->pdo, $_POST, $this->errors, false, true);
        }
        // Update User
        elseif (isset($_POST["editUser"])) {
            require_once __DIR__ . '/../includes/updateUser.php';
            updateUserService($this->pdo, $_POST, $this->errors);
        }
        // Delete User
        elseif (isset($_POST["deleteUser"])) {
            require_once __DIR__ . '/../includes/deleteUser.php';
            $userId = isset($_POST["userId"]) ? (int)$_POST["userId"] : 0;
            deleteUserService($this->pdo, $userId, $this->errors);
        }
        // No need to fetch users here; we redirect to GET afterward
    }

    private function showUsersTable() {
        // Expose locals for the view (avoid $this in templates if you prefer)
        $users       = $this->users;
        $pagination  = $this->pagination;
        $totalUsers  = $this->totalUsers;
        $searchTerm  = $this->search;

        include __DIR__ . '/../components/header.php';
        include __DIR__ . '/../view/usersView.php';
        include __DIR__ . '/../components/footer.php';
    }

    // Keep these only if your view still references $this->...
    public function getErrors()    { return $this->errors; }
    public function getUsers()     { return $this->users; }
    public function getSearchTerm(){ return $this->search; }
}
