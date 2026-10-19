<?php

    /*** Initialize the page and recognize a Post Request */
    $errors = [];
    $inventory = '';
    $gameName = '';
    $gameConsole = '';
    $price = 0;
    $fileUpload = false;

    // Validating Area
    if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $inventory = strtoupper(trim(filter_input(INPUT_POST, 'inventoryCode') ?? ''));
        $gameName = trim(filter_input(INPUT_POST, 'gameName') ?? '');
        $gameConsole = trim(filter_input(INPUT_POST, 'console') ?? '');
        $price = trim(filter_input(INPUT_POST, 'price') ?? 0);

        if(!$inventory){
            $errors['inventory'] = 'Inventory code is required.';
        }
        elseif(!preg_match('/^GAME-\d{4}$/', $inventory)) {
            $errors['inventory'] = 'Inventory code must follow the format GAME-1234.';
        }

        if(!$gameName){
            $errors['gameName'] = 'Enter the name of the game.';
        }

        // This arrary is made so we can make a form validation of the selected values
        $consoleArray = [
            'xbox360',
            'xbox1',
            'ps4',
            'ps5',
            'switch1',
            'switch2',
            'steam'
        ];

        if(!in_array($gameConsole, $consoleArray, true)){
            $errors['console'] = 'Choose a valid game console.';
        }
        

         if($price === 0) {
            $errors['price'] = 'Please enter a price.';
        } elseif(!is_numeric($price) || (float)$price < 0) {
            $errors['price'] = 'Please enter a valid price of $0.00 up to $10000.00.';
        }
     
        if(!isset($_FILES['fileUpload'])){
            die('No file uploaded.');

        }

        $uploadedFile = $_FILES['fileUpload'];
        print_r($uploadedFile);

        // We created this because we couldn't find milliseconds in Google.
        $now = DateTime::createFromFormat('U.u', microtime(true));
        //This will put in the extention of the file. This is perserving the orignal extention.
        //Can't use : as part of a file format. 
        // this is the way to create a unique file name using milliseconds.
        $filename = $now->format('Hisv') . '.' . pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
        echo($filename);

        $destination = __DIR__ . '/uploads/' . $filename;
        echo($destination);
        // I honestly don't know what this mean. I just copy from our in class lab.
        //This function in php is used to move an uploaded file to a new location.
         move_uploaded_file(
            $uploadedFile['tmp_name'],
            $destination
        );

        $csvPath = __DIR__ . '/games/games.csv';
        // I'm sure this is appending the $csvPath
        $fileHandle = fopen($csvPath, 'a');

        // Game list is the array so I can make rows in the csv. I had to chose $filename because we need the path of the image as a reference instead of the actual image.
        $gameList = [$inventory, $gameName, $gameConsole, $price, $filename];
        fputcsv($fileHandle, $gameList);
        

        fclose($fileHandle);

        
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Game Store</title>
    <?php require __DIR__ . '/includes/bootstrap.php'; ?>
    <style>
        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body{
            background-color: #f4f7f6;
            color: #2d3748;
            line-height: 1.6;
            padding: 40px 20px;
        }

        .navbaar .navbar-brand{
            display: inline-block;
            background-color: #888585;
            color: #e9ebed;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <?php require __DIR__ . '/includes/navigation.php'; ?>
    <!-- Display error summary -->
 <?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert">
        <h3 class="h5">Please correct the following:</h3>
            <ul class="mb-0">
                <?php foreach ($errors as $message): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
    </div>
    <?php endif; ?>

    <!-- Display success message -->
    <?php if($fileUpload): ?>
        <div class="alert alert-success" role="status">
            You sucessfully uploaded your file.
        </div>
    <?php endif; ?>

    <form action="index.php" method="post" enctype="multipart/form-data">

         <!-- Inventory Code Label -->
        <div class="mb-3">
            <label for="inventoryCode">Inventory Code:</label>
            <input class="form-control <?= isset($errors['inventoryCode']) ? 'is-invalid' : '' ?>" type="text" name="inventoryCode" id="inventoryCode"
            value="<?= htmlspecialchars($inventory) ?>" placeholder="GAME-1234" require>

             <!-- Inventory Code Sticky Error Message -->
            <?php if (isset($errors['inventoryCode'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['inventoryCode'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

         <!-- Video Game Name Label -->
        <div class="mb-3">
            <label for="gameName">Video Game Name:</label>
            <input class="form-control <?= isset($errors['gameName']) ? 'is-invalid' : '' ?>" type="text" name="gameName" id="gameName" value="<?= htmlspecialchars($gameName) ?>">

            <!-- Game Name Sticky Error Message -->
            <?php if (isset($errors['gameName'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['gameName'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>



         <!-- Console Label -->
        <div class="mb-3">
            <label for="console"></label>
            <select class="form-select <?= isset($errors['console']) ? 'is-invalid' : '' ?>" name="console" id="console">
                <option selected>Choose a game console</option>
                <option value="xbox360">Xbox360</option>
                <option value="xbox1">Xbox One</option>
                <option value="ps4">PS4</option>
                <option value="ps5">PS5</option>
                <option value="switch1">Nintendo Switch</option>
                <option value="switch2">Nintendo Switch 2</option>
                <option value="steam">Steam</option>
            </select>

             <!-- Console Sticky Error Message -->
            <?php if (isset($errors['console'])): ?>
                <div class="invalid-feedback">
                    <?= htmlspecialchars($errors['console']) ?>
                </div>
            <?php endif; ?>
        </div>

         <!-- Price Label -->
        <div class="mb-3">
            <label for="price">Enter Price ($):</label>
            <input type="number" name="price" id="price" min="0.00" max="10000.00" placeholder="0.00">

            <!-- Price Sticky Error Message -->
            <?php if (isset($errors['price'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>

        <!-- File Upload Label -->
        <div class="mb-3">
            <label for="fileUpload">Choose a file to upload:</label>
            <input type="file" id="fileUpload" name=fileUpload>

               <?php if (isset($errors['fileUpload'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['fileUpload'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-secondary">Submit</button>
    </form>
</body>
</html>