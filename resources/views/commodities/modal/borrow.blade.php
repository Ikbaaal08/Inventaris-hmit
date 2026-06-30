<div class="modal fade" id="borrow_commodity_modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
	role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalLabel">Pinjam Barang</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="{{ route('peminjaman.store') }}" method="POST">
				@csrf
				<input type="hidden" name="commodity_id" id="commodity_id">

				<div class="modal-body">
					<div class="form-group">
						<label for="commodity_name">Nama Barang</label>
						<input type="text" class="form-control" id="commodity_name" readonly>
					</div>

					<div class="form-group">
						<label for="quantity">Jumlah yang Dipinjam <span class="text-danger">*</span></label>
						<input type="number" class="form-control" name="quantity" id="quantity" min="1" value="1" required>
					</div>

					<div class="form-group">
						<label for="borrow_date">Tanggal Pinjam <span class="text-danger">*</span></label>
						<input type="date" class="form-control" name="borrow_date" id="borrow_date" value="{{ date('Y-m-d') }}" required>
					</div>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						<i class="fas fa-times mr-1"></i> Batal
					</button>
					<button type="submit" class="btn btn-warning text-white">
						<i class="fas fa-handshake mr-1"></i> Pinjam
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
