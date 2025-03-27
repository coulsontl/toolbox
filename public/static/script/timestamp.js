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

// 检查时间是否在夏令时或冬令时切换期间
function checkDSTTransition(dateObj, timezone) {
    try {
        // 获取前后一小时的时间
        const prevHour = new Date(dateObj.getTime() - 3600000);
        const nextHour = new Date(dateObj.getTime() + 3600000);
        
        // 创建时区格式化器
        const formatter = new Intl.DateTimeFormat('en-US', {
            timeZone: timezone,
            hour: '2-digit',
            hour12: false
        });
        
        // 格式化三个时间点的小时
        const prevHourFormatted = parseInt(formatter.format(prevHour));
        const currentHourFormatted = parseInt(formatter.format(dateObj));
        const nextHourFormatted = parseInt(formatter.format(nextHour));
        
        // 检查小时差异
        const prevDiff = (currentHourFormatted - prevHourFormatted + 24) % 24;
        const nextDiff = (nextHourFormatted - currentHourFormatted + 24) % 24;
        
        // 夏令时开始：跳过一小时（2:00 → 3:00）
        if (prevDiff === 2 && nextDiff === 1) {
            return {
                isDST: true,
                type: 'start',
                message: '注意：此时间处于夏令时开始切换期间，时钟向前调整一小时'
            };
        }
        // 夏令时结束：重复一小时（2:00 → 1:00）
        else if (prevDiff === 1 && nextDiff === 2) {
            return {
                isDST: true,
                type: 'end',
                message: '注意：此时间处于夏令时结束切换期间，时钟向后调整一小时'
            };
        }
        // 非特殊时间
        return {
            isDST: false,
            type: 'none',
            message: ''
        };
    } catch (e) {
        console.error('检查夏令时出错:', e);
        return {
            isDST: false,
            type: 'error',
            message: ''
        };
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
        
        // 只更新时间戳→日期时间的显示
        const $dateInput = $(this).closest('.input-group').find('input');
        if($dateInput.hasClass('time1-bj') && $('.time1').val()) {
            const timestamp = $('.time1').val();
            if(timestamp) {
                const date = new Date(parseInt(timestamp) * (timestampUnit === 's' ? 1000 : 1));
                // 使用按钮中保存的时区ID，而不是全局时区
                const buttonTimezone = $button.find('.timezone-text').data('timezone-id');
                $dateInput.val(formatDateTime(date, buttonTimezone, timestampUnit === 'ms'));
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

    // 修改时间戳转日期时间的转换按钮事件
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

            // 获取当前时区选择按钮中保存的时区ID
            const selectedTimezone = $(this).closest('.row')
                                           .find('.timezone-select .timezone-text')
                                           .data('timezone-id') || currentTimezone;

            // 检查是否处于夏令时切换期间
            const dstInfo = checkDSTTransition(date, selectedTimezone);
            if (dstInfo.isDST) {
                // 显示夏令时警告消息
                const warningDiv = $('<div class="alert alert-warning" style="margin-top:10px;padding:5px 10px;"></div>')
                    .text(dstInfo.message);
                
                // 移除之前的警告
                $(this).closest('.row').find('.alert-warning').remove();
                // 添加新警告
                $(this).closest('.row').append(warningDiv);
                
                // 5秒后自动隐藏警告
                setTimeout(() => warningDiv.fadeOut(), 5000);
            } else {
                // 移除之前的警告
                $(this).closest('.row').find('.alert-warning').remove();
            }

            // 使用选定的时区格式化日期
            $('.time1-bj').val(formatDateTime(date, selectedTimezone, timestampUnit === 'ms'));
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
            
            // 创建临时日期对象（本地时间，用于检查夏令时）
            const testDate = new Date(year, month - 1, day, hour, minute, second);
            
            // 检查是否处于夏令时切换期间
            const dstInfo = checkDSTTransition(testDate, timezone);
            if (dstInfo.isDST) {
                // 显示夏令时警告消息
                const warningDiv = $('<div class="alert alert-warning" style="margin-top:10px;padding:5px 10px;"></div>')
                    .text(dstInfo.message + '，可能影响转换精度');
                
                // 移除之前的警告
                $(this).closest('.row').find('.alert-warning').remove();
                // 添加新警告
                $(this).closest('.row').append(warningDiv);
                
                // 5秒后自动隐藏警告
                setTimeout(() => warningDiv.fadeOut(), 5000);
            } else {
                // 移除之前的警告
                $(this).closest('.row').find('.alert-warning').remove();
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