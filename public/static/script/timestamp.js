// 全局变量定义
let isRunning = true;
let timer = 0;
let timestampUnit = 's'; // 时间戳输入框的单位
let dateUnit = 's';      // 日期时间输入框的单位
let currentTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone; // 获取浏览器默认时区

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

// 获取时区显示名称
function getTimezoneName(timezoneId) {
    const timezone = commonTimezones.find(tz => tz.id === timezoneId);
    return timezone ? timezone.name : timezoneId;
}

// 填充时区下拉列表
function populateTimezoneList() {
    const $lists = $('.timezone-list');
    $lists.each(function() {
        const $list = $(this);
        // 添加当前时区（如果不在常用时区列表中）
        if (!commonTimezones.some(tz => tz.id === currentTimezone)) {
            $list.append(`<li><a href="#" data-timezone="${currentTimezone}">${currentTimezone}</a></li>`);
        }
        // 添加常用时区
        commonTimezones.forEach(tz => {
            $list.append(`<li><a href="#" data-timezone="${tz.id}">${tz.name}</a></li>`);
        });
    });
    
    // 设置初始时区显示和数据
    const timezoneName = getTimezoneName(currentTimezone);
    $('.timezone-select .timezone-text').text(timezoneName || currentTimezone)
                                      .data('timezone-id', currentTimezone);
}

