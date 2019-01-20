<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayersController extends Controller
{
    //
    public function __construct()
    {
    }

    public function index(DB $db)
    {
        $data = array();
        $data["players"] = $db::select('SELECT * FROM `teams` a, `players` b WHERE a.id = b.team_id AND b.is_delete IS NULL ');
        return view('admin.players', $data);
    }

    public function getAllTeams(DB $db)
    {
        $data = array();
        $data["teams"] = $db::select('SELECT * FROM `teams`');
        return json_encode($data);
    }

    public function newPlayer(Request $request, DB $db)
    {
        $data = array();
        $data["teams"] = $db::select('SELECT * FROM `teams` WHERE `isdelete` IS NULL');
        if ($request->isMethod('post')) {
            $data['name'] = $request->input('name');
            $data['team_id'] = $request->input('team_id');
            $data['player_param'] = $request->input('player_param');
            $this->validate(
                $request,
                [
                    'name' => 'required',
                    'team_id' => 'required'
                ]
            );
            $db::insert('INSERT INTO `players` (`player_name`, `team_id`, `player_param`) VALUES (?, ?, ?)', [$data['name'], $data['team_id'], $data["player_param"]]);
            return redirect()->route('players');
        }
        $data["newPlayer"] = true;
        return view('admin.players', $data);
    }

    public function deletePlayer($player_id, DB $db)
    {
        $db::update('UPDATE `players` SET `is_delete` = 1 WHERE `id` =' . $player_id);
        return redirect()->route('players');
    }

    public function editPlayer($player_id, Request $request, DB $db)
    {
        $data = array();
        $data["teams"] = $db::select('SELECT * FROM `teams` WHERE `isdelete` IS NULL');
        $data["player"] = $db::select('SELECT * FROM `teams` a, `players` b WHERE a.id = b.team_id AND b.id = ' . $player_id)[0];
        if ($request->isMethod('post')) {
            $data['name'] = $request->input('name');
            $data['team_id'] = $request->input('team_id');
            $data['player_param'] = $request->input('player_param');
            $data['enable_voting'] = $request->input('enable_voting');
            $this->validate(
                $request,
                [
                    'name' => 'required | min:5',
                    'team_id' => 'required'
                ]
            );
            $db::update('UPDATE `players` SET `player_name` = ? , `team_id` = ? , `player_param` = ?, `enable_voting` = ? WHERE `id` = ? ', [$data['name'], $data["team_id"], $data["player_param"], $data["enable_voting"], $player_id,]);
            return redirect('players');
        }
        $data["newPlayer"] = true;
        return view('admin.players', $data);
    }

    public function playing11()
    {
        return view('admin.playing11');
    }
}
