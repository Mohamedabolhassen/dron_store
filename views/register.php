<!doctype html>
<html class="no-js" lang="en">

<!-- register.html  03:24:53 GMT -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Drophut - Single Product eCommerce Template</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <!-- CSS 
    ========================= -->
   

    <!-- Plugins CSS -->
    <link rel="stylesheet" href="assets/css/plugins.css">
    
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">

</head>

<body>

    <?php require __DIR__ . '/../inc/header.php'; ?>
    <?php require_once __DIR__ . '/../config/storefront_db.php'; $flash = storefront_take_flash(); ?>

    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="?route=index-2">home</a></li>
                            <li>Register</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    <!--breadcrumbs area end-->

	<section class="account">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-10">
					<div class="account-contents" style="background: url('assets/img/about/about1.jpg'); background-size: cover;">
                        <div class="row">
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <div class="account-thumb">
                                    <h2>Register now</h2>
                                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quis consectetur similique deleniti pariatur enim cumque eum</p>
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12">
                                <div class="account-content">
                                    <?php if ($flash): ?>
                                        <p role="<?php echo $flash['kind'] === 'error' ? 'alert' : 'status'; ?>">
                                            <?php echo storefront_escape($flash['message']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <form action="?route=newuser" method="post">
                                        <input type="hidden" name="csrf_token" value="<?php echo storefront_escape(storefront_csrf_token()); ?>">
                                        <div class="single-acc-field">
                                            <label for="name">Name</label>
                                            <input type="text" name="name" id="name" maxlength="150" minlength="2" placeholder="Enter Your Name" required>
                                        </div>
                                        <div class="single-acc-field">
                                            <label for="email">Email</label>
                                            <input type="email" name="email" id="email" maxlength="150" placeholder="Enter your Email" required>
                                        </div>
                                        <div class="single-acc-field">
                                            <label for="password">Password</label>
                                            <input type="password" name="password" id="password" minlength="8" maxlength="4096" placeholder="At least 8 characters" required>
                                        </div>
                                        <div class="single-acc-field boxes">
                                            <input type="checkbox" id="checkbox">
                                            <label for="checkbox">I'm not a robot</label>
                                        </div>
                                        <div class="single-acc-field">
                                            <button name="submit" type="submit">Register now</button>
                                        </div>
                                        <a href="?route=login">Already account? Login</a>
                                    </form>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</section>

    <?php require __DIR__ . '/../inc/fotter.php'; ?>