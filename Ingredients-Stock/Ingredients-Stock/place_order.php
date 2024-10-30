<?php
// Include the database connection
include 'connection/db.php';

if (isset($_POST['item_name']) && isset($_POST['how_much'])) {
    $item_name = $_POST['item_name'];
    $how_much = (int)$_POST['how_much'];

    // Fetch the current stock quantity for the selected item
    $current_stock = DB::query('SELECT Quantity FROM stock_ingredients WHERE ItemName = :ItemName', 
                               array(':ItemName' => $item_name))[0]['Quantity'];

    // Check if the requested quantity exceeds the available stock
    if ($how_much > $current_stock) {
        // Not enough stock to fulfill the order
        echo "Error: Insufficient stock for " . $item_name . ". Available quantity: " . $current_stock;
    } elseif ($current_stock <= 0) {
        echo "Error: This item is out of stock.";
    } else {
        // Proceed to update the stock
        DB::query('UPDATE stock_ingredients SET Quantity = Quantity - :how_much WHERE ItemName = :ItemName', 
                  array(':ItemName' => $item_name, ':how_much' => $how_much));

        // Insert a notification for the order placement
        DB::query('INSERT INTO notifications (message, date) VALUES (:message, NOW())', 
                  array(':message' => 'Order placed for ' . $how_much . ' units of ' . $item_name));

        echo "Order placed successfully for " . $how_much . " units of " . $item_name . ". Stock updated.";
    }
}
?>
