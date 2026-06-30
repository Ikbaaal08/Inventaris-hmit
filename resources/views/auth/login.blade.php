<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
	<title>Login &mdash; {{ config('app.name') }}</title>

	<!-- General CSS Files -->
	<link rel="stylesheet" href="{{ url('assets/bootstrap/css/bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ url('assets/fontawesome/css/all.css') }}">

	<!-- Custom Modern Login CSS -->
	<link rel="stylesheet" href="{{ url('assets/css/modern-login.css') }}">
</head>

<body class="modern-login-body">
	<!-- Animated Background Glow Orbs -->
	<div class="bg-glow-container">
		<div class="glow-orb glow-orb-1"></div>
		<div class="glow-orb glow-orb-2"></div>
		<div class="glow-orb glow-orb-3"></div>
	</div>

	<div class="login-wrapper">
		<div class="login-card">
			<div class="login-header">
				<div class="login-logo-container">
					<i class="fas fa-boxes-stacked"></i>
				</div>
				<h1 class="login-title">Inventaris HMIT</h1>
				<p class="login-subtitle">Himpunan Mahasiswa Informatika</p>
			</div>

			<!-- Dynamic Greeting -->
			<div class="text-center">
				<div class="login-greeting" id="greetings"></div>
			</div>

			<!-- Display Alerts -->
			@include('utilities.alert')

			<!-- Login Form -->
			<form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate="">
				@csrf

				<!-- Email Address -->
				<div class="form-group-custom">
					<label for="email">Email</label>
					<div class="input-wrapper-custom">
						<i class="fas fa-envelope input-icon-custom"></i>
						<input id="email" type="email" class="form-control-custom @error('email') is-invalid @enderror" name="email"
							placeholder="Masukkan alamat email.." value="{{ old('email') }}" required autofocus tabindex="1">
					</div>
					@error('email')
					<div class="invalid-feedback-custom">
						<i class="fas fa-circle-exclamation"></i>
						<strong>{{ $message }}</strong>
					</div>
					@enderror
				</div>

				<!-- Password -->
				<div class="form-group-custom">
					<div class="d-flex justify-content-between align-items-center mb-1">
						<label for="password" class="mb-0">Password</label>
					</div>
					<div class="input-wrapper-custom">
						<i class="fas fa-lock input-icon-custom"></i>
						<input id="password" type="password" class="form-control-custom @error('password') is-invalid @enderror" name="password"
							placeholder="Masukkan kata sandi.." required tabindex="2">
						<button type="button" class="password-toggle-btn" id="togglePassword" tabindex="-1">
							<i class="fas fa-eye"></i>
						</button>
					</div>
					@error('password')
					<div class="invalid-feedback-custom">
						<i class="fas fa-circle-exclamation"></i>
						<strong>{{ $message }}</strong>
					</div>
					@enderror
				</div>

				<!-- Submit Button -->
				<button type="submit" class="btn-submit-custom" tabindex="3">
					<span>Sign In</span>
					<i class="fas fa-arrow-right-to-bracket"></i>
				</button>
			</form>
		</div>

		<!-- Footer Info -->
		<div class="login-footer">
			&copy; {{ date('Y') }} HMIT &bull; Sumbawa, Indonesia
		</div>
	</div>

	<!-- General JS Scripts -->
	<script src="{{ url('assets/js/jquery-3.5.1.min.js') }}"></script>
	<script src="{{ url('assets/js/popper.min.js') }}"></script>
	<script src="{{ url('assets/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ url('assets/js/jquery.nicescroll.min.js') }}"></script>
	<script src="{{ url('assets/js/moment.min.js') }}"></script>
	<script src="{{ url('assets/js/stisla.js') }}"></script>

	<!-- Page Specific JS File -->
	@include('layouts.partials.greetings')

	<script>
		$(document).ready(function() {
			// Populate the dynamic greeting
			$("#greetings").html(greetings());

			// Password visibility toggle
			$("#togglePassword").click(function() {
				const passwordInput = $("#password");
				const type = passwordInput.attr("type") === "password" ? "text" : "password";
				passwordInput.attr("type", type);
				
				// Toggle eye icon class
				const icon = $(this).find("i");
				if (type === "text") {
					icon.removeClass("fa-eye").addClass("fa-eye-slash");
				} else {
					icon.removeClass("fa-eye-slash").addClass("fa-eye");
				}
			});
		});
	</script>
</body>

</html>
