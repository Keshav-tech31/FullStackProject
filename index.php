<?php
require 'vendor/autoload.php';

// MongoDB connection
$uri = "mongodb+srv://Keshav:Keshav677@fullstackcluster.wun6jvs.mongodb.net/?retryWrites=true&w=majority&appName=FullStackCluster";

// Check if it's an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

try {
    // Connect to MongoDB Atlas
    $client = new MongoDB\Client($uri);
    $database = $client->FullStackDB;
    $collection = $database->students;
    
    // Process form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect form data
        $formData = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'rollno' => $_POST['rollno'] ?? '',
            'branch' => $_POST['branch'] ?? '',
            'admissionYear' => $_POST['admissionYear'] ?? '',
            'passoutYear' => $_POST['passoutYear'] ?? '',
            'feedback' => $_POST['feedback'] ?? '',
            'submissionDate' => date('Y-m-d H:i:s')
        ];
        
        // Insert data into MongoDB
        $insertResult = $collection->insertOne($formData);
        
        if ($insertResult->getInsertedCount() > 0) {
            // Success response
            $responseData = [
                'status' => 'success',
                'message' => 'Your review has been submitted successfully!'
            ];
        } else {
            // Error response
            $responseData = [
                'status' => 'error',
                'message' => 'Failed to submit your review. Please try again.'
            ];
        }
        
        // Handle response based on request type
        if ($isAjax) {
            // Return JSON for AJAX requests
            header('Content-Type: application/json');
            echo json_encode($responseData);
            exit;
        } else {
            // Redirect with status and message for regular form submission
            header("Location: index.html?status={$responseData['status']}&message=" . 
                   urlencode($responseData['message']));
            exit;
        }
    }
    
} catch (Exception $e) {
    // Handle connection or other errors
    $responseData = [
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ];
    
    if ($isAjax) {
        // Return JSON for AJAX requests
        header('Content-Type: application/json');
        echo json_encode($responseData);
        exit;
    } else {
        // Redirect with error message for regular form submission
        header("Location: index.html?status=error&message=" . 
               urlencode('Database error: ' . $e->getMessage()));
        exit;
    }
}
?>