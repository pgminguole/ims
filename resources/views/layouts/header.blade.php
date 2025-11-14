<div class="header">
    <nav class="navbar py-4">
        <div class="container-xxl">

            <!-- header rightbar icon -->
            <div class="h-right d-flex align-items-center mr-5 mr-lg-0 order-1">

                <div class="dropdown user-profile ml-2 ml-sm-3 d-flex align-items-center">
                    <a class="nav-link dropdown-toggle pulse p-0" href="#" role="button" data-bs-toggle="dropdown"
                       data-bs-display="static">
                        <img class="avatar lg rounded-circle img-thumbnail" src="{{ asset('images/profile.png') }}"
                             alt="profile">
                    </a>
                    <div class="dropdown-menu rounded-lg shadow border-0 dropdown-animation dropdown-menu-end p-0 m-0">
                        <div class="card border-0 w280">
                            <div class="card-body pb-0">
                                <div class="d-flex py-1">
                                    <img class="avatar rounded-circle" src="{{ asset('images/profile.png') }}"
                                         alt="profile">
                                    <div class="flex-fill ms-3">
                                        <p class="mb-0"><span
                                                class="font-weight-bold">{{ Auth::user()->full_name }}</span>
                                        </p>
                                        <small class="">{{ Auth::user()->email }}</small>
                                    </div>
                                </div>

                                <div>
                                    <hr class="dropdown-divider border-dark">
                                </div>
                            </div>
                            <div class="list-group m-2 ">
                                <a href=""
                                   class="list-group-item list-group-item-action border-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24px"
                                         height="24px" viewBox="0 0 38 38" class="me-3">
                                        <path xmlns="http://www.w3.org/2000/svg"
                                              d="M36.15,38H1.85l0.16-1.14c0.92-6.471,3.33-7.46,6.65-8.83c0.43-0.17,0.87-0.36,1.34-0.561  c0.19-0.08,0.38-0.17,0.58-0.26c1.32-0.61,2.14-1.05,2.64-1.45c0.18,0.48,0.47,1.13,0.93,1.78C15.03,28.78,16.53,30,19,30  c2.47,0,3.97-1.22,4.85-2.46c0.46-0.65,0.75-1.3,0.931-1.78c0.5,0.4,1.319,0.84,2.64,1.45c0.2,0.09,0.39,0.17,0.58,0.26  c0.47,0.2,0.91,0.391,1.34,0.561c3.32,1.37,5.73,2.359,6.65,8.83L36.15,38z M20,13v4h-2v-4H20z"
                                              style="fill:var(--primary-color);" data-st="fill:var(--chart-color4);">
                                        </path>
                                        <path xmlns="http://www.w3.org/2000/svg" class="st0"
                                              d="M21.67,17.34C21.22,18.27,20.29,19,19,19s-2.22-0.73-2.67-1.66l-1.79,0.891C15.31,19.78,16.88,21,19,21  s3.69-1.22,4.46-2.77L21.67,17.34z M15,10.85c-0.61,0-1.1,0.38-1.1,1.65s0.49,1.65,1.1,1.65s1.1-0.38,1.1-1.65S15.61,10.85,15,10.85  z M23,10.85c-0.61,0-1.1,0.38-1.1,1.65s0.489,1.65,1.1,1.65s1.1-0.38,1.1-1.65S23.61,10.85,23,10.85z M35.99,36.86  c-0.92-6.471-3.33-7.46-6.65-8.83c-0.43-0.17-0.87-0.36-1.34-0.561c-0.19-0.09-0.38-0.17-0.58-0.26c-1.32-0.61-2.14-1.05-2.64-1.45  c-0.521-0.42-0.7-0.8-0.761-1.29C26.55,22.74,28,19.8,28,17V4.56l-1.18,0.21C26.1,4.91,25.58,5,25.05,5  c-1.439,0-2.37-0.24-3.35-0.49C20.71,4.26,19.68,4,18.21,4c-1.54,0-2.94,0.69-3.83,1.9l1.61,1.18C16.5,6.39,17.31,6,18.21,6  c1.22,0,2.08,0.22,3,0.45C22.22,6.71,23.36,7,25.05,7c0.32,0,0.63-0.02,0.95-0.06V17c0,3.44-2.62,7-7,7s-7-3.56-7-7V6.29  C12.23,5.59,13.61,2,18.21,2c1.61,0,2.76,0.28,3.88,0.55C23.06,2.78,23.98,3,25.05,3C26.12,3,27.19,2.74,28,2.47V0.34  C27.34,0.61,26.17,1,25.05,1c-0.83,0-1.6-0.18-2.49-0.4C21.38,0.32,20.05,0,18.21,0c-5.24,0-7.64,3.86-8.18,5.89L10,17  c0,2.8,1.45,5.74,3.98,7.47c-0.06,0.49-0.24,0.87-0.76,1.29c-0.5,0.4-1.32,0.84-2.64,1.45c-0.2,0.09-0.39,0.18-0.58,0.26  c-0.47,0.2-0.91,0.391-1.34,0.561c-3.32,1.37-5.73,2.359-6.65,8.83L1.85,38h34.3L35.99,36.86z M4.18,36c0.81-4.3,2.28-4.9,5.24-6.12  c0.62-0.25,1.29-0.53,2-0.86c1.09-0.5,2.01-0.949,2.73-1.479c0.8-0.56,1.36-1.22,1.64-2.12C16.76,25.78,17.83,26,19,26  s2.24-0.22,3.21-0.58c0.28,0.9,0.84,1.561,1.64,2.12c0.721,0.53,1.641,0.979,2.73,1.479c0.71,0.33,1.38,0.61,2,0.86  c2.96,1.22,4.43,1.83,5.24,6.12H4.18z">
                                        </path>
                                    </svg>Profile Page
                                </a>

                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="list-group-item list-group-item-action border-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24px"
                                         height="24px" viewBox="0 0 24 24" class="me-3">
                                        <rect xmlns="http://www.w3.org/2000/svg" class="st0" width="24"
                                              height="24" style="fill:none;;" fill="none"></rect>
                                        <path xmlns="http://www.w3.org/2000/svg"
                                              d="M20,4c0-1.104-0.896-2-2-2H6C4.896,2,4,2.896,4,4v16c0,1.104,0.896,2,2,2h12  c1.104,0,2-0.896,2-2V4z"
                                              style="fill:var(--primary-color);" data-st="fill:var(--chart-color4);">
                                        </path>
                                        <path xmlns="http://www.w3.org/2000/svg" class="st0"
                                              d="M15,6.81v2.56c0.62,0.7,1,1.62,1,2.63c0,2.21-1.79,4-4,4s-4-1.79-4-4c0-1.01,0.38-1.93,1-2.63V6.81  C7.21,7.84,6,9.78,6,12c0,3.31,2.69,6,6,6c3.31,0,6-2.69,6-6C18,9.78,16.79,7.84,15,6.81z M13,6.09C12.68,6.03,12.34,6,12,6  s-0.68,0.03-1,0.09V12h2V6.09z">
                                        </path>
                                    </svg>Sign out
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- menu toggler -->
            <button class="navbar-toggler p-0 border-0 menu-toggle order-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#mainHeader">
                <span class="fa fa-bars"></span>
            </button>

            <!-- main menu Search-->
            <div class="order-0 col-lg-4 col-md-6 col-sm-12 col-12 mb-3 mb-md-0 d-flex align-items-center">
                <div class="main-search px-3 flex-fill">
                    Hi {{ auth()->user()->first_name }}, Welcome to Asset Manager.
                </div>
            </div>

        </div>
    </nav>
</div>