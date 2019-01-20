@extends('layouts.app')

@section('content')
    <div id="wrapper">

        @include('layouts.sidebar')

        <div class="clearfix"></div>

        <div class="content-wrapper">
            <div class="container-fluid">

                <!--Start Dashboard Content-->
                <div class="row pt-2 pb-2">
                    <div class="col-sm-12">
                        <div class="btn-group float-sm-right">
                            @if(isset($display_all) && $display_all)
                                <a href="{{ url('/matches/new') }}" type="button"
                                   class="btn btn-primary waves-effect waves-light">
                                    Add Match
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- End Breadcrumb-->
                @if(isset($display_all) && $display_all)
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header"><i class="fa fa-table"></i> Matches</div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="default-datatable" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Match</th>
                                                <th>Toss Won By</th>
                                                <th>Elected To</th>
                                                <th>No Of Overs</th>
                                                <th>Result</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($matches as $player)
                                                <tr>
                                                    <td>{{$player->team_a}} vs {{$player->team_b}}</td>
                                                    <td>{{$player->toss_won}}</td>
                                                    <td>{{$player->elected}}</td>
                                                    <td>{{$player->no_of_overs}}</td>
                                                    <td>{!!($player->in_progress) ? '<a class="btn btn-warning btn-sm" href="/match/start/'.$player->id.'">In Progress</a>' : '<a class="btn btn-info btn-sm" href="/match/start/'.$player->id.'" disabled>Match Completed</a>'!!}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <th>Match</th>
                                                <th>Toss Won By</th>
                                                <th>Elected To</th>
                                                <th>No Of Overs</th>
                                                <th>Result</th>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- End Row-->
                @endif

                @if(isset($newMatch) && $newMatch)
                    <div class="row">
                        <div class="col-lg-10 mx-auto">
                            <div class="card">
                                <div class="card-body">
                                    <div class="card-title">Add Match</div>
                                    <hr>
                                    <form action="{{ url('/matches/new') }}"
                                          method="post" id="addMatchForm">
                                        @csrf

                                        <div class="form-group">
                                            <label for="">First Team</label>
                                            <select name="team_id_a" id="team_id_a" class="form-control" required>
                                                <option {{!isset($player)? 'selected' : ''}} disabled value="0">Select
                                                    Team
                                                </option>
                                                @foreach($teams as $team)
                                                    <option {{isset($player) && $player->team_id == $team->id ? 'selected' : ''}} value="{{$team->id}}">{{ $team->team_name }}</option>
                                                @endforeach
                                            </select>
                                            @if ($errors->has('team_id_a'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('team_id_a') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="">Second Team</label>
                                            <select name="team_id_b" id="team_id_b" class="form-control" required>
                                                <option selected disabled value="0">Select
                                                    Team
                                                </option>
                                            </select>
                                            @if ($errors->has('team_id_b'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('team_id_b') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="custom-radio" id="toss">
                                            Toss Won By <br>
                                            <label>
                                                <input type="radio" name="toss_won_by" required> Team A
                                            </label>

                                            <label style="margin-left: 15px">
                                                <input type="radio" name="toss_won_by" required> Team B
                                            </label> <br>
                                            @if ($errors->has('toss_won_by'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('toss_won_by') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="">Elected</label>
                                            <select name="elected_to" class="form-control" required>
                                                <option value="1">
                                                    Batting
                                                </option>
                                                <option value="2">
                                                    Bowling
                                                </option>
                                            </select>
                                            @if ($errors->has('elected_to'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('elected_to') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="input-1">No of Overs</label>
                                            <input type="number" class="form-control" name="no_of_overs" id="input-1"
                                                   placeholder="Enter No of Overs" required>
                                            @if ($errors->has('no_of_overs'))
                                                <span>
                                                    <strong class="invalid">{{ $errors->first('no_of_overs') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="row" id="playing11_1">
                                            <div class="form-group col-lg-8">
                                                <label for="">Select Playing XI of Team 1</label>
                                                <select multiple="multiple" class="multi-select" id="my_multi_select1"
                                                        name="team1[]" required>

                                                </select>
                                                <span class="invalid hidden" id="playing_error_1">Cannot add more than 11 players</span>
                                                @if ($errors->has('team1'))
                                                    <span> <br>
                                                    <strong class="invalid">{{ $errors->first('team1') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="">Select Captain of Team 1</label>
                                                <select class="form-control" name="select_captain_1"
                                                        id="select_captain_1" required>

                                                </select>
                                                <br><br>
                                                <label for="">Select Wicket Keeper of Team 1</label>
                                                <select class="form-control" name="select_wk_1" id="select_wk_1" required>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="row" id="playing11_2">
                                            <div class="form-group col-lg-8">
                                                <label for="">Select Playing XI of Team 2</label>
                                                <select multiple="multiple" class="multi-select" id="my_multi_select2"
                                                        name="team2[]" required>

                                                </select>
                                                <span class="invalid hidden" id="playing_error_2">Cannot add more than 11 players</span>
                                                @if ($errors->has('team2'))
                                                    <span> <br>
                                                    <strong class="invalid">{{ $errors->first('team2') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="">Select Captain of Team 2</label>
                                                <select class="form-control" name="select_captain_2"
                                                        id="select_captain_2" required>

                                                </select>
                                                <br><br>
                                                <label for="">Select Wicket Keeper of Team 2</label>
                                                <select class="form-control" name="select_wk_2" id="select_wk_2" required>

                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary shadow-primary px-5"><i
                                                        class="icon-lock"></i> Start Match
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(isset($startMatch) && $startMatch)
                            <input type="hidden" id="match_id" value="{{$match_id}}">
                            <div class="row">
                                <div class="card" style="width: 100%">
                                    <div class="card-body">
                                        <div id="teamssumm" class="team-bold"></div>
                                        <div id="score_card_high" class="hidden"
                                             style="margin-bottom: 20px; text-align: center; font-size: 20px">
                                            <b><span id="first_team_name"></span> <span id="first_innings_score"></span></b>
                                            <br>
                                            <b><span id="innings"></span> Innings: <span id="batting_team"></span> <span
                                                        id="team_runs_disp">12</span>/<span
                                                        id="wickets">1</span></b> (<span id="overs_bowled"></span>)
                                            <br>
                                            <span style="float: right"><button class="btn btn-gradient-ibiza" id="end_match">End Match</button></span>
                                            <br>
                                        </div>
                                        <div class="table-responsive" id="table-score">

                                        </div>
                                        <div id="match_finish" class="hidden"
                                             style="margin-top: 20px;font-size: 20px;font-weight: bold;text-align: center;">
                                            Match Completed <br>
                                            Match Won By: <span id="winning_team"></span>
                                        </div>
                                        <div id="end_match_disp" class="hidden"
                                             style="margin-top: 20px;font-size: 20px;font-weight: bold;text-align: center;">
                                            Match Ended <br>
                                            No result
                                            <br>Reason: <span id="reason"></span>
                                        </div>
                                        <div id="score-buttons" class="row hidden" style="margin-top: 15px">
                                            <div class="col-lg-12" id="recent_balls"><b>Recent</b> <br>

                                            </div>
                                            <div class="col-lg-6">
                                                <label for="runs">Runs</label>
                                                <input type="text" id="runs_per_ball" class="form-control">
                                                <input type="number" id="is_legal" hidden>
                                                <input type="number" id="total_runs" hidden>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-success" id="submitball"
                                                        style="margin-top: 30px">Submit
                                                </button>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-danger" id="clearAll" style="margin-top: 30px">
                                                    Clear
                                                </button>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-warning" id="undoball"
                                                        style="margin-top: 30px">Undo
                                                </button>
                                            </div>
                                            <div class="col-lg-12" style="margin-top: 20px"></div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-info" id="runs0">0 run</button>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-info" id="runs1">1 run</button>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-info" id="runs2">2 runs</button>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-info" id="runs3">3 runs</button>
                                            </div>
                                            <div class="col-lg-1">
                                                <button class="btn btn-info" id="runs4">4 runs</button>
                                            </div>
                                            <div class="col-lg-1" style="margin-left: 30px">
                                                <button class="btn btn-info" id="runs5">5 runs</button>
                                            </div>
                                            <div class="col-lg-1" style="margin-left: 30px;">
                                                <button class="btn btn-info" id="runs6">6 runs</button>
                                            </div>
                                            <div class="col-lg-12" style="margin-top: 20px"></div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-gradient-bloody" id="runsNb">Nb</button>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-gradient-bloody" id="runsWd">Wd</button>
                                            </div>
                                            <div class="col-lg-2">
                                                <button class="btn btn-gradient-bloody" id="runsW">W</button>
                                            </div>
                                            <div class="col-lg-1">
                                                <button class="btn btn-gradient-bloody" id="runsLb">Lb</button>
                                            </div>
                                            <div class="col-lg-1">
                                                <button disabled class="btn btn-gradient-bloody" id="runsplus1">+1
                                                </button>
                                            </div>
                                            <div class="col-lg-1">
                                                <button disabled class="btn btn-gradient-bloody" id="runsplus2">+2
                                                </button>
                                            </div>
                                            <div class="col-lg-1">
                                                <button disabled class="btn btn-gradient-bloody" id="runsplus3">+3
                                                </button>
                                            </div>
                                            <div class="col-lg-1">
                                                <button disabled class="btn btn-gradient-bloody" id="runsplus4">+4
                                                </button>
                                            </div>
                                            <div class="col-lg-1">
                                                <button disabled class="btn btn-gradient-bloody" id="runsplus6">+6
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Row-->
                            <div id="myModalfirstinnings" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <form method="post" id="initialcondition">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Start Innings <span id="inningsDisp"></span>
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row" id="batsmen">
                                                    <div class="col-lg-6">
                                                        Striker
                                                        <select required name="b1" id="batsmen1"
                                                                class="form-control">
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6">
                                                        <br>
                                                        Non Striker
                                                        <select required name="b1" id="batsmen2"
                                                                class="form-control">
                                                            <option value="" selected disabled>Select Batsman
                                                                One
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6">
                                                        <br>
                                                        Select Bowler Over 1
                                                        <select required name="bow1" id="bowler1"
                                                                class="form-control">
                                                            <option value="" selected disabled>Select Bowler
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-12">
                                                        <div class="row" id="over1"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" class="btn btn-info" value="Submit">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div id="end_match_modal" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <form method="post" id="end_match_form">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Are you sure?
                                                </h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row" id="batsmen">
                                                    <div class="col-lg-6">
                                                        <label for="">Reason for ending match</label>
                                                        <input type="text" class="form-control" id="reason_end_match" name="" required>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" class="btn btn-info" value="Submit">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div id="nextOverSelect" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <form method="post" id="nextOverForm">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Over <span id="over_number"></span> is Completed
                                                </h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-lg-6" id="nextBowler">

                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6" id="onStrike">

                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" class="btn btn-info" value="Submit">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div id="batsmanOut" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <form method="post" id="batsmanOutForm">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Out</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-lg-6" id="outOptions">
                                                        <label>Out How</label>
                                                        <select class="form-control" name="out_param" id="out_params"
                                                                required="required"></select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6" id="whoIsOut">
                                                        <label>Who is Out</label>
                                                        <select class="form-control" name="who_is_ou" id="who_is_out"
                                                                required="required"></select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6" id="fielder1">
                                                        <label>Fielder 1</label>
                                                        <select class="form-control" id="fielder_1"></select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6" id="fielder2">
                                                        <label>Fielder 2</label>
                                                        <select class="form-control" id="fielder_2"></select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" class="btn btn-info" value="Submit">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div id="nextBatsman" class="modal fade" role="dialog">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <form method="post" id="nextBatsmanForm">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Next Batsman</h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <label>Select Batsman</label>
                                                        <select class="form-control" id="next_batsman"
                                                                required></select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                    <div class="col-lg-6" id="nextOnStrike">
                                                        <label>On strike</label>
                                                        <select class="form-control" id="next_on_strike"
                                                                required></select>
                                                    </div>
                                                    <div class="col-lg-6"></div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <input type="submit" class="btn btn-info" value="Submit">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    @endif

                    <!--End Dashboard Content-->

                    </div>
                    <!-- End container-fluid-->

            </div>

        </div>
    </div>

@endsection