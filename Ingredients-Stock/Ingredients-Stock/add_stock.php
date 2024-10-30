<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <title>Ingredients Stock Management System | Add Stock</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <script src="vendor/jquery-3.2.1.min.js" charset="utf-8"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js" charset="utf-8"></script>
</head>
<body>
    <?php include_once 'header.php'; ?>

    <?php
    // Handle adding stock item
    if (isset($_POST['add_item'])) {
        // Capture form data and sanitize inputs
        $item_name = htmlspecialchars(trim($_POST['item_name']));
        $category = htmlspecialchars(trim($_POST['category']));
        $quantity = intval($_POST['quantity']);  // Cast to integer
        $price = floatval($_POST['price']);  // Cast to float
        $date = htmlspecialchars(trim($_POST['date']));

        // Check for missing inputs
        if (empty($item_name) || empty($category) || empty($quantity) || empty($price) || empty($date)) {
            $alert = "All fields are required. Please fill in all fields.";
        } else {
            // Check for duplicate entry
            if (!DB::query('SELECT ItemName FROM stock_ingredients WHERE ItemName=:ItemName', array(':ItemName' => $item_name))) {
                // Insert new item into the database
                DB::query('INSERT INTO stock_ingredients (ItemName, Category, Quantity, Price, Date) VALUES (:ItemName, :Category, :Quantity, :Price, :Date)',
                    array(':ItemName' => $item_name, ':Category' => $category, ':Quantity' => $quantity, ':Price' => $price, ':Date' => $date));
                $success = "Item added successfully!";
            } else {
                $alert = "Duplicate Entry of Item! Try again...";
            }
        }
    }

    // Handle deleting an item
    if (isset($_POST['delete'])) {
        $display_item_name = $_POST['display_item_name'];
        DB::query('DELETE FROM `stock_ingredients` WHERE ItemName=:ItemName', array(':ItemName' => $display_item_name));
        $delete_success = "Item Deleted Successfully!";
    }

    // Handle updating an item
    if (isset($_POST['update'])) {
        $display_item_name = $_POST['display_item_name'];
        $display_item_category = $_POST['display_item_category'];
        $display_item_quantity = intval($_POST['display_item_quantity']); // Cast to integer
        $display_item_price = floatval($_POST['display_item_price']); // Cast to float

        DB::query('UPDATE stock_ingredients SET Category=:Category, Quantity=:Quantity, Price=:Price WHERE ItemName=:ItemName',
            array(':Category' => $display_item_category, ':Quantity' => $display_item_quantity, ':Price' => $display_item_price, ':ItemName' => $display_item_name));
        $delete_success = "Item Updated Successfully!";
    }
    ?>

    <div class="container">
        <div class="container-fluid">
            <h3>Add Ingredients Stock</h3>
        </div>
        <?php
        if (isset($delete_success)) {
            echo '
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-success">
                            <strong>Success!</strong> &nbsp;' . $delete_success . '
                        </div>
                    </div>
                </div>
            ';
        }
        ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading"><span class="fa fa-cart-plus"></span>&nbsp;&nbsp;Add Ingredients Stock</div>
                        <div class="panel-body">
                            <?php
                            if (isset($alert)) {
                                echo '
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="alert alert-warning">
                                                <strong>Warning!</strong> &nbsp;' . $alert . '
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }
                            if (isset($success)) {
                                echo '
                                    <div class="row">
                                        <div class="col-sm-15">
                                            <div class="alert alert-success">
                                                <strong>Success!</strong> &nbsp;' . $success . '
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }
                            ?>
                            <form class="form" action="add_stock.php" method="post">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="item_name">Item Name</label>
                                        <input class="form-control" type="text" name="item_name" id="item_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="category">Category</label>
                                        <select class="form-control" id="category" name="category" required>
                                            <option value="">--Please Select--</option>
                                            <option value="Carbohydrates">Carbohydrates</option>
                                            <option value="Proteins">Proteins</option>
                                            <option value="Sea Food">Sea Food</option>
                                            <option value="Patty">Patty</option>
                                            <option value="Fruits">Fruits</option>
                                            <option value="Vegetables">Vegetables</option>
                                            <option value="DairyProducts">Dairy Products</option>
                                            <option value="Others">Others</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input class="form-control" type="number" min="1" name="quantity" id="quantity" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="price">Price</label>
                                        <input class="form-control" type="number" step="0.01" min="0" name="price" id="price" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="date">Date</label>
                                        <input class="form-control" type="date" name="date" id="date" required>
                                    </div>
                                    <div class="form-group">
                                        <input class="form-control btn btn-success" style="border-radius:0%;" type="submit" name="add_item" id="add_item" value="Add Item">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="panel panel-danger" id="panel_table">
                        <div class="panel-heading">Ingredients Stock List</div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="10%">Item Name</th>
                                            <th width="20%">Category</th>
                                            <th width="10%">Price</th>
                                            <th width="20%">Date</th>
                                            <th width="10%">Quantity</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $posts_display = $mysqli->query("SELECT * FROM stock_ingredients ORDER BY Id DESC");

                                        while ($posting = $posts_display->fetch_assoc()) {
                                            echo '
                                                 <form class="form" action="add_stock.php" method="post">
            <tr>
                <td><input type="text" value="' . htmlspecialchars($posting['ItemName']) . '" id="readonly" name="display_item_name" readonly></td>
                <td>
                    <select id="readonly" name="display_item_category">
                        <option>' . htmlspecialchars($posting['Category']) . '</option>
                        <option value="Carbohydrates">Carbohydrates</option>
                        <option value="Proteins">Proteins</option>
                        <option value="Sea Food">Sea Food</option>
                        <option value="Patty">Patty</option>
                        <option value="Fruits">Fruits</option>
                        <option value="Vegetables">Vegetables</option>
                        <option value="DairyProducts">Dairy Products</option>
                        <option value="Others">Others</option>
                    </select>
                </td>
                <td><input type="number" name="display_item_price" value="' . htmlspecialchars($posting['Price']) . '" min="0" step="0.01" required></td>
                <td><input type="date" name="display_item_date" value="' . htmlspecialchars($posting['Date']) . '" required></td>
                <td><input type="number" name="display_item_quantity" value="' . htmlspecialchars($posting['Quantity']) . '" min="1" required></td>
                <td>
                    <button type="submit" name="update" class="btn btn-warning">Update</button>
                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this item?\');">Delete</button>
                </td>
            </tr>
        </form>
    ';
}
?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>