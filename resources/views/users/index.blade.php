<x-layout>
	<x-slot name="title">Halaman Daftar Pengguna</x-slot>
	<x-slot name="page_heading">Daftar Pengguna</x-slot>

	<div class="row justify-content-center">
		<div class="col-lg-4 col-md-6 col-sm-6 col-12">
			<div class="card card-statistic-1">
				<div class="card-icon bg-primary">
					<i class="fas fa-user-shield"></i>
				</div>
				<div class="card-wrap">
					<div class="card-header">
						<h4>Jumlah Admin</h4>
					</div>
					<div class="card-body">
						{{ $admin_count }}
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-12">
			<div class="card card-statistic-1">
				<div class="card-icon bg-success">
					<i class="fas fa-users"></i>
				</div>
				<div class="card-wrap">
					<div class="card-header">
						<h4>Jumlah Pengguna</h4>
					</div>
					<div class="card-body">
						{{ $user_count }}
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-body">
			@include('utilities.alert')
			<div class="d-flex justify-content-between align-items-center mb-3">
				<div>
					@can('ubah pengguna')
					<button type="button" id="btn-generate-password-bulk" class="btn btn-warning" disabled>
						<i class="fas fa-fw fa-key"></i>
						Generate Password (<span id="selected-count">0</span>)
					</button>
					@endcan
				</div>
				<div>
					@can('tambah pengguna')
					<button type="button" class="btn btn-info mr-2" data-toggle="modal" data-target="#nim_generate_modal">
						<i class="fas fa-fw fa-id-card"></i>
						Generate Akun NIM
					</button>
					<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#user_create_modal">
						<i class="fas fa-fw fa-plus"></i>
						Tambah Data
					</button>
					@endcan
				</div>
			</div>

			<x-filter>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="role_id">Peran:</label>
							<select name="role_id" id="role_id" @class(['form-control' , 'is-valid'=>
								request()->filled('role_id')])>
								<option value="">Pilih peran..</option>
								@foreach ($roles as $role)
								<option value="{{ $role->id }}" @selected(request('role_id')==$role->id)>
									{{ $role->name }}
								</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>

				<x-slot name="resetFilterURL">{{ route('pengguna.index') }}</x-slot>
			</x-filter>

			<div class="row">
				<div class="col-lg-12">
					<x-datatable>
						<thead>
							<tr>
								<th scope="col" class="text-center" style="width: 40px; vertical-align: middle;">
									<input type="checkbox" id="check-all-users">
								</th>
								<th scope="col">#</th>
								<th scope="col">Nama Lengkap</th>
								<th scope="col">Alamat Email</th>
								<th scope="col">Peran</th>
								<th scope="col">Tanggal Ditambahkan</th>
								<th scope="col">Aksi</th>
							</tr>
						</thead>
						<tbody>
							@foreach($users as $user)
							<tr>
								<td class="text-center" style="vertical-align: middle;">
									<input type="checkbox" class="user-select-checkbox" value="{{ $user->id }}">
								</td>
								<th scope="row" style="vertical-align: middle;">{{ $loop->iteration }}</th>
								<td style="vertical-align: middle;">{{ $user->name }}</td>
								<td style="vertical-align: middle;">{{ $user->email }}</td>
								<td style="vertical-align: middle;">{{ $user->getRoleNames()->first() }}</td>
								<td style="vertical-align: middle;">{{ date('m/d/Y H:i A', strtotime($user->created_at)) }}</td>
								<td class="text-center" style="vertical-align: middle;">
									<div class="btn-group">
										@can('detail pengguna')
										<a data-id="{{ $user->id }}" class="btn btn-sm btn-info text-white show-modal mr-2"
											data-toggle="modal" data-target="#show_user">
											<i class="fas fa-fw fa-search"></i>
										</a>
										@endcan
										@can('ubah pengguna')
										<a data-id="{{ $user->id }}" class="btn btn-sm btn-success text-white edit-modal mr-2"
											data-toggle="modal" data-target="#user_edit_modal" title="Ubah data">
											<i class="fas fa-fw fa-edit"></i>
										</a>
										@endcan
										@can('hapus pengguna')
										<form action="{{ route('pengguna.destroy', $user->id) }}" method="POST">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-sm btn-danger text-white delete-button">
												<i class="fas fa-fw fa-trash-alt"></i>
											</button>
										</form>
										@endcan
									</div>
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
	@include('users.modal.create')
	@include('users.modal.show')
	@include('users.modal.edit')
	@include('users.modal.generate_nim')
	@include('users.modal.credentials_result')
	@endpush

	@push('js')
	@include('users._script')
	@endpush
</x-layout>
