<header id="header" class="transparent-header-modern fixed-header-bg-white w-100">
    <div class="main-nav secondary-nav hover-success-nav py-2">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light p-0"> 
                        <a class="navbar-brand position-relative" href="index.php">
                            <img class="nav-logo" src="images/logo/hslogo2.png" alt="">
                        </a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav mr-auto">
                                <li class="nav-item dropdown"> 
                                    <a class="nav-link" href="index.php" role="button" aria-haspopup="true" aria-expanded="false">Home</a>
                                </li>
                                <li class="nav-item"> <a class="nav-link" href="about.php">About</a> </li>
                                <li class="nav-item"> <a class="nav-link" href="contact.php">Contact</a> </li>
                                <li class="nav-item"> <a class="nav-link" href="property.php">Properties</a> </li>
                                <?php if (isset($_SESSION['uemail']) && ($_SESSION['utype'] === 'agent')) { ?>
                                <li class="nav-item"> <a class="nav-link" href="admin_bookings.php">Bookings</a> </li>
                                <?php } ?>

                                <?php if (isset($_SESSION['uemail'])) { ?>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">My Account</a>
                                        <ul class="dropdown-menu">
                                            <li class="nav-item"> <a class="nav-link" href="profile.php">Profile</a> </li>

                                            <?php if ($_SESSION['utype'] === 'agent') { ?>
                                                <li class="nav-item"> <a class="nav-link" href="feature.php">Your Property</a> </li>
                                            <?php } ?>

                                            <li class="nav-item"> <a class="nav-link" href="logout.php">Logout</a> </li>
                                        </ul>
                                    </li>
                                <?php } else { ?>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Login / Register</a>
                                        <ul class="dropdown-menu">
                                            <li class="nav-item"> <a class="nav-link" href="admin/index.php">Admin</a> </li>
                                            <li class="nav-item"> <a class="nav-link" href="login.php">Agent</a> </li>
                                            <li class="nav-item"> <a class="nav-link" href="login.php">User</a> </li>
                                            <li class="nav-item"> <a class="nav-link" href="register.php">Register</a> </li>
                                        </ul>
                                    </li>
                                <?php } ?>
                            </ul>

                            <?php if (isset($_SESSION['utype']) && $_SESSION['utype'] === 'agent') { ?>
                                <a class="btn btn-success d-none d-xl-block" style="border-radius:30px;" href="submitproperty.php">Submit Property</a>
                            <?php } ?>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
