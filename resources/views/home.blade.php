@extends('layouts.app')

@section('content')
    <div id="wrapper">

        @include('layouts.sidebar')

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!--Start Dashboard Content-->

                <div class="row mt-3">
                    <div class="col-12 col-lg-6 col-xl-3">
                        <div class="card border-info border-left-sm">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-info">4500</h4>
                                        <span>Total Orders</span>
                                    </div>
                                    <div class="align-self-center w-circle-icon rounded-circle gradient-scooter">
                                        <i class="icon-basket-loaded text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-3">
                        <div class="card border-danger border-left-sm">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-danger">7850</h4>
                                        <span>Total Expenses</span>
                                    </div>
                                    <div class="align-self-center w-circle-icon rounded-circle gradient-bloody">
                                        <i class="icon-wallet text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-3">
                        <div class="card border-success border-left-sm">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-success">87.5%</h4>
                                        <span>Total Revenue</span>
                                    </div>
                                    <div class="align-self-center w-circle-icon rounded-circle gradient-quepal">
                                        <i class="icon-pie-chart text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 col-xl-3">
                        <div class="card border-warning border-left-sm">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-warning">8400</h4>
                                        <span>New Users</span>
                                    </div>
                                    <div class="align-self-center w-circle-icon rounded-circle gradient-blooker">
                                        <i class="icon-user text-white"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--End Row-->


                <div class="row">
                    <div class="col-12 col-lg-8 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                Product Sales
                                <div class="card-action">
                                    <div class="dropdown">
                                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                                            <i class="icon-options"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void();">Action</a>
                                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="dashboard-chart-1"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                Trending Products
                                <div class="card-action">
                                    <div class="dropdown">
                                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                                            <i class="icon-options"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void();">Action</a>
                                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="" style="">
                                    <canvas id="dashboard-chart-2" height="335"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div><!--End Row-->

                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header border-0">
                                Recent Orders
                                <div class="card-action">
                                    <div class="dropdown">
                                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                                            <i class="icon-options"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void();">Action</a>
                                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-items-center table-flush">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Photo</th>
                                        <th>Product ID</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Shipping</th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td>Iphone 5</td>
                                        <td><img src="assets/images/products/01.png" class="product-img" alt="product img"></td>
                                        <td>#9405822</td>
                                        <td><span class="badge gradient-quepal text-white shadow">Paid</span></td>
                                        <td>$ 1250.00</td>
                                        <td>03 Aug 2017</td>
                                        <td><div class="progress shadow" style="height: 6px;">
                                                <div class="progress-bar gradient-quepal" role="progressbar" style="width: 100%"></div>
                                            </div></td>
                                    </tr>

                                    <tr>
                                        <td>Earphone GL</td>
                                        <td><img src="assets/images/products/02.png" class="product-img" alt="product img"></td>
                                        <td>#9405820</td>
                                        <td><span class="badge gradient-blooker text-white shadow">Pending</span></td>
                                        <td>$ 1500.00</td>
                                        <td>03 Aug 2017</td>
                                        <td><div class="progress shadow" style="height: 6px;">
                                                <div class="progress-bar gradient-blooker" role="progressbar" style="width: 60%"></div>
                                            </div></td>
                                    </tr>

                                    <tr>
                                        <td>HD Hand Camera</td>
                                        <td><img src="assets/images/products/03.png" class="product-img" alt="product img"></td>
                                        <td>#9405830</td>
                                        <td><span class="badge gradient-bloody text-white shadow">Failed</span></td>
                                        <td>$ 1400.00</td>
                                        <td>03 Aug 2017</td>
                                        <td><div class="progress shadow" style="height: 6px;">
                                                <div class="progress-bar gradient-bloody" role="progressbar" style="width: 70%"></div>
                                            </div></td>
                                    </tr>

                                    <tr>
                                        <td>Clasic Shoes</td>
                                        <td><img src="assets/images/products/04.png" class="product-img" alt="product img"></td>
                                        <td>#9405825</td>
                                        <td><span class="badge gradient-quepal text-white shadow">Paid</span></td>
                                        <td>$ 1200.00</td>
                                        <td>03 Aug 2017</td>
                                        <td><div class="progress shadow" style="height: 6px;">
                                                <div class="progress-bar gradient-quepal" role="progressbar" style="width: 100%"></div>
                                            </div></td>
                                    </tr>

                                    <tr>
                                        <td>Hand Watch</td>
                                        <td><img src="assets/images/products/05.png" class="product-img" alt="product img"></td>
                                        <td>#9405840</td>
                                        <td><span class="badge gradient-bloody text-white shadow">Failed</span></td>
                                        <td>$ 1800.00</td>
                                        <td>03 Aug 2017</td>
                                        <td><div class="progress shadow" style="height: 6px;">
                                                <div class="progress-bar gradient-bloody" role="progressbar" style="width: 40%"></div>
                                            </div></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div><!--End Row-->

                <div class="row">
                    <div class="col-12 col-lg-7 col-xl-8">
                        <div class="card">
                            <div class="card-header">Top Selling Country
                                <div class="card-action">
                                    <div class="dropdown">
                                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                                            <i class="icon-options"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void();">Action</a>
                                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-7 col-xl-8 border-right">
                                        <div id="dashboard-map" style="height: 274px"></div>
                                    </div>
                                    <div class="col-lg-5 col-xl-4">

                                        <p><i class="flag-icon flag-icon-us mr-1"></i> USA <span class="float-right">70%</span></p>
                                        <div class="progress" style="height: 7px;">
                                            <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" style="width: 70%"></div>
                                        </div>

                                        <p class="mt-3"><i class="flag-icon flag-icon-ca mr-1"></i> Canada <span class="float-right">65%</span></p>
                                        <div class="progress" style="height: 7px;">
                                            <div class="progress-bar bg-danger progress-bar-striped" role="progressbar" style="width: 65%"></div>
                                        </div>

                                        <p class="mt-3"><i class="flag-icon flag-icon-gb mr-1"></i> England <span class="float-right">60%</span></p>
                                        <div class="progress" style="height: 7px;">
                                            <div class="progress-bar bg-success progress-bar-striped" role="progressbar" style="width: 60%"></div>
                                        </div>

                                        <p class="mt-3"><i class="flag-icon flag-icon-au mr-1"></i> Australia <span class="float-right">55%</span></p>
                                        <div class="progress" style="height: 7px;">
                                            <div class="progress-bar bg-warning progress-bar-striped" role="progressbar" style="width: 55%"></div>
                                        </div>

                                        <p class="mt-3"><i class="flag-icon flag-icon-in mr-1"></i> India <span class="float-right">50%</span></p>
                                        <div class="progress" style="height: 7px;">
                                            <div class="progress-bar bg-info progress-bar-striped" role="progressbar" style="width: 50%"></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5 col-xl-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-primary">45,85,240</h4>
                                        <span>Total Likes</span>
                                    </div>
                                    <div class="align-self-center w-circle-icon rounded gradient-violet">
                                        <i class="icon-like text-white"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-danger">78,50,325</h4>
                                        <span>Comments</span>
                                    </div>
                                    <div class="align-self-center w-circle-icon rounded gradient-ibiza">
                                        <i class="icon-speech text-white"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="media">
                                    <div class="media-body text-left">
                                        <h4 class="text-dark">25,40,354</h4>
                                        <span>Total Shares</span>
                                    </div>
                                    <div class="align-self-center w-circle-icon rounded gradient-royal">
                                        <i class="icon-share text-white"></i></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div><!--End Row-->

                <div class="row">
                    <div class="col-12 col-lg-4 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                Last Week Revenue
                                <div class="card-action">
                                    <div class="dropdown">
                                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                                            <i class="icon-options"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void();">Action</a>
                                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="dashboard-chart-3" height="240"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                Orders Summary
                                <div class="card-action">
                                    <div class="dropdown">
                                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                                            <i class="icon-options"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void();">Action</a>
                                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="dashboard-chart-4" height="240"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                Top Selling Categories
                                <div class="card-action">
                                    <div class="dropdown">
                                        <a href="javascript:void();" class="dropdown-toggle dropdown-toggle-nocaret" data-toggle="dropdown">
                                            <i class="icon-options"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <a class="dropdown-item" href="javascript:void();">Action</a>
                                            <a class="dropdown-item" href="javascript:void();">Another action</a>
                                            <a class="dropdown-item" href="javascript:void();">Something else here</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void();">Separated link</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <canvas id="dashboard-chart-5" height="240"></canvas>
                            </div>
                        </div>
                    </div>
                </div><!--End Row-->

                <!--End Dashboard Content-->

            </div>
            <!-- End container-fluid-->

        </div><!--End content-wrapper-->
        <!--Start Back To Top Button-->
        <a href="javaScript:void();" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
        <!--End Back To Top Button-->

        <!--Start footer-->
        <footer class="footer">
            <div class="container">
                <div class="text-center">
                    Copyright © 2018 Rocker Admin
                </div>
            </div>
        </footer>
        <!--End footer-->

    </div>
@endsection
