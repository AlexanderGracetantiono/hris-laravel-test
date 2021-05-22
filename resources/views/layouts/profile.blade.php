<div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
    <div class="offcanvas-header d-flex align-items-center justify-content-between pb-5">
        <h3 class="font-weight-bold m-0">User Profile</h3>
        <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
            <i class="ki ki-close icon-xs text-muted"></i>
        </a>
    </div>
    <div class="offcanvas-content pr-5 mr-n5">
        <div class="d-flex align-items-center mt-5">
            <div class="symbol symbol-100 mr-5">
                <div class="flex-shrink-0 mr-4 mt-lg-0 mt-3">
                    <div class="symbol symbol-lg-75 d-none">
                        <!-- <img alt="Pic" src="assets/media/users/300_10.jpg"> -->
                    </div>
                    <div class="symbol symbol-lg-75 symbol-primary">
                        <span class="symbol-label font-size-h3 font-weight-boldest">{{ session('user_initial_name') }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column">
                <a href="#" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary">{{ session('user_full_name') }}</a>
                <div class="text-muted mt-1">
                    @if (session('user_role') == 1)
                        CekOri Administrator
                    @endif
                    @if (session('brand_type') == 1)
                        @if (session('user_role') == 2)
                            QR Approver
                        @elseif (session('user_role') == 3)
                            PIC Brand
                        @elseif (session('user_role') == 4)
                            Production Administrator
                        @elseif (session('user_role') == 5)
                            Packaging Administrator
                        @elseif (session('user_role') == 8)
                            Store Inventory Administrator
                        @endif
                    @elseif(session('brand_type') == 2)
                        @if (session('user_role') == 2)
                            QR Approver
                        @elseif (session('user_role') == 3)
                            Lab PIC Brand
                        @elseif (session('user_role') == 4)
                            Lab Testing Doctor
                        @elseif (session('user_role') == 5)
                            Laboratorium Doctor
                        @elseif (session('user_role') == 8)
                            Result Doctor
                        @endif
                    @endif
                </div>
                <div class="navi mt-2">
                    <a href="{{ route('logout_process')}}" class="btn btn-sm btn-primary font-weight-bolder py-2 px-5">Log Out</a>
                </div>
            </div>
        </div>
        <div class="separator separator-dashed mt-8 mb-5"></div>
    </div>
</div>
