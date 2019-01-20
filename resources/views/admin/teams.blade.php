@extends('layouts.app')

@section('content')
    <div id="wrapper">

        @include('layouts.sidebar')

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!--Start Dashboard Content-->
                <div class="row pt-2 pb-2">
                    <div class="col-sm-9">
                        <h4 class="page-title">Teams</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javaScript:void(0);">MCL</a></li>
                            <li class="breadcrumb-item"><a href="javaScript:void(0);">Teams</a></li>
                        </ol>
                    </div>
                    <div class="col-sm-3">
                        <div class="btn-group float-sm-right">
                            <a href="{{ url('/teams/new') }}" type="button"
                                    class="btn btn-primary waves-effect waves-light">
                                Add New Team
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Breadcrumb-->
                @if(!isset($newTeam))
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header"><i class="fa fa-table"></i> Teams</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="default-datatable" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($teams as $team)
                                                <tr>
                                                    <td>{{$team->team_name}}</td>
                                                    <td><a href="{{ url('/teams/edit/'.$team->id) }}"><i
                                                                    class="fa fa-pencil" aria-hidden="true"></i></a>
                                                    </td>
                                                    <td><a href="{{ url('teams/delete/'.$team->id) }}"><i
                                                                    class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th>Name</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Row-->
                @endif

                @if(isset($newTeam) && $newTeam)
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">Add Team</div>
                                    <hr>
                                    <form action="{{ (isset($team->team_name)) ? url('/teams/edit/'.$team->id) : url('/teams/new') }}"
                                          method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label for="input-1">Name</label>
                                            <input type="text" class="form-control" name="name" id="input-1"
                                                   placeholder="Enter Your Name" value="{{$team->team_name ?? ''}}">
                                            @if ($errors->has('name'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary shadow-primary px-5"><i
                                                        class="icon-lock"></i> Submit
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif


                    <!--End Dashboard Content-->

                    </div>
                    <!-- End container-fluid-->

            </div><!--End content-wrapper-->
            <!--Start Back To Top Button-->
            <a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i> </a>
            <!--End Back To Top Button-->

            <!--End footer-->

        </div>

@endsection