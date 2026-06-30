<script>
	$(document).ready(function () {
		new TomSelect("#user_create_modal form #role_id");
		const userRoleInput = new TomSelect("#user_edit_modal form #role_id");

		$(".show-modal").click(function () {
			const id = $(this).data("id");
			let url = "{{ route('api.pengguna.show', ':paramID') }}".replace(
				":paramID",
				id
			);

			$.ajax({
				url: url,
				header: {
					"Content-Type": "application/json",
				},
				success: (res) => {
					$("#show_user #name").val(res.data.name);
					$("#show_user #email").val(res.data.email);
					$("#show_user #role_id").val(res.data.role.name);
				},
				error: (err) => {
					alert("error occured, check console");
					console.log(err);
				},
			});
		});

		$(".edit-modal").on("click", function () {
			const id = $(this).data("id");
			let url = "{{ route('api.pengguna.show', ':paramID') }}".replace(
				":paramID",
				id
			);

			let updateURL = "{{ route('pengguna.update', ':paramID') }}".replace(
				":paramID",
				id
			);

			$.ajax({
				url: url,
				method: "GET",
				header: {
					"Content-Type": "application/json",
				},
				success: (res) => {
					$("#user_edit_modal form #name").val(res.data.name);
					$("#user_edit_modal form #email").val(res.data.email);

					if(res.data.role !== null) {
						userRoleInput.setValue(res.data.role.id);
					}

					$("#user_edit_modal form").attr("action", updateURL);
				},
				error: (err) => {
					alert("error occured, check console");
					console.log(err);
				},
			});
		});

		// Checkbox selection state logic
		const checkAll = $("#check-all-users");
		const bulkBtn = $("#btn-generate-password-bulk");
		const countSpan = $("#selected-count");

		function updateBulkButtonState() {
			const checkedCount = $(".user-select-checkbox:checked").length;
			countSpan.text(checkedCount);
			if (checkedCount > 0) {
				bulkBtn.prop("disabled", false);
			} else {
				bulkBtn.prop("disabled", true);
			}
		}

		checkAll.change(function () {
			const isChecked = $(this).is(":checked");
			$(".user-select-checkbox").prop("checked", isChecked);
			updateBulkButtonState();
		});

		$(document).on("change", ".user-select-checkbox", function () {
			const allCheckboxCount = $(".user-select-checkbox").length;
			const checkedCheckboxCount = $(".user-select-checkbox:checked").length;
			
			checkAll.prop("checked", allCheckboxCount === checkedCheckboxCount);
			updateBulkButtonState();
		});

		// NIM Modal password selection visibility toggle
		$('input[name="password_type"]').change(function () {
			if ($(this).val() === "custom") {
				$("#custom_password_group").slideDown(200);
				$("#custom_password_input").attr("required", true);
			} else {
				$("#custom_password_group").slideUp(200);
				$("#custom_password_input").removeAttr("required").val("");
			}
		});

		// Global store for credentials data to facilitate downloads
		let activeGeneratedData = [];

		function showCredentialsResult(created, skipped) {
			activeGeneratedData = created;
			const tbody = $("#credentials-result-tbody");
			tbody.empty();

			// Handle success list
			if (created && created.length > 0) {
				$("#stats-created-count").text(created.length);
				$("#stats-created-container").show();

				created.forEach(function (user) {
					tbody.append(`
						<tr>
							<td style="vertical-align: middle;">${user.name}</td>
							<td style="vertical-align: middle;">${user.email}</td>
							<td style="vertical-align: middle;">
								<span class="font-weight-bold font-monospace">${user.password}</span>
							</td>
							<td class="text-center" style="vertical-align: middle;">
								<button type="button" class="btn btn-sm btn-info copy-row-password" data-password="${user.password}">
									<i class="fas fa-copy"></i> Salin
								</button>
							</td>
						</tr>
					`);
				});
			} else {
				$("#stats-created-container").hide();
			}

			// Handle skipped list (duplicates)
			const skippedTbody = $("#skipped-accounts-tbody");
			skippedTbody.empty();
			if (skipped && skipped.length > 0) {
				$("#stats-skipped-count").text(skipped.length);
				$("#stats-skipped-container").show();
				$("#skipped-accounts-container").show();

				skipped.forEach(function (item) {
					skippedTbody.append(`
						<tr>
							<td>${item.nim}</td>
							<td>${item.email}</td>
							<td class="text-danger font-weight-bold">${item.reason}</td>
						</tr>
					`);
				});
			} else {
				$("#stats-skipped-container").hide();
				$("#skipped-accounts-container").hide();
			}

			// Show Results Modal
			$("#credentials_result_modal").modal("show");
		}

		// Row-level Copy button handler
		$(document).on("click", ".copy-row-password", function () {
			const password = $(this).data("password");
			navigator.clipboard.writeText(password).then(() => {
				Swal.fire({
					toast: true,
					position: 'top-end',
					icon: 'success',
					title: 'Password berhasil disalin!',
					showConfirmButton: false,
					timer: 2000
				});
			});
		});

		// Copy All credentials button handler
		$("#btn-copy-all-credentials").click(function () {
			if (activeGeneratedData.length === 0) return;

			let text = "";
			activeGeneratedData.forEach(user => {
				text += `Nama: ${user.name}\nEmail: ${user.email}\nPassword: ${user.password}\n\n`;
			});

			navigator.clipboard.writeText(text).then(() => {
				Swal.fire({
					toast: true,
					position: 'top-end',
					icon: 'success',
					title: 'Semua akun berhasil disalin!',
					showConfirmButton: false,
					timer: 2000
				});
			});
		});

		// Download TXT
		$("#btn-download-txt").click(function () {
			if (activeGeneratedData.length === 0) return;

			let content = "=== DATA AKUN GENERATED ===\n\n";
			activeGeneratedData.forEach(user => {
				content += `Nama     : ${user.name}\nEmail    : ${user.email}\nPassword : ${user.password}\n-------------------------\n`;
			});

			const blob = new Blob([content], { type: "text/plain;charset=utf-8" });
			const link = document.createElement("a");
			link.href = URL.createObjectURL(blob);
			link.download = `akun-generated-${moment().format('YYYYMMDD-HHmmss')}.txt`;
			link.click();
		});

		// Download CSV
		$("#btn-download-csv").click(function () {
			if (activeGeneratedData.length === 0) return;

			let csvContent = "Nama Lengkap,Alamat Email,Kata Sandi\n";
			activeGeneratedData.forEach(user => {
				// Escape double quotes
				const escapedName = user.name.replace(/"/g, '""');
				const escapedEmail = user.email.replace(/"/g, '""');
				const escapedPassword = user.password.replace(/"/g, '""');
				csvContent += `"${escapedName}","${escapedEmail}","${escapedPassword}"\n`;
			});

			const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8" });
			const link = document.createElement("a");
			link.href = URL.createObjectURL(blob);
			link.download = `akun-generated-${moment().format('YYYYMMDD-HHmmss')}.csv`;
			link.click();
		});

		// Form Submission: NIM generation
		$("#nim-generate-form").submit(function (e) {
			e.preventDefault();
			const form = $(this);
			
			Swal.fire({
				title: 'Sedang Memproses...',
				text: 'Mohon tunggu selagi akun anggota dibuat.',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			$.ajax({
				url: form.attr("action"),
				method: "POST",
				data: form.serialize(),
				success: (res) => {
					Swal.close();
					if (res.code === 200) {
						$("#nim_generate_modal").modal("hide");
						form.trigger("reset");
						$("#custom_password_group").hide();
						$("#custom_password_input").removeAttr("required");
						
						showCredentialsResult(res.data.created, res.data.skipped);
					} else {
						Swal.fire('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
					}
				},
				error: (err) => {
					Swal.close();
					Swal.fire('Gagal!', 'Terjadi kesalahan sistem. Coba lagi.', 'error');
					console.error(err);
				}
			});
		});

		// Button Handler: Bulk generate password
		$("#btn-generate-password-bulk").click(function () {
			const checkedCheckboxes = $(".user-select-checkbox:checked");
			if (checkedCheckboxes.length === 0) return;

			Swal.fire({
				title: 'Generate Password?',
				text: `Kata sandi baru akan di-generate untuk ${checkedCheckboxes.length} user terpilih.`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Ya, Generate!',
				cancelButtonText: 'Batal',
				reverseButtons: true
			}).then((result) => {
				if (result.isConfirmed) {
					// Gather IDs
					const userIds = [];
					checkedCheckboxes.each(function () {
						userIds.push($(this).val());
					});

					Swal.fire({
						title: 'Sedang Memproses...',
						text: 'Mohon tunggu selagi men-generate password baru.',
						allowOutsideClick: false,
						didOpen: () => {
							Swal.showLoading();
						}
					});

					$.ajax({
						url: "{{ route('pengguna.generate-password') }}",
						method: "POST",
						data: {
							_token: "{{ csrf_token() }}",
							user_ids: userIds
						},
						success: (res) => {
							Swal.close();
							if (res.code === 200) {
								// Reset checkboxes and button state
								checkAll.prop("checked", false);
								$(".user-select-checkbox").prop("checked", false);
								updateBulkButtonState();

								showCredentialsResult(res.data, []);
							} else {
								Swal.fire('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
							}
						},
						error: (err) => {
							Swal.close();
							Swal.fire('Gagal!', 'Terjadi kesalahan sistem. Coba lagi.', 'error');
							console.error(err);
						}
					});
				}
			});
		});

		// Reload page to reflect updates in the main datatable when results modal is closed
		$("#credentials_result_modal").on("hidden.bs.modal", function () {
			window.location.reload();
		});
	});
</script>
