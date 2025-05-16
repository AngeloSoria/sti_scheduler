<?php

// strict type
declare(strict_types=1);

// Database connection
require_once __DIR__ . '../../../config/dbConnection.php';

// vendor autoload
require __DIR__ . '../../../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '../../../');
$dotenv->load();

/**
 * Class FileUploader
 *
 * Handles file uploads with validation for file type and size.
 */
class FileUploader
{
    private $database;
    private $connection;
    /**
     * @var string $targetDir Directory where files will be uploaded.
     */
    private $targetDir;

    /**
     * @var array $allowedTypes Allowed file extensions.
     */
    private $allowedTypes;

    /**
     * @var int $maxFileSize Maximum allowed file size in bytes.
     */
    private $maxFileSize;

    /**
     * FileUploader constructor.
     *
     * @param string $targetDir Directory to upload files to.
     * @param array $allowedTypes Allowed file extensions (default: jpg, png, jpeg, gif).
     * @param int $maxFileSize Maximum file size in bytes (default: 2MB).
     */
    public function __construct($targetDir, $allowedTypes = ['jpg', 'png', 'jpeg', 'gif'], $maxFileSize = 25 * 1024 * 1024)
    {
        // Ensure the target directory ends with a slash
        $this->targetDir = rtrim($targetDir, '/') . '/';
        $this->allowedTypes = $allowedTypes;
        $this->maxFileSize = $maxFileSize;
        $this->database = new Database();
        $this->connection = $this->database->getConnection();
    }

    /**
     * Handles the file upload process.
     *
     * @param array $file The $_FILES['file'] array from the upload form.
     * @return array Result of the upload with 'success' or 'error' key.
     */
    public function upload($file)
    {
        // Check if the file was uploaded via HTTP POST
        if (!is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'File not uploaded.'];
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'File upload error: ' . $file['error']];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['error' => 'File size exceeds the maximum limit.'];
        }

        // Get file extension and validate it
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($fileExtension), $this->allowedTypes)) {
            return ['error' => 'Invalid file type.'];
        }

        // Generate a unique file name to prevent overwriting
        $newFileName = uniqid() . '.' . $fileExtension;
        $targetFilePath = $this->targetDir . $newFileName;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return ['success' => true, 'fileName' => $newFileName];
        } else {
            return ['error' => 'Failed to move uploaded file.'];
        }
    }

    public function uploadProfilePic($file, $userId)
    {
        // Check if the file was uploaded via HTTP POST
        if (!is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'File not uploaded.'];
        }

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'File upload error: ' . $file['error']];
        }

        // Check file size
        if ($file['size'] > $this->maxFileSize) {
            return ['error' => 'File size exceeds the maximum limit.'];
        }

        // Get file extension and validate it
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($fileExtension), $this->allowedTypes)) {
            return ['error' => 'Invalid file type.'];
        }

        // Generate a unique file name to prevent overwriting
        $newFileName = uniqid() . '.' . $fileExtension;
        $targetFilePath = $this->targetDir . $newFileName;
        $file_path = 'src/uploads/profile_pic/';

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            // Update the profile picture in the database
            $stmt = $this->connection->prepare("UPDATE users SET ProfilePic = ? WHERE UserID = ?");
            $stmt->execute([$file_path . $newFileName, $userId]);
            return ['success' => true, 'file_path' => $file_path . $newFileName];
        } else {
            return ['error' => 'Failed to move uploaded file.'];
        }
    }
}
