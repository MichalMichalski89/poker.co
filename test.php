<?php
    session_start();
    $ss = $_SESSION["user_ID"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <link href="./Bootstrap/bootstrap.min.css" rel="stylesheet" > 

    <link rel="stylesheet" href="./CSS/styles.css">

    <script src="https://kit.fontawesome.com/aec776f9d4.js" crossorigin="anonymous"></script>

    <title>Home</title>
    
    <!-- cover bs example -->
    <link href="cover.css" rel="stylesheet">
  </head>
  <body class=" d-flex text-center text-white bg-dark">
    
<div class=" d-flex p-3 mx-auto flex-column">
  <header class="mb-auto">
    <div>
      <nav class="nav nav-masthead justify-content-center float-md-end">
        <a class="nav-link active" aria-current="page" href="#">About</a>
        <a class="nav-link" href="#Players">Players</a>
        <a class="nav-link" href="#Venues">Venues</a>
        <a class="nav-link" href="#Contact">Contact</a>
        <a href="#" class="btn btn-sm btn-secondary fw-bold border-white bg-white">Sign In</a>

      </nav>
    </div>
  </header>




  <section id="Players" class="">
    <h1 class="logo " >PokerinPub.co.uk</h1>
    <p class="lead comf">COMING SOON</p>
    <p class="lead">
    <!-- <a href="#" class="btn btn-lg btn-secondary fw-bold border-white bg-white">Register</a> -->
    </p>
  </section>

  <footer class="mt-auto text-white-50">
    <!-- <p>Website managed by <a href="https://getbootstrap.com/" class="text-white">Bootstrap</a>, by <a href="https://twitter.com/mdo" class="text-white">@mdo</a>.</p> -->
  </footer>
</div>

<script type="text/javascript">

(function () {
  'use strict'

    let ss = "<?php echo $ss; ?>" ;

    console.log(ss);

    //console.log("bootstrap validation passed");
})();

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



</script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
<script src="./Bootstrap/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
</body>
</html>