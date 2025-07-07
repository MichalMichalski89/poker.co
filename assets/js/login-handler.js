$(document).ready(function () {
    $("#login-form").submit(function (e) {
      e.preventDefault(); // Prevent form's default submission behavior
  
      // Clear any existing error message
      $("#login-error").addClass("d-none").text("");
  
      // Serialize form data
      const formData = $(this).serialize();
  
      // Send AJAX request
      $.ajax({
        type: "POST",
        url: "assets/php/usersys/login.php",
        data: formData,
        dataType: "json",
        success: function (response) {
          if (response.success) {
            // Redirect to dashboard on success
            window.location.href = "dashboard/index.php";
          } else {
            // Show error message in modal
            $("#login-error").removeClass("d-none").text(response.message);
          }
        },
        error: function () {
          // AJAX call failed
          $("#login-error").removeClass("d-none").text("Server error, please try again.");
        }
      });
    });
  });
  