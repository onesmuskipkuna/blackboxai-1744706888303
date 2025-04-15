<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';

class Student {
    private $db;

    private $primaryClasses = ['PG', 'PP1', 'PP2', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
    private $juniorSecondaryClasses = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10'];

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function addStudent($firstName, $lastName, $guardianName, $phone, $schoolLevel, $class) {
        try {
            $sql = "INSERT INTO students (first_name, last_name, guardian_name, phone_number, school_level, class, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $this->db->query($sql, [$firstName, $lastName, $guardianName, $phone, $schoolLevel, $class]);
            return true;
        } catch (Exception $e) {
            error_log("Add student error: " . $e->getMessage());
            return false;
        }
    }

    public function getClassesByLevel($level) {
        if (strtolower($level) === 'primary') {
            return $this->primaryClasses;
        } elseif (strtolower($level) === 'junior secondary') {
            return $this->juniorSecondaryClasses;
        }
        return [];
    }

    public function promoteStudent($studentId, $toClass) {
        try {
            // Fetch current student class
            $sql = "SELECT class FROM students WHERE id = ?";
            $stmt = $this->db->query($sql, [$studentId]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$student) {
                return false;
            }
            $currentClass = $student['class'];

            // Validate promotion logic: ensure toClass is next class after currentClass
            $allClasses = array_merge($this->primaryClasses, $this->juniorSecondaryClasses);
            $currentIndex = array_search($currentClass, $allClasses);
            $toIndex = array_search($toClass, $allClasses);
            if ($toIndex !== $currentIndex + 1) {
                // Invalid promotion
                return false;
            }

            // Update student class
            $updateSql = "UPDATE students SET class = ? WHERE id = ?";
            $this->db->query($updateSql, [$toClass, $studentId]);

            // TODO: Handle fee balance carry forward and fee structure update

            return true;
        } catch (Exception $e) {
            error_log("Promote student error: " . $e->getMessage());
            return false;
        }
    }
}
