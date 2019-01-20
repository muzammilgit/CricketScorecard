/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});

let teams;
let players1;
let players2;
let json_v = {
    allbatsmen: [],
    allbowlers: [],
    currentbatsmen: [],
    currentbowler: '',
    currentover: [],
    team_runs_disp: 0,
    wickets: 0,
    overs_bowled: 0,
    balls_bowled: 0,
    overs_bowled_total: '0.0',
    match_id: 0,
    over_id: 0,
    no_of_overs: 0,
    innings: 1,
    first_team_runs: 0,
    first_team_id: 0,
    first_team_name: '',
    second_team_id: 0,
    second_team_name: ''
};

let totalballs = 6;
let out_params = [
    {
        id: 1,
        name: 'Bowled'
    },
    {
        id: 2,
        name: 'Catch Out'
    },
    {
        id: 3,
        name: 'Lbw'
    },
    {
        id: 4,
        name: 'Run Out'
    },
    {
        id: 5,
        name: 'Stumped'
    },
    {
        id: 6,
        name: 'Hit Wicket'
    },
    {
        id: 7,
        name: 'Handling the ball'
    }
];
const csrf_token = $('meta[name="csrf-token"]').attr('content');
let fielder_1 = '';
let fielder_2 = '';

function getOtherTeams() {
    const data = {
        team_id: $('#team_id_a').val(),
        _token: csrf_token
    };
    $.post('/matches/getOtherTeams', data).then(val => {
        const k = '<option selected disabled>Select Team</option>';
        val = k + val;
        $('#team_id_b').html(val);
    });

    const data1 = {
        _token: csrf_token
    };

    $.post('/matches/getOtherTeams', data1).then(val => {
        teams = JSON.parse(val);
    });
}

function getName(ta) {
    for (let ev of teams) {
        if (parseInt(ev['id']) === parseInt(ta)) {
            return ev["team_name"];
        }
    }
}

function getTossMembers() {
    const ta = $('#team_id_a').val();
    const tb = $('#team_id_b').val();
    $('#toss').html('Toss Won By <br><label>' +
        '<input type="radio" name="toss_won_by" value="' + ta + '">' + getName(ta) +
        '</label>' +
        '<label style="margin-left: 15px">' +
        '<input type="radio" name="toss_won_by" value="' + tb + '">' + getName(tb) +
        '</label>');

    const data1 = {
        _token: csrf_token,
        team_a: ta,
        team_b: tb
    };

    $.post('/matches/getBothTeamPlayers', data1).then(val => {
        $('#playing11_1').removeClass('hidden');
        $('#playing11_2').removeClass('hidden');
        const my_multi_select1 = $('#my_multi_select1');
        const my_multi_select2 = $('#my_multi_select2');
        my_multi_select1.multiSelect('destroy');
        my_multi_select2.multiSelect('destroy');
        val = JSON.parse(val);
        players1 = val["players1"];
        players2 = val["players2"];
        let html1 = '<optgroup label="Select Players">';
        for (let ev of players1) {
            html1 += '<option value="' + ev["id"] + '">' + ev["player_name"] + '</option>';
        }
        html1 += '</optgroup>';
        my_multi_select1.html(html1);
        html1 = '';
        for (let ev of players2) {
            html1 += '<option value="' + ev["id"] + '">' + ev["player_name"] + '</option>';
        }
        my_multi_select2.html(html1);
        getDropdown('my_multi_select1');
        getDropdown('my_multi_select2');
    });

}

function getOtherBatsman(batsmen, bowlers) {
    const b1 = $('#batsmen1');
    b1.attr('disabled', '');
    localStorage.setItem('batsmen', JSON.stringify(batsmen));
    localStorage.setItem('bowlers', JSON.stringify(bowlers));
    batsmen = batsmen.filter(v => v['pid'] !== parseInt(b1.val()));
    const k = '<option selected disabled>Select Batsman</option>';
    let v = k;
    for (let ev of batsmen) {
        v += '<option value="' + ev.pid + '">' + ev.player_name + '</option>';
    }
    $('#batsmen2').html(v);
}

function disableMe() {
    const batsmen = localStorage.getItem('batsmen');
    let b;
    let k = JSON.parse(batsmen);
    b = k.filter(v => parseInt(v.pid) === parseInt($('#batsmen1').val()));
    k = JSON.parse(batsmen);
    b.push(k.filter(v => parseInt(v.pid) === parseInt($('#batsmen2').val()))[0]);
    localStorage.setItem('current_batsmen', JSON.stringify(b));
}

