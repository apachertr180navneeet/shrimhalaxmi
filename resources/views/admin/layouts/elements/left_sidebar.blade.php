<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand demo">
		<a href="{{route('admin.dashboard')}}" class="app-brand-link">
			<span class="app-brand-logo demo">
			</span>
			<span class="app-brand-text demo menu-text fw-bold ms-2">
				{{ (string) config('app.name') !== 'nan' && config('app.name') ? config('app.name') : 'Shri Maha Laxmi' }}
			</span>
		</a>

		<a href="javascript:void(0);"
			class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
			<i class="bx bx-chevron-left bx-sm align-middle"></i>
		</a>
	</div>

	<div class="menu-inner-shadow"></div>

	<ul class="menu-inner py-1">
		<li class="menu-item {{ request()->is('admin/dashboard') ? 'active' : ''}}">
			<a href="{{route('admin.dashboard')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-home-circle"></i>
				<div data-i18n="Dashboard">Dashboard</div>
			</a>
		</li>


		{{-- Vendor --}}
		@can('vendor-list')
		<li class="menu-item {{ request()->is('admin/vendors*') ? 'active' : ''}}">
			<a href="{{ route('admin.vendors.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-store"></i>
				<div>Vendor</div>
			</a>
		</li>
		@endcan


		{{-- Job Worker --}}
		@can('jobworker-list')
		<li class="menu-item {{ request()->is('admin/jobworkers*') ? 'active' : ''}}">
			<a href="{{ route('admin.jobworkers.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-user"></i>
				<div>Job Worker</div>
			</a>
		</li>
		@endcan


		{{-- Customer --}}
		@can('customer-list')
		<li class="menu-item {{ request()->is('admin/customers*') ? 'active' : ''}}">
			<a href="{{ route('admin.customers.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-user"></i>
				<div>Customer</div>
			</a>
		</li>
		@endcan


		{{-- Item --}}
		@can('item-list')
		<li class="menu-item {{ request()->is('admin/items*') ? 'active' : ''}}">
			<a href="{{ route('admin.items.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-package"></i>
				<div>Item</div>
			</a>
		</li>
		@endcan


		{{-- Purchase --}}
		@can('purchase-list')
		<li class="menu-item {{ request()->is('admin/purchases*') ? 'active' : ''}}">
			<a href="{{ route('admin.purchases.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-cart"></i>
				<div>Purchase</div>
			</a>
		</li>
		@endcan


		{{-- Job Work Assign --}}
		@can('jobassign-list')
		<li class="menu-item {{ request()->is('admin/job-work-assignments*') ? 'active' : ''}}">
			<a href="{{ route('admin.jobworkassignments.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-clipboard"></i>
				<div>Job Work Assign</div>
			</a>
		</li>
		@endcan


		{{-- Job Worker Inward --}}
		@can('inward-list')
		<li class="menu-item {{ request()->is('admin/job-worker-inwards*') ? 'active' : ''}}">
			<a href="{{ route('admin.jobworkerinwards.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-clipboard"></i>
				<div>Job Worker Inward</div>
			</a>
		</li>
		@endcan


		{{-- Order Dispatch --}}
		@can('dispatch-list')
		<li class="menu-item {{ request()->is('admin/order-dispatches*') ? 'active' : ''}}">
			<a href="{{ route('admin.orderdispatches.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-clipboard"></i>
				<div>Order Dispatch</div>
			</a>
		</li>
		@endcan

		{{-- Role & Permission Management --}}
		@can('role-list')
		<li class="menu-item {{ request()->is('admin/roles*') ? 'active' : '' }}">
			<a href="{{ route('admin.roles.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-lock"></i>
				<div>Role Management</div>
			</a>
		</li>
		@endcan

		@can('members-list')
		<li class="menu-item {{ request()->is('admin/members*') ? 'active' : '' }}">
			<a href="{{ route('admin.members.index') }}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-user"></i>
				<div>Members Management</div>
			</a>
		</li>
		@endcan
	</ul>
</aside>
