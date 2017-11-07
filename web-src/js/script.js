function drawChart(myData) {
    const data = {
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
}

$.get('./app_dev.php/statuses', function(data) {
    drawChart(data);
})

$.get('./app_dev.php/favourite/genre')
    .done(function(data) {
        $("#favouriteGenre").text(data);
    })
    .fail(function() {
        alert( "error" );
    });

$.get('./app_dev.php/favourite/artist')
    .done(function(data) {
        $("#favouriteArtist").text(data['items'][0].name);
    })
    .fail(function() {
        alert( "error" );
    });

$.get('./app_dev.php/favourite/track')
    .done(function(data) {
        $("#favouriteTrack").html('<strong>' + data['items'][0]['artists'][0].name + '</strong> - '+ data['items'][0].name);
    })
    .fail(function() {
        alert( "error" );
    });

$.get('./app_dev.php/recently/track')
    .done(function(data) {
        $("#recentlyPlayedTrack").html('<strong>' + data['items'][0]['track']['artists'][0].name + '</strong> - '+ data['items'][0]['track'].name);
    })
    .fail(function() {
        alert( "error" );
    });