function initializeMatch() {
    $('#myModalfirstinnings').modal('hide');
    const b1 = $('#batsmen1');
    const b2 = $('#batsmen2');
    const b3 = $('#bowler1');
    let l = json_v.allbatsmen;
    json_v.currentbatsmen.push(l.filter(v => v['pid'] === parseInt(b1.val()))[0]);
    l = json_v.allbatsmen;
    json_v.currentbatsmen.push(l.filter(v => v['pid'] === parseInt(b2.val()))[0]);
    l = json_v.allbowlers;
    json_v.currentbowler = {...l.filter(v => v['pid'] === parseInt(b3.val()))[0]};
    databaseInsert(false);
    $('#table-score').html(getInnerHtml());
    return false;
}

function databaseInsert(flag) {
    // initialization Insert
    let data = {
        _token: csrf_token,
        insertOver: 'yes',
        match_id: json_v.match_id,
        currentbatsmen: json_v.currentbatsmen,
        currentbowler: json_v.currentbowler,
        over: json_v.overs_bowled,
        only_current: flag,
        innings: json_v.innings

    };
    $.post('/match/' + json_v.match_id + '/insertOver', data).then(data => {
        data = JSON.parse(data);
        json_v.over_id = data["over_id"];
    })
}

function getInnerHtml() {
    getRecentBalls();
    $('#score_card_high').removeClass('hidden');
    $('#team_runs_disp').html(json_v.team_runs_disp);
    $('#overs_bowled').html(json_v.overs_bowled_total);
    $('#wickets').html(json_v.wickets);

    let innerhtml = '<table id="score-table" class="table table-bordered">' +
        '<thead>' +
        '<tr>' +
        '<th>Batsman Name</th>' +
        '<th>Runs Scored</th>' +
        '<th>Balls Faced</th>' +
        '<th>Fours</th>' +
        '<th>Sixes</th>' +
        '</tr>' +
        '</thead><tbody>';
    for (let ev of json_v.currentbatsmen) {
        innerhtml += '<tr>';
        if (ev["on_strike"]) {
            innerhtml += '<td>' + ev["player_name"] + ' <span style="color: red;">*</span>' + ' </td>';
        } else {
            innerhtml += '<td>' + ev["player_name"] + ' </td>';
        }

        innerhtml += '<td>' + ev["runs"] + '</td>' +
            '<td>' + ev["balls_faced"] + '</td>' +
            '<td>' + ev["fours"] + '</td>' +
            '<td>' + ev["sixes"] + '</td>' +
            '</tr>';
    }
    innerhtml += '<tr>' +
        '<td class="bold">Bowler Name</td>' +
        '<td class="bold">Overs</td>' +
        '<td class="bold">Maiden</td>' +
        '<td class="bold">Runs</td>' +
        '<td class="bold">Economy Rate</td>' +
        '</tr>' +
        '<tr>' +
        '<td>' + json_v.currentbowler["player_name"] + '</td>' +
        '<td id="current_ball">' + json_v.currentbowler["current_ball"] + '</td>' +
        '<td>' + json_v.currentbowler["maiden"] + '</td>' +
        '<td id="bowler_runs">' + json_v.currentbowler["runs"] + '</td>' +
        '<td id="economy_rate">' + json_v.currentbowler["eco_rate"] + '</td>' +
        '</tr>' +
        '</tbody>' +
        '</table>';
    $('#score-buttons').removeClass('hidden');
    if (parseInt(json_v.innings) === 2) {
        $('#innings').html('2nd');
        $('#first_innings_score').html(json_v.first_innings_score);
        $('#first_team_name').html('1st Innings: ' + json_v.first_team_name);
        if (matchOver()) {
            $('#score-buttons').addClass('hidden');
            $('#match_finish').removeClass('hidden');
            submitWinnerToServer(parseInt(json_v.overs_bowled), parseInt(json_v.no_of_overs), parseInt(json_v.wickets), parseInt(json_v.first_team_runs), parseInt(json_v.team_runs_disp));
        }
    } else {
        $('#innings').html('1st');
    }
    return innerhtml;
}

function matchOver() {
    return ((parseInt(json_v.innings) === 2) && parseInt(json_v.overs_bowled) === parseInt(json_v.no_of_overs)) || (parseInt(json_v.wickets) === 10) || (parseInt(json_v.first_team_runs) < parseInt(json_v.team_runs_disp));
}

