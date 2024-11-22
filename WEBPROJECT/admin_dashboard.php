<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit;
}

require 'db_connection.php'; // Ensure you have a database connection file

// Fetch all flight bookings for display
$sql = "SELECT * FROM bookings";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Flight Bookings</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #1a1a2e;
            color: #eaeaea;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            background: #16213e;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }
        h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #eaeaea;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            color: #eaeaea;
        }
        th, td {
            border: 1px solid #4e4e50;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #0f3460;
            color: #eaeaea;
        }
        tr:nth-child(even) {
            background-color: #1a1a2e;
        }
        tr:hover {
            background-color: #53354a;
        }
        .button {
            padding: 0.6rem 1.2rem;
            color: white;
            background-color: #ff4c29;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .button:hover {
            background-color: #16213e;
            color: #ff4c29;
            transform: scale(1.05);
        }
        .link-button {
            text-decoration: none;
            color: #eaeaea;
            transition: color 0.3s ease;
        }
        .link-button:hover {
            color: #ffd369;
        }
        .action-links a {
            margin: 0 5px;
        }
        @media screen and (max-width: 768px) {
            table, th, td {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard - Flight Bookings</h1>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Departure Date</th>
                    <th>Return Date</th>
                    <th>Class</th>
                    <th>Total Payment</th>
                    <th>Passengers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['departure']) ?></td>
                            <td><?= htmlspecialchars($row['arrival']) ?></td>
                            <td><?= htmlspecialchars($row['departure_date']) ?></td>
                            <td><?= htmlspecialchars($row['return_date'] ?: 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['seat_class']) ?></td>
                            <td>RM<?= htmlspecialchars($row['total_payment']) ?></td>
                            <td><?= htmlspecialchars($row['num_passengers']) ?></td>
                            <td class="action-links">
                                <a href="edit.php?id=<?= urlencode($row['id']) ?>" class="link-button">Edit</a>
                                <a href="delete.php?id=<?= urlencode($row['id']) ?>" 
                                   class="link-button" 
                                   onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Back to Admin Dashboard button -->
        <a href="admin.php" class="button">Back to Admin Panel</a>
    </div>
</body>
</html>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['customer_name']) ?></td>
                            <td><?= htmlspecialchars($row['departure']) ?></td>
                            <td><?= htmlspecialchars($row['arrival']) ?></td>
                            <td><?= htmlspecialchars($row['departure_date']) ?></td>
                            <td><?= htmlspecialchars($row['return_date'] ?: 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['seat_class']) ?></td>
                            <td>$<?= htmlspecialchars($row['total_payment']) ?></td>
                            <td><?= htmlspecialchars($row['num_passengers']) ?></td>
                            <td>
                                <!-- Edit link -->
                                <a href="edit.php?id=<?= urlencode($row['id']) ?>" class="link-button">Edit</a>

                                <!-- Delete link -->
                                <a href="delete.php?id=<?= urlencode($row['id']) ?>" 
                                   class="link-button" 
                                   onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>
</html>