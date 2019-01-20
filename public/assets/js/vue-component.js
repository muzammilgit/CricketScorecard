Vue.component('batsmen-team', {
    props: ['batsmen'],
    data: function () {
        return {
            getOtherBatsmen: function (batsmen, event) {
                $('#batsmen').append('<batsmen-team :batsmen="batsmen"></batsmen-team>');
            }
        }
    },
    template: `
    <select class="form-control" v-on:change="getOtherBatsmen(batsmen,$event)">
        <option :value="b.id" v-for="b in batsmen">{{b.player_name}}</option>
    </select>`
});

const app = new Vue({
    el: '#app'
});