function submitWinnerToServer(overs_bowled, total_overs, wickets, first_team_runs, second_team_runs) {
    let winning_team = '';
    let winning_team_id = '';
    if (overs_bowled === total_overs) {
        if (first_team_runs === second_team_runs) {
            // match drawn
            winning_team = 'Match Drawn';
            winning_team_id = 0;

        } else if (first_team_runs > second_team_runs) {
            // winner first team
            winning_team = json_v.first_team_name;
            winning_team_id = json_v.first_team_id;

        } else {
            // winner second team
            winning_team = json_v.second_team_name;
            winning_team_id = json_v.second_team_id;
        }
    } else if (wickets === 10) {
        // winner team 1
        winning_team = json_v.first_team_name;
        winning_team_id = json_v.first_team_id;
    } else if (second_team_runs > first_team_runs) {
        // winner team 2
        winning_team = json_v.second_team_name;
        winning_team_id = json_v.second_team_id;
    }
    $.post('/match/' + json_v.match_id + '/matchFinish', {
        _token: csrf_token,
        winning_team_id: winning_team_id
    }).then((data) => {

    });
    $('#winning_team').html(winning_team);
}

function calculateRuns(runs, extras_flag, extras_runs) {
    const insertVal = $('#runs_per_ball');
    if (!extras_flag) {
        insertVal.val(runs)
    } else {
        let condition = insertVal.val();
        if (condition === "Nb" && runs === "Lb") {
            insertVal.val(condition + ' + ' + runs);
            $('#is_legal').val(6);
            $('#total_runs').val(2);
        } else if (parseInt(extras_runs) === 0) {
            insertVal.val(runs);
        } else {
            insertVal.val(runs + ' + ' + parseInt(extras_runs));
        }
    }
}

function removeDisabledButtons() {
    for (let i = 1; i < 7; i++) {
        $('#runsplus' + i).removeAttr('disabled');
    }
}

function disableButtons() {
    for (let i = 1; i < 7; i++) {
        $('#runsplus' + i).attr('disabled', '');
    }
}

function clearAll() {
    $('#total_runs').val(0);
    $('#runs_per_ball').val('');
}

function submitBall() {
    const run = $('#runs_per_ball');
    if (run.val() === '') {
        return;
    }
    disableButtons();
    const is_legal = $('#is_legal');
    const curr_ball = {
        is_legal: is_legal.val(),
        total_runs: $('#total_runs').val()
    };
    if (parseInt(curr_ball.is_legal) === 1) {
        curr_ball.total_runs = run.val();
    }
    json_v.currentover.push(curr_ball);
    if (parseInt(is_legal.val()) > 1) {
        $('#recent_balls').append(' <button class="btn btn-min btn-outline-dark" id="recent' + (++totalballs) + '"></button>');
    }
    $('#recent' + (json_v.currentover.length)).html(run.val());
    let k = json_v.currentover;
    k = k.filter(v => parseInt(v["is_legal"]) === 1 || parseInt(v["is_legal"]) === 3 || parseInt(v["is_legal"]) === 4).length;
    updateBowler(k, curr_ball.total_runs);
    updateBatsmen(k, curr_ball.total_runs);
    if (parseInt(curr_ball.is_legal) === 3) {
        getNextBatsman();
    }
    json_v.team_runs_disp = parseInt(json_v.team_runs_disp) + parseInt(curr_ball.total_runs);
    if (k === 6) {
        // Start new over
        // open a modal
        if ((parseInt(is_legal.val()) !== 3)) {
            setTimeout(() => {
                window.location.reload(true);
            }, 500)
        }
        ++(json_v.overs_bowled);
        json_v.balls_bowled = 0;
        json_v.overs_bowled_total = json_v.overs_bowled + '.' + json_v.balls_bowled;
    } else {
        json_v.overs_bowled_total = json_v.overs_bowled + '.' + k;
    }
    $('#table-score').html(getInnerHtml());
    clearAll();
}

function undoButton() {
    removeFromServer();
}

function removeFromServer() {
    $('#undoball').attr('disabled', '');
    let data = {
        _token: csrf_token,
        over_id: json_v.over_id
    };
    $.post('/match/' + json_v.match_id + '/removeBall', data).then((data) => {
        window.location.reload(true);
    });
}

function insertBallServer(is_legal, total_runs, scored_by) {
    if (parseInt(json_v.overs_bowled) === parseInt(json_v.no_of_overs) || parseInt(json_v.wickets) === 10) {
        // start new Innings

        json_v.innings = 2;
    }
    let data = {
        _token: csrf_token,
        currentbatsmen: json_v.currentbatsmen,
        currentbowler: json_v.currentbowler,
        is_legal: is_legal,
        total_runs: total_runs,
        over_id: json_v.over_id,
        scored_by: scored_by,
        out_params: $('#out_params').val(),
        fielder_1: fielder_1,
        fielder_2: fielder_2,
        innings: json_v.innings
    };
    $.post('/match/' + json_v.match_id + '/insertBall', data).then(data => {
        let k = json_v.currentover;
        k = k.filter(v => parseInt(v["is_legal"]) === 1 || parseInt(v["is_legal"]) === 3 || parseInt(v["is_legal"]) === 4).length;
        if (k === 6) {
            // Start new over
            // open a modal
            if ((parseInt(is_legal.val()) !== 3)) {
                setTimeout(() => {
                    window.location.reload(true);
                });
            }
        }
        if (parseInt(json_v.overs_bowled) === parseInt(json_v.no_of_overs) || parseInt(json_v.wickets) === 10) {
            // start new Innings
            window.location.reload(true);
        }
    })
}

