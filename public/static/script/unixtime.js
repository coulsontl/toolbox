// 全局变量定义
let isRunning = true;
let timer = 0;
let timestampUnit = 's'; // 时间戳输入框的单位
let dateUnit = 's';      // 日期时间输入框的单位
let currentTimezone = 'Asia/Shanghai'; // 默认时区

// 常用时区列表
const commonTimezones = [
    { id: 'Asia/Shanghai', name: '中国标准时间 (UTC+8)' },
    { id: 'Asia/Tokyo', name: '日本标准时间 (UTC+9)' },
    { id: 'Asia/Singapore', name: '新加坡标准时间 (UTC+8)' },
    { id: 'Europe/London', name: '英国标准时间 (UTC+0/+1)' },
    { id: 'Europe/Paris', name: '欧洲中部时间 (UTC+1/+2)' },
    { id: 'America/New_York', name: '美国东部时间 (UTC-5/-4)' },
    { id: 'America/Los_Angeles', name: '美国西部时间 (UTC-8/-7)' },
    { id: 'UTC', name: '协调世界时 (UTC)' }
];

// 填充时区下拉列表
function populateTimezoneList() {
    const $lists = $('.timezone-list');
    $lists.each(function() {
        const $list = $(this);
        commonTimezones.forEach(tz => {
            $list.append(`<li><a href="#" data-timezone="${tz.id}">${tz.name}</a></li>`);
        });
    });
    
    // 设置初始时区显示
    $('.timezone-select .timezone-text').text('Asia/Shanghai');
}

// 格式化日期时间
function formatDateTime(date, timezone = currentTimezone) {
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: timezone
    };
    
    const formatter = new Intl.DateTimeFormat('zh-CN', options);
    const parts = formatter.formatToParts(date);
    const values = {};
    
    parts.forEach(part => {
        values[part.type] = part.value;
    });
    
    return `${values.year}-${values.month}-${values.day} ${values.hour}:${values.minute}:${values.second}`;
}

// 更新当前时间显示
function updateCurrentTime() {
    const now = new Date();
    const timestamp = Math.round(now.getTime() / 1000);
    
    $('.current-timestamp .value').text(timestamp);
    $('.current-datetime .value').text(formatDateTime(now));
    
    if (isRunning) {
        timer = setTimeout(updateCurrentTime, 1000);
    }
}

// 刷新当前时间
function currentTime() {
    const now = new Date();
    const timestamp = Math.round(now.getTime() / 1000);
    
    $('.current-timestamp .value').text(timestamp);
    $('.current-datetime .value').text(formatDateTime(now));
}

// 开始计时器
function startTimer() {
    isRunning = true;
    updateCurrentTime();
}

// 停止计时器
function stopTimer() {
    isRunning = false;
    clearTimeout(timer);
}

