<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Staff panel | Profile</title>

    <!-- Bootstrap -->
    <link href="/static/bootstrap-3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <div class="container">

<?PHP include 'nav.inc.php' ?>

      <dl class="dl-horizontal">
        <dt>Login:</dt>
        <dd><?=Staff::current()->uid?></dd>
        <dt>Display name:</dt>
        <dd><?=Staff::current()->name?></dd>
        <dt>Fullname:</dt>
        <dd><?=Staff::current()->fullname?></dd>
        <dt>Email:</dt>
        <dd><?=Staff::current()->mail?></dd>
        <dt>Mobile:</dt>
        <dd><?=Staff::current()->mobile?></dd>
        <dt>Is keeper:</dt>
        <dd><?=Staff::current()->isKeeper()?'Yes':'No'?></dd>
      </dl>

    </div> <!-- /container -->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/static/js/jquery-2.1.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/static/bootstrap-3.3.5/js/bootstrap.min.js"></script>
  </body>
</html>
