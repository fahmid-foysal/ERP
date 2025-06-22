function logout() {
            if (confirm("Are you sure you want to logout?")) {
                $.ajax({
                    url: "https://mondolmotors.com/api/logout.php", // Use HTTPS
                    type: "POST",
                    dataType: "json",
                    xhrFields: {
                        withCredentials: true // Ensure session cookies are sent
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            window.location.href = "login.php"; // Redirect on success
                        } else {
                            alert("Logout failed: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Logout Error:", xhr.status, xhr.responseText);
                        alert("An error occurred while logging out.");
                    }
                });
            }
        }