<?php

namespace App\Service;

use Exception;
use PDO;

class Database
{
    const STATUS_ARRAY = [
        'Not Started',
        'In Progress',
        'Completed'
    ];

    private $host = '127.0.0.1';
    private $port = 3307;
    private $user = 'root';
    private $pass = 'secret@123';
    private $db = 'mindatlas';

    /**
     * Connect to the DB.
     * 
     * @return PDO
     */
    private function connectDB()
    {
        $conn = new PDO('mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->db, $this->user, $this->pass);

        // Set the PDO mode to exception.
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;
    }

    /**
     * Run the select queries.
     * 
     * @return bool
     */
    private function runSelectQuery($query)
    {
        $conn = $this->connectDB();

        $stmt = $conn->prepare($query);
        $stmt->execute();

        // Set the resulting array to associative.
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $stmt->fetchAll();
    }

    /**
     * Run the operation queries.
     * 
     * @return bool
     */
    private function runOperationQuery($query)
    {
        $conn = $this->connectDB();

        $stmt = $conn->prepare($query);
        $stmt->execute();

        return true;
    }

    /**
     * Get the total count of tables.
     * 
     * @return array
     */
    public function getTablesCount()
    {
        try {
            $query = 'SELECT count(*) AS total FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = "' . $this->db . '"';

            return $this->runSelectQuery($query);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Create all tables.
     * 
     * @return bool
     */
    public function createTable()
    {
        try {
            // Query to create table.
            $createUsersSql = "CREATE TABLE Users (
            user_id INT NOT NULL AUTO_INCREMENT,
            fname VARCHAR(255),
            surname VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (user_id));";

            $createCoursesSql = "CREATE TABLE Courses (
            course_id INT NOT NULL AUTO_INCREMENT,
            description VARCHAR(255),
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (course_id));";

            $createEnrolmentsSql = "CREATE TABLE Enrolments (
            enrolment_id INT NOT NULL AUTO_INCREMENT,
            user_id INT NOT NULL,
            course_id INT NOT NULL,
            status ENUM('Not Started', 'In Progress', 'Completed') DEFAULT 'Not Started',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (enrolment_id));";

            return $this->runOperationQuery($createUsersSql . $createCoursesSql . $createEnrolmentsSql);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Seed the Users.
     * 
     * @return bool
     */
    public function seedUsers()
    {
        // User Names.
        $userNames = array(
            'Christopher Walker',
            'Ryan Thompson',
            'Ethan Anderson',
            'John Johnson',
            'Zoey Tremblay',
            'Sarah Peltier',
            'Michelle Cunningham',
            'Samantha Simpson',
            'Bishal Dhakal',
            'Anjali Majhi'
        );

        $insertSql = '';

        foreach ($userNames as $userName) {
            $splitNames = explode(' ', $userName);

            $insertSql = $insertSql . "INSERT INTO Users (fname, surname) VALUES ('$splitNames[0]', '$splitNames[1]');";
        }

        return $this->runOperationQuery($insertSql);
    }

    /**
     * Seed the Courses.
     * 
     * @return bool
     */
    private function seedCourses()
    {
        // Course Names.
        $courseNames = [
            'English',
            'Math',
            'Art',
            'Science',
            'History',
            'Music',
            'Geography',
            'P.E (Physical Education)',
            'Drama',
            'Biology',
            'Chemistry',
            'Physics',
            'I.T (Information Technology)',
            'Foreign Languages',
            'Social Studies',
            'Technology',
            'Philosophy',
            'Graphic Design',
            'Literature',
            'Algebra',
            'Geometry'
        ];

        $insertSql = '';

        foreach ($courseNames as $courseName) {
            $insertSql = $insertSql . "INSERT INTO Courses (description) VALUES ('$courseName');";
        }

        return $this->runOperationQuery($insertSql);
    }

    /**
     * Seed the Enrolments.
     * 
     * @return bool
     */
    private function seedEnrolments()
    {
        $users = $this->getUsers();
        $courses = $this->getCourses();

        $status = [
            'Not Started',
            'In Progress',
            'Completed'
        ];

        $insertSql = '';

        foreach ($users as $user) {
            $userId = $user['user_id'];

            foreach ($courses as $course) {
                $courseId = $course['course_id'];
                $selectedStatus = $status[rand(0, 2)];
                $insertSql = $insertSql . "INSERT INTO Enrolments (user_id, course_id, status) VALUES ($userId, $courseId, '$selectedStatus');";
            }
        }

        return $this->runOperationQuery($insertSql);
    }

    /**
     * Seed all data.
     * 
     * @return json
     */
    public function seedData()
    {
        try {
            // Seed Users.
            $this->seedUsers();

            // Seed Courses.
            $this->seedCourses();

            // Seed Enrolments.
            $this->seedEnrolments();

            echo json_encode([
                'error' => 0,
                'message' => 'Successful',
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'error' => 1,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the Users.
     * 
     * @return array
     */
    public function getUsers()
    {
        try {
            $query = "SELECT * FROM Users";

            return $this->runSelectQuery($query);
        } catch (Exception $e) {

            return [];
        }
    }

    /**
     * Get the Courses.
     * 
     * @return array
     */
    public function getCourses()
    {
        try {
            $query = "SELECT * FROM Courses";

            return $this->runSelectQuery($query);
        } catch (Exception $e) {

            return [];
        }
    }

    /**
     * Get the Enrolments.
     * 
     * @param bool $getAll
     * 
     * @return array
     */
    public function getEnrolments($getAll = false)
    {
        try {
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 10;
            $startLimit = ($page - 1) * $perPage;

            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $course = isset($_GET['course']) ? (int) $_GET['course'] : null;
            $user = isset($_GET['user']) ? (int) $_GET['user'] : null;
            $status = isset($_GET['status']) ? $_GET['status'] : null;

            $query = "SELECT * FROM Enrolments
            LEFT JOIN Users ON Enrolments.user_id = Users.user_id 
            LEFT JOIN Courses ON Enrolments.course_id = Courses.course_id";

            $whereClauses = '';

            if (isset($course) && $course) {
                $whereClauses = " WHERE Enrolments.course_id = $course ";
            }

            if (isset($user) && $user) {
                $whereClauses = $whereClauses !== '' ? $whereClauses . 'AND ' : ' WHERE ';
                $whereClauses = $whereClauses  . "Enrolments.user_id = $user ";
            }

            if (isset($status) && $status) {
                $whereClauses = $whereClauses !== '' ? $whereClauses . 'AND ' : ' WHERE ';
                $whereClauses = $whereClauses . "Enrolments.status = '$status' ";
            }

            if ($search !== '') {
                $whereClauses = $whereClauses !== '' ? $whereClauses . 'AND ' : ' WHERE ';
                $whereClauses = $whereClauses . "Users.fname LIKE '$search%' OR Users.surname LIKE '$search%' OR
                Courses.description LIKE '$search%'";
            }

            $query = $query . $whereClauses;

            if (!$getAll) {
                $query = $query . " LIMIT $startLimit,$perPage;";
            }

            return $this->runSelectQuery($query);
        } catch (Exception $e) {

            return [];
        }
    }

    /**
     * Get the page count.
     * 
     * @param int $perPage
     * 
     * @return int
     */
    public function getPageCount($perPage)
    {
        $enrolments = count($this->getEnrolments(true));

        return ceil($enrolments / $perPage);
    }

    /**
     * Get the total records count.
     * 
     * @return int
     */
    public function getTotalRecords()
    {
        return count($this->getEnrolments(true));
    }

    /**
     * Get all the filters for $_GET request.
     * 
     * @return array
     */
    public function getAllFilters()
    {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 10;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $courseId = isset($_GET['course']) ? (int) $_GET['course'] : null;
        $userId = isset($_GET['user']) ? (int) $_GET['user'] : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;

        return [
            $page,
            $perPage,
            $search,
            $courseId,
            $userId,
            $status
        ];
    }
}