function getNextBatsman() {
    const who_is_out = $('#who_is_out');
    json_v.allbatsmen.map(v => {
        if (parseInt(v["pid"]) === parseInt(who_is_out.val()))
            return v["is_out"] = true
    });
    let batsmen = json_v.allbatsmen.filter(v => !v["is_out"]);
    json_v.currentbatsmen = json_v.currentbatsmen.filter(v => v["pid"] !== parseInt(who_is_out.val()));
    batsmen = batsmen.filter(v => parseInt(v["pid"]) !== parseInt(json_v.currentbatsmen[0]["pid"]));
    let html = '<option selected disabled>Select Batsman</option>';
    for (let ev of batsmen) {
        html += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
    }
    $('#next_batsman').html(html);
    $('#nextBatsman').modal({backdrop: 'static', keyboard: false});
}

function getNextOver() {
    $('#over_number').html(json_v.overs_bowled);
    $('#nextOverSelect').modal({backdrop: 'static', keyboard: false});
    getRecentBalls();
    const nextBowler = json_v.allbowlers.filter(v => parseInt(v["pid"]) !== parseInt(json_v.currentbowler.pid));
    let inhtml = '<label>On Strike</label>' +
        '<select class="form-control" required id="batsman_on_strike">' +
        '<option selected disabled>Select Batsman on Strike</option>';
    for (let ev of json_v.currentbatsmen) {
        inhtml += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
    }
    inhtml += '</select>';
    $('#onStrike').html(inhtml);
    inhtml = '<label>Select Bowler</label>' +
        '<select class="form-control" required id="next_bowler">' +
        '<option selected disabled>Select Bowler</option>';
    for (let ev of nextBowler) {
        inhtml += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
    }
    inhtml += '</select>';
    $('#nextBowler').html(inhtml);
}

function getRecentBalls() {
    let html = '<b>Recent</b> <br>';
    let end = 7;
    let i = 1;
    let runs = 0;
    for (let ev of json_v.currentover) {
        switch (parseInt(ev.is_legal)) {
            case 1:
                html += ' <button class="btn btn-min btn-outline-dark" id="recent' + i + '">' + ev["total_runs"] + '</button>';
                break;
            case 2:
                end++;
                runs = 'Wd';
                if (parseInt(ev["total_runs"]) > 1)
                    runs += ' + ' + (parseInt(ev["total_runs"]) - 1);
                html += ' <button class="btn btn-min btn-outline-dark" id="recent' + i + '">' + runs + '</button>';
                break;
            case 3:
                runs = 'W';
                if (parseInt(ev["total_runs"]) > 0)
                    runs += ' + ' + ev["total_runs"];
                html += ' <button class="btn btn-min btn-outline-dark" id="recent' + i + '">' + runs + '</button>';
                break;
            case 4:
                runs = 'Lb';
                if (parseInt(ev["total_runs"]) > 1)
                    runs += ' + ' + (parseInt(ev["total_runs"]) - 1);
                html += ' <button class="btn btn-min btn-outline-dark" id="recent' + i + '">' + runs + '</button>';
                break;
            case 5:
                end++;
                runs = 'Nb';
                if (parseInt(ev["total_runs"]) > 1)
                    runs += ' + ' + (parseInt(ev["total_runs"]) - 1);
                html += ' <button class="btn btn-min btn-outline-dark" id="recent' + i + '">' + runs + '</button>';
                break;
            case 6:
                end++;
                runs = 'Nb + Lb';
                if (parseInt(ev["total_runs"]) > 2)
                    runs += ' + ' + (parseInt(ev["total_runs"]) - 2);
                html += ' <button class="btn btn-min btn-outline-dark" id="recent' + i + '">' + runs + '</button>';
                break;
        }
        i++;
    }
    for (let i = (json_v.currentover.length + 1); i < end; i++) {
        html += ' <button class="btn btn-min btn-outline-dark" id="recent' + i + '"></button>'
    }
    $('#recent_balls').html(html);
}