// 格式化日期时间
function formatDateTime(date, timezone = currentTimezone, showMilliseconds = false) {
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
    
    if (showMilliseconds) {
        // 添加毫秒
        const milliseconds = date.getMilliseconds().toString().padStart(3, '0');
        return `${values.year}-${values.month}-${values.day} ${values.hour}:${values.minute}:${values.second}.${milliseconds}`;
    } else {
        return `${values.year}-${values.month}-${values.day} ${values.hour}:${values.minute}:${values.second}`;
    }
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
        
        // 更新按钮文本和时区ID
        $button.find('.timezone-text').text(timezoneName)
                                 .data('timezone-id', newTimezone);
        
        // 更新当前时区
        currentTimezone = newTimezone;
        
        // 只更新时间戳→日期时间的显示
        const $dateInput = $(this).closest('.input-group').find('input');
        if($dateInput.hasClass('time1-bj') && $('.time1').val()) {
            const timestamp = $('.time1').val();
            if(timestamp) {
                const date = new Date(parseInt(timestamp) * (timestampUnit === 's' ? 1000 : 1));
                $dateInput.val(formatDateTime(date, newTimezone, timestampUnit === 'ms'));
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
                newValue = (parseInt(currentValue) * 1000).toString();
            } else if(newUnit === 's' && timestampUnit === 'ms') {
                // 从毫秒转换为秒，取整
                newValue = Math.floor(parseInt(currentValue) / 1000).toString();
            }
            
            if(newValue) {
                $input.val(newValue);
                
                // 如果已经有转换结果，也需要更新日期时间显示
                if($('.time1-bj').val()) {
                    const date = new Date(parseInt(newValue) * (newUnit === 's' ? 1000 : 1));
                    $('.time1-bj').val(formatDateTime(date, currentTimezone, newUnit === 'ms'));
                }
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
        const dateTimeStr = $('.time2-bj').val();

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
                // 检查日期时间输入是否包含毫秒
                const timeComponents = dateTimeStr.split('.');
                if (timeComponents.length > 1) {
                    // 如果包含毫秒，使用原始的毫秒值
                    const milliseconds = parseInt(timeComponents[1]);
                    newValue = (parseInt(currentValue) * 1000 + milliseconds).toString();
                } else {
                    // 如果不包含毫秒，补充000
                    newValue = (parseInt(currentValue) * 1000).toString();
                }
            } else if(newUnit === 's' && dateUnit === 'ms') {
                // 从毫秒转换为秒，取整
                newValue = Math.floor(parseInt(currentValue) / 1000).toString();
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

        try {
            // 检查时间戳是否为有效数字
            if(isNaN(timestamp)) {
                throw new Error('无效的时间戳');
            }

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

            // 创建 UTC 时间对象
            const timestampNum = parseInt(correctedTimestamp);
            const date = new Date(timestampUnit === 'ms' ? timestampNum : timestampNum * 1000);

            // 检查日期是否有效
            if(isNaN(date.getTime())) {
                throw new Error('无效的日期');
            }

            // 检查是否是夏令时切换的无效时间
            const formatter = new Intl.DateTimeFormat('en-US', {
                timeZone: currentTimezone,
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            const parts = formatter.formatToParts(date);
            const formattedHour = parts.find(p => p.type === 'hour')?.value;
            
            // 如果格式化后的小时数与原始时间相差超过1小时，说明可能是在夏令时切换期间
            const originalHour = date.getHours();
            const hourDiff = Math.abs(parseInt(formattedHour) - originalHour);
            if (hourDiff > 1) {
                console.warn('注意：此时间处于夏令时切换期间');
            }

            // 使用 formatDateTime 转换为目标时区的时间，并根据单位决定是否显示毫秒
            $('.time1-bj').val(formatDateTime(date, currentTimezone, timestampUnit === 'ms'));
        } catch(error) {
            console.error('时间戳转换错误:', error);
            alert(error.message || '时间戳格式不正确或转换出错');
        }
    });

    $('.date2time').on('click', function() {
        const dateStr = $('.time2-bj').val();
        if(!dateStr) return;
        
        try {
            // 解析日期时间字符串
            const [datePart, timePart] = dateStr.split(' ');
            if(!datePart || !timePart) {
                throw new Error('日期时间格式不正确');
            }

            const [year, month, day] = datePart.split('-').map(Number);
            const timeComponents = timePart.split('.');
            const [hour, minute, second] = timeComponents[0].split(':').map(Number);
            const milliseconds = timeComponents[1] ? parseInt(timeComponents[1]) : 0;

            // 验证日期时间各个部分
            if([year, month, day, hour, minute, second].some(isNaN)) {
                throw new Error('日期时间包含无效数值');
            }
            
            // 获取用户选择的时区ID（不是显示文本）
            const $timezoneSelect = $(this).closest('.row').find('.timezone-select');
            const timezone = $timezoneSelect.find('.timezone-text').data('timezone-id') || currentTimezone;
            
            // 检查是否是夏令时切换的特殊时间
            const testDate = new Date(year, month - 1, day, hour, minute, second);
            const prevHour = new Date(testDate.getTime() - 3600000); // 前一个小时
            const nextHour = new Date(testDate.getTime() + 3600000); // 后一个小时
            
            const formatter = new Intl.DateTimeFormat('en-US', {
                timeZone: timezone,
                hour: '2-digit',
                hour12: false
            });
            
            const currentHour = parseInt(formatter.format(testDate));
            const prevFormattedHour = parseInt(formatter.format(prevHour));
            const nextFormattedHour = parseInt(formatter.format(nextHour));
            
            // 检查是否在夏令时切换期间（跳过或重复的小时）
            if (Math.abs(nextFormattedHour - prevFormattedHour) !== 2) {
                console.warn('注意：此时间可能处于夏令时切换期间');
            }
            
            // 通用的时区转换逻辑，适用于所有时区
            let timestamp;
            
            try {
                // 步骤1: 获取时区偏移量
                const formatter = new Intl.DateTimeFormat('en-US', {
                    timeZone: timezone,
                    timeZoneName: 'longOffset'
                });
                
                // 使用当前日期获取时区偏移
                const offsetInfo = formatter.formatToParts(new Date())
                    .find(part => part.type === 'timeZoneName')?.value || '';
                
                // 解析偏移值 (如 GMT+08:00)
                let offsetHours = 0;
                const offsetMatch = offsetInfo.match(/GMT([+-])(\d{1,2})(?::(\d{2}))?/);
                
                if (offsetMatch) {
                    const sign = offsetMatch[1] === '-' ? -1 : 1;
                    const hours = parseInt(offsetMatch[2]);
                    const minutes = offsetMatch[3] ? parseInt(offsetMatch[3]) : 0;
                    offsetHours = sign * (hours + minutes / 60);
                }
                
                // 步骤2: 构建ISO 8601格式的日期字符串，包含时区偏移
                const offsetSign = offsetHours >= 0 ? '+' : '-';
                const absOffsetHours = Math.abs(offsetHours);
                const offsetHoursPart = Math.floor(absOffsetHours).toString().padStart(2, '0');
                const offsetMinutesPart = Math.round((absOffsetHours % 1) * 60).toString().padStart(2, '0');
                
                // 构建带时区信息的ISO日期字符串
                const isoString = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}T${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}:${second.toString().padStart(2, '0')}.${milliseconds.toString().padStart(3, '0')}${offsetSign}${offsetHoursPart}:${offsetMinutesPart}`;
                
                // 步骤3: 使用ISO字符串创建日期，JavaScript会自动处理时区转换
                const dateWithOffset = new Date(isoString);
                
                // 步骤4: 获取正确的UNIX时间戳（毫秒）
                timestamp = dateWithOffset.getTime();
            } catch (e) {
                console.error('时区转换错误:', e);
                
                // 备用方案: 简单处理为UTC时间
                console.warn('使用备用UTC转换方法');
                timestamp = Date.UTC(year, month - 1, day, hour, minute, second, milliseconds);
            }
            
            // 检查转换结果是否有效
            if(isNaN(timestamp)) {
                throw new Error('时间转换结果无效');
            }
            
            // 根据选择的单位显示结果
            if(dateUnit === 'ms') {
                $('.time2').val(timestamp);
            } else {
                $('.time2').val(Math.floor(timestamp / 1000));
            }
        } catch(error) {
            console.error('时间转换错误:', error);
            alert(error.message || '时间格式不正确或转换出错');
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
        // 填充时间戳输入框（使用当前单位）
        $('.time1').val(Math.round(now.getTime() / (timestampUnit === 's' ? 1000 : 1)));
        // 填充日期时间输入框（不显示毫秒）
        $('.time2-bj').val(formatDateTime(now, currentTimezone));
    } catch (error) {
        console.error('初始化错误:', error);
    }
});