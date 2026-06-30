<div class="modal fade" id="credentials_result_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
	aria-labelledby="credentialsResultLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="credentialsResultLabel">Hasil Pembuatan Akun / Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<div class="modal-body">
				<!-- Summary statistics -->
				<div class="mb-3 d-flex gap-3 align-items-center">
					<span class="badge badge-success px-3 py-2 font-weight-bold" id="stats-created-container" style="display: none;">
						Berhasil: <span id="stats-created-count">0</span>
					</span>
					<span class="badge badge-warning px-3 py-2 font-weight-bold" id="stats-skipped-container" style="display: none;">
						Dilewati (Duplikat): <span id="stats-skipped-count">0</span>
					</span>
				</div>

				<div class="alert alert-warning" role="alert">
					<i class="fa-solid fa-triangle-exclamation mr-2"></i>
					<strong>Peringatan!</strong> Segera salin atau unduh daftar akun di bawah ini. Setelah modal ini ditutup, kata sandi plain-text tidak dapat dilihat lagi.
				</div>

				<!-- Created Accounts Table -->
				<div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
					<table class="table table-bordered table-striped" id="credentials-result-table">
						<thead>
							<tr>
								<th>Nama Lengkap</th>
								<th>Alamat Email</th>
								<th>Kata Sandi</th>
								<th class="text-center" style="width: 100px;">Aksi</th>
							</tr>
						</thead>
						<tbody id="credentials-result-tbody">
							<!-- Populated via JS -->
						</tbody>
					</table>
				</div>

				<!-- Skipped accounts details (if any) -->
				<div id="skipped-accounts-container" class="mt-4" style="display: none;">
					<h6 class="text-danger font-weight-bold">Akun Dilewati (Email Sudah Terdaftar):</h6>
					<div class="table-responsive" style="max-height: 150px; overflow-y: auto;">
						<table class="table table-bordered table-sm">
							<thead>
								<tr>
									<th>NIM</th>
									<th>Email</th>
									<th>Keterangan</th>
								</tr>
							</thead>
							<tbody id="skipped-accounts-tbody">
								<!-- Populated via JS -->
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Modal Footer with action buttons -->
			<div class="modal-footer d-flex justify-content-between">
				<div>
					<button type="button" class="btn btn-dark" id="btn-copy-all-credentials">
						<i class="fas fa-copy mr-1"></i> Salin Semua
					</button>
					<button type="button" class="btn btn-outline-primary" id="btn-download-txt">
						<i class="fas fa-file-lines mr-1"></i> Unduh TXT
					</button>
					<button type="button" class="btn btn-outline-success" id="btn-download-csv">
						<i class="fas fa-file-csv mr-1"></i> Unduh CSV
					</button>
				</div>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					<i class="fas fa-times mr-1"></i> Tutup
				</button>
			</div>
		</div>
	</div>
</div>
