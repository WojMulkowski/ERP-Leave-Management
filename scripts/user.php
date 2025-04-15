<?php
class User{
    private $id;
    private $firstName;
    private $lastName;
    private $email;
    private $password;
    private $birthDate;
    private $gender;
    private $roleId;
    private string $employedFrom;
    private string $accountCreatedDate;
    function __construct($id, $firstName, $lastName, $email, $password, $birthDate, $gender, $roleID, $employedFrom, $accountCreatedDate){
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->birthDate = $birthDate;
        $this->gender = $gender;
        $this->roleId = $roleID;
        $this->employedFrom = $employedFrom;
        $this->accountCreatedDate = $accountCreatedDate;
    }
    public function getId(): int { return $this->id; }
    public function getFirstname(): string { return $this->firstName; }
    public function getLastname(): string { return $this->lastName; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getBirthDate(): string { return $this->birthDate; }
    public function getGender(): string { return $this->gender; }
    public function getRoleId(): int { return $this->roleId; }
    public function getEmployedFrom(): string { return $this->employedFrom; }
    public function getAccountCreatedDate(): string { return $this->accountCreatedDate; }

    public static function findUser($email, $password, $conn){
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && password_verify($password, $row['password'])) {
            return new User($row["id"], $row["firstname"], $row["lastname"], $row["email"], $row["password"], $row["birth_date"], $row["gender"], $row["role_id"], $row["employed_from"], $row["account_created_date"]);
        } else {
            return null;
        }
    }
    public static function logInUser(User $user) {
        $_SESSION['logged_user'] = array(
            'id'       => $user->getId(),
            'firstname'=> $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email'    => $user->getEmail(),
            'birth_date' => $user->getBirthDate(),
            'gender'   => $user->getGender(),
            'role_id'  => $user->getRoleId(),
            'employed_from' => $user->getEmployedFrom(),
            'account_created_date' => $user->getAccountCreatedDate()
        );
    }
    public static function logOutUser(){
        session_unset();
        session_destroy();
        header('Location: ../index.php');
        exit();
    }

    
}
?>