function updateBowler(balls, runs) {
    const current_over = json_v.currentbowler["overs"];
    json_v.currentbowler.runs = parseInt(runs) + parseInt(json_v.currentbowler.runs);
    if (parseInt(balls) === 6) {
        json_v.currentbowler.eco_rate = getEconomyRate((current_over + 1), 0, json_v.currentbowler.runs);
        json_v.currentbowler.current_ball = parseInt(current_over) + 1;
    } else {
        json_v.currentbowler.eco_rate = getEconomyRate(current_over, balls, json_v.currentbowler.runs);
        json_v.currentbowler.current_ball = current_over + '.' + balls;
    }
}

function getEconomyRate(current_over, balls, runs) {
    switch (balls) {
        case 1:
            current_over += 0.167;
            break;
        case 2:
            current_over += 0.333;
            break;
        case 3:
            current_over += 0.5;
            break;
        case 4:
            current_over += 0.67;
            break;
        case 5:
            current_over += 0.833;
            break;
    }
    if (current_over === 0.0)
        return 0;
    return Math.round(runs / current_over);
}

function updateBatsmen(balls, runs) {
    const is_legal = $('#is_legal');
    let bonstrike = json_v.currentbatsmen.filter(v => v["on_strike"])[0];
    const pid = bonstrike["pid"];
    let bnonstrike = json_v.currentbatsmen.filter(v => !v["on_strike"])[0];
    if (parseInt(is_legal.val()) === 1 || parseInt(is_legal.val()) === 4) {
        // legal delivery
        runs = parseInt(runs);
        switch (runs) {
            case 1:
            case 3:
            case 5:

                bonstrike["balls_faced"] = parseInt(bonstrike["balls_faced"]) + 1;
                if (parseInt(is_legal.val()) === 1) {
                    bonstrike["runs"] = parseInt(bonstrike["runs"]) + runs;
                }
                (runs === 5) ? ++bonstrike["fours"] : '';
                bnonstrike["on_strike"] = true;
                bonstrike["on_strike"] = false;
                break;
            case 0:
            case 2:
            case 4:
            case 6:
                bonstrike["balls_faced"] = parseInt(bonstrike["balls_faced"]) + 1;
                if (parseInt(is_legal.val()) === 1) {
                    bonstrike["runs"] = parseInt(bonstrike["runs"]) + runs;
                }
                (runs === 4) ? ++bonstrike["fours"] : '';
                (runs === 6) ? ++bonstrike["sixes"] : '';
                break;
        }
    }
    else if (parseInt(is_legal.val()) === 3) {
        json_v.wickets++;
        runs = parseInt(runs);
        switch (runs) {
            case 1:
            case 2:
            case 3:
                bonstrike["balls_faced"] = parseInt(bonstrike["balls_faced"]) + 1;
                bonstrike["runs"] = parseInt(bonstrike["runs"]) + runs;
                bonstrike["on_strike"] = false;
                break;
        }
    }
    else if (parseInt(is_legal.val()) === 5) {
        runs = parseInt(runs) - 1;
        switch (runs) {
            case 1:
            case 3:
            case 5:
                bonstrike["balls_faced"] = parseInt(bonstrike["balls_faced"]) + 1;
                bonstrike["runs"] = parseInt(bonstrike["runs"]) + runs;
                bnonstrike["on_strike"] = true;
                bonstrike["on_strike"] = false;
                break;
            case 0:
            case 2:
            case 4:
            case 6:
                if (runs !== 0)
                    bonstrike["balls_faced"] = parseInt(bonstrike["balls_faced"]) + 1;
                bonstrike["runs"] = parseInt(bonstrike["runs"]) + runs;
                (runs === 4) ? ++bonstrike["fours"] : '';
                (runs === 6) ? ++bonstrike["sixes"] : '';
                break;
        }
        runs++;
    }
    else if (parseInt(is_legal.val()) === 6) {
        runs = parseInt(runs) - 1;
        switch (runs) {
            case 1:
            case 3:
            case 5:
                bonstrike["balls_faced"] = parseInt(bonstrike["balls_faced"]) + 1;
                bnonstrike["on_strike"] = true;
                bonstrike["on_strike"] = false;
                break;
            case 0:
            case 2:
            case 4:
            case 6:
                if (runs !== 0)
                    bonstrike["balls_faced"] = parseInt(bonstrike["balls_faced"]) + 1;
                break;
        }
        runs++;
    } else {
        switch (parseInt(runs)) {
            case 2:
            case 4:
            case 6:
                (runs === 5) ? ++bonstrike["fours"] : '';
                bnonstrike["on_strike"] = true;
                bonstrike["on_strike"] = false;
                break;
        }
    }
    json_v.currentbatsmen = [];
    json_v.currentbatsmen.push(bonstrike, bnonstrike);
    insertBallServer(is_legal.val(), runs, pid);
}

