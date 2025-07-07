





// document.getElementById("login-form").addEventListener("submit", function(e) {
//     e.preventDefault(); // prevent normal form submission
  
//     const formData = new FormData(this);
  
//     fetch("../assets//php/usersys/login.php", {
//       method: "POST",
//       body: formData
//     })
//     .then(response => response.json())
//     .then(data => {
//       if (data.success) {
//         window.location.href = "../../index.php"; // redirect if login successful
//       } else {
//         document.getElementById("login-message").textContent = data.message;
//       }
//     })
//     .catch(error => {
//       console.error("Error:", error);
//     });
//   });
  