// 初始化事件监听
function initEventListeners() {
    // 时区切换事件
    $(document).on('click', '.timezone-list a', function(e) {
        e.preventDefault();
        const newTimezone = $(this).data('timezone');
        const $button = $(this).closest('.input-group-btn').find('.timezone-select');
        const timezoneName = $(this).text();
        
        // 更新按钮文本
        $button.find('.timezone-text').text(newTimezone);
        
        // 更新当前时区
        currentTimezone = newTimezone;
        
        // 只在时间戳→日期时间转换中更新显示
        const $dateInput = $(this).closest('.input-group').find('input');
        if($dateInput.hasClass('time1-bj') && $('.time1').val()) {
            const timestamp = $('.time1').val();
            if(timestamp) {
                const date = new Date(parseInt(timestamp) * (timestampUnit === 's' ? 1000 : 1));
                $dateInput.val(formatDateTime(date, newTimezone));
            }
        }
    });

    // 时间戳输入框的单位切换事件
    $('.time1').closest('.input-group').find('.dropdown-menu a').on('click', function(e) {
        e.preventDefault();
        const newUnit = $(this).data('unit');
        const $dropdown = $(this).closest('.input-group-btn');
        const $button = $dropdown.find('button');
        const $input = $('.time1');
        const currentValue = $input.val();

        // 更新按钮文本
        if(newUnit === 'ms') {
            $button.html('毫秒(ms) <span class="caret"></span>');
        } else {
            $button.html('秒(s) <span class="caret"></span>');
        }
        
        // 只有在有输入值时才进行转换
        if(currentValue && !isNaN(currentValue)) {
            let newValue;
            if(newUnit === 'ms' && timestampUnit === 's') {
                // 从秒转换为毫秒
                newValue = (parseFloat(currentValue) * 1000).toString();
            } else if(newUnit === 's' && timestampUnit === 'ms') {
                // 从毫秒转换为秒
                newValue = (parseFloat(currentValue) / 1000).toString();
            }
            
            if(newValue) {
                $input.val(newValue);
            }
        }
        
        // 更新时间戳单位
        timestampUnit = newUnit;
    });

    // 日期时间输入框的单位切换事件
    $('.time2').closest('.input-group').find('.dropdown-menu a').on('click', function(e) {
        e.preventDefault();
        const newUnit = $(this).data('unit');
        const $dropdown = $(this).closest('.input-group-btn');
        const $button = $dropdown.find('button');
        const $input = $('.time2');
        const currentValue = $input.val();

        // 更新按钮文本
        if(newUnit === 'ms') {
            $button.html('毫秒(ms) <span class="caret"></span>');
        } else {
            $button.html('秒(s) <span class="caret"></span>');
        }
        
        // 只有在有输入值时才进行转换
        if(currentValue && !isNaN(currentValue)) {
            let newValue;
            if(newUnit === 'ms' && dateUnit === 's') {
                // 从秒转换为毫秒
                newValue = (parseFloat(currentValue) * 1000).toString();
            } else if(newUnit === 's' && dateUnit === 'ms') {
                // 从毫秒转换为秒
                newValue = (parseFloat(currentValue) / 1000).toString();
            }
            
            if(newValue) {
                $input.val(newValue);
            }
        }
        
        // 更新日期时间单位
        dateUnit = newUnit;
    });

    // 转换按钮事件
    $('.time2date').on('click', function() {
        const timestamp = $('.time1').val();
        if(!timestamp) return;

        // 检查并修正时间戳格式
        let correctedTimestamp = timestamp;
        if(timestampUnit === 'ms') {
            // 如果是毫秒，应该是13位
            if(timestamp.length < 13) {
                correctedTimestamp = timestamp + '0'.repeat(13 - timestamp.length);
            } else if(timestamp.length > 13) {
                correctedTimestamp = timestamp.substring(0, 13);
            }
        } else {
            // 如果是秒，应该是10位
            if(timestamp.length < 10) {
                correctedTimestamp = timestamp + '0'.repeat(10 - timestamp.length);
            } else if(timestamp.length > 10) {
                correctedTimestamp = timestamp.substring(0, 10);
            }
        }

        // 如果时间戳被修正，更新输入框
        if(correctedTimestamp !== timestamp) {
            $('.time1').val(correctedTimestamp);
        }

        let date = new Date();
        if(timestampUnit === 'ms') {
            date = new Date(parseInt(correctedTimestamp));
        } else {
            date = new Date(parseInt(correctedTimestamp) * 1000);
        }
        
        $('.time1-bj').val(formatDateTime(date, currentTimezone));
    });

    $('.date2time').on('click', function() {
        const dateStr = $('.time2-bj').val();
        if(!dateStr) return;
        
        try {
            // 创建一个特定时区的日期对象
            const [datePart, timePart] = dateStr.split(' ');
            const [year, month, day] = datePart.split('-');
            const [hour, minute, second] = timePart.split(':');
            
            // 获取用户选择的时区
            const timezone = $(this).closest('.row').find('.timezone-select .timezone-text').text();
            
            // 创建一个UTC的日期对象
            const date = new Date(Date.UTC(
                parseInt(year),
                parseInt(month) - 1,
                parseInt(day),
                parseInt(hour),
                parseInt(minute),
                parseInt(second)
            ));
            
            // 获取时区偏移量（小时）
            let offset = 0;
            switch(timezone) {
                case 'Asia/Shanghai':
                case 'Asia/Singapore':
                    offset = -8; // UTC+8
                    break;
                case 'Asia/Tokyo':
                    offset = -9; // UTC+9
                    break;
                case 'Europe/London':
                    offset = 0; // UTC+0
                    break;
                case 'Europe/Paris':
                    offset = -1; // UTC+1
                    break;
                case 'America/New_York':
                    offset = 5; // UTC-5
                    break;
                case 'America/Los_Angeles':
                    offset = 8; // UTC-8
                    break;
                case 'UTC':
                    offset = 0;
                    break;
            }
            
            // 应用时区偏移
            const timestamp = date.getTime() + (offset * 3600 * 1000);
            
            // 根据选择的单位显示结果
            if(dateUnit === 'ms') {
                $('.time2').val(timestamp);
            } else {
                $('.time2').val(Math.floor(timestamp / 1000));
            }
        } catch(error) {
            console.error('时间转换错误:', error);
        }
    });
}

// 页面加载完成后初始化
$(function() {
    try {
        // 合并的启动/停止按钮
        $('.toggle-timer').on('click', function() {
            const $btn = $(this);
            if (isRunning) {
                stopTimer();
                $btn.text('启动');
                $btn.removeClass('btn-danger').addClass('btn-primary');
            } else {
                startTimer();
                $btn.text('停止');
                $btn.removeClass('btn-primary').addClass('btn-danger');
            }
        });

        initEventListeners();
        populateTimezoneList();
        updateCurrentTime();
        startTimer(); // 默认启动定时器
        
        // 获取当前时间
        const now = new Date();
        // 填充Unix时间戳输入框（使用当前单位）
        $('.time1').val(Math.round(now.getTime() / (timestampUnit === 's' ? 1000 : 1)));
        // 填充日期时间输入框
        $('.time2-bj').val(formatDateTime(now, currentTimezone));
    } catch (error) {
        console.error('初始化错误:', error);
    }
});