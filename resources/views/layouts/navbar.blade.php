<div id="kt_header" class="header flex-column header-fixed">
    <div class="header-top">
        <div class="container">
            <div class="d-none d-lg-flex align-items-center mr-3">
                <a href="{{ route('home') }}" class="mr-20">
                    <img alt="Logo" src="{{ asset('app-logo.png') }}" class="max-h-50px" />
                </a>
                <ul class="header-tabs nav align-self-end font-size-lg" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('home') }}"
                            class="nav-link py-4 px-6 {{ request()->is('home') ? 'active' : '' }}">Home</a>
                    </li>
                    {{-- <?php if (session('user_role') == '1' || session('user_role') == '3' || session('user_role') == '4' || session('user_role') == '5' || session('user_role') == '8') { ?>
                        @if (session('brand_type') == 1 || session('brand_type') == 0)
                            <li class="nav-item mr-3">
                                <a href="
                                    @if (session('user_role') == 1)
                                        {{ route('master_data_company_view') }}
                                    @elseif(session('user_role') == 3)
                                        {{ route('master_data_plant_view') }}
                                    @elseif (session('user_role') == 4)
                                        {{ route('master_data_batch_production_view') }}
                                    @elseif (session('user_role') == 5)
                                        {{ route('master_data_batch_packaging_view') }}
                                    @elseif (session('user_role') == 8)
                                        {{ route('master_data_batch_store_view') }}
                                    @endif
                                " class="nav-link py-4 px-6 {{ request()->is('master_data/companies/*') ? 'active' : '' }} {{ request()->is('manufacture/master_data/companies/*') ? 'active' : '' }} {{ request()->is('test_lab/master_data/companies/*') ? 'active' : '' }}">
                                    @if (session('user_role') == 1)
                                        Member
                                    @elseif(session('user_role') == 3)
                                        Brand Center
                                    @elseif(session('user_role') == 4 || session('user_role') == 5 || session('user_role') == 8)
                                        Batch
                                    @endif
                                </a>
                            </li>
                        @elseif (session('brand_type') == 2)
                            @if (session('user_role') == 1 || session('user_role') == 3 || session('user_role') == 8)
                                <li class="nav-item mr-3">
                                    <a href="
                                        @if (session('user_role') == 1)
                                            {{ route('master_data_company_view') }}
                                        @elseif (session('user_role') == 3)
                                            {{ route('master_data_lab_plant_view') }}
                                        @elseif (session('user_role') == 8)
                                            {{ route('master_data_lab_batch_store_view') }}
                                        @endif
                                    " class="nav-link py-4 px-6 {{ request()->is('master_data/companies/*') ? 'active' : '' }} {{ request()->is('manufacture/master_data/companies/*') ? 'active' : '' }} {{ request()->is('test_lab/master_data/companies/*') ? 'active' : '' }}">
                                        @if (session('user_role') == 1)
                                            Member
                                        @else
                                            Lab
                                        @endif
                                    </a>
                                </li>   
                            @endif
                        @endif
                    <?php } ?> --}}
                    {{-- <?php if (session('user_role') == '4') { ?>
                        <li class="nav-item mr-3">
                            <a href=" @if (session('brand_type') == 1)
                                    {{ route('master_data_category_view') }}
                                @else
                                    {{ route('master_data_lab_category_view') }}
                                @endif
                            " class="nav-link py-4 px-6 {{ request()->is('product/product_attribute/*') ? 'active' : '' }} {{ request()->is('manufacture/master_data/product/*') ? 'active' : '' }} {{ request()->is('test_lab/master_data/product/*') ? 'active' : '' }}">
                                <?php if (session('brand_type') == '1') { ?>
                                    Product
                                <?php } else { ?>
                                    Test Lab Type
                                <?php } ?>
                            </a>
                        </li>
                    <?php } ?> --}}
                    {{-- <?php if (session('user_role') == '1' || session('user_role') == '3') { ?>
                        <li class="nav-item mr-3">
                            <a href="{{ route('order_qr_view') }}" class="nav-link py-4 px-6 {{ request()->is('transaction/*') ? 'active' : '' }}">QR Code</a>
                        </li>
                    <?php } ?> --}}
                    <?php if (session('user_role') != '2') { ?>
                        <li class="nav-item mr-3">
                            <a href="{{ route('master_data_employee_view') }}" class="nav-link py-4 px-6 {{ request()->is('master_data/master_employees/*') ? 'active' : '' }}">
                                    Employees
                            </a>
                        </li>
                    <?php } ?>
                    {{-- <?php if (session('user_role') == '1') { ?>
                        <li class="nav-item mr-3">
                            <a href="{{ route('outdated_application_version_view') }}" class="nav-link py-4 px-6 {{ request()->is('application/*') ? 'active' : '' }}">Version</a>
                        </li>
                    <?php } ?> --}}
                    {{-- <?php if (session('user_role') == '3') { ?>
                        <li class="nav-item mr-3">
                            <a href="{{ route('customer_report_qr_view') }}" class="nav-link py-4 px-6 {{ request()->is('report/*') ? 'active' : '' }}">Report</a>
                        </li>
                    <?php } ?> --}}
                </ul>
            </div>
            <div class="topbar bg-primary">
                <div class="topbar-item">
                    <div class="btn btn-icon btn-hover-transparent-white w-auto d-flex align-items-center btn-lg px-2"
                        id="kt_quick_user_toggle">
                        <div class="d-flex flex-column text-right pr-3">
                            <span
                                class="text-white opacity-50 font-weight-bold font-size-sm d-none d-md-inline">{{ session('user_full_name') }}</span>
                            <span class="text-white font-weight-bolder font-size-sm d-none d-md-inline">
                                @if (session('user_role') == 1)
                                    HRIS Administrator
                                    @elseif (session('user_role') == 2)
                                        Staff Admin
                                    @elseif (session('user_role') == 3)
                                        PIC Brand
                                    @elseif (session('user_role') == 4)
                                        Production Administrator
                                    @elseif (session('user_role') == 5)
                                        Packaging Administrator
                                    @elseif (session('user_role') == 8)
                                        Store Inventory Administrator
                                    @endif
                            </span>
                        </div>
                        <span class="symbol symbol-35">
                            <span
                                class="symbol-label font-size-h5 font-weight-bold text-white bg-white-o-30">{{ session('user_initial_name') }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom">
        <div class="container">
            <div class="header-navs header-navs-left" id="kt_header_navs">
                {{-- <ul class="header-tabs p-5 p-lg-0 d-flex d-lg-none nav nav-bold nav-tabs" role="tablist">
                    <li class="nav-item mr-2">
                        <a href="{{ route('home') }}"
                            class="nav-link py-4 px-6 {{ request()->is('home') ? 'active' : '' }}" data-toggle="tab"
                            data-target="#kt_header_tab_1" role="tab">Home</a>
                    </li>
                    <?php if (session('user_role') == '1' || session('user_role') == '3') { ?>
                    <li class="nav-item">
                        <a href="@if (session('user_role')=='3' ) {{ route('dashboard') }} @elseif(session('user_role') == '1') {{ route('dashboard_admin') }} @endif"
                            class="nav-link py-4 px-6 {{ request()->is('dashboard') ? 'active' : '' }} {{ request()->is('dashboard_admin') ? 'active' : '' }}">Scan</a>
                    </li>
                    <?php } ?>
                    <?php if (session('user_role') == '1' || session('user_role') == '3' ||
                    session('user_role') == '4' || session('user_role') == '5' || session('user_role') == '8') { ?>
                    @if (session('brand_type') == 1 || session('brand_type') == 0)
                        <li class="nav-item mr-3">
                            <a href="
                                    @if (session('user_role')==1) {{ route('master_data_company_view') }}
                                @elseif(session('user_role') == 3)
                                        {{ route('master_data_plant_view') }}
                                @elseif (session('user_role') == 4)
                                        {{ route('master_data_batch_production_view') }}
                                @elseif (session('user_role') == 5)
                                        {{ route('master_data_batch_packaging_view') }}
                                @elseif (session('user_role') == 8)
                                        {{ route('master_data_batch_store_view') }} @endif " class="
                                nav-link py-4 px-6 {{ request()->is('master_data/companies/*') ? 'active' : '' }}
                                {{ request()->is('manufacture/master_data/companies/*') ? 'active' : '' }}
                                {{ request()->is('test_lab/master_data/companies/*') ? 'active' : '' }}">
                                @if (session('user_role') == 1)
                                    Member
                                @elseif(session('user_role') == 3)
                                    Brand Center
                                @elseif(session('user_role') == 4 || session('user_role') == 5 ||
                                    session('user_role') == 8)
                                    Batch
                                @endif
                            </a>
                        </li>
                    @elseif (session('brand_type') == 2)
                        @if (session('user_role') == 1 || session('user_role') == 3 || session('user_role') == 8)
                            <li class="nav-item mr-3">
                                <a href="
                                        @if (session('user_role')==1) {{ route('master_data_company_view') }}
                                    @elseif (session('user_role') == 3)
                                            {{ route('master_data_lab_plant_view') }}
                                    @elseif (session('user_role') == 8)
                                            {{ route('master_data_lab_batch_store_view') }} @endif " class=" nav-link py-4 px-6
                                    {{ request()->is('master_data/companies/*') ? 'active' : '' }}
                                    {{ request()->is('manufacture/master_data/companies/*') ? 'active' : '' }}
                                    {{ request()->is('test_lab/master_data/companies/*') ? 'active' : '' }}">
                                    <?php if (session('user_role') == '1') { ?>
                                    Member
                                    <?php } else { ?>
                                    <?php if (session('brand_type') == '1') { ?>
                                    Brand
                                    <?php } else { ?>
                                    Lab
                                    <?php } ?>
                                    <?php } ?>
                                </a>
                            </li>
                        @endif
                    @endif
                    <?php } ?>
                    <?php if (session('user_role') == '4') { ?>
                    <li class="nav-item mr-3">
                        <a href="#"
                            class="nav-link py-4 px-6 {{ request()->is('manufacture/master_data/product/*') ? 'active' : '' }} {{ request()->is('test_lab/master_data/product/*') ? 'active' : '' }}"
                            data-toggle="tab" data-target="#kt_header_tab_3" role="tab">
                            <?php if (session('brand_type') == '1') { ?>
                            Product
                            <?php } else { ?>
                            Test Lab Type
                            <?php } ?>
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (session('user_role') == '1' || session('user_role') == '3') { ?>
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('transaction/*') ? 'active' : '' }}"
                            data-toggle="tab" data-target="#kt_header_tab_4" role="tab">QR Code</a>
                    </li>
                    <?php } ?>
                    <?php if (session('user_role') != '2') { ?>
                    <li class="nav-item mr-3">
                        <a href="#"
                            class="nav-link py-4 px-6 {{ request()->is('master_data/master_employees/*') ? 'active' : '' }}"
                            data-toggle="tab" data-target="#kt_header_tab_6" role="tab">
                            Users
                        </a>
                    </li>
                    <?php } ?>
                    <?php if (session('user_role') == '1') { ?>
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('application/*') ? 'active' : '' }}"
                            data-toggle="tab" data-target="#kt_header_tab_5" role="tab">Version</a>
                    </li>
                    <?php } ?>
                    <?php if (session('user_role') == '3') { ?>
                    <li class="nav-item mr-3">
                        <a href="#" class="nav-link py-4 px-6 {{ request()->is('report/*') ? 'active' : '' }}"
                            data-toggle="tab" data-target="#kt_header_tab_7" role="tab">Report</a>
                    </li>
                    <?php } ?>
                </ul> --}}
                <div class="tab-content">
                    {{-- <div class="tab-pane py-5 p-lg-0 justify-content-between {{ request()->is('home') ? 'show active' : '' }}"
                        id="kt_header_tab_1">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <li class="menu-item {{ request()->is('home') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('home') }}" class="menu-link">
                                        <span class="menu-text">Home</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div> --}}

                    {{-- <div class="tab-pane py-5 p-lg-0 justify-content-between {{ request()->is('dashboard') ? 'show active' : '' }} {{ request()->is('dashboard/*') ? 'show active' : '' }} {{ request()->is('dashboard_admin') ? 'show active' : '' }}"
                        id="kt_header_tab_7">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <li class="menu-item {{ request()->is('dashboard') ? 'menu-item-active' : '' }} {{ request()->is('dashboard_admin') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('dashboard') }}" class="menu-link">
                                        <span class="menu-text">Report</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('dashboard/customer_scan') ? 'menu-item-active' : '' }} {{ request()->is('dashboard_admin') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('dashboard_scan_zeta') }}" class="menu-link">
                                        <span class="menu-text">Customer Scan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div> --}}

                    {{-- <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('master_data/companies/*') ? 'show active' : '' }} {{ request()->is('manufacture/master_data/companies/*') ? 'show active' : '' }} {{ request()->is('test_lab/master_data/companies/*') ? 'show active' : '' }}"
                        id="kt_header_tab_2">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <?php if (session('user_role') == '1') { ?>
                                <li class="menu-item {{ request()->is('master_data/companies/company/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_company_view') }}" class="menu-link">
                                        <span class="menu-text">Master Data Company</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('master_data/companies/brand/*') ? 'menu-item-active' : '' }} {{ request()->is('master_data/companies/employees_brand/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_brand_view') }}" class="menu-link">
                                        <span class="menu-text">Master Data Brand</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (session('user_role') == '3') { ?>
                                <?php if (session('brand_type') == '1') { ?>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/plant/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_plant_view') }}" class="menu-link">
                                        <span class="menu-text">Production / Packaging Center</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (session('brand_type') == '2') { ?>
                                <li class="menu-item {{ request()->is('test_lab/master_data/companies/plant/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_lab_plant_view') }}" class="menu-link">
                                        <span class="menu-text">Testing Center / Laboratorium</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <li class="menu-item {{ request()->is('master_data/companies/logo_brand/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('logo_brand_view') }}" class="menu-link">
                                        <span class="menu-text">Upload brand logo</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (session('user_role') == '4') { ?>
                                <?php if (session('brand_type') == '1') { ?>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/batch_production/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_batch_production_view') }}" class="menu-link">
                                        <span class="menu-text">Batch Production</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php } ?>
                                <?php if (session('user_role') == '5') { ?>
                                <?php if (session('brand_type') == '1') { ?>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/batch_packaging/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_batch_packaging_view') }}" class="menu-link">
                                        <span class="menu-text">Batch Acceptance</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/pool_product/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_pool_product_view') }}" class="menu-link">
                                        <span class="menu-text">Pool Product</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/sub_batch_packaging/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_sub_batch_packaging_view') }}" class="menu-link">
                                        <span class="menu-text">Batch Packaging</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php } ?>
                                <?php if (session('user_role') == '8') { ?>
                                <?php if (session('brand_type') == '1') { ?>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/batch_store/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_batch_store_view') }}" class="menu-link">
                                        <span class="menu-text">Batch Store</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/pool_product/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_pool_product_view') }}" class="menu-link">
                                        <span class="menu-text">Pool Product</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('manufacture/master_data/companies/batch_delivery/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_batch_delivery_view') }}" class="menu-link">
                                        <span class="menu-text">Batch Delivery</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (session('brand_type') == '2') { ?>
                                <li class="menu-item {{ request()->is('test_lab/master_data/companies/batch_store/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_lab_batch_store_view') }}" class="menu-link">
                                        <span class="menu-text">Lab Result</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php } ?>
                            </ul>
                        </div>
                    </div> --}}

                    {{-- <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('product/product_attribute/*') ? 'show active' : '' }} {{ request()->is('manufacture/master_data/product/*') ? 'show active' : '' }} {{ request()->is('test_lab/master_data/product/*') ? 'show active' : '' }}"
                        id="kt_header_tab_3">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <?php if (session('brand_type') == '1') { ?>
                                <li class="menu-item {{ request()->is('manufacture/master_data/product/categories/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_category_view') }}" class="menu-link">
                                        <span class="menu-text">Category</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('manufacture/master_data/product/product/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_product_view') }}" class="menu-link">
                                        <span class="menu-text">Product</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('manufacture/master_data/product/product_model/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_product_model_view') }}" class="menu-link">
                                        <span class="menu-text">Model</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('manufacture/master_data/product/product_version/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_product_version_view') }}" class="menu-link">
                                        <span class="menu-text">Version</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <?php if (session('brand_type') == '2') { ?>
                                <li class="menu-item {{ request()->is('test_lab/master_data/product/categories/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_lab_category_view') }}" class="menu-link">
                                        <span class="menu-text">Test Lab Type</span>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div> --}}

                    {{-- <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('transaction/qr/*') ? 'show active' : '' }} {{ request()->is('kabar_masyarakat/*') ? 'show active' : '' }}"
                        id="kt_header_tab_4">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <li class="menu-item {{ request()->is('transaction/qr/order_qr/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('order_qr_view') }}" class="menu-link">
                                        <span class="menu-text">Order QR</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div> --}}

                    {{-- <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('application/*') ? 'show active' : '' }} {{ request()->is('kabar_masyarakat/*') ? 'show active' : '' }}"
                        id="kt_header_tab_5">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <li class="menu-item {{ request()->is('application/outdated_application_version/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('outdated_application_version_view') }}" class="menu-link">
                                        <span class="menu-text">Mobile Compability</span>
                                    </a>
                                </li>
                                <li class="menu-item {{ request()->is('application/legal_version/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('legal_version_view') }}" class="menu-link">
                                        <span class="menu-text">Privacy Policy & Term Services Version</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div> --}}

                    <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('master_data/master_employees/*') ? 'show active' : '' }} {{ request()->is('kabar_masyarakat/*') ? 'show active' : '' }}"
                        id="kt_header_tab_6">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <?php if (session('user_role') != '9') { ?>
                                <li class="menu-item {{ request()->is('master_data/master_employees/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('master_data_employee_view') }}" class="menu-link">
                                        <span class="menu-text">Employee List</span>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                    {{-- <div class="tab-pane p-5 p-lg-0 justify-content-between {{ request()->is('report/*') ? 'show active' : '' }}"
                        id="kt_header_tab_7">
                        <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                            <ul class="menu-nav">
                                <?php if (session('user_role') == '3') { ?>
                                <li class="menu-item {{ request()->is('report/customer_qr_report/*') ? 'menu-item-active' : '' }}"
                                    aria-haspopup="true">
                                    <a href="{{ route('customer_report_qr_view') }}" class="menu-link">
                                        <span class="menu-text">Customer Report QR</span>
                                    </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div> --}}

                </div>
            </div>
        </div>
    </div>
</div>
