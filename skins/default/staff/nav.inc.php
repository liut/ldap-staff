
      <!-- Static navbar -->
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
          <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="/">Home</a></li>
              <li><a href="#">About</a></li>
              <li><a href="#">Contact</a></li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
              <?PHP if (Staff::current()->isLogin()): ?>
              <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                  <i class="glyphicon glyphicon-user"></i> <?=Staff::current()->name?> <i class="glyphicon glyphicon-chevron-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                  <li><a href="/profile"><i class="glyphicon glyphicon-cog"></i> Profile</a></li>
                  <li><a href="/sign/out"><i class="glyphicon glyphicon-log-out"></i> Sign out</a></li>
                </ul>
              </li>
              <?PHP else:?>
              <li><a href="/sign/in">Sign in</a></li>
              <?PHP endif;?>
              <li><a href="/password">Change Password</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
      </nav>
