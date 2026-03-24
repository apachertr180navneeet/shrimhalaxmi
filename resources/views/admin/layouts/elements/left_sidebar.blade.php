<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
	<div class="app-brand demo">
		<a href="{{route('admin.dashboard')}}" class="app-brand-link">
			<span class="app-brand-logo demo">
			</span>
			<span class="app-brand-text demo menu-text fw-bold ms-2">{{ config('app.name') }}</span>
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


		<li class="menu-item {{ request()->is('admin/vendors*') ? 'active' : ''}}">
			<a href="{{route('admin.vendors.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-store"></i>
				<div data-i18n="Vendor">Vendor</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/jobworkers*') ? 'active' : ''}}">
			<a href="{{route('admin.jobworkers.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-user"></i>
				<div data-i18n="Job Worker">Job Worker</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/customers*') ? 'active' : ''}}">
			<a href="{{route('admin.customers.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-user"></i>
				<div data-i18n="Customer">Customer</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/items*') ? 'active' : ''}}">
			<a href="{{route('admin.items.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-package"></i>
				<div data-i18n="Item">Item</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/purchases*') ? 'active' : ''}}">
			<a href="{{route('admin.purchases.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-cart"></i>
				<div data-i18n="Purchase">Purchase</div>
			</a>
		</li>

		<li class="menu-item {{ request()->is('admin/job-work-assignments*') ? 'active' : ''}}">
			<a href="{{route('admin.jobworkassignments.index')}}" class="menu-link">
				<i class="menu-icon tf-icons bx bx-clipboard"></i>
				<div data-i18n="Job Work Assign">Job Work Assign</div>
			</a>
		</li>
		
	</ul>
</aside>
