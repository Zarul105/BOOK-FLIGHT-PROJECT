<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = 'localhost';
$dbname = 'flight_booking'; // Update this with your actual database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the booking ID (id) from the URL
    $booking_id = isset($_GET['id']) ? $_GET['id'] : null;

    // Check if the booking ID is provided
    if (!$booking_id) {
        die("Booking ID is missing. Please return to the <a href='admin_dashboard.php'>admin dashboard</a> and select a booking to edit.");
    }

    // Fetch the booking details from the database
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
    $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no booking found, display an error message
    if (!$booking) {
        die("Booking not found.");
    }

    // Pre-fill form data with existing booking data
    $customerName = $booking['customer_name'] ?? '';
    $departureDate = $booking['departure_date'] ? (new DateTime($booking['departure_date']))->format('Y-m-d') : '';
    $returnDate = $booking['return_date'] ? (new DateTime($booking['return_date']))->format('Y-m-d') : '';
    $departure = $booking['departure'] ?? '';
    $arrival = $booking['arrival'] ?? '';
    $seatClass = $booking['seat_class'] ?? '';  // Correct column name
    $numPassengers = $booking['num_passengers'] ?? '';
    $totalPayment = $booking['total_payment'] ?? '';

    // Handle form submission (to update booking)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the form data
        $customerName = $_POST['customer_name'];
        $departureDate = $_POST['departure_date'];
        $returnDate = $_POST['return_date'] ? $_POST['return_date'] : null;
        $departure = $_POST['departure'];
        $arrival = $_POST['arrival'];
        $seatClass = $_POST['seatClass']; // Correct column name
        $numPassengers = $_POST['num_passengers'];
        $totalPayment = $_POST['total_payment'];

        // Update the booking record in the database
        $updateStmt = $pdo->prepare("UPDATE bookings SET customer_name = :customerName, departure_date = :departureDate, return_date = :returnDate, departure = :departure, arrival = :arrival, seat_class = :seatClass, num_passengers = :numPassengers, total_payment = :totalPayment WHERE id = :id");
        $updateStmt->bindParam(':customerName', $customerName);
        $updateStmt->bindParam(':departureDate', $departureDate);
        $updateStmt->bindParam(':returnDate', $returnDate, PDO::PARAM_NULL);
        $updateStmt->bindParam(':departure', $departure);
        $updateStmt->bindParam(':arrival', $arrival);
        $updateStmt->bindParam(':seatClass', $seatClass);  // Correct column name
        $updateStmt->bindParam(':numPassengers', $numPassengers);
        $updateStmt->bindParam(':totalPayment', $totalPayment);
        $updateStmt->bindParam(':id', $booking_id, PDO::PARAM_INT);

        if ($updateStmt->execute()) {
            echo "Booking updated successfully!";
        } else {
            echo "Failed to update booking.";
        }
    }
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Edit flight booking record at Zym Airline Corporate.">
    <title>Edit Flight Booking Record</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #2c3e50;
            color: #000000;
        }

        .container {
            max-width: 600px;
            margin: 3rem auto;
            padding: 2rem;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            font-size: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        label {
            font-weight: bold;
            margin-top: 1rem;
            display: block;
        }

        input[type="text"],
        input[type="date"],
        select,
        input[type="number"] {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 0.8rem;
            margin-top: 1.5rem;
            background-color: #2c3e50;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #000000;
            transition: background-color 0.3s ease-in;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: white;
            background-color: #2c3e50;
            padding: 0.8rem;
            border-radius: 5px;
            text-decoration: none;
        }

        a:hover {
            background-color: #000000;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const seatClassSelect = document.getElementById('seatClass');
            const numPassengersInput = document.getElementById('num-passengers');
            const totalPaymentInput = document.getElementById('total-payment');

            function calculateTotalPayment() {
                let classPrice = 0;
                switch (seatClassSelect.value) {
                    case 'Economy':
                        classPrice = 150;
                        break;
                    case 'Business':
                        classPrice = 250;
                        break;
                    case 'First Class':
                        classPrice = 1000;
                        break;
                }

                const numPassengers = parseInt(numPassengersInput.value || 0);
                const totalPayment = numPassengers * classPrice;
                totalPaymentInput.value = totalPayment.toFixed(2);
            }

            seatClassSelect.addEventListener('change', calculateTotalPayment);
            numPassengersInput.addEventListener('input', calculateTotalPayment);

            calculateTotalPayment();
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Edit Flight Booking Record</h1>
        <form action="edit.php?id=<?php echo $booking['id']; ?>" method="post">
            <label for="customer-name">Customer Name</label>
            <input type="text" id="customer-name" name="customer_name" value="<?php echo htmlspecialchars($customerName); ?>" required>

            <label for="departure-date">Departure Date</label>
            <input type="date" id="departure-date" name="departure_date" value="<?php echo htmlspecialchars($departureDate); ?>" required>

            <label for="return-date">Return Date (Optional)</label>
            <input type="date" id="return-date" name="return_date" value="<?php echo htmlspecialchars($returnDate); ?>">

            <label for="departure">Departure</label>
            <input type="text" id="departure" name="departure" value="<?php echo htmlspecialchars($departure); ?>" required>

            <label for="arrival">Arrival</label>
            <input type="text" id="arrival" name="arrival" value="<?php echo htmlspecialchars($arrival); ?>" required>

            <label for="seatClass">Seat Class</label>
            <select id="seatClass" name="seatClass" required>
                <option value="Economy" <?php echo $seatClass == 'Economy' ? 'selected' : ''; ?>>Economy</option>
                <option value="Business" <?php echo $seatClass == 'Business' ? 'selected' : ''; ?>>Business</option>
                <option value="First Class" <?php echo $seatClass == 'First Class' ? 'selected' : ''; ?>>First Class</option>
            </select>

            <label for="num-passengers">Number of Passengers</label>
            <input type="number" id="num-passengers" name="num_passengers" value="<?php echo htmlspecialchars($numPassengers); ?>" required>

            <label for="total-payment">Total Payment</label>
            <input type="number" id="total-payment" name="total_payment" value="<?php echo htmlspecialchars($totalPayment); ?>" readonly>

            <button type="submit">Update Booking</button>
        </form>

        <!-- Back to Admin Dashboard Button -->
        <a href="admin_dashboard.php">Back to Admin Dashboard</a>
    </div>
</body>
</html>
