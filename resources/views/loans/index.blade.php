<x-layout>
	<x-slot name="title">Halaman Daftar Peminjaman</x-slot>
	<x-slot name="page_heading">Daftar Peminjaman Barang</x-slot>

	<div class="card">
		<div class="card-body">
			@include('utilities.alert')

			<div class="row">
				<div class="col-lg-12">
					<x-datatable>
						<thead>
							<tr>
								<th scope="col">#</th>
								@if(auth()->user()->hasRole(['Administrator', 'Ketua Himpunan']))
								<th scope="col">Nama Anggota</th>
								@endif
								<th scope="col">Nama Barang</th>
								<th scope="col">Jumlah</th>
								<th scope="col">Tanggal Pinjam</th>
								<th scope="col">Tanggal Kembali</th>
								<th scope="col">Status</th>
								<th scope="col">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach($loans as $loan)
							<tr>
								<th scope="row">{{ $loop->iteration }}</th>
								@if(auth()->user()->hasRole(['Administrator', 'Ketua Himpunan']))
								<td>{{ $loan->user->name }}</td>
								@endif
								<td>{{ $loan->commodity->name }}</td>
								<td>{{ $loan->quantity }}</td>
								<td>{{ \Carbon\Carbon::parse($loan->borrow_date)->format('d-m-Y') }}</td>
								<td>
									{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('d-m-Y') : '-' }}
								</td>
								<td>
									@if($loan->status === 'dipinjam')
									<span class="badge badge-warning text-white">Dipinjam</span>
									@else
									<span class="badge badge-success">Dikembalikan</span>
									@endif
								</td>
								<td>
									@if($loan->status === 'dipinjam')
										@if($loan->user_id === auth()->id())
										<button type="button" class="btn btn-sm btn-success return-btn" data-id="{{ $loan->id }}" data-action="{{ route('peminjaman.update', $loan->id) }}" data-toggle="modal" data-target="#return_modal">
											<i class="fas fa-undo mr-1"></i> Kembalikan
										</button>
										@else
										-
										@endif
									@else
									<div class="d-flex flex-column gap-1">
										<button class="btn btn-sm btn-secondary mb-1" disabled>
											<i class="fas fa-check mr-1"></i> Selesai
										</button>
										@if($loan->return_photo)
										<button type="button" class="btn btn-sm btn-info view-proof-btn" data-photo="{{ asset($loan->return_photo) }}">
											<i class="fas fa-eye mr-1"></i> Lihat Bukti
										</button>
										@endif
									</div>
									@endif
								</td>
							</tr>
							@endforeach
						</tbody>
					</x-datatable>
				</div>
			</div>
		</div>
	</div>

	@push('modal')
	<!-- Modal: Upload Bukti Pengembalian -->
	<div class="modal fade" id="return_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="returnModalLabel">Bukti Pengembalian Barang</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="return-form" method="POST" enctype="multipart/form-data">
					@csrf
					@method('PUT')
					<div class="modal-body">
						<div class="form-group">
							<label for="return_date">Tanggal Kembali <span class="text-danger">*</span></label>
							<input type="date" class="form-control" name="return_date" id="return_date" value="{{ date('Y-m-d') }}" required>
						</div>

						<div class="form-group">
							<label for="return_photo_input">Upload Bukti Foto Pengembalian <span class="text-danger">*</span></label>
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="return_photo_input" name="return_photo" accept="image/*" required>
								<label class="custom-file-label" for="return_photo_input">Pilih file gambar..</label>
							</div>
							<small class="form-text text-muted">Format yang didukung: JPG, JPEG, PNG, SVG. Maksimal 2MB.</small>
						</div>
						<!-- Local Image Preview inside modal -->
						<div class="form-group text-center d-none" id="modal-preview-container">
							<img id="modal-image-preview" src="#" alt="Pratinjau Gambar" style="max-height: 200px; max-width: 100%; border-radius: 8px; border: 1px solid #ddd; padding: 4px;">
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-success">
							<i class="fas fa-check mr-1"></i> Simpan & Kembalikan
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal: Pratinjau Gambar Bukti -->
	<div class="modal fade" id="preview_modal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="previewModalLabel">Bukti Pengembalian</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body text-center">
					<img id="preview-image" src="" alt="Bukti Pengembalian" style="max-height: 500px; max-width: 100%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
				</div>
				<div class="modal-footer">
					<a href="" id="btn-download-proof" class="btn btn-primary" download>
						<i class="fas fa-download mr-1"></i> Unduh Gambar
					</a>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
				</div>
			</div>
		</div>
	</div>
	@endpush

	@push('js')
	<script>
		$(document).ready(function() {
			// Set action URL dynamically when return button is clicked (delegated for DataTables compatibility)
			$(document).on("click", ".return-btn", function() {
				const actionUrl = $(this).data("action");
				$("#return-form").attr("action", actionUrl);
				// Reset form & file input label
				$("#return-form").trigger("reset");
				$("#return-form .custom-file-label").text("Pilih file gambar..");
				$("#modal-preview-container").addClass("d-none");
				$("#return_modal").modal("show");
			});

			// Custom file input handler (updates the filename label and shows preview)
			$(document).on("change", "#return_photo_input", function() {
				const file = this.files[0];
				if (file) {
					// Update label text
					$(this).siblings(".custom-file-label").text(file.name);
					
					// Show local preview
					const reader = new FileReader();
					reader.onload = function(e) {
						$("#modal-image-preview").attr("src", e.target.result);
						$("#modal-preview-container").removeClass("d-none");
					}
					reader.readAsDataURL(file);
				}
			});

			// Show proof image preview modal (delegated for DataTables compatibility)
			$(document).on("click", ".view-proof-btn", function() {
				const photoUrl = $(this).data("photo");
				$("#preview-image").attr("src", photoUrl);
				$("#btn-download-proof").attr("href", photoUrl);
				$("#preview_modal").modal("show");
			});
		});
	</script>
	@endpush
</x-layout>
