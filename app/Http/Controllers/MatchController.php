<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class MatchController extends Controller
{
    //
    public function __construct(DB $db)
    {

    }

    public function index(DB $db)
    {
        $data = array();
        $data["display_all"] = true;
        $data["matches"] = $db::select('SELECT * FROM `matches`');
        foreach ($data["matches"] as $a) {
            $a->team_a = $db::select('SELECT `team_name` FROM `teams` WHERE `id` =' . $a->team_a_id)[0]->team_name;
            $a->team_b = $db::select('SELECT `team_name` FROM `teams` WHERE `id` =' . $a->team_b_id)[0]->team_name;
            $a->toss_won = $db::select('SELECT `team_name` FROM `teams` WHERE `id` =' . $a->toss_won_by)[0]->team_name;
            ($a->elected_to == 1) ? $a->elected = 'Batting' : $a->elected = 'Bowling';
            ($a->won_by == NULL) ? $a->in_progress = true : $a->in_progress = false;
            $a->overs_bowled = $db::select('SELECT COUNT(`id`) as overs FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $a->id)[0]->overs;
            $a->balls_bowled = $db::select('SELECT COUNT(`id`) as balls FROM `balls` WHERE `is_delete` IS NULL AND `isvalid` IN (1,3,4) AND `over_id` = (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $a->id . ' ORDER BY `id` DESC LIMIT 1)')[0]->balls;
            if ($a->balls_bowled < 6) {
                $a->overs_bowled--;
            } else if ($a->balls_bowled == 6) {
                $a->balls_bowled = 0;
            }
            $a->team_runs_disp = $db::select('SELECT SUM(`runs_scored`) as runs FROM `balls` WHERE `is_delete` IS NULL AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $a->id . ' ORDER BY `id` DESC)')[0]->runs;
            $a->overs_bowled_total = $a->overs_bowled . '.' . $a->balls_bowled;
        }
        return view('admin.matches', $data);
    }

    public function getBothTeamPlayers(Request $request, DB $db)
    {
        $team_a = $request->input('team_a');
        $team_b = $request->input('team_b');
        $data = array();
        $data["players1"] = $db::select('SELECT * FROM `players` WHERE `is_delete` IS NULL AND `team_id` =' . $team_a);
        $data["players2"] = $db::select('SELECT * FROM `players` WHERE `is_delete` IS NULL AND `team_id` =' . $team_b);
        return json_encode($data);
    }

    public function newMatch(DB $db, Request $request)
    {
        $data = array();
        $data["teams"] = $db::select('SELECT * FROM `teams` WHERE `isdelete` IS NULL');
        $data["newMatch"] = true;
        if ($request->isMethod('post')) {
            $data['name'] = $request->input('name');
            $data['team_id'] = $request->input('team_id');
            $data["my_multi_select1"] = $request->team1;
            $data["my_multi_select2"] = $request->team2;
//            dd($request);
            $this->validate(
                $request,
                [
                    'no_of_overs' => 'required',
                    'toss_won_by' => 'required',
                    'elected_to' => 'required',
                    'team_id_a' => 'required',
                    'team_id_b' => 'required',
                    'team1' => 'required|array|min:11|max:11',
                    'team2' => 'required|array|min:11|max:11'
                ]
            );
            $db::insert('INSERT INTO `matches` (`no_of_overs`, `toss_won_by`, `elected_to`, `team_a_id`, `team_b_id`) VALUES (?, ?, ?, ?, ?)', [$request->no_of_overs, $request->toss_won_by, $request->elected_to, $request->team_id_a, $request->team_id_b]);
            $data["match_id"] = $db::getPdo()->lastInsertId();
            $db::update('UPDATE `players` SET `is_playing` = ? WHERE `team_id` = ? OR `team_id` = ?', [NULL, $request->team_id_a, $request->team_id_b]);
            foreach ($data["my_multi_select1"] as $id) {
                $db::update('UPDATE `players` SET `is_playing` = ? WHERE `id` = ?', [1, $id]);
                if ($id == $request->select_captain_1) {
                    $db::insert('INSERT INTO `playing11`( `player_id`, `match_id`, `is_captain`) VALUES (? , ?, ?)', [$id, $data["match_id"], 1]);
                } else if ($id == $request->select_wk_1) {
                    $db::insert('INSERT INTO `playing11`( `player_id`, `match_id`, `is_wk`) VALUES (? , ?, ?)', [$id, $data["match_id"], 1]);
                } else {
                    $db::insert('INSERT INTO `playing11`( `player_id`, `match_id`) VALUES (? , ?)', [$id, $data["match_id"]]);
                }
            }
            foreach ($data["my_multi_select2"] as $id) {
                $db::update('UPDATE `players` SET `is_playing` = ? WHERE `id` = ?', [1, $id]);
                if ($id == $request->select_captain_2) {
                    $db::insert('INSERT INTO `playing11`( `player_id`, `match_id`, `is_captain`) VALUES (? , ?, ?)', [$id, $data["match_id"], 1]);
                } else if ($id == $request->select_wk_2) {
                    $db::insert('INSERT INTO `playing11`( `player_id`, `match_id`, `is_wk`) VALUES (? , ?, ?)', [$id, $data["match_id"], 1]);
                } else {
                    $db::insert('INSERT INTO `playing11`( `player_id`, `match_id`) VALUES (? , ?)', [$id, $data["match_id"]]);
                }
            }
            return redirect()->route('matches');
        }
        return view('admin.matches', $data);
    }

    public function getOtherTeams(DB $db, Request $request)
    {
        if ($request->input('team_id')) {

            $k = $db::select('SELECT * FROM `teams` WHERE `isdelete` IS NULL AND `id` <> ' . $request->input('team_id'));
            $a = '';
            foreach ($k as $b) {
                $a .= '<option value="' . $b->id . '">' . $b->team_name . '</option>';
            }
            return $a;
        } else {
            return json_encode($db::select('SELECT * FROM `teams` WHERE `isdelete` IS NULL '));
        }
    }

    public function getOtherBatsman(DB $db, Request $request)
    {
        if ($request->input('batsman_id')) {

            $k = $db::select('SELECT * FROM `players` WHERE `isdelete` IS NULL AND `is_playing` = 1 AND `id` <> ' . $request->input('batsman_id') . ' AND `team_id` = ' . $request->input('team_id'));
            $a = '';
            foreach ($k as $b) {
                $a .= '<option value="' . $b->id . '">' . $b->team_name . '</option>';
            }
            return $a;
        } else {
            return json_encode($db::select('SELECT * FROM `teams` WHERE `isdelete` IS NULL '));
        }
    }

    public function startMatch($match_id, DB $db)
    {
        $data = array();
        $a = $db::select('SELECT * FROM `matches` WHERE `id` = ' . $match_id)[0];
        if ($a->elected_to == 1) {
            $id = $a->toss_won_by;
            $bowl = ($a->toss_won_by == $a->team_a_id) ? $a->team_b_id : $a->team_a_id;
        } else {
            $id = ($a->toss_won_by == $a->team_a_id) ? $a->team_b_id : $a->team_a_id;
            $bowl = $a->toss_won_by;
        }
        $data["match_id"] = $match_id;
        $data["allbatsmen"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `is_playing` = 1 AND `team_id` = ' . $id);
        $data["allbowlers"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `is_playing` = 1 AND `team_id` = ' . $bowl);
        $data["startMatch"] = true;
        return view('admin.matches', $data);
    }

    public function getPlayers($match_id, DB $db)
    {
        $data = array();
        $data["innings"] = 1;
        $data["end_match"] = false;
        $a = $db::select('SELECT * FROM `matches` WHERE `id` = ' . $match_id)[0];
        if ($a->end_match == 1) {
            $data["end_match"] = true;
            $data["reason"] = $a->reason;
        }
        $data["no_of_overs"] = $a->no_of_overs;
        $data["wickets"] = $db::select('SELECT COUNT(`id`) as wickets FROM `balls` WHERE `innings` = ' . $data["innings"] . ' AND `is_delete` IS NULL AND `isvalid` IN (3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ')')[0]->wickets;
        $data["overs_bowled"] = $db::select('SELECT COUNT(`id`) as overs FROM `overs` WHERE `is_delete` IS NULL AND `innings` = 1 AND `match_id` = ' . $match_id)[0]->overs;
        $data["balls_bowled"] = $db::select('SELECT COUNT(`id`) as balls FROM `balls` WHERE `is_delete` IS NULL AND `isvalid` IN (1,3,4) AND `innings` = ' . $data["innings"] . ' AND `over_id` = (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC LIMIT 1)')[0]->balls;
        if ($data["balls_bowled"] < 6 && $data["balls_bowled"] > 0) {
            $data["overs_bowled"]--;
        } else if ($data["balls_bowled"] == 6) {
            $data["balls_bowled"] = 0;
        }
        if ($a->elected_to == 1) {
            $id = $a->toss_won_by;
            $bowl = ($a->toss_won_by == $a->team_a_id) ? $a->team_b_id : $a->team_a_id;
        } else {
            $id = ($a->toss_won_by == $a->team_a_id) ? $a->team_b_id : $a->team_a_id;
            $bowl = $a->toss_won_by;
        }
        $data["first_team_id"] = $id;
        $data["first_team_name"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $id)[0]->team_name;
        $data["second_team_id"] = $bowl;
        $data["second_team_name"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $bowl)[0]->team_name;
        if ($data["no_of_overs"] == $data["overs_bowled"] || $data["wickets"] == 10) {
            $data["innings"] = 2;
            $c = $id;
            $id = $bowl;
            $bowl = $c;
        }
        $data["match_id"] = $match_id;
        $data["batsmen"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `is_playing` = 1 AND `team_id` = ' . $id);
        $batsmen1 = array();
        foreach ($data["batsmen"] as $batsman) {
            $batsman->runs = 0;
            $batsman->balls_faced = 0;
            $batsman->fours = 0;
            $batsman->sixes = 0;
            $batsman->on_strike = false;
            $batsman->is_out = false;
            $batsmen1[] = $batsman;
        }
        $arr = $db::select('SELECT `team_name`, `logo` FROM `teams` WHERE `isdelete` IS NULL AND `id` = ' . $id)[0];
        $data["batting_team"] = $arr->team_name;
        $data["batting_team_logo"] = $arr->logo;
        $data["batsmen"] = $batsmen1;
        $data["bowlers"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `is_playing` = 1 AND `team_id` = ' . $bowl);
        $bowler1 = array();
        foreach ($data["bowlers"] as $bowler) {
            $bowler->runs = 0;
            $bowler->overs = 0;
            $bowler->maiden = 0;
            $bowler->eco_rate = 0;
            $bowler->current_ball = 0;
            $bowler1[] = $bowler;
        }
        $data["bowlers"] = $bowler1;
        $k = $db::select('SELECT * FROM `current_batsman_and_bowler` WHERE `innings` = ' . $data["innings"] . ' AND `is_delete` IS NULL AND `m_id` = ' . $match_id . ' ORDER BY `id` DESC LIMIT 1');
        if (count($k) > 0) {
            $k = $k[0];
            $bat = array();
            array_push($bat, $this->filterPlayer($data["batsmen"], $k->b1_id, $k->on_strike, $match_id, $data["innings"]));
            array_push($bat, $this->filterPlayer($data["batsmen"], $k->b2_id, $k->on_strike, $match_id, $data["innings"]));
            $data["currentbatsmen"] = $bat;
            $data["currentbowler"] = $this->filterPlayer($data["bowlers"], $k->bowler_id, false, $match_id, $data["innings"]);
        } else {
            $data["currentbatsmen"] = array();
            $data["currentbowler"] = array();
        }
        $data["currentover"] = $db::select('SELECT `isvalid` as is_legal, `runs_scored` as total_runs FROM `balls` WHERE `innings` = ' . $data["innings"] . ' AND  `is_delete` IS NULL AND `over_id` = (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC LIMIT 1)');
        $data["team_runs_disp"] = $db::select('SELECT SUM(`runs_scored`), IF(SUM(`runs_scored`) IS NULL,0,SUM(`runs_scored`)) as runs FROM `balls` WHERE `innings` = ' . $data["innings"] . ' AND  `is_delete` IS NULL AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
        $data["first_team_runs"] = $db::select('SELECT SUM(`runs_scored`), IF(SUM(`runs_scored`) IS NULL,0,SUM(`runs_scored`)) as runs FROM `balls` WHERE `innings` = 1 AND `is_delete` IS NULL AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
        $data["first_team_wickets"] = $db::select('SELECT COUNT(`id`) as wickets FROM `balls` WHERE `innings` = 1 AND `is_delete` IS NULL AND `isvalid` IN (3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ')')[0]->wickets;
        $data["first_innings_score"] = $data["first_team_runs"] . '/' . $data["first_team_wickets"];
        $data["over_id"] = $db::select('SELECT `id` FROM `overs` WHERE `match_id` =' . $match_id . ' ORDER BY `id` DESC LIMIT 1');
        if (count($data["over_id"]) > 0) {
            $data["over_id"] = $data["over_id"][0]->id;
        } else {
            $data["over_id"] = 0;
        }
        $data["wickets"] = $db::select('SELECT COUNT(`id`) as wickets FROM `balls` WHERE `innings` = ' . $data["innings"] . ' AND `is_delete` IS NULL AND `isvalid` IN (3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ')')[0]->wickets;
        $data["overs_bowled"] = $db::select('SELECT COUNT(`id`) as overs FROM `overs` WHERE `innings` = ' . $data["innings"] . ' AND `is_delete` IS NULL AND `match_id` = ' . $match_id)[0]->overs;
        $data["balls_bowled"] = $db::select('SELECT COUNT(`id`) as balls FROM `balls` WHERE `innings` = ' . $data["innings"] . ' AND  `is_delete` IS NULL AND `isvalid` IN (1,3,4) AND `innings` = ' . $data["innings"] . ' AND `over_id` = (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `is_delete` IS NULL ORDER BY `id` DESC LIMIT 1)')[0]->balls;
        if ($data["balls_bowled"] < 6) {
            if ($data["overs_bowled"] != 0)
                $data["overs_bowled"]--;
        } else if ($data["balls_bowled"] == 6) {
            $data["balls_bowled"] = 0;
            $data["newOver"] = true;
        }
        $data["overs_bowled_total"] = $data["overs_bowled"] . '.' . $data["balls_bowled"];
        $data["crr"] = $this->getEconomyRate($data["balls_bowled"], $data["overs_bowled"], $data["team_runs_disp"]);
        if ($data["overs_bowled"] == 0 && $data["balls_bowled"] == 0 && count($data["currentbatsmen"]) == 0)
            $data["startMatch"] = true;
        return json_encode($data);
    }

    public function getScorecard($match_id, DB $db)
    {
        $data = array();
        $data["innings"] = 1;
        $a = $db::select('SELECT * FROM `matches` WHERE `id` = ' . $match_id)[0];
        $data["no_of_overs"] = $a->no_of_overs;
        if ($a->elected_to == 1) {
            $id = $a->toss_won_by;
            $bowl = ($a->toss_won_by == $a->team_a_id) ? $a->team_b_id : $a->team_a_id;
        } else {
            $id = ($a->toss_won_by == $a->team_a_id) ? $a->team_b_id : $a->team_a_id;
            $bowl = $a->toss_won_by;
        }
        $data["first_innings_wickets"] = $db::select('SELECT COUNT(`id`) as wickets FROM `balls` WHERE `innings` = ' . $data["innings"] . ' AND `innings` = 1 AND `is_delete` IS NULL AND `isvalid` IN (3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ')')[0]->wickets;
        $data["first_innings_overs_bowled"] = $db::select('SELECT COUNT(`id`) as overs FROM `overs` WHERE `is_delete` IS NULL AND `innings` = 1 AND `match_id` = ' . $match_id)[0]->overs;
        $data["first_innings_balls_bowled"] = $db::select('SELECT COUNT(`id`) as balls FROM `balls` WHERE `is_delete` IS NULL AND `isvalid` IN (1,3,4) AND `innings` = ' . $data["innings"] . ' AND `over_id` = (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC LIMIT 1)')[0]->balls;
        if ($data["first_innings_balls_bowled"] > 0 && $data["first_innings_overs_bowled"] != 6) {
            $data["first_innings_overs_bowled"]--;
        } else if ($data["first_innings_overs_bowled"] == 6) {
            $data["first_innings_overs_bowled"] = 0;
        }
        $data["first_innings_all_batsmen"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `team_id` = ' . $id);
        $data["first_innings_batsmen"] = $db::select('SELECT * FROM `balls` WHERE `isvalid` = 3 AND over_id IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ') AND scored_by IN (SELECT `id` FROM `players` WHERE `team_id` = ' . $id . ') ORDER BY `id` ASC');
        $data["first_innings_batting_team"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $id)[0]->team_name;
        $data["first_innings_bowling_team"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $bowl)[0]->team_name;
        $data["match_id"] = $match_id;
        $batsmen1 = array();
        if (count($data["first_innings_batsmen"]) > 0) {
            foreach ($data["first_innings_batsmen"] as $batsman) {
                $batsman = $this->filterPlayerScorecard($data["first_innings_all_batsmen"], $batsman->scored_by, true, $batsman, $match_id, $data["innings"], $batsman->out_param);
                $batsmen1[] = $batsman;
            }
        }
        $batsman = $db::select('SELECT * FROM `current_batsman_and_bowler` WHERE `m_id` = ' . $match_id . ' AND `is_delete` IS NULL AND `innings` = 1 ORDER BY `id` DESC LIMIT 1')[0];
        $b2_id = $batsman->b2_id;
        $batsman->out_param = 0;
        $batsman = $this->filterPlayerScorecard($data["first_innings_all_batsmen"], $batsman->b1_id, true, $batsman, $match_id, $data["innings"], 0);
        $batsmen1[] = $batsman;
        $batsman->out_param = 0;
        $batsman = $this->filterPlayerScorecard($data["first_innings_all_batsmen"], $b2_id, true, $batsman, $match_id, $data["innings"], 0);
        $batsmen1[] = $batsman;
        $data["first_innings_batsmen"] = $batsmen1;
        $data["first_innings_all_bowlers"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `team_id` = ' . $bowl);
        $data["first_innings_bowlers"] = $db::select('SELECT DISTINCT b.bowler_id FROM `balls` as a, `overs` as b WHERE a.over_id=b.id AND b.is_delete IS NULL AND b.match_id = ' . $match_id . ' AND b.innings = 1');
        $bowler1 = array();
        foreach ($data["first_innings_bowlers"] as $bowler) {
            $bowler = $this->filterPlayerScorecard($data["first_innings_all_bowlers"], $bowler->bowler_id, false, $bowler, $match_id, $data["innings"], 0);
            $bowler1[] = $bowler;
        }
        $data["first_innings_bowlers"] = $bowler1;
        $data["first_team_runs"] = $db::select('SELECT SUM(`runs_scored`), IF(SUM(`runs_scored`) IS NULL,0,SUM(`runs_scored`)) as runs FROM `balls` WHERE `innings` = 1 AND `is_delete` IS NULL AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
        $data["innings"] = 2;
        $c = $id;
        $id = $bowl;
        $bowl = $c;
        $data["second_innings_wickets"] = $db::select('SELECT COUNT(`id`) as wickets FROM `balls` WHERE `innings` = ' . $data["innings"] . ' AND `is_delete` IS NULL AND `isvalid` IN (3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ')')[0]->wickets;
        $data["second_innings_overs_bowled"] = $db::select('SELECT COUNT(`id`) as overs FROM `overs` WHERE `is_delete` IS NULL AND `innings` = 2 AND `match_id` = ' . $match_id)[0]->overs;
        $data["second_innings_balls_bowled"] = $db::select('SELECT COUNT(`id`) as balls FROM `balls` WHERE `is_delete` IS NULL AND `isvalid` IN (1,3,4) AND `innings` = ' . $data["innings"] . ' AND `over_id` = (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC LIMIT 1)')[0]->balls;
        if ($data["second_innings_balls_bowled"] > 0 && $data["second_innings_balls_bowled"] != 6) {
            $data["second_innings_overs_bowled"]--;
        } else if ($data["second_innings_balls_bowled"] == 6) {
            $data["second_innings_balls_bowled"] = 0;
        }
        $data["second_innings_all_batsmen"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `team_id` = ' . $id);
        $data["second_innings_batsmen"] = $db::select('SELECT * FROM `balls` WHERE `isvalid` = 3 AND over_id IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ') AND scored_by IN (SELECT `id` FROM `players` WHERE `team_id` = ' . $id . ') ORDER BY `id` ASC');
        $data["second_innings_batting_team"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $id)[0]->team_name;
        $data["second_innings_bowling_team"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $bowl)[0]->team_name;
        $batsmen1 = array();
        foreach ($data["second_innings_batsmen"] as $batsman) {
            $batsman = $this->filterPlayerScorecard($data["second_innings_all_batsmen"], $batsman->scored_by, true, $batsman, $match_id, $data["innings"], $batsman->out_param);
            $batsmen1[] = $batsman;
        }
        $batsman = $db::select('SELECT * FROM `current_batsman_and_bowler` WHERE `m_id` = ' . $match_id . ' AND `is_delete` IS NULL AND  `innings` = 2 ORDER BY `id` DESC LIMIT 1')[0];
        $b2_id = $batsman->b2_id;
        $batsman = $this->filterPlayerScorecard($data["second_innings_all_batsmen"], $batsman->b1_id, true, $batsman, $match_id, $data["innings"], 0);
        $batsmen1[] = $batsman;
        $batsman = $this->filterPlayerScorecard($data["second_innings_all_batsmen"], $b2_id, true, $batsman, $match_id, $data["innings"], 0);
        $batsmen1[] = $batsman;
        $data["second_innings_batsmen"] = $batsmen1;
        $data["second_innings_all_bowlers"] = $db::select('SELECT a.id as pid, player_name FROM `players` a WHERE a.team_id AND `team_id` = ' . $bowl);
        $data["second_innings_bowlers"] = $db::select('SELECT DISTINCT b.bowler_id FROM `balls` as a, `overs` as b WHERE a.over_id=b.id AND b.is_delete IS NULL AND b.match_id = ' . $match_id . ' AND b.innings = 2');
        $bowler1 = array();
        foreach ($data["second_innings_bowlers"] as $bowler) {
            $bowler = $this->filterPlayerScorecard($data["second_innings_all_bowlers"], $bowler->bowler_id, false, $bowler, $match_id, $data["innings"], 0);
            $bowler1[] = $bowler;
        }
        $data["second_innings_bowlers"] = $bowler1;
        $data["second_team_runs"] = $db::select('SELECT SUM(`runs_scored`), IF(SUM(`runs_scored`) IS NULL,0,SUM(`runs_scored`)) as runs FROM `balls` WHERE `innings` = 2 AND `is_delete` IS NULL AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
        $data["won_by"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $a->won_by)[0]->team_name;
        $data["toss_won_by"] = $db::select('SELECT `team_name` FROM `teams` WHERE `id` = ' . $a->toss_won_by)[0]->team_name;
        $data["elected_to"] = ($a->elected_to == 1) ? 'Bat' : 'Bowl';
        return json_encode($data);
    }

    public function filterPlayer($arr, $id, $on_strike, $match_id, $innings)
    {
        foreach ($arr as $ev) {
            if ($id == $ev->pid) {
                if (!$on_strike) {
                    $ev->runs = DB::select('SELECT SUM(`runs_scored`) as runs, IF(SUM(runs_scored) IS NULL,0,SUM(runs_scored)) as total_runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->total_runs;
                    $ev->overs = DB::select('SELECT COUNT(`id`) as overs FROM `overs` WHERE `innings` = ' . $innings . ' AND `match_id` = ' . $match_id . ' AND `is_delete` IS NULL AND `bowler_id` = ' . $ev->pid)[0]->overs;
                    $ev->wickets = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND `is_delete` IS NULL AND `isvalid` IN (3) AND `out_param` <> 4 AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->runs;
                    $ev->balls = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND `is_delete` IS NULL AND `isvalid` IN (1,3,4) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->runs;
                    if ($ev->balls >= 6) {
                        $ev->balls = $ev->balls % 6;
                    } else {
                        $ev->overs--;
                    }
                    $ev->current_ball = $ev->overs . '.' . $ev->balls;
                    return $ev;
                }
                $ev->runs = DB::select('SELECT SUM(`runs_scored`) as runs, IF(SUM(runs_scored) IS NULL,0,SUM(runs_scored)) as total_runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `isvalid` IN (1) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->total_runs;
                $ev->no_balls = DB::select('SELECT * FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `isvalid` IN (5,6) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)');
                $ev->balls_faced = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `isvalid` IN (1,3,4) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
                foreach ($ev->no_balls as $no_ball) {
                    if ($no_ball->runs_scored > 1) {
                        $ev->balls_faced++;
                        if ($no_ball->isvalid != 6)
                            $ev->runs = $ev->runs + ($no_ball->runs_scored - 1);
                    }
                }
                $ev->fours = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `runs_scored` IN (4,5) AND `isvalid` IN (1,3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
                $ev->sixes = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `runs_scored` IN (6,7) AND `isvalid` IN (1,3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
                if ($on_strike == $id) {
                    $ev->on_strike = true;
                }
                return $ev;
            }
        }
    }

    public function filterPlayerScorecard($arr, $id, $on_strike, $batsman, $match_id, $innings, $out_params)
    {
        foreach ($arr as $ev) {
            if ($id == $ev->pid) {
                if (!$on_strike) {
                    $ev->runs = DB::select('SELECT SUM(`runs_scored`) as runs, IF(SUM(runs_scored) IS NULL,0,SUM(runs_scored)) as total_runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->total_runs;
                    $ev->overs = DB::select('SELECT COUNT(`id`) as overs FROM `overs` WHERE `innings` = ' . $innings . ' AND `match_id` = ' . $match_id . ' AND `is_delete` IS NULL AND `bowler_id` = ' . $ev->pid)[0]->overs;
                    $ev->balls = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND `is_delete` IS NULL AND `isvalid` IN (1,3,4) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->runs;
                    $ev->wickets = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND `is_delete` IS NULL AND `isvalid` IN (3) AND `out_param` <> 4 AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->runs;
                    $ev->no_ball = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND `is_delete` IS NULL AND `isvalid` IN (5,6) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->runs;
                    $ev->wide_ball = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND `is_delete` IS NULL AND `isvalid` IN (2) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `bowler_id` = ' . $ev->pid . ')')[0]->runs;
                    if ($ev->balls >= 6) {
                        $ev->balls = $ev->balls % 6;
                    } else {
                        $ev->overs--;
                    }
                    $ev->current_ball = $ev->overs . '.' . $ev->balls;
                    $ev->economy_rate = $this->getEconomyRate($ev->balls, $ev->overs, $ev->runs);
                    return $ev;
                }
                $ev->runs = DB::select('SELECT SUM(`runs_scored`) as runs, IF(SUM(runs_scored) IS NULL,0,SUM(runs_scored)) as total_runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `isvalid` IN (1) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->total_runs;
                $ev->no_balls = DB::select('SELECT * FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `isvalid` IN (5,6) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)');
                $ev->balls_faced = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `isvalid` IN (1,3,4) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
                foreach ($ev->no_balls as $no_ball) {
                    if ($no_ball->runs_scored > 1) {
                        $ev->balls_faced++;
                        if ($no_ball->isvalid != 6)
                            $ev->runs = $ev->runs + ($no_ball->runs_scored - 1);
                    }
                }
                $ev->fours = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `runs_scored` IN (4,5) AND `isvalid` IN (1,3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
                $ev->sixes = DB::select('SELECT COUNT(`id`) as runs FROM `balls` WHERE `innings` = ' . $innings . ' AND  `is_delete` IS NULL AND `scored_by` = ' . $ev->pid . ' AND `runs_scored` IN (6,7) AND `isvalid` IN (1,3) AND `over_id` IN (SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' ORDER BY `id` DESC)')[0]->runs;
                $ev->strike_rate = round(($ev->runs / $ev->balls_faced) * 100, 2);
                $ev->fielder_1 = '';
                $ev->fielder_2 = '';
                switch ($out_params) {
                    case 0:
                        $ev->out_param = 'batting';
                        break;
                    case 1:
                        $ev->out_param = 'Bowled';
                        break;
                    case 2:
                        $ev->out_param = 'Catch Out';
                        $ev->fielder_1 = DB::select('SELECT `player_name` FROM `players` WHERE `id` =' . $batsman->fielder_1)[0]->player_name;
                        break;
                    case 3:
                        $ev->out_param = 'Lbw';
                        break;
                    case 4:
                        $ev->out_param = 'Run Out';
                        $ev->fielder_1 = DB::select('SELECT `player_name` FROM `players` WHERE `id` =' . $batsman->fielder_1)[0]->player_name;
                        $ev->fielder_2 = DB::select('SELECT `player_name` FROM `players` WHERE `id` =' . $batsman->fielder_2)[0]->player_name;
                        break;
                    case 5:
                        $ev->out_param = 'Stumped';
                        $ev->fielder_1 = DB::select('SELECT `player_name` FROM `` WHERE `id` =' . $batsman->fielder_1)[0]->player_name;
                        break;
                    case 6:
                        $ev->out_param = 'Hit Wicket';
                        break;
                }
                return $ev;
            }
        }
    }

    public function insertOver($match_id, DB $db, Request $request)
    {
        $data = array();
        $data["over_id"] = null;
        $currentbatsmen = $request->input('currentbatsmen');
        $currentbowler = $request->input('currentbowler');
        $innings = $request->input('innings');
        $b1 = $currentbatsmen[0]["pid"];
        $b2 = $currentbatsmen[1]["pid"];
        if ($currentbatsmen[0]["on_strike"] == 'true') {
            $onstrike = $b1;
        }
        if ($currentbatsmen[1]["on_strike"] == 'true') {
            $onstrike = $b2;
        }
        $b3 = $currentbowler["pid"];
        if ($request->input('only_current') == 'false') {
            $db::insert('INSERT INTO `overs` (`match_id`, `bowler_id`, `innings`) VALUES (?,?,?)', [$match_id, $currentbowler["pid"], $innings]);
            $data["over_id"] = $db::getPdo()->lastInsertId();
        }
        $data["over_id"] = $db::select('SELECT `id` FROM `overs` WHERE `is_delete` IS NULL AND `match_id` = ' . $match_id . ' AND `innings` = ' . $innings . ' ORDER BY `id` DESC LIMIT 1')[0]->id;
        $db::insert('INSERT INTO `current_batsman_and_bowler` (`b1_id`, `b2_id`, `on_strike`, `bowler_id`, `m_id`, `innings`) VALUES (?,?,?,?,?,?)', [$b1, $b2, $onstrike, $b3, $match_id, $innings]);
        $this->s3BucketInsert($match_id, $db);
        return json_encode($data);
    }

    public function insertBall($match_id, DB $db, Request $request)
    {
        $is_valid = $request->input('is_legal');
        $total_runs = $request->input('total_runs');
        $over_id = $request->input('over_id');
        $scored_by = $request->input('scored_by');
        $innings = $request->input('innings');
        $out_params = null;
        $fielder_1 = null;
        $fielder_2 = null;
        if ($is_valid == 3) {
            $out_params = $request->input('out_params');
            $fielder_1 = $request->input('fielder_1');
            $fielder_2 = $request->input('fielder_1');
            if ($fielder_1 == '') {
                $fielder_1 = null;
            }
            if ($fielder_2 == '') {
                $fielder_2 = null;
            }
        }
        $db::insert('INSERT INTO `balls` (`isvalid`, `out_param`, `fielder_1`, `fielder_2`, `runs_scored`, `scored_by`, `over_id`, `innings`) VALUES (?,?,?,?,?,?,?,?)', [$is_valid, $out_params, $fielder_1, $fielder_2, $total_runs, $scored_by, $over_id, $innings]);
        if (count($db::select('SELECT * FROM `current_batsman_and_bowler` WHERE `innings` = ' . $innings . ' AND `is_delete` IS NULL AND `m_id` = ' . $match_id)) > 0) {
            $currentbatsmen = $request->input('currentbatsmen');
            $currentbowler = $request->input('currentbowler');
            $b1 = $currentbatsmen[0]["pid"];
            $b2 = $currentbatsmen[1]["pid"];
            if ($currentbatsmen[0]["on_strike"] == 'true') {
                $onstrike = $b1;
            }
            if ($currentbatsmen[1]["on_strike"] == 'true') {
                $onstrike = $b2;
            }
            $b3 = $currentbowler["pid"];
            $db::insert('INSERT INTO `current_batsman_and_bowler` (`b1_id`, `b2_id`, `on_strike`, `bowler_id`, `m_id`, `innings`) VALUES (?,?,?,?,?,?)', [$b1, $b2, $onstrike, $b3, $match_id, $innings]);
            $this->s3BucketInsert($match_id, $db);
        }
    }

    public function removeBall($match_id, DB $db, Request $request)
    {
        $over_id = $request->input('over_id');
        $id_arr = $db::select('SELECT `id` FROM `balls` WHERE `over_id` = ' . $over_id . ' AND `is_delete` IS NULL ORDER BY `id` DESC');
        if (count($id_arr) > 0) {
            $db::update('UPDATE `balls` SET `is_delete` = ? WHERE `id` = ?', [1, $id_arr[0]->id]);
        } else {
            $db::update('UPDATE `overs` SET `is_delete` = ? WHERE `id` = ?', [1, $over_id]);
        }
        $id_arr = $db::select('SELECT `id` FROM `current_batsman_and_bowler` WHERE `m_id` = ' . $match_id . ' AND `is_delete` IS NULL ORDER BY `id` DESC');
        $db::update('UPDATE `current_batsman_and_bowler` SET `is_delete` = ? WHERE `id` = ?', [1, $id_arr[0]->id]);
    }

    public function s3BucketInsert($match_id, DB $db)
    {
        File::put(storage_path('teamgsb.json'), $this->getPlayers($match_id, $db));
        Storage::disk('s3')->put('teamgsb/teamgsb.json', File::get(storage_path('teamgsb.json')));
    }

    public function addOver($match_id, DB $db)
    {
        $db::insert('INSERT INTO `overs` (`match_id`) VALUES (?)', [$match_id]);
        return $db::getPdo()->lastInsertId();
    }

    public function addBall(DB $db, Request $request)
    {
        $is_valid = $request->input('is_valid');
        $runs_scored = $request->input('runs_scored');
        $scored_by = $request->input('scored_by');
        $over_id = $request->input('over_id');
        $db::insert('INSERT INTO `balls` (`isvalid`, `runs_scored`, `scored_by`, `over_id`) VALUES (?,?,?,?)', [$is_valid, $runs_scored, $scored_by, $over_id]);
        $data = $db::select('SELECT COUNT(`id`) as no_of_balls FROM `balls` WHERE `over_id` = ' . $over_id . ' AND `isvalid` IN (1,5)')[0];
        return json_encode($data->no_of_balls);
    }

    public function matchFinish(DB $db, Request $request, $match_id)
    {
        $db::update('UPDATE `matches` SET `won_by` = ? WHERE `id` = ?', [$request->input('winning_team_id'), $match_id]);
        $this->s3BucketInsert($match_id, $db);
    }

    public function endMatch(DB $db, Request $request, $match_id)
    {
        $db::update('UPDATE `matches` SET `end_match` = ?, `reason` = ? WHERE `id` = ?', [1, $request->input('reason'), $match_id]);
        $this->s3BucketInsert($match_id, $db);
    }

    public function getEconomyRate($balls, $over, $runs)
    {
        if ($over == 0 && $balls == 0) {
            return 0;
        }
        switch ($balls) {
            case 1:
                $over = $over + 0.167;
                break;
            case 2:
                $over = $over + 0.333;
                break;
            case 3:
                $over = $over + 0.5;
                break;
            case 4:
                $over = $over + 0.67;
                break;
            case 5:
                $over = $over + 0.833;
                break;
        }
        return round($runs / $over, 2);
    }
}
