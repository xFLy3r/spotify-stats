
var json = (function () {
    var json = null;
    $.ajax({
        'async': false,
        'global': false,
        'url':  window.location.href + "/statuses",
        'dataType': "json",
        'success': function (data) {
            json = data;
        }
    });
    return json;
})();
var data = {
    "labels": [
        'Free',
        'Premium'
    ],
    datasets: [{
        data: [json[0], json[1]],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
        ],
        borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)',
        ],
        borderWidth: 1
    }]};
var ctx = document.getElementById("test").getContext("2d");
var test = new Chart(ctx,{
    type: 'pie',
    data: data
});