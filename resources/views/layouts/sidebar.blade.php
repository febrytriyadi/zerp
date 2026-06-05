<nav class="w-64 bg-white border-r border-gray-200 min-h-screen overflow-y-auto flex-shrink-0 hidden lg:block">
    <div class="p-4">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-800">
            <span class="text-xl font-bold">ZERP</span>
        </a>
    </div>
    <ul class="space-y-1 px-2">
        <li>
            <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2 rounded-md text-sm {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
        </li>

        <li x-data="{ open: {{ request()->is('master/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('master/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    Master Data
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('master.companies.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('master/companies*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Companies</a></li>
                <li><a href="{{ route('master.fiscal-periods.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('master/fiscal-periods*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Fiscal Periods</a></li>
                <li><a href="{{ route('master.chart-of-accounts.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('master/chart-of-accounts*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Chart of Accounts</a></li>
                <li><a href="{{ route('master.customers.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('master/customers*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Customers</a></li>
                <li><a href="{{ route('master.suppliers.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('master/suppliers*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Suppliers</a></li>
                <li><a href="{{ route('master.products.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('master/products*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Products</a></li>
                <li><a href="{{ route('master.warehouses.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('master/warehouses*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Warehouses</a></li>
            </ul>
        </li>

        <li x-data="{ open: {{ request()->is('finance/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('finance/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Finance
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('finance.cash-receipts.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('finance/cash-receipts*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Cash Receipts</a></li>
                <li><a href="{{ route('finance.cash-disbursements.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('finance/cash-disbursements*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Cash Disbursements</a></li>
            </ul>
        </li>

        <li x-data="{ open: {{ request()->is('accounting/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('accounting/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Accounting
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('accounting.journal-entries.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('accounting/journal-entries*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Journal Entries</a></li>
            </ul>
        </li>

        <li x-data="{ open: {{ request()->is('sales/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('sales/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Sales
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('sales.quotations.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('sales/quotations*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Quotations</a></li>
                <li><a href="{{ route('sales.orders.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('sales/orders*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Orders</a></li>
                <li><a href="{{ route('sales.invoices.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('sales/invoices*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Invoices</a></li>
                <li><a href="{{ route('sales.customer-payments.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('sales/customer-payments*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Customer Payments</a></li>
            </ul>
        </li>

        <li x-data="{ open: {{ request()->is('purchasing/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('purchasing/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    Purchasing
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('purchasing.requests.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('purchasing/requests*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Requests</a></li>
                <li><a href="{{ route('purchasing.orders.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('purchasing/orders*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Orders</a></li>
                <li><a href="{{ route('purchasing.invoices.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('purchasing/invoices*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Invoices</a></li>
                <li><a href="{{ route('purchasing.received-goods.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('purchasing/received-goods*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Received Goods</a></li>
            </ul>
        </li>

        <li x-data="{ open: {{ request()->is('inventory/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('inventory/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Inventory
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('inventory.stock-opnames.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('inventory/stock-opnames*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Stock Opnames</a></li>
                <li><a href="{{ route('inventory.stock-adjustments.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('inventory/stock-adjustments*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Stock Adjustments</a></li>
                <li><a href="{{ route('inventory.movements.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('inventory/movements*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Movements</a></li>
            </ul>
        </li>

        <li x-data="{ open: {{ request()->is('production/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('production/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Production
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('production.assemblies.index') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('production/assemblies*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Assemblies</a></li>
            </ul>
        </li>

        <li x-data="{ open: {{ request()->is('reports/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-md text-sm {{ request()->is('reports/*') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Reports
                </span>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <ul x-show="open" class="mt-1 ml-4 space-y-1" x-cloak>
                <li><a href="{{ route('reports.cash-book') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('reports/cash-book*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Cash Book</a></li>
                <li><a href="{{ route('reports.general-ledger') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('reports/general-ledger*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">General Ledger</a></li>
                <li><a href="{{ route('reports.trial-balance') }}" class="block px-3 py-2 rounded-md text-sm {{ request()->is('reports/trial-balance*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-100' }}">Trial Balance</a></li>
            </ul>
        </li>
    </ul>

    <div class="border-t border-gray-200 p-4 mt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</nav>