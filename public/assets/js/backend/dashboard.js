define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Template, Echarts) {

    var data = Config.data;
    var Controller = {
        index: function () {
            // 基于准备好的dom，初始化echarts实例
            var myChart = Echarts.init(document.getElementById('echart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                legend: {
                    data: ['浏览数','订单数量','订单商品数量','支付成功订单','支付成功商品数量']
                },
                xAxis: {type: 'category',data:['浏览数','订单数量','订单商品数量','支付成功订单','支付成功商品数量']},
                yAxis: {type: 'value'},
                grid: {},
                tooltip: {
                    trigger: 'axis',
                    showContent: true
                },
                series: [
                    {
                        "name": "",
                        "type": "bar",
                        "showBackground": true,
                        "backgroundStyle": {
                            "color": "rgba(220, 220, 220, 0.8)"
                        },
                        "data":[data.visit,data.order_count,data.order_nums,data.pay_done,data.pay_done_nums]
                    }
                ]};

            //动态添加数据，可以通过Ajax获取数据然后填充
            setInterval(function () {
                window.location.reload()
            }, 60000);
            $(window).resize(function () {
                myChart.resize();
            });
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option);
        }
    };
    return Controller;
});