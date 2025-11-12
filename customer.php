<?php 
include('db_con.php'); 

// ✅ Fetch existing customers from the database
$query = "
    SELECT 
        c.CustomerID, 
        c.Name, 
        c.Age, 
        c.Gender, 
        c.Email, 
        c.ContactNo, 
        c.Address,
        o.OrderStatus
    FROM customer AS c
    LEFT JOIN `order` AS o 
        ON c.CustomerID = o.CustomerID
        AND o.OrderID = (
            SELECT MAX(o2.OrderID)
            FROM `order` AS o2
            WHERE o2.CustomerID = c.CustomerID
        )
    ORDER BY c.CustomerID ASC
";
$result = mysqli_query($connection, $query);
$customers = mysqli_fetch_all($result, MYSQLI_ASSOC);

// ✅ Handle new customer form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $CustomerID = $_POST['id'];
    $Name = $_POST['name'];
    $Age = $_POST['age'];
    $Gender = $_POST['gender'];
    $Email = $_POST['email'];
    $Contact = $_POST['contact'];
    $Address = $_POST['address'];
    $Status = $_POST['status'];

    $insert = "INSERT INTO customer (CustomerID, Name, Age, Gender, Email, ContactNo, Address)
               VALUES ('$CustomerID', '$Name', '$Age', '$Gender', '$Email', '$Contact', '$Address')";
    $insertCustomerResult = mysqli_query($connection, $insert);
    if ($insertCustomerResult) {
        // Redirect to refresh page and avoid re-submission
        $insertOrder = "
        INSERT INTO `order` (CustomerID, OrderStatus)
        VALUES ('$CustomerID', '$Status')
    ";
    $insertOrderResult = mysqli_query($connection, $insertOrder);
    } 
    if ($insertOrderResult) {
        echo "<script>alert('Customer and Order added successfully!');</script>";
    } else {
        echo "<script>alert('Order insert failed: " . mysqli_error($connection) . "');</script>";
    }
}
else{
    echo "<script>alert('Customer insert failed: " . mysqli_error($connection) . "');</script>";
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TailorPro - Customer Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar bg-dark p-3 text-white">
    <button class="btn btn-outline-light" onclick="history.back()">← Back</button>
    <h3 class="ms-3">✂️ TailorPro - Customer Management</h3>
</header>

<main class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2>Customer Management</h2>
            <p class="text-muted">Track and manage your customers</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#newCustomer">+ New Customer</button>
    </div>

    <!-- 🧾 New Customer Form -->
    <div id="newCustomer" class="collapse mb-4">
        <div class="card card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Customer ID</label>
                        <input type="text" name="CustomerID" class="form-control" placeholder="CUST006" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="Name" class="form-control" placeholder="Customer Name" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Age</label>
                        <input type="number" name="Age" class="form-control" placeholder="Age" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Gender</label>
                        <select name="Gender" class="form-select" required>
                            <option value="">Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="example@gmail.com" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="Contact" class="form-control" placeholder="Phone number" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Address</label>
                        <input type="text" name="Address" class="form-control" placeholder="Address" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="OrderStatus" class="form-select">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-success" type="submit">Save Customer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🧍 Customer Table -->
    <table class="table table-bordered table-striped bg-white">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
                <?php 
                    // convert status to css class like "status-completed"
                    $statusClass = strtolower(str_replace(' ', '-', $c['status']));
                ?>
                <tr>
                    <td><?= htmlspecialchars($c['CustomerID']); ?></td>
                    <td><?= htmlspecialchars($c['Name']); ?></td>
                    <td><?= htmlspecialchars($c['Age']); ?></td>
                    <td><?= htmlspecialchars($c['Gender']); ?></td>
                    <td><?= htmlspecialchars($c['Email']); ?></td>
                    <td><?= htmlspecialchars($c['ContactNo']); ?></td>
                    <td><?= htmlspecialchars($c['Address']); ?></td>
                    <td><span class="status <?= $statusClass; ?>"><?= htmlspecialchars($c['OrderStatus']); ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
