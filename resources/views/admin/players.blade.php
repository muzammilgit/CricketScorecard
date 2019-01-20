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
                        <h4 class="page-title">Players</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javaScript:void(0);">MCL</a></li>
                            <li class="breadcrumb-item"><a href="javaScript:void(0);">Players</a></li>
                        </ol>
                    </div>
                    <div class="col-sm-3">
                        <div class="btn-group float-sm-right">
                            <a href="{{ url('/players/new') }}" type="button"
                                    class="btn btn-primary waves-effect waves-light">
                                Add New Player
                            </a>
                        </div>
                    </div>
                </div>
                <!-- End Breadcrumb-->
                @if(!isset($newPlayer))
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header"><i class="fa fa-table"></i> Players</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="default-datatable" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Team</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($players as $player)
                                                <tr>
                                                    <td>{{$player->player_name}}</td>
                                                    <td>{{$player->team_name}}</td>
                                                    <td><a href="{{ url('/players/edit/'.$player->id) }}"><i
                                                                    class="fa fa-pencil" aria-hidden="true"></i></a>
                                                    </td>
                                                    <td><a href="{{ url('/players/delete/'.$player->id) }}"><i
                                                                    class="fa fa-trash" aria-hidden="true"></i></a></td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th>Name</th>
                                                <th>Team</th>
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

                @if(isset($newPlayer) && $newPlayer)
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">Add Player</div>
                                    <hr>
                                    <form action="{{ (isset($player->player_name)) ? url('/players/edit/'.$player->id) : url('/players/new') }}"
                                          method="post">
                                        @csrf
                                        <div class="form-group">
                                            <label for="input-1">Name</label>
                                            <input type="text" class="form-control" name="name" id="input-1"
                                                   placeholder="Enter Your Name"
                                                   value="{{$player->player_name ?? old('name')}}">
                                            @if ($errors->has('name'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('name') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="">Team</label>
                                            <select name="team_id" class="form-control">
                                                <option {{!isset($player)? 'selected' : ''}} disabled value="0">Select
                                                    Team
                                                </option>
                                                @foreach($teams as $team)
                                                    <option {{isset($player) && $player->team_id == $team->id ? 'selected' : ''}} value="{{$team->id}}">{{ $team->team_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('team_id'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('team_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="">Player Category</label>
                                            <select name="player_param" class="form-control">
                                                <option {{!isset($player)? 'selected' : ''}} disabled value="0">Select Type of Player
                                                </option>
                                                <option {{isset($player) && $player->player_param == 1 ? 'selected' : ''}} value="1">Batsman</option>
                                                <option {{isset($player) && $player->player_param == 2 ? 'selected' : ''}} value="2">Bowler</option>
                                                <option {{isset($player) && $player->player_param == 3 ? 'selected' : ''}} value="3">All Rounder</option>
                                            </select>
                                            @if ($errors->has('player_param'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('player_param') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        {{--<div class="form-group icheck-primary">
                                            <div class="icheck-primary">
                                                <input type="checkbox" name="enable_voting" {{isset($player) && $player->enable_voting == 1 ? 'checked' : ''}} id="primary" value="1" />
                                                <label for="primary">Enable Voting</label>
                                            </div>
                                            @if ($errors->has('team_id'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('team_id') }}</strong>
                                                </span>
                                            @endif
                                        </div>--}}
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

            <!--Start footer-->
            <!--End footer-->

        </div>

@endsection