var TimeManagementEasyCalendar = (function ($) {
    'use strict';

    var me = {
        isInitialized: false,

        selector: {
            easyCalendar: '#easycalendar',
            easyCalendarLegend: '#easycalendar-legend',
            calendarattributes: '#calendarattributes'
        },

        storage: {
            days: [],
            dataEndpoint: '',
            monthNames: [
                'Januar', 'Februar', 'März', 'April', 'Mai',
                'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],
            statusMapping: {
                'unpaid': 'Unbezahlt',
                'absent': 'Fehltag',
                'away': 'Abwesend',
                'vacation': 'Urlaub',
                'request-vacation': 'Urlaubsantrag',
                'remove-vacation': 'Stornoantrag Urlaub',
                'sick': 'Krank',
                'request-sick': 'Krankheitsantrag',
                'remove-sick': 'Stornoantrag Krankheit'
            }
        },

        init: function () {
            if (me.isInitialized === true) {
                return;
            }
            me.registerMonthNames();
            me.drawCalendar();
            me.drawLegend();
        },

        drawLegend: function () {

            for (let statusClass in me.storage.statusMapping) {
                let statusTxt = me.storage.statusMapping[statusClass];

                let html = '<span class="' + statusClass + ' box">m</span><span class="txt">' + statusTxt + '</span>';

                $(me.selector.easyCalendarLegend).append(html);
            }
        },

        registerMonthNames: function () {

            if ($(me.selector.calendarattributes).length !== 0) {
                try {
                    let calendarattributes = JSON.parse($(me.selector.calendarattributes).html());
                    me.storage.monthNames = calendarattributes.monthNames;
                }
                catch (e) {
                    //do nothing, fallback from storage
                }
            }
        },

        drawCalendar: function () {

            me.storage.dataEndpoint = $(me.selector.easyCalendar).data('endpoint');

            $.ajax({
                url: me.storage.dataEndpoint,
                type: 'POST',
                dataType: 'json',
                success: function (data) {

                    if (data.error) {
                        me.storage.$dialog.find(me.selector.msg).text(data.error);
                    } else {
                        let formattedData = [];
                        if (data.is_expanded === true) {
                            formattedData = me.buildCalendarDataExpanded(data.holidays, data.calendar_data,
                                data.calendar_year);
                        } else {
                            formattedData = me.buildCalendarDataSummed(data.holidays, data.calendar_data,
                                data.calendar_year);
                        }

                        let html = '';
                        for (let i = 0; i < formattedData.length; i++) {
                            html +=
                                '<div ' +
                                'class="' + formattedData[i].class + '" ' +
                                'title="' + formattedData[i].title + '" ' +
                                'data-day="' + formattedData[i].day + '" ' +
                                'data-month="' + formattedData[i].month + '" ' +
                                'data-year="' + formattedData[i].year + '" ' +
                                '>' +
                                formattedData[i].txt +
                                '</div>';
                        }

                        $(me.selector.easyCalendar).append(html);
                    }
                },
                beforeSend: function () {}
            });
        },

        buildCalendarDataExpanded: function (holidays, vacations, year) {

            let dayAmount = 31;
            let monthAmount = 12;

            let currentMonth = new Date().getMonth() + 1;

            let allDays = me.buildCalendarFirstLine(year, dayAmount);

            for (let month = 1; month <= monthAmount; month++) {

                let daysInMonth = me.daysInMonth(year, month);
                let monthName = me.storage.monthNames[month - 1];

                allDays.push({
                    txt: monthName,
                    class: (currentMonth === month ? 'month today' : 'month'),
                    title: '',
                    year: year,
                    month: month,
                    day: 0
                });

                // first line with month
                for (let day = 1; day <= dayAmount; day++) {

                    let classAndTitle = me.findClassAndTitle(daysInMonth, holidays, year, month, day);
                    let htmlClass = classAndTitle.htmlClass;
                    let title = classAndTitle.title;

                    allDays.push({
                        txt: '',
                        class: htmlClass,
                        title: title,
                        year: year,
                        month: month,
                        day: day
                    });
                }

                // month info by employee
                if (vacations) {
                    for (let employeeName in vacations) {

                        if (vacations.hasOwnProperty(employeeName)) {
                            let employeeVacation = vacations[employeeName];

                            allDays.push({
                                txt: employeeName,
                                class: 'employee-name',
                                title: '',
                                year: year,
                                month: month,
                                day: 0
                            });

                            for (let day = 1; day <= dayAmount; day++) {

                                let classAndTitle = me.findClassAndTitle(daysInMonth, holidays, year, month, day);
                                let htmlClass = classAndTitle.htmlClass;
                                let title = classAndTitle.title;

                                let date = me.buildDateString(year, month, day);
                                if (date in employeeVacation) {

                                    let type = employeeVacation[date];
                                    htmlClass += ' ' + type;
                                    title = me.mapTypeToName(type) + ': ' + employeeName;
                                }

                                allDays.push({
                                    txt: '',
                                    class: htmlClass,
                                    title: title,
                                    year: year,
                                    month: month,
                                    day: day
                                });
                            }
                        }
                    }
                }
            }
            return allDays;
        },

        mapTypeToName: function (type) {

            if (type.search('half')) {
                type = type.replace('half', '').trim();
            }


            if (type in me.storage.statusMapping) {
                return me.storage.statusMapping[type];
            }
            return '';
        },

        buildCalendarFirstLine: function (year, dayAmount) {

            let allDays = [];

            let currentDay = new Date().getDate();

            //first line with day-numbers
            for (let day = 0; day <= dayAmount; day++) {

                if (day === 0) {
                    allDays.push({
                        txt: '',
                        class: 'top',
                        title: '',
                        year: year,
                        month: 0,
                        day: 0
                    });
                } else {
                    allDays.push({
                        txt: day,
                        class: (day === currentDay ? 'top today' : 'top'),
                        title: '',
                        year: year,
                        month: 0,
                        day: 0
                    });
                }
            }
            return allDays;
        },

        buildCalendarDataSummed: function (holidays, vacations, year) {

            let dayAmount = 31;
            let monthAmount = 12;

            let summedVacations = me.sumVacations(vacations);
            let allDays = me.buildCalendarFirstLine(year, dayAmount);

            let currentMonth = new Date().getMonth() + 1;

            for (let month = 1; month <= monthAmount; month++) {

                let daysInMonth = me.daysInMonth(year, month);
                let monthName = me.storage.monthNames[month - 1];

                allDays.push({
                    txt: monthName,
                    class: (currentMonth === month ? 'month today' : 'month'),
                    title: '',
                    year: year,
                    month: month,
                    day: 0
                });

                for (let day = 1; day <= dayAmount; day++) {

                    let classAndTitle = me.findClassAndTitle(daysInMonth, holidays, year, month, day);
                    let htmlClass = classAndTitle.htmlClass;
                    let title = classAndTitle.title;

                    let date = me.buildDateString(year, month, day);

                    if (summedVacations.hasOwnProperty(date)) {

                        let txt = '';
                        let typeAddress = summedVacations[date];

                        let countTypes = 0;
                        for (let type in typeAddress) {
                            countTypes++;
                        }

                        for (let type in typeAddress) {
                            if (typeAddress.hasOwnProperty(type)) {
                                let addresses = typeAddress[type];
                                txt +=
                                    '<div class="' + type + ' inline" ' +
                                    'title="' + me.mapTypeToName(type) + ': ' + addresses.join(', ') + '" ' +
                                    'style="width:' + Math.floor(100 / countTypes) + '%">' +
                                    (addresses.length === 1 ? '' : addresses.length) +
                                    '</div>';
                            }
                        }

                        allDays.push({
                            txt: txt,
                            class: htmlClass,
                            title: title,
                            year: year,
                            month: month,
                            day: day
                        });
                    } else {
                        allDays.push({
                            txt: '',
                            class: htmlClass,
                            title: title,
                            year: year,
                            month: month,
                            day: day
                        });
                    }
                }
            }
            return allDays;
        },

        sumVacations: function (vacations) {

            let summedVacation = {};

            for (let employeeName in vacations) {
                if (vacations.hasOwnProperty(employeeName)) {

                    let employeeVacation = vacations[employeeName];

                    for (let date in employeeVacation) {
                        if (employeeVacation.hasOwnProperty(date)) {

                            let type = employeeVacation[date];

                            if (summedVacation.hasOwnProperty(date)) {

                                let typeAddress = summedVacation[date];
                                if (typeAddress.hasOwnProperty(type)) {

                                    let addresses = typeAddress[type];
                                    addresses.push(employeeName);
                                    typeAddress[type] = addresses;
                                    summedVacation[date] = typeAddress;
                                } else {

                                    let typeAddress = summedVacation[date];
                                    typeAddress[type] = [employeeName];
                                    summedVacation[date] = typeAddress;
                                }
                            } else {

                                let typeAddress = {};
                                typeAddress[type] = [employeeName];
                                summedVacation[date] = typeAddress;
                            }
                        }
                    }
                }
            }
            return summedVacation;
        },

        buildDateString: function (year, month, day) {
            return year + '-' + (month < 10 ? '0' + month : month) + '-' + (day < 10 ? '0' + day : day);
        },

        findClassAndTitle: function (daysInMonth, holidays, year, month, day) {

            let date = me.buildDateString(year, month, day);

            let htmlClass = 'standard';
            let title = '';

            //always counting to 31, therefore some days dont exist
            if (day > daysInMonth) {
                htmlClass = 'noday';
            }

            if (me.isSaturday(date) || me.isSunday(date)) {
                htmlClass = me.getWeekDayName(date);
            } else {
                htmlClass += ' ' + me.getWeekDayName(date);
            }

            // is a holiday
            if (date in holidays) {
                htmlClass = 'holiday';
                title = holidays[date];
            }

            return {
                htmlClass: htmlClass,
                title: title
            };
        },

        daysInMonth: function (year, month) {
            return new Date(year, month, 0).getDate();
        },

        isSaturday: function (dateString) {
            let day = new Date(dateString).getDay();
            return (day === 6);
        },

        isSunday: function (dateString) {
            let day = new Date(dateString).getDay();
            return (day === 0);
        },

        getWeekDayName: function (dateString) {
            let weekdays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
            let day = new Date(dateString).getDay();
            return weekdays[day];
        }
    };

    return {
        init: me.init
    };

})(jQuery);

$(document).ready(function () {
    TimeManagementEasyCalendar.init();
});
