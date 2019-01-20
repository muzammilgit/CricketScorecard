<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Teams extends Controller
{
    //
    public function __construct()
    {

    }

    public function index(DB $db)
    {
        $data = array();
        $data['msg'] = '';
        $data["teams"] = $db::select('SELECT * FROM `teams` WHERE `isdelete` IS NULL');
        if (count($data['teams']) == 0) {
            $data['msg'] = 'No Data Available';
        }
        return view('admin.teams', $data);
    }

    public function newTeam(Request $request, DB $db)
    {
        $data = array();
        $data['name'] = $request->input('name');
        $data['email'] = $request->input('email');
        $data['details'] = $request->input('details');
        if ($request->isMethod('post')) {
            $this->validate(
                $request,
                [
                    'name' => 'required | min:5'
                ]
            );
            $db::insert('INSERT INTO `teams` (`team_name`) VALUES (?)', [$data['name']]);
            return redirect('teams');
        }
        $data["newTeam"] = true;
        return view('admin.teams', $data);
    }

    public function editTeam($team_id, Request $request, DB $db)
    {
        $data = array();
        $data["team"] = $db::select('SELECT * FROM `teams` WHERE `id` = ' . $team_id)[0];
        if ($request->isMethod('post')) {
            $data['name'] = $request->input('name');
            $this->validate(
                $request,
                [
                    'name' => 'required | min:5'
                ]
            );
            $db::update('UPDATE `teams` SET `team_name` = ? WHERE `id` = ?', [$data['name'], $team_id]);
            return redirect('teams');
        }
        $data["newTeam"] = true;
        return view('admin.teams', $data);
    }

    public function deleteTeam($team_id,DB $db)
    {
        $db::update('UPDATE `teams` SET `isdelete` = 1 WHERE `id` =' . $team_id);
        return redirect('teams');
    }

}