function submitOverForm() {
    json_v.currentover = [];
    $('#nextOverSelect').modal('hide');
    const batsman_on_strike = $('#batsman_on_strike');
    const next_bowler = $('#next_bowler');
    json_v.currentbowler = json_v.allbowlers.filter(v => parseInt(v["pid"]) === parseInt(next_bowler.val()))[0];
    for (let ev of json_v.currentbatsmen) {
        ev["on_strike"] = (parseInt(ev.pid) === parseInt(batsman_on_strike.val()));
    }
    databaseInsert(false);
    return false;
}

function openOutModal() {
    let html = '<option selected disabled>Out How</option>';
    for (let ev of out_params) {
        html += '<option value="' + ev["id"] + '">' + ev["name"] + '</option>';
    }
    $('#out_params').html(html);
    html = '<option selected disabled>Who is Out</option>';
    for (let ev of json_v.currentbatsmen) {
        html += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
    }
    $('#who_is_out').html(html);
    $('#batsmanOut').modal({backdrop: 'static', keyboard: false});
}

function getFielderInvolved() {
    const id = parseInt($('#out_params').val());
    const fielder1 = $('#fielder1');
    const fielder2 = $('#fielder2');
    const fielder_1 = $('#fielder_1');
    const fielder_2 = $('#fielder_2');
    let html = '<option selected disabled>Fielder 1</option>';
    for (let ev of json_v.allbowlers) {
        html += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
    }
    let html2 = '<option selected disabled>Fielder 1</option>';
    for (let ev of json_v.allbowlers) {
        html2 += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
    }
    fielder1.addClass('hidden');
    fielder2.addClass('hidden');
    switch (id) {
        case 2:
        case 5:
            fielder1.removeClass('hidden');
            fielder_1.html(html);
            break;
        case 4:
            fielder1.removeClass('hidden');
            fielder2.removeClass('hidden');
            fielder_1.html(html);
            fielder_2.html(html2);
            break;
    }
}

function submitBatsmanOut() {
    let out_params = ($('#out_params').val());
    let who_is_out = ($('#who_is_out').val());
    if (out_params === null) {
        alert('Please Select Out Param');
        return false;
    }
    if (who_is_out === null) {
        alert('Please Select Who is out');
        return false;
    }
    out_params = parseInt(out_params);
    who_is_out = parseInt(who_is_out);
    $('#is_legal').val(3);
    const total_runs = $('#total_runs');
    total_runs.val(0);
    fielder_1 = '';
    fielder_2 = '';
    switch (out_params) {
        case 2:
        case 5:
            fielder_1 = $('#fielder_1').val();
            break;
        case 4:
            fielder_1 = $('#fielder_1').val();
            fielder_2 = $('#fielder_2').val();
            break;
    }
    if (out_params === 4) { // if run out
        removeDisabledButtons();
    }
    $('#runs_per_ball').val('W');
    $('#batsmanOut').modal('hide');
    return false;
}

function getBatsmanOnStrike() {
    const next_batsman = $('#next_batsman');
    json_v.currentbatsmen.push(json_v.allbatsmen.filter(v => parseInt(v["pid"]) === parseInt(next_batsman.val()))[0]);
    let html = '<option selected disabled>Select Batsman</option>';
    for (let ev of json_v.currentbatsmen) {
        html += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
    }
    $('#next_on_strike').html(html);
}

function submitNextBatsman() {
    let next_on_strike = $('#next_on_strike').val();
    let next_batsman = $('#next_batsman').val();
    console.log(next_batsman, next_on_strike, "console");
    if (next_batsman === null) {
        alert('Please Select Next Batsman');
        return false;
    }
    if (next_on_strike === null) {
        alert('Please Select Next Batsman on Strike');
        return false;
    }
    json_v.currentbatsmen.map(v => v["on_strike"] = parseInt(v["pid"]) === parseInt(next_on_strike));
    $('#nextBatsman').modal('hide');
    $('#table-score').html(getInnerHtml());
    databaseInsert(true);
    return false;
}

