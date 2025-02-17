<!-- loginpage.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup Form</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <section class="wrapper">
        <div class="form signup">
            <header>Signup</header>
            <form id="signupForm">
                <input type="text" name="fullname" placeholder="Full name" required>
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <div class="checkbox">
                    <input type="checkbox" id="signupCheck" required>
                    <label for="signupCheck">I accept all terms & conditions</label>
                </div>
                <input type="submit" value="Signup">
            </form>
        </div>
        <div class="form login">
            <header>Login</header>
            <form id="loginForm">
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <a href="#">Forgot password?</a>
                <input type="submit" value="Login">
            </form>
        </div>
    </section>

    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <span id="toastMessage"></span>
    </div>

    <script>
        const wrapper = document.querySelector(".wrapper"),
            signupHeader = document.querySelector(".signup header"),
            loginHeader = document.querySelector(".login header");

        loginHeader.addEventListener("click", () => {
            wrapper.classList.add("active");
        });

        signupHeader.addEventListener("click", () => {
            wrapper.classList.remove("active");
        });

        // Function to show toast notification
        function showToast(message, type = "success") {
            const toast = document.getElementById("toast");
            const toastMessage = document.getElementById("toastMessage");

            // Set the toast message
            toastMessage.textContent = message;

            // Set toast color based on type
            toast.style.backgroundColor = type === "success" ? "#4CAF50" : "#E74C3C";

            // Show the toast
            toast.classList.add("show");

            // Hide the toast after 3 seconds
            setTimeout(() => {
                toast.classList.remove("show");
            }, 3000);
        }

        // Handle signup form submission
        document.getElementById("signupForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = {
        fullname: document.querySelector("input[name='fullname']").value.trim(),
        email: document.querySelector("input[name='email']").value.trim(),
        password: document.querySelector("input[name='password']").value.trim(),
    };

    console.log("Sending data:", JSON.stringify(formData)); // Debugging line

    fetch("signup.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData),
    })
        .then(response => response.json())
        .then(data => {
            console.log("Response received:", data); // Debugging line

            showToast(data.message, data.status === "success" ? "success" : "error");

            if (data.status === "success") {
                document.getElementById("signupForm").reset(); // Clear form on success
            }
        })
        .catch(error => {
            console.error("Fetch error:", error);
            showToast("An error occurred. Please try again.", "error");
        });
});


        // Handle login form submission
        // Handle login form submission
       
        document.getElementById("loginForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            const data = {
                email: formData.get("email"),
                password: formData.get("password"),
            };

            fetch("login.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(data => {
                console.log("Server Response:", data);

                showToast(data.message, data.status === "success" ? "success" : "error");

                if (data.status === "success") {
                    setTimeout(() => {
                        console.log("Redirecting to:", data.redirect);
                        window.location.href = data.redirect;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showToast("An error occurred. Please try again.", "error");
            });
        });




    </script>


</body>

</html>
