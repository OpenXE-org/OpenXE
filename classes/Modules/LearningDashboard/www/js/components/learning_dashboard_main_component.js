Vue.component('learning-dashboard-main', {
    props: ['tabs'],
    template:
        '<div id="learning-dashboard" class="grid-wrapper grid-padded">' +
        '   <learning-dashboard-header ' +
        '       :tabs="tabs">' +
        '   </learning-dashboard-header>' +
        '   <learning-dashboard-tabs' +
        '       :tabs="tabs">' +
        '   </learning-dashboard-tabs>' +
        '</div>'
});
