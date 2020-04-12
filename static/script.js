$(function(){
    $.ajax({
        url: 'https://api.nowtime.cc/v1/today_in_history',
        dataType: 'json',
        success(res){
            if(res.code === 200){
                //调用函数，渲染“时间线”
                xuanran_time_line(data2dl(res.data));
            }
        }
    })
});

/**
 * 数据格式转换
 * @param data
 * @returns {[]}
 */
function data2dl(data){
    let arr = [];
    for(let item in data){
        let split = (data[item].data).split('${{delimiter}}');
        for(let pure in split){
            arr.push({
                title: split[pure],
                link:
                    'https://www.google.com/search?q='+
                    (data[item].year+"").replace("-", "前")+"年"
                    +data[item].month+"月"
                    +data[item].day+"日 "+split[pure].substring(0, 38),
                time: (data[item].year+"").replace("-", "前")+"年"+data[item].month+"月"+data[item].day+"日",
                type: data[item]['type']
            });
        }
    }

    return arr;
}


/**
 * 渲染时间线
 * @param data {[]}            需要进行时间线分割的数据
 * @param selector string      css 选择器
 */
function xuanran_time_line(data=[], selector=".time-line") {
    let type2en = [
        'big-event',
        'born',
        'die'
    ];
    let type2cn = [
        '大事件',
        '出生',
        '逝世'
    ];

    let time_line = document.querySelector(selector);
    let _group_month= group_month(data),html;

    time_line.innerHTML = '';//清空时间线
    for(let item in _group_month){
        html = '<li class="tl-header">\n' +
            '    <h2>'+item+'</h2>\n' +
            '</li>' +
            '<ul class="tl-body">';

        for(let items in _group_month[item]){
            ttt = _group_month[item][items];
            html += '<li>\n' +
                '    <span>'+time_d(ttt["time"])+'</span>\n' +
                '    <h3 class="'+type2en[ttt['type']-1]+'">\n' +
                '        <a title="类别：'+type2cn[ttt['type']-1]+'，点击可查询相关资料" href="'+ttt["link"]+'" target="_blank">'+ttt["title"]+'</a>\n' +
                '    </h3>\n' +
                '</li>\n'
        }
        html += '</ul>';

        time_line.innerHTML += html;
    }
    time_line.innerHTML += '<li class="tl-header start">\n' +
        '    <h2>事件开始</h2>\n' +
        '</li>';
}

/**
 * 按月份分组
 * @param data {[]}
 * @returns {[]}
 */
function group_month(data){
    let result = [];

    for (let item in data){
        let date = data[item].time;

        if(!result[date]){
            result[date] = [data[item]];
        }else{
            result[date].push(data[item]);
        }
    }

    return result;
}

function time_d(date) {
    return date.split("月")[1];
}