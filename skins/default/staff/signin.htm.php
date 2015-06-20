<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Staff panel | signin</title>

    <!-- Bootstrap -->
    <link href="/static/bootstrap-3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="/static/css/bootstrapValidator.css" rel="stylesheet">
    <link href="/static/css/font-awesome.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <div class="container">

      <nav class="navbar navbar-default">
        <div class="container-fluid">
          <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Staff panel</a>
          </div>
        </div>
      </nav>

    <form class="form-horizontal" id="form1" method="post" action="/sign/in">
      <div class="form-group">
        <label for="username" class="col-sm-2 control-label">Username</label>
        <div class="col-sm-10 col-md-8">
          <input type="text" class="form-control" name="username" id="username" placeholder="Username" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <label for="password" class="col-sm-2 control-label">Password</label>
        <div class="col-sm-10 col-md-8">
          <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10 col-md-8">
          <label><input type="checkbox" name="remember" value="remember-me"> Remember me </label>
        </div>
      </div>
      <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
          <button type="submit" class="btn btn-default">Submit</button>
        </div>
      </div>
    </form>

  </div>


  <script src="/static/js/jquery-1.11.3.min.js"></script>
  <script src="/static/bootstrap-3.3.5/js/bootstrap.min.js"></script>
  <script src="/static/js/bootstrapValidator.min.js"></script>
  <script src="/static/js/bootstrap-dust-func.js"></script>
  <script type="text/javascript">
      jQuery(document).ready(function () {

        $('#form1')
        .bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                username: {
                    message: 'The username is not valid',
                    validators: {
                        notEmpty: {
                            message: 'The username is required and can\'t be empty'
                        },
                        stringLength: {
                            min: 4,
                            max: 30,
                            message: 'The username must be more than 4 and less than 30 characters long'
                        },
                        /*remote: {
                            url: 'remote.php',
                            message: 'The username is not available'
                        },*/
                        regexp: {
                            regexp: /^[a-zA-Z0-9_\.]+$/,
                            message: 'The username can only consist of alphabetical, number, dot and underscore'
                        },
                        callback: {
                          callback: function(value, validator) {return true;}
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'The old password is required and can\'t be empty'
                        },
                        callback: {
                          callback: function(value, validator) {return true;}
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault();

            // Get the form instance
            var $form = $(e.target);

            // Get the BootstrapValidator instance
            var bv = $form.data('bootstrapValidator');

            // Use Ajax to submit form data
            $.post($form.attr('action'), $form.serialize(), function(res) {
                // console.log(res);
                if (!!res.meta.ok) {
                  Dust.alert('成功', 'OK', function(){
                    bv.resetForm(true);
                    // $("#form1").get(0).reset();
                  });
                } else if (typeof res.error != "undefined") {
                  var error = res.error
                  if (typeof error.field === "string") {
                    bv.updateMessage(error.field, 'callback', error.message);
                    bv.updateStatus(error.field, bv.STATUS_INVALID, 'callback');
                    // Dust.alert(error.message);
                  }
                } else {
                  alertAjaxResult(res);
                }
            }, 'json');
            console.log(bv)
        });

      });
  </script>
</body>
</html>
