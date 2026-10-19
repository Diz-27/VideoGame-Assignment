<?php
    // include __DIR__ . '/games/index.php';
  $csvPath = __DIR__ . '/games/games.csv';

   $rows = [];

   

   $file = fopen($csvPath, 'r');
   

   while(($row = fgetcsv($file)) !== false){
    array_push($rows, $row);
    
   }

   fclose($file);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Listings</title>
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

        main{
            margin: 10px;
            padding: 5px;
            background-color: lightgray;
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

        h1 {
            display: flex;
            justify-content: center;
            align-items: center;
        }

    
        th {
            padding: 15px; /* Adds 15px of space on all sides inside the header */
        }

        td {
            padding: 12px; /* Adds 12px of space on all four sides inside the data cells */
        }

        img{
            max-width: 100px;
            height: auto;
        }



    </style>
</head>
<body>
    
    <?php include __DIR__ . '/includes/navigation.php'; ?>

    <h1>Video Game Listings</h1>

    <main>
       <table>
        <thead>
            <tr>
                <th>Inventory Code</th>
                <th>Game Name</th>
                <th>Game Console</th>
                <th>Price</th>
                <th>Uploaded File</th>
            </tr>

              <!-- Show a friendly message when $rows is empty. -->
      <?php if (empty($rows)): ?>
            <p>No game data was found.</p>
         <?php endif ?>
        <!-- Loop through $rows in this table. -->
     <?php foreach($rows as $row){ ?>
            <tr>
                <td><?= htmlspecialchars($row[0]) ?></td>
                <td><?= htmlspecialchars($row[1]) ?></td>
                <td><?= htmlspecialchars($row[2]) ?></td>
                <td>$<?= htmlspecialchars(number_format((float) $row[3], 2)) ?></td>
                <td><img src="<?php echo 'uploads/' . $row[4] ;?>" alt="<?=  htmlspecialchars($row[4]) ?>"></td>
            <tr>
            
        <?php } ?>
        </thead>
    </table>
    </main>

      
    
 
</body>
</html>