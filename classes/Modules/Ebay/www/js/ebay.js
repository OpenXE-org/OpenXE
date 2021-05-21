function loggingPositionsMoreData(){
    oMoreData1stocklogging = $('#show_errors_only').prop("checked")?1:0;
    var oTableL = $('#stocklogging').dataTable();
    oTableL.fnFilter('a');
    oTableL.fnFilter('');
}
