<div class="card mt50 b-a">
    <div class="card-body">
        <canvas id="vote-status-chart" width="400"></canvas>
    </div>
</div>

<?php
$vote_label = array();
$vote_data = array();
foreach ($poll_answers as $vote_status) {
    $vote_label[] = $vote_status->title;
    $vote_data[] = $vote_status->total_vote;
}
?>

<script type="text/javascript">
    "use strict";

    //for vote status chart
    var labels = <?php echo json_encode($vote_label) ?>;
    var voteData = <?php echo json_encode($vote_data) ?>;
    var voteStatusChart = document.getElementById("vote-status-chart");

    //get background color for chart
    var getColor = ['#29c2c2', '#2d9cdb', '#d43480', '#83c340', '#ad159e', '#f1c40f', '#e74c3c', '#e18a00', '#34495e', '#dbadff', '#ff6384', '#36a2eb', '#4bc0c0', '#9966ff', '#e7e9ed', '#aab7b7', '#ffa64d', '#ffcd56', '#C0392B', '#E74C3C', '#9B59B6', '#9B59B6', '#2980B9', '#1ABC9C', '#27AE60', '#F1C40F', '#E67E22', '#34495E', '#D0D3D4'];

    new Chart(voteStatusChart, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [
                {
                    data: voteData,
                    backgroundColor: getColor,
                    borderWidth: 0
                }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    fontColor: "#898fa9"
                }
            },
            animation: {
                animateScale: true
            }
        }
    });
</script>