function getBowlerAndBatsmen(id) {
    const data = {
        _token: csrf_token
    };
    $.post('/match/' + id + '/getPlayers', data).then(function (data) {
        // Get Initial Players Here From Server
        data = JSON.parse(data);
        json_v.allbatsmen = data["batsmen"];
        json_v.allbowlers = data["bowlers"];
        json_v.match_id = data["match_id"];
        json_v.currentbatsmen = data["currentbatsmen"];
        json_v.currentbowler = data["currentbowler"];
        json_v.over_id = data["over_id"];
        json_v.currentover = data["currentover"];
        json_v.overs_bowled = data["overs_bowled"];
        json_v.balls_bowled = data["balls_bowled"];
        json_v.overs_bowled_total = data["overs_bowled_total"];
        json_v.team_runs_disp = data["team_runs_disp"];
        json_v.wickets = data["wickets"];
        json_v.no_of_overs = data["no_of_overs"];
        json_v.innings = data["innings"];
        json_v.first_team_runs = data["first_team_runs"];
        json_v.first_team_id = data["first_team_id"];
        json_v.first_team_name = data["first_team_name"];
        json_v.second_team_id = data["second_team_id"];
        json_v.second_team_name = data["second_team_name"];
        json_v.first_innings_score = data["first_innings_score"];
        json_v.end_match = data["end_match"];
        $('#batting_team').html(data["batting_team"]);
        if (data["newOver"] && !matchOver()) {
            getNextOver();
        }
        $('#teamssumm').html(json_v.first_team_name + ' V/s ' + json_v.second_team_name);
        if (data["startMatch"]) {
            $('#inningsDisp').html(data.innings);
            $('#myModalfirstinnings').modal({backdrop: 'static', keyboard: false});
            let html = '<option value="" selected disabled>Select Batsman</option>';
            for (let ev of json_v.allbatsmen) {
                html += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
            }
            $('#batsmen1').html(html);
            html = '<option value="" selected disabled>Select Bowler</option>';
            for (let ev of json_v.allbowlers) {
                html += '<option value="' + ev["pid"] + '">' + ev["player_name"] + '</option>';
            }
            $('#bowler1').html(html);
        } else {
            $('#table-score').html(getInnerHtml());
        }
        if (parseInt(json_v.overs_bowled) === parseInt(json_v.no_of_overs) || parseInt(json_v.wickets) === 10) {
            // start new Innings
        }
        if (data["end_match"]) {
            $('#end_match_disp').removeClass('hidden');
            $('#score-buttons').addClass('hidden');
            $('#reason').html(data["reason"]);
        }
    });
}

