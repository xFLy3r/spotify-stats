$.ajax({
    url: window.location.href + "statuses",
    async: false,
    dataType: 'json',
    success: function (json) {
        myData = json;
    }
});

var data = {
    labels: [
        'Free',
        'Premium'
    ],
    datasets: [{
        data: [myData['countOfFree'], myData['countOfPremium']],
        backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)'
        ],
        borderColor: [
            'rgba(255,99,132,1)',
            'rgba(54, 162, 235, 1)'
        ],
        borderWidth: 1
    }]

};


$(function() {
    var ctx = document.getElementById("test").getContext("2d");
    var test = new Chart(ctx,{
        type: 'pie',
        data: data,
        options: {
            legend: {
                labels: {
                    fontSize: 24,
                    fontFamily: 'sans-serif'
                }
            }
        }
    });
});

