<div class="row-fluid">
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon"><i class="icon-tasks"></i></span>
            <h5>Số lượng tài khoản đăng ký</h5>
        </div>
        <div class="widget-content">
            <div class="row-fluid">
                <div class="span12" style="margin: 0;">
                    <canvas id="myChart" style="height:400px !important;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
   
    const labels = <?php $js_array_day = json_encode($register_day_chart); echo $js_array_day; ?>;

        const datapoints = <?php $js_array_value = json_encode($register_value_chart); echo $js_array_value; ?>;
        const data = {
        labels: labels,
        datasets: [
            {
            label: 'Tài khoản đăng ký mới',
            data: datapoints,
            fill: false,
            cubicInterpolationMode: 'monotone'
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            maintainAspectRatio: true,
        },
    };

    

    const ctx = document.getElementById('myChart');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: data,
    });

    
});


   
</script>