$(document).ready(function () {
    $('#nextBatsman').on('hidden.bs.modal', function () {
        let k = json_v.currentover.filter(v => parseInt(v["is_legal"]) === 1 || parseInt(v["is_legal"]) === 3 || parseInt(v["is_legal"]) === 4).length;
        if (parseInt($('#is_legal').val()) === 3 && k === 6 && !matchOver()) {
            getNextOver();
        }
    });

    $.post('/getAllTeams', {
        _token: csrf_token
    }).then((data) => {
        data = JSON.parse(data);
        teams = data["teams"];
    });

    getBowlerAndBatsmen($('#match_id').val());
    $('#default-datatable').DataTable();
    getRecentBalls();

    $('#batsmen1').change(function () {
        json_v.allbatsmen.map(v => {
            if (v['pid'] === parseInt(this.value))
                return v['on_strike'] = true;
        });
        let alb = json_v.allbatsmen;
        alb = alb.filter(v => v['pid'] !== parseInt(this.value));
        let v = '<option selected disabled>Select Batsman</option>';
        for (let ev of alb) {
            v += '<option value="' + ev.pid + '">' + ev.player_name + '</option>';
        }
        $('#batsmen2').html(v);
    });

    $('#initialcondition').submit(function () {
        return initializeMatch();
    });

    for (let i = 0; i < 7; i++) {
        $('#runs' + i).click(function () {
            calculateRuns(i, false, 0);
            $('#is_legal').val(1);
            $('#total_runs').val(i);
        })
    }

    $('#addMatchForm').submit(() => {
        const p1 = $('#my_multi_select1').val();
        const p2 = $('#my_multi_select2').val();
        let flag = true;
        if (p1.length < 11) {
            $('#playing_error_1').html('There should be 11 players');
            $('#playing_error_1').removeClass('hidden');
            flag = false;
        }
        if (p2.length < 11) {
            $('#playing_error_2').html('There should be 11 players');
            $('#playing_error_2').removeClass('hidden');
            flag = false;
        }
        return flag;
    });

    $('#runsNb').click(function () {
        removeDisabledButtons();
        calculateRuns('Nb', true, 0);
        $('#is_legal').val(5);
        $('#total_runs').val(1);
    });

    $('#runsWd').click(function () {
        removeDisabledButtons();
        calculateRuns('Wd', true, 0);
        $('#is_legal').val(2);
        $('#total_runs').val(1);
    });

    $('#runsLb').click(function () {
        removeDisabledButtons();
        $('#is_legal').val(4);
        $('#total_runs').val(1);
        calculateRuns('Lb', true, 0);
    });

    for (let i = 0; i < 7; i++) {
        $('#runsplus' + i).click(function () {
            calculateRuns($('#runs_per_ball').val(), true, i);
            if (parseInt($('#is_legal').val()) === 3) {
                $('#total_runs').val(i);
            } else if (parseInt($('#is_legal').val()) === 6) {
                $('#total_runs').val(i + 2);
            } else {
                $('#total_runs').val(i + 1);
            }
        })
    }

    $('#clearAll').click(function () {
        clearAll();
    });

    $('#submitball').click(function () {
        submitBall();
    });

    $('#nextOverForm').submit(function () {
        const batsman_on_strike = $('#batsman_on_strike').val();
        const next_bowler = $('#next_bowler').val();
        if (next_bowler === null) {
            alert('Please select next Bowler');
            return false;
        }
        if (batsman_on_strike === null) {
            alert('Please select the batsman on strike');
            return false;
        }
        submitOverForm();
        $('#table-score').html(getInnerHtml());
        return false;
    });

    $('#runsW').click(function () {
        openOutModal();
    });

    $('#out_params').change(function () {
        getFielderInvolved();
    });

    $('#batsmanOutForm').submit(function () {
        return submitBatsmanOut();
    });

    $('#nextBatsmanForm').submit(function () {
        return submitNextBatsman();
    });

    $('#next_batsman').change(function () {
        getBatsmanOnStrike();
    });

    $('#team_id_a').change(function () {
        getOtherTeams();
    });

    $('#team_id_b').change(function () {
        getTossMembers();
    });

    $('#undoball').click(() => {
        undoButton();
    });

    $('#end_match').click(() => {
        $('#end_match_modal').modal({backdrop: 'static', keyboard: false});
    });

    $('#end_match_form').submit(() => {
        $.post('/match/' + json_v.match_id + '/endMatch', {
            _token: csrf_token,
            reason: $('#reason_end_match').val()
        }).then((data) => {
            $('#end_match_modal').modal('hide');
            $('#end_match_disp').removeClass('hidden');
            $('#score-buttons').addClass('hidden');
        });
        return false;
    });

    const my_multi_select1 = $('#my_multi_select1');
    const my_multi_select2 = $('#my_multi_select2');
    const playing_error_1 = $('#playing_error_1');
    const playing_error_2 = $('#playing_error_2');

    my_multi_select1.change(() => {
        const value = my_multi_select1.val();
        if (value.length > 11) {
            value.pop();
            playing_error_1.removeClass('hidden');
            my_multi_select1.val(value);
            my_multi_select1.multiSelect('destroy');
            getDropdown('my_multi_select1');
        } else {
            playing_error_1.addClass('hidden');
        }
        addcaptain(value, 'select_captain_1', 'select_wk_1', players1);
    });

    my_multi_select2.change(() => {
        const value = my_multi_select2.val();
        if (value.length > 11) {
            value.pop();
            playing_error_2.removeClass('hidden');
            my_multi_select2.val(value);
            my_multi_select2.multiSelect('destroy');
            getDropdown('my_multi_select2');
        } else {
            // enable again
            playing_error_2.addClass('hidden');
        }
        addcaptain(value, 'select_captain_2', 'select_wk_2', players2);
    });

});

function addcaptain(val, id, wk, arr) {
    let html = '';
    let k = '';
    for (let ev of val) {
        k = arr.filter(v => parseInt(v["id"]) === parseInt(ev))[0];
        html += '<option value="' + k["id"] + '">' + k["player_name"] + '</option>';
    }
    $('#' + id).html(html);
    $('#' + wk).html(html);
}

function getDropdown(id) {
    $('#' + id).multiSelect({
        selectableHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
        selectionHeader: "<input type='text' class='form-control search-input' autocomplete='off' placeholder='search...'>",
        afterInit: function (ms) {
            var that = this,
                $selectableSearch = that.$selectableUl.prev(),
                $selectionSearch = that.$selectionUl.prev(),
                selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
                selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

            that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function (e) {
                    if (e.which === 40) {
                        that.$selectableUl.focus();
                        return false;
                    }
                });

            that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function (e) {
                    if (e.which == 40) {
                        that.$selectionUl.focus();
                        return false;
                    }
                });
        },
        afterSelect: function (values) {
            this.qs1.cache();
            this.qs2.cache();
        },
        afterDeselect: function () {
            this.qs1.cache();
            this.qs2.cache();
        }
    });
}

