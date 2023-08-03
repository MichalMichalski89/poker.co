
// innitiate form validation

(function () {
    'use strict'
      console.log("bs val starts");
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')
  
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
      .forEach(function (form) {
        form.addEventListener('submit', function (event) {
  
          alert("checking form form validity: " + form.checkValidity())
  
  
           if (!form.checkValidity()) {
             event.preventDefault();
             event.stopPropagation();
           }
  
          form.classList.add('was-validated')
        }, false)
      })
      console.log("bootstrap validation passed");
  })();

  // run validation of fields on key up

  window.onload = function () {
  console.log("working");
  //document.getElementById("").addEventListener("keyup", ()=>{alert("changin")});

  // registration fields
  $('#username2').keyup(validateUser);
  $('#email2').keyup(validateEmail);
  $('#password2').keyup(validatePassword);
  $('#confirm-password').keyup(validatePasswordConfirm);

  }


// check if user name already exists on database

  function validateUser(event){
    // reset duplicate error message
    // $(event.target).siblings("div.duplicate").addClass("d-none");
    // event.target.setCustomValidity("");



    console.log("validator running with: " + event.target.value);
    //console.log(this);

    // check if basic validation passed
    if (basicValidation(event.target)){
       console.log("basic validation passed, checking for duplicates");

       
    // check for duplicates and show/hide messages 
    getData("./assets/php/usersys/userVerify.php", {username: event.target.value})
        .then((availablility) => {
            console.log("php returned: " + availablility);
            console.log(event.target.checkValidity());
            //let validity = event.target.checkValidity()

            if(!availablility) {

                // do if not available
                event.target.setCustomValidity( event.target.value + "database duplicate error");
                //$(event.target).addClass("is-invalid");
                $(event.target).siblings("div.duplicate").removeClass("d-none");
                $(event.target).siblings("div.invalid-feedback").addClass("d-none");
                $(event.target).siblings("div.valid-feedback").addClass("d-none");
                console.log("duplicates found")

            } else {
                $(event.target).siblings("div.duplicate").addClass("d-none");
                $(event.target).siblings("invalid-feedback").addClass("d-none");
                event.target.setCustomValidity("");
                console.log("NO duplicates found")
            }
        });      
    }  else {
        console.log("FAILLLL")
    }

}



//check if email address already exists on the database

function validateEmail(event){

    //$(event.target).val(this.value.toLowerCase())

    console.log("validator running with: " + $(event.target)[0].value.toLowerCase());
    //console.log(this);

    getData("./assets/php/usersys/emailVerify.php", {email: (event.target.value).toLowerCase()})
        .then((availablility) => {
            console.log(event.target.checkValidity());


            //let validity = event.target.checkValidity()

            if (basicValidation(event.target)){

                console.log(" basic val passed, checking for dups")
                
            if(!availablility) {
                console.log(" NOT available")
                $(event.target).siblings("div.duplicate").removeClass("d-none");
                $(event.target).siblings("div.invalid-feedback").addClass("d-none");
                $(event.target).siblings("div.valid-feedback").addClass("d-none");

            } else {
                console.log(" IS available")
                $(event.target).siblings("div.duplicate").addClass("d-none");
                $(event.target).siblings("invalid-feedback").addClass("d-none");
                event.target.setCustomValidity("");

            }
        }
        });
}

// validate pass 

function validatePassword(event){
    console.log("validating password")
    
    basicValidation(event.target);

}

// check if passwords match

function validatePasswordConfirm(event){
    let pass = $("#password2");
    let confr = $("#confirm-password");
    //let both = $("#floatingPassword, #floatingPasswordConfirm");

            if (pass.val() !== confr.val()) {

                console.log("Pass is:   not the same --- " + pass.val() + " ---  " + confr.val());
                
                // $(event.target).addClass("is-invalid");
                event.target.setCustomValidity("passwords do not match");
                $(event.target).siblings("div.not-match").removeClass("d-none");
                $(event.target).siblings("div.invalid-feedback").addClass("d-none");

            } else {
                console.log("Pass is: the same ");

                $(event.target).removeClass("is-invalid");
                event.target.setCustomValidity("");
                $(event.target).siblings("div.not-match").addClass("d-none");
                // confr.siblings("div.invalid-feedback").removeClass("d-none");

            }

}





// reusable function for JSapi validation

function basicValidation(e){
    $(e).siblings("div.duplicate").addClass("d-none");
    e.setCustomValidity("");
    
    if (e.checkValidity()){
        console.log("basic validation passed removing error message");
        $(e).siblings("div.invalid-feedback").addClass("d-none");
        $(e).siblings("div.valid-feedback").removeClass("d-none");
        return true;

    }else {
        console.log("basic validation not passed showing error div");
        $(e).siblings("div.invalid-feedback").removeClass("d-none");
        $(e).siblings("div.valid-feedback").addClass("d-none");
        return false;
    }

}

// reusable function for ajax calls

function getData (url, data = null) {
    return new Promise((resolve, reject) => {

        $.ajax({
            url: url,
            type: 'post',
            dataType: "json",
            data: data,

            success: function(result) {
                resolve(result);
            },
            error: function(errorThrown) {
                reject(errorThrown)
            },
        })
    })
};

function kill(){
    $("#register-form").removeClass("was-validated");
    }
  
  $(function() {

  $('#login-form-link').click(function(e) {
  $("#login-form").delay(100).fadeIn(100);
  $("#register-form").fadeOut(100);
  $('#register-form-link').removeClass('active');
  $(this).addClass('active');
  e.preventDefault();
  });

  $('#register-form-link').click(function(e) {
  $("#register-form").delay(100).fadeIn(100);
  $("#login-form").fadeOut(100);
  $('#login-form-link').removeClass('active');
  $(this).addClass('active');
  e.preventDefault();
  });

  });