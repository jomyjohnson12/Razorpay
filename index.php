<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpage.php");
    exit();
}

require_once 'db_connection.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT fullname FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$fullname = ($result->num_rows > 0) ? htmlspecialchars($result->fetch_assoc()['fullname']) : "Guest";

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Welcome Page</title>
    <!-- Link External CSS -->
    <link rel="stylesheet" href="stylemain.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <a href="logout.php" class="logout-btn">Logout</a>
    <h1>Welcome, <?php echo $fullname; ?>!</h1>

    <h2>Pay with Razorpay</h2>
    <div class="payment-form">
        <h2>Payment Details</h2>
        <div class="form-group">
            <label for="amount">Amount (INR)</label>
            <input type="text" id="amount" class="input-box" placeholder="Enter Amount">
        </div>
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" class="input-box" placeholder="Enter Name">
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" class="input-box" placeholder="Enter Email">
        </div>
        <div class="form-group">
            <label for="contact">Contact Number</label>
            <input type="text" id="contact" class="input-box" placeholder="Enter Contact Number">
        </div>
        <div class="form-group">
            <label for="description">Payment Description</label>
            <textarea id="description" class="input-box description-box" placeholder="Enter Description"></textarea>
        </div>
        <button id="pay-btn" class="pay-btn">Proceed to Pay</button>
    </div>

    <!-- Payment Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Payment Successful!</h2>
            <p id="successMessage"></p>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close error-close">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage"></p>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var successModal = $('#successModal');
            var errorModal = $('#errorModal');
            var closeButtons = $('.close');

            // Close Modal on X click
            closeButtons.click(function () {
                successModal.hide();
                errorModal.hide();
            });

            // Close Modal on outside click
            $(window).click(function (event) {
                if (event.target === successModal[0]) {
                    successModal.hide();
                }
                if (event.target === errorModal[0]) {
                    errorModal.hide();
                }
            });

            $("#pay-btn").click(function () {
                var amount = $("#amount").val() * 100;
                var name = $("#name").val();
                var email = $("#email").val();
                var contact = $("#contact").val();
                var description = $("#description").val();

                if (!amount || !name || !email || !contact || !description) {
                    $("#errorMessage").text("Please fill all the fields.");
                    errorModal.show();
                    return;
                }

                $.ajax({
                    url: "order.php",
                    type: "GET",
                    data: { 
                        amount: amount, 
                        name: name, 
                        email: email, 
                        contact: contact, 
                        description: description 
                    },
                    success: function (response) {
                        var order = JSON.parse(response);
                        var options = {
                            "key": "rzp_test_Mhi7WOLwO9KURC",
                            "amount": order.amount,
                            "currency": "INR",
                            "name": name,
                            "description": description,
                            "order_id": order.id,
                            "handler": function (response) {
                                $.ajax({
                                    url: "verify.php",
                                    type: "POST",
                                    data: {
                                        payment_id: response.razorpay_payment_id,
                                        order_id: response.razorpay_order_id,
                                        signature: response.razorpay_signature
                                    },
                                    success: function () {
                                        // Clear input fields
                                        $("#amount").val('');
                                        $("#name").val('');
                                        $("#email").val('');
                                        $("#contact").val('');
                                        $("#description").val('');

                                        // Show success modal
                                        $("#successMessage").text("Order ID: " + response.razorpay_order_id);
                                        $("#successModal").show();
                                    }
                                });
                            },
                            "prefill": { "name": name, "email": email, "contact": contact },
                            "theme": { "color": "#3399cc" }
                        };
                        var rzp1 = new Razorpay(options);
                        rzp1.open();
                    }
                });
            });
        });
    </script>
</body>

</html>
