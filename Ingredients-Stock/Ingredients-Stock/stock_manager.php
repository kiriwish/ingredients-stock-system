<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <title>Ingredients Stock Management System | Add Stock</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/button.css">
    <script src="vendor/jquery-3.2.1.min.js" charset="utf-8"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js" charset="utf-8"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="js/script.js"></script>
    <style>
        /* Add custom styles for notification button */
        .floating-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #007bff; /* Bootstrap primary color */
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            cursor: pointer;
        }
        .notification-panel {
            display: none; /* Hide by default */
            position: fixed;
            bottom: 90px; /* Adjust as needed */
            left: 20px;
            background-color: white;
            border: 1px solid #007bff; /* Bootstrap primary color */
            padding: 10px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Floating Notification Button -->
    <button id="notificationBtn" class="floating-btn" onclick="toggleNotificationPanel()">
        <i class="fa fa-bell"></i>
    </button>

    <!-- Notification Panel (hidden by default) -->
    <div id="notificationPanel" class="notification-panel">
        <h4>Notifications</h4>
        <ul>
            <li>No new notifications</li>
        </ul>
    </div>

    <?php include_once 'header.php'; ?>

    <!-- Code to select an Item -->
    <?php  
    if (isset($_POST['select'])) {
        $txtSelect = $_POST['txtSelect'];
        $getItemName = DB::query('SELECT ItemName FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName' => $txtSelect))[0]['ItemName'];
        $getItemCategory = DB::query('SELECT Category FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName' => $txtSelect))[0]['Category'];
        $getItemQuantity = DB::query('SELECT Quantity FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName' => $txtSelect))[0]['Quantity'];
        $getItemPrice = DB::query('SELECT Price FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName' => $txtSelect))[0]['Price'];
    }
    ?>

    <!-- Code to update Quantity -->
    <?php 
   if (isset($_POST['setStock'])) {
       $item_name = $_POST['item_name'];
       $item_quantity = $_POST['item_quantity'];
       $how_much = $_POST['how_much'];
   
       // Fetch price and current stock for the item 
       $item_price = DB::query('SELECT Price, Quantity FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName' => $item_name))[0];
       $current_stock = $item_price['Quantity'];
       $item_price = $item_price['Price'];
       
       // Calculate total amount
       $total_amount = $how_much * $item_price;
   
       // Check if there's enough stock
       if ($how_much > $current_stock) {
           $warning = "Sorry, insufficient stock. Available quantity: $current_stock.We will notify you when it is available";
       } else {
           // Update stock quantity
           $new_quantity = $current_stock - $how_much; // Calculate new stock quantity
           DB::query('UPDATE stock_ingredients SET Quantity=:Quantity WHERE ItemName=:ItemName', array(':ItemName' => $item_name, ':Quantity' => $new_quantity));
           
           // Create success message with total amount
           $success = "Stock Manager Updated the Quantity of this Item... Total Amount: KES " . number_format($total_amount, 2); // Added space after KES
           
           // Update notification panel
           echo "<script>notifyUser('$success');</script>";
       }
   }
   ?>

    <div class="container">
        <div class="container-fluid">
            <h3>Ingredients Stock Manager</h3>
        </div>
    </div>

    <div class="container">
        <?php if (isset($success)) { ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-success">
                        <strong>Success!</strong> &nbsp;<?php echo $success; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        
        <?php if (isset($warning)) { ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> &nbsp;<?php echo $warning; ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-sm-4">
                <div class="panel panel-default">
                    <div class="panel-heading"><span class="fa fa-area-chart"></span>&nbsp;&nbsp;Ingredients Stock Manager</div>
                    <div class="panel-body">
                        <div class="col-sm-15">
                            <form class="form-inline" action="stock_manager.php" method="POST">
                                <div class="form-group">
                                    <label for="txt_search_category">Search Stock Category</label>
                                    <select class="form-control" id="txt_search_category" name="txt_search_category" required>
                                        <option value="">Please Select..</option>
                                        <option value="Bread">Bread</option>
                                        <option value="Meat">Meat</option>
                                        <option value="Sea Food">Sea Food</option>
                                        <option value="Patty">Patty</option>
                                        <option value="Fruits">Fruits</option>
                                        <option value="Vegetables">Vegetables</option>
                                        <option value="DairyProducts">Dairy Products</option>
                                        <option value="Others">Others</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary" style="border-radius:0%;" type="submit" name="btn_search_category">Search</button>
                                </div>  
                            </form>
                        </div>
                    </div>  
                </div>
            </div>

            <div class="col-sm-8">
                <div class="panel panel-danger">
                    <div class="panel-heading"><span class="fa fa-globe"></span>&nbsp;&nbsp;Manage Section</div>
                    <div class="panel-body">
                        <form class="form" action="stock_manager.php" method="POST">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="item_name">Item Name</label>
                                    <input class="form-control text-center" type="text" name="item_name" id="item_name" value="<?php if (isset($getItemName)) { echo $getItemName; } ?>" readonly>
                                </div>
                            </div> 
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="item_Quantity">Quantity available</label>
                                    <input class="form-control text-center" min="1" type="number" name="item_quantity" id="item_quantity" value="<?php if (isset($getItemQuantity)) { echo $getItemQuantity; } ?>" readonly>
                                </div>
                            </div>  
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="quantity">Quantity to Order</label>
                                    <input class="form-control text-center" min="1" type="number" name="how_much" id="how_much" required>
                                </div>
                            </div>  
                            <div class="col-sm-12 text-center">
                                <div class="form-group">
                                    <button class="btn btn-success" style="border-radius:0%;" type="submit" name="setStock">Place Order</button>
                                </div>  
                            </div>  
                        </form>
                    </div>
                </div>
            </div>  
        </div>

        <div class="col-sm-15">
            <div class="panel panel-danger" id="panel_table">
                <div class="panel-heading"><span class="fa fa-desktop"></span>&nbsp;&nbsp;Items List</div>
                <div class="panel-body" id="div_table">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Date Added</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php 
    $items = DB::query('SELECT * FROM stock_ingredients'); // Ensure this query includes the necessary fields
    foreach ($items as $item) { 
        ?>
        <tr>
            <td><?php echo isset($item['ID']) ? $item['ID'] : 'N/A'; ?></td>
            <td><?php echo isset($item['ItemName']) ? $item['ItemName'] : 'N/A'; ?></td>
            <td><?php echo isset($item['Category']) ? $item['Category'] : 'N/A'; ?></td>
            <td><?php echo isset($item['Quantity']) ? $item['Quantity'] : 'N/A'; ?></td>
            <td><?php echo isset($item['Price']) ? 'KES' . number_format($item['Price'], 2) : 'N/A'; ?></td>
            <td><?php echo isset($item['DateAdded']) ? date('Y-m-d', strtotime($item['DateAdded'])) : '2024-10-24'; ?></td>
            <td>
                <form action="stock_manager.php" method="POST">
                    <input type="hidden" name="txtSelect" value="<?php echo $item['ItemName']; ?>">
                    <button type="submit" name="select" class="btn btn-primary btn-sm">Select</button>
                </form>
            </td>
        </tr>
    <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle the notification panel
        function toggleNotificationPanel() {
            const panel = document.getElementById('notificationPanel');
            panel.style.display = (panel.style.display === 'none' || panel.style.display === '') ? 'block' : 'none';
        }

        // Function to notify user
        function notifyUser(message) {
            const notificationList = document.querySelector('#notificationPanel ul');
            const newNotification = document.createElement('li');
            newNotification.textContent = message;
            notificationList.appendChild(newNotification);
            toggleNotificationPanel(); // Show notification panel
        }
    </script>
</body>
</html>

