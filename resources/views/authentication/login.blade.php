<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<title>HRIS Desktop | Login Page</title>
	<meta name="description" content="Login page example" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
	<link href="assets/css/pages/login/login-1.css" rel="stylesheet" type="text/css" />
	<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css" />
	<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
	<link rel="shortcut icon" href="{{ asset('app-logo.png') }}" />
</head>

<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled page-loading">
	<div class="d-flex flex-column flex-root">
		<div class="login login-1 login-7 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
			<div class="login-aside d-flex flex-column flex-row-auto" style="background-color: #f6e5e9;">
				<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
					<a href="#" class="text-center mb-10">
						<img src="{{ asset('app-logo.png') }}"  class="max-h-90px" alt="" />
					</a>
					<h3 class="font-weight-bolder text-center font-size-h4 font-size-h1-lg" style="color: #4a1c40;">HRIS
						<br>Backend Portal</h3>
				</div>
				<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" 
				style="background-image: url(assets/media/svg/illustrations/login-visual-4.svg)">
			</div>
			</div>

			<div class="login-content flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
				<div class="d-flex flex-column-fluid flex-center">
					<div class="login-form login-signin">
						<form action="{{ route("login_process") }}" method="POST" class="form" novalidate="novalidate" id="kt_login_signin_form" data-form-success-redirect="{{ route("home") }}">
							@csrf
							<input type="hidden" name="recaptcha" id="recaptcha">
							<div class="pb-13 pt-lg-0 pt-5">
								<h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Login Page</h3>
								<span class="text-muted font-weight-bold font-size-h4">Enter Account Name and password</span>
							</div>
							<div class="form-group">
								<div class="d-flex justify-content-between mt-n5">
									<label class="font-size-h6 font-weight-bolder text-dark pt-5">Account Name</label>
									<a href="forgotaccount/view" hidden="true" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5">Forget Account Name ?</a>
								</div>
								<input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="username"id="account_name_email" autocomplete="off" />
							</div>
							<div class="form-group">
								<div class="d-flex justify-content-between mt-n5">
									<label class="font-size-h6 font-weight-bolder text-dark pt-5">Password</label>
									<a href="forgotpassword/view" hidden="true" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5">Forget Password ?</a>
								</div>
								<input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg" type="password" name="password" autocomplete="off" />
							</div>
							<?php
							if (session('is_blocked') == "true") { ?>
								<div class="pb-lg-0 pb-5">
									<div class="d-flex justify-content-start mt-n5">
										<label class="font-size-h6-xsm font-weight-bolder text-dark pt-5">This account is blocked click
											<a href="unblock/view" class="text-primary font-size-h6-xsm font-weight-bolder text-hover-primary pt-5"> here </a>
											to unblock account
										</label>
									</div>
								</div>
							<?php } ?>
							<div class="pb-lg-0 pb-5">
								<button type="submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Sign In</button>
							</div>
						</form>

					</div>
					<div class="login-form login-forgot">
						<form action="{{ route("forgot_password") }}" method="POST" class="form" novalidate="novalidate" id="kt_login_forgot_form" data-form-success-redirect="{{ route("login_view") }}">
							@csrf
							<div class="pb-13 pt-lg-0 pt-5">
								<h3 class="font-weight-bolder text-dark font-size-h4 font-size-h1-lg">Forget Password ?</h3>
							</div>
							<div class="form-group">
								<div class="d-flex justify-content-between mt-n5">
									<label class="font-size-h6 font-weight-bolder text-dark pt-5">Email Account</label>
								</div>
								<input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg" type="text" name="account_name_email" autocomplete="off" />
							</div>
							<div class="d-flex justify-content-between mt-n5">
								<button type="button" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3">Back</button>
								<button type="submit" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3">Submit</button>
							</div>
						</form>
					</div>
				</div>
				{{-- <div class="d-flex justify-content-lg-start justify-content-center align-items-end py-7 py-lg-0">
						<a href="#" class="text-primary font-weight-bolder font-size-h5">Terms</a>
						<a href="#" class="text-primary ml-10 font-weight-bolder font-size-h5">Plans</a>
						<a href="#" class="text-primary ml-10 font-weight-bolder font-size-h5">Contact Us</a>
					</div> --}}
			</div>
		</div>
	</div>
	<script>

		var KTAppSettings = {
			"breakpoints": {
				"sm": 576,
				"md": 768,
				"lg": 992,
				"xl": 1200,
				"xxl": 1200
			},
			"colors": {
				"theme": {
					"base": {
						"white": "#ffffff",
						"primary": "#6993FF",
						"secondary": "#E5EAEE",
						"success": "#1BC5BD",
						"info": "#8950FC",
						"warning": "#FFA800",
						"danger": "#F64E60",
						"light": "#F3F6F9",
						"dark": "#212121"
					},
					"light": {
						"white": "#ffffff",
						"primary": "#E1E9FF",
						"secondary": "#ECF0F3",
						"success": "#C9F7F5",
						"info": "#EEE5FF",
						"warning": "#FFF4DE",
						"danger": "#FFE2E5",
						"light": "#F3F6F9",
						"dark": "#D6D6E0"
					},
					"inverse": {
						"white": "#ffffff",
						"primary": "#ffffff",
						"secondary": "#212121",
						"success": "#ffffff",
						"info": "#ffffff",
						"warning": "#ffffff",
						"danger": "#ffffff",
						"light": "#464E5F",
						"dark": "#ffffff"
					}
				},
				"gray": {
					"gray-100": "#F3F6F9",
					"gray-200": "#ECF0F3",
					"gray-300": "#E5EAEE",
					"gray-400": "#D6D6E0",
					"gray-500": "#B5B5C3",
					"gray-600": "#80808F",
					"gray-700": "#464E5F",
					"gray-800": "#1B283F",
					"gray-900": "#212121"
				}
			},
			"font-family": "Poppins"
		};
	</script>
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
	<script src="assets/js/pages/custom/login/login-general.js"></script>
	<script src="custom/js/authentication/login/login.js"></script>
	<script src="custom/js/authentication/login/forgotPassword.js"></script>
	<script>
		$('.form').on('keyup keypress', function(e) {
		var keyCode = e.keyCode || e.which;
		if (keyCode === 13) { 
			e.preventDefault();
			return false;
		}
		});
	</script>
</body>

</html>