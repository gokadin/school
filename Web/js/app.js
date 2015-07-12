Vue.http.headers.common['CSRF-TOKEN'] = $('#csrf-token').attr('content');

Vue.filter('formatDateForMessage', function(value) {
    if (!value) {
        return value;
    }

    var dateAndTime = value.split(' ');
    var dateOnly = dateAndTime[0].split('-');
    var timeOnly = dateAndTime[1].split(':');

    var date = new Date(dateOnly[0],
        (parseInt(dateOnly[1]) - 1),
        dateOnly[2],
        timeOnly[0],
        timeOnly[1],
        timeOnly[2]);
    var now = new Date();
    var delta = new Date(now - date);

    var hours = date.getHours();
    var minutes = date.getMinutes();

    if (hours < 10) {
        hours = '0' + hours.toString();
    }
    if (minutes < 10) {
        minutes = '0' + minutes.toString();
    }

    var dateString = hours + ':' + minutes;
    if (delta < 86400000) {
        return dateString;
    }

    dateString = getMonthName(date.getMonth() + 1, true) + ' ' + date.getDate() + ' ' + dateString;
    if (delta < 1892160000000) {
        return dateString;
    }

    return date.getFullYear() + ' ' + dateString;

    return delta;
});

function getMonthName(number, isShort) {
    switch (number) {
        case 1:
            return isShort ? 'Jan' : 'January';
        case 2:
            return isShort ? 'Feb' : 'February';
        case 3:
            return isShort ? 'Mar' : 'March';
        case 4:
            return isShort ? 'Apr' : 'April';
        case 5:
            return isShort ? 'May' : 'May';
        case 6:
            return isShort ? 'Jun' : 'June';
        case 7:
            return isShort ? 'Jul' : 'July';
        case 8:
            return isShort ? 'Aug' : 'August';
        case 9:
            return isShort ? 'Sept' : 'September';
        case 10:
            return isShort ? 'Oct' : 'October';
        case 11:
            return isShort ? 'Nov' : 'November';
        case 12:
            return isShort ? 'Dec' : 'December';
    }
}

function buildPhpDateTime(value) {
    var year = value.getFullYear();
    var month = value.getMonth() + 1;
    var day = value.getDate();
    var hour = value.getHours();
    var minute = value.getMinutes();
    var second = value.getSeconds();

    return year + '-' + month + '-' + day + ' ' + hour + ':' + minute + ':' + second;
}