

document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("registerForm").addEventListener("submit", function (e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        fetch("register.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => alert(data));
    });

    document.getElementById("loginForm").addEventListener("submit", function (e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        fetch("login.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes("successful")) {
                window.location.href = "dashboard.php";
            } else {
                alert(data);
            }
        });
    });
});
