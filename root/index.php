<?php
// Include the header file
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toy Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #0056b3;
        }
        p {
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to My PHP Page!</h1>
        <p>This is the main content of your `index.php` file.</p>
        <p>It's a simple example demonstrating how to include header and footer files in PHP.</p>
        <p>You can add more dynamic content or logic here.</p>
    </div>
</body>
</html>

<?php
// Include the footer file
include 'includes/footer.php';
?>
