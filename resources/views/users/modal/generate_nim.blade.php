<div class="modal fade" id="nim_generate_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
	aria-labelledby="nimGenerateModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="nimGenerateModalLabel">Generate Akun dari NIM</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="nim-generate-form" action="{{ route('pengguna.generate-by-nim') }}" method="POST">
				@csrf

				<div class="modal-body">
					<!-- Info Alert -->
					<div class="alert alert-info" role="alert">
						<i class="fa-solid fa-circle-info mr-2"></i>
						Isi parameter di bawah ini untuk membuat akun anggota dalam jumlah banyak sekaligus.
					</div>

					<div class="row">
						<!-- NIM Input List -->
						<div class="col-md-12">
							<div class="form-group">
								<label for="nims_input">
									Daftar NIM <span class="text-danger">*</span>
								</label>
								<textarea class="form-control" name="nims" id="nims_input" rows="4" 
									placeholder="Masukkan NIM (pisahkan dengan koma, spasi, atau baris baru)" required></textarea>
								<small class="text-muted">Contoh: 2108561001, 2108561002, 2108561003</small>
							</div>
						</div>

						<!-- Email Suffix -->
						<div class="col-md-12">
							<div class="form-group">
								<label for="email_suffix_input">
									Sufiks Domain Email <span class="text-danger">*</span>
								</label>
								<input type="text" class="form-control" name="email_suffix" id="email_suffix_input" 
									value="@student.unud.ac.id" placeholder="Contoh: @student.unud.ac.id" required>
								<small class="text-muted">Email akan terbentuk format: [NIM][sufiks]</small>
							</div>
						</div>

						<!-- Name Prefix -->
						<div class="col-md-12">
							<div class="form-group">
								<label for="name_prefix_input">
									Prefiks Nama Lengkap
								</label>
								<input type="text" class="form-control" name="name_prefix" id="name_prefix_input" 
									value="Anggota" placeholder="Contoh: Anggota (Nama menjadi 'Anggota [NIM]')">
							</div>
						</div>

						<!-- Password Settings -->
						<div class="col-md-12">
							<div class="form-group">
								<label class="d-block">Pilihan Password <span class="text-danger">*</span></label>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="password_type" id="pw_random" value="random" checked>
									<label class="form-check-label" for="pw_random">Karakter Acak</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="password_type" id="pw_nim" value="nim">
									<label class="form-check-label" for="pw_nim">Gunakan NIM</label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="password_type" id="pw_custom" value="custom">
									<label class="form-check-label" for="pw_custom">Kustom</label>
								</div>
							</div>
						</div>

						<!-- Custom Password Text Field (hidden by default) -->
						<div class="col-md-12" id="custom_password_group" style="display: none;">
							<div class="form-group">
								<label for="custom_password_input">Kata Sandi Kustom <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="custom_password" id="custom_password_input" 
									placeholder="Masukkan kata sandi kustom">
							</div>
						</div>

						<!-- Select Role -->
						<div class="col-md-12">
							<div class="form-group">
								<label for="role_id_select">
									Pilih Peran <span class="text-danger">*</span>
								</label>
								<select name="role_id" id="role_id_select" class="form-control" required>
									@foreach ($roles as $role)
									<option value="{{ $role->id }}" @selected($role->name === 'Anggota')>{{ $role->name }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>
				</div>

				<!-- Modal Footer -->
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Batal
					</button>
					<button type="submit" class="btn btn-info">
						<i class="fas fa-gears mr-1"></i> Proses Akun
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
