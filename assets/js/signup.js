
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


// check if user name already exists on database

  function validateUser(event){
    console.log("validator running with: " + event.target.value);
    //console.log(this);
    
    getData("./PHP/userVerify.php", {username: event.target.value})
        .then((availablility) => {
            console.log("php returned: " + availablility);
            console.log(event.target.checkValidity());

            //let validity = event.target.checkValidity()

            if(!availablility) {
                event.target.setCustomValidity( event.target.value + "database duplicate error");
                $(event.target).addClass("is-invalid");
                $(event.target).siblings("div.duplicate").removeClass("d-none");
                $(event.target).siblings("div.invalid-feedback").addClass("d-none")
                //event.target

            } else {
                $(event.target).siblings("div.duplicate").addClass("d-none");
                $(event.target).removeClass("is-invalid");
                $(event.target).siblings("div.invalid-feedback").removeClass("d-none");
                event.target.setCustomValidity("");
            }
        });
}



//check if email address already exists on the database

function validateEmail(event){

    $(event.target).val(this.value.toLowerCase())

    console.log("validator running with: " + $(event.target)[0].value.toLowerCase());
    //console.log(this);

    getData("./PHP/emailVerify.php", {email: (event.target.value).toLowerCase()})
        .then((availablility) => {
            console.log(event.target.checkValidity());

            //let validity = event.target.checkValidity()


            if(!availablility) {
                event.target.setCustomValidity( event.target.value + "database duplicate error");
                $(event.target).addClass("is-invalid");
                $(event.target).siblings("div.duplicate").removeClass("d-none");
                $(event.target).siblings("div.invalid-feedback").addClass("d-none")

            } else {
                $(event.target).siblings("div.duplicate").addClass("d-none");
                $(event.target).removeClass("is-invalid");
                $(event.target).siblings("div.invalid-feedback").removeClass("d-none");
                event.target.setCustomValidity("");

            }
        });
}

// check if passwords match

function validatePassword(event){

    let pass = $("#floatingPassword");
    let confr = $("#floatingPasswordConfirm");
    //let both = $("#floatingPassword, #floatingPasswordConfirm");

            if (pass.val() !== confr.val()) {

                console.log("Pass is:   not the same --- " + pass.val() + " ---  " + confr.val());
                
                confr.addClass("is-invalid");
                confr[0].setCustomValidity("passwords do not match");
                confr.siblings("div.not-match").removeClass("d-none");
                confr.siblings("div.invalid-feedback").addClass("d-none");

            } else {
                console.log("Pass is: the same ");

                confr.removeClass("is-invalid");
                confr[0].setCustomValidity("");
                confr.siblings("div.not-match").addClass("d-none");
                confr.siblings("div.invalid-feedback").removeClass("d-none");

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