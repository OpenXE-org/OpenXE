/**
 * jQuery grider
 * Versión 0.7
 * @author: Boris Barroso Camberos
 * @email: boriscyber@gmail.com
 * @license MIT
 * http://www.opensource.org/licenses/mit-license.php
 */
(function($) {

    $.fn.extend({
        grider: function(config) {
            return this.each(function() {
                new $.Grider(this, config);
            });
        }
    });

  /**
  * Transforms a grid that contains data, and allows to do calculations con form elements
  * in a simple way. Example:
  * <table id="grid">
  *   <tr>
  *     <th col="price" summary="min">Price</th>
  *     <th col="quantity">Quantity</th>
  *     <th col="discount">Discount</th>
  *     <th col="subtotal" formula="price*quantity+(1-0.9(discount))" summary="sum">Subtotal</th>
  *   </tr>
  *   <tr>
  *     <td><input type="text" name="det[0][quantity]" /></td>
  *     <td><input type="text" name="det[0][price]" /></td>
  *     <td><input type="checkbox" name="det[0][discount]" /></td>
  *     <td> </td>
  *   </tr>
  * </table>
  *
  * And then you execute $('#grid').grider();
  *
  * @attr col: Defines the col name, it is a unique identifier for that col in the table
  *
  * @attr summary: Defines the type of calculation it will be done on the column,
  * operations that can be done are "sum", "avg", "max", "min" and "count"
  *
  * @attr formula: Calculates the formula with the columns you defined, right now it does simple calculations, 
  * the formula is evaluated eval(formula), to calculate
  *
  * Configurations for the config variable
  * @param boolean config['initCalc'] Defines if formula should make calculations when it initializes, in case that you already did calculations server side
  *
  * @param boolean config['addRow'] Defines if the add row link will appear
  * @param string config['addRowText'] Text that will display to add rows
  * @param boolean config['delRow'] Defines if it will appear the delete link to delete a row
  * @param string config['delRowText'] Text that will be displayed for deleting a row
  * @param boolean config['countRow'] Indicates if it will do count the rows, necessary for adding and deleting rows
  * @param integer config['countRowCol'] Defines the column in which the count will be displayed, defauls = 0
  * @param boolean config['countRowAdd'] Indicates if it will be able to add Row
  * @param boolean config['rails'] accepts the use of rails nested attributes
  */
    $.Grider = function(table, config) {

        /**
         * Defaults
         */
        var defaults = Grider.defaults;
        var config;

        if(config) {
            for(var k in defaults) {
                if(typeof(defaults[k]) == "boolean" && typeof(config[k]) == "boolean" ) {
                    config[k] = config[k];
                }else{
                    config[k] = config[k] || defaults[k];
                }
            }
        }else{
            config = defaults;
        }

        var cols = {};
        // Identifies if there is a summary column
        var summaryRow = false;
        var formulaSet = false; // Indicates if the formula was added
        config = config || {};
        setGrider(table);

        /**
        * This function prepares all to set the table
        * @param DOM t Table
        */
        function setGrider(t) {
            $(table).find('tr:first').addClass('noedit');
            // Allow to count rows
            if(config['countRow']) {
                if(config['countRowAdd']) {
                    var className = $(table).find("th:first").attr("class");
                    className = className != "" ? ' class="' + className + '"': "";
                    $(table).find('tr.noedit:first').prepend('<th ' + className +'>'+ config.countRowText +'</th>');
                    $(table).find('tr:not(.noedit)').each(function(index, elem){
                        var ind = index+1;
                        $(elem).prepend('<td>'+ind+'</td>');
                    });
                }
            }
 
            var l = $(table).find("tr:not(.noedit):first td").length;
            for(var i = 0; i < l; i++) {
                setColumn(t.rows[0].cells[i], i);
            }
            // Types of columns
            setColType();
            // Setting formulas and summaries
            for(var i = 0; i < l; i++) {
                setFormula(t.rows[0].cells[i]);
                setSummary(t.rows[0].cells[i]);
            }
            // Calculation of formulas for the first Time
            if(formulaSet && config.initCalc) {
                var rows = $(table).find('tr:not(.noedit)');
                rows.each(function(index, elem) {
                    var pos = index + 1;
                    for(var k in cols) {
                        if(cols[k].formula) {
                            calculateFormula(cols[k].name, pos);
                        }
                    }
                });
            }
            for(var k in cols){
                if(cols[k].summary) {
                    calculateSummary(k);
                }
            };

            // Allow to add rows
            if(config['addRow']) {
                $(table).append(config['addRowText']);
                $(table).find("caption a").click(function() {
                    addRow();
                });
            }

            // Allow to delete rows
            if(config['delRow']) {
                $(table).find('tr:not(.noedit)').each(function(index,elem){
                    $(elem).append(config['delRowText']);
                });
                $(table).find('a.delete').live("click", function(){
                    delRow(this);
                    return true;
                });
            }

            // Add events to the elements in the table, elements that are related to a summary or formula
            setEvents();
            // Allows to add rows using tab when cursor on a delete link
            if(config.addRowWithTab)
                addRowWithTab();
        }

        /**
         * Allows to add a new row, the new row is added at the endof the editable rows
         */
        function addRowWithTab() {
            $(table).find("tr:not(.noedit):last a.delete").live("keydown",function(e) {
                if(e.keyCode == 9) {
                    addRow();
                }
            });
        }

        /**
        * Determines the type of element that is contained in the TD
        */
        function setColType() {
            var row = $(table).find('tr:not(.noedit):first')[0]; // Finds the first row tha is editable

            for(var k in cols) {
                var cell = $(row).find('td:eq(' + cols[k].pos + ')')[0];

                var node = $(cell).find('select')[0] || $(cell).find('input:not(:submit)')[0] || $(cell).find('select')[0];
                try {
                     type = node.nodeName.toLowerCase();
                }catch(e){ type = false }

                if(type) {

                    switch(type) {
                        case 'input':
                            cols[k]['type'] = 'input:'+ node.type;
                            break;
                        case 'select':
                            cols[k]['type'] = 'select';
                            break;
                        case 'textarea':
                            cols[k]['type'] = 'textarea';
                            break;
                        default:
                            cols[k]['type'] = 'input:text';
                            break;
                    }
                }else{
                    // Allows to use jQuery selectors
                    cols[k]['type'] = '';
                }
            }
        }

        /**
         * Allows to define columns with its names
         * @param DOM cell TD
         * @param integer pos Column number, starts on 0
         */
        function setColumn(cell, pos) {
            var col = $(cell).attr('col');
            if(col) {
                cols[col] = {
                    pos: pos,
                    name: col
                };
            }
        }

        /**
         * Alows columns to calculate summaries, like avg (average), sum, max, count
         * @param DOM cell
         */
        function setSummary(cell) {
            var summary = $(cell).attr('summary');
            var summaryCell = false;
            var col = $(cell).attr('col');
            if(summary == 'sum' || summary == 'avg' || summary == 'max' || summary == 'min' || summary == 'count') {
                cols[col]['summary'] = summary;
                summaryCell = true;
            }

            // Add the summary row
            if(!summaryRow && summaryCell) {
                var html = '<tr class="summary noedit">';
                $(table).find("tr:not(.noedit):first td").each(function(index, elem) {
                    var style = $(elem).attr("style") ? ' style="' + $(elem).attr("style") + '"' : "";
                    html += "<td" + style + "></td>";
                });
                html+='</tr>';
                $(table).append(html);
                summaryRow = true;
            }
        }

        /**
        * Calculates the summary of a column
        * @param String col, name of the column
        */
        function calculateSummary(col) {
            var summary = cols[col].summary;
            var pos = parseInt(cols[col].pos) + 1;
            var cells = $(table).find('tr:not(.noedit) td:nth-child(' + pos + ')');
            var res = 0, sum = 0, max = null, min = null;
            if(summary != 'count') {
                var val = 0;

                cells.each(function(index, elem) {
                    if(cols[col].type == "") {
                        val = $(elem).html() * 1;
                    }else{
                        val = $(elem).find(cols[col].type).val() * 1;
                    }

                    switch(summary) {
                        case 'sum':
                            sum+= val;
                            break;
                        case 'avg':
                            sum+= val;
                            break;
                        case 'max':
                            if(!max){
                                max = val;
                            } else if(max < val) {
                                max = val;
                            }
                            break;
                        case 'min':
                            if(!min){
                                min = val;
                            } else if(min > val) {
                                min = val;
                            }
                            break;
                    }
                });

                switch(summary) {
                    case 'sum': res = sum; break;
                    case 'avg': res = sum/cells.length; break;
                    case 'max': res = max; break;
                    case 'min': res = min; break;
                }
            }else{
                res = cells.length;
            }
            res = res.toFixed(config.decimals);
            $(table).find('tr.summary td:nth-child(' + pos +')').html(res);
        }


        /**
         * Fires the event required
         * @param Event e
         */
        function fireCellEvent(e) {
            var target = e.target || e.srcElement;
            if(target.nodeType == 1) {
                var rowNum = $(target).parents('tr')[0].rowIndex;
                var colNum = $(target).parents('td')[0].cellIndex;

                var col = findColBy(colNum, 'pos');
                for(var k in cols) {
                    if(cols[k].formula) {
                                                try{
                            var reg = '\\b'+ col.name +'\\b';
                            reg = new RegExp(reg);
                            if(reg.test(cols[k].formula)) {
                                calculateFormula(k, rowNum);
                            }
                        }catch(e){}
                    }
                }

            }
        }

        /**
         * creates the formulas to be calculated
         * @param DOM cell
         */
        function setFormula(cell) {

            formulaSet = true;
            var formula = $(cell).attr('formula');
            var col = $(cell).attr('col');
            if(formula) {
                cols[col]['formula'] = formula;

                // Register elements that trigger the calculation of a formula
                for(var k in cols) {
                    reg = "\\b" + k + "\\b";
                    var reg = new RegExp(reg);
                    // Definir que elementos tienen evento
                    if( reg.test(formula)) {
                        if(cols[k].type != '')
                            cols[k]["event"] = true;
                    }
                }
            }
        }

        /**
        * Prepares the events requied in the grid
        * @param string col, name of the column
        */
        function setEvents() {
            for(k in cols) {
                if(cols[k].event) {
                    var pos = parseInt(cols[k]['pos']) + 1;
                    var exp = 'tr td:nth-child(' + pos + ') ' + cols[k].type;
                    // Shitty Internet Explorer, not posible to use "live"
                    if(cols[k].type == 'input:text' || cols[k].type == 'textarea' || cols[k].type == 'select' ) {
                        $(table).find(exp).unbind("change");
                        $(table).find(exp).change( function(e) {
                            fireCellEvent(e);
                        });
                    }else if( cols[k].type == 'input:checkbox') {
                        $(table).find(exp).unbind("click");
                        $(table).find(exp).click( function(e) {
                            fireCellEvent(e);
                        });
                    }
                }
            }
        }

        /**
         * Calculates the formula according to de columns
         * @param String col, column name
         * @param Integer pos, position of the row where the event was generated
         */
        function calculateFormula(col, pos) {
            var pat = cols[col].formula.match(/\b[a-z_-]+[0-9]*\b/ig);
            var formu = cols[col].formula;
            var row = $(table).find('tr:eq('+ pos + ')');
            // Again needed for IE
            for(var k in pat) {
                if(!/^\d+$/.test(k)) {
                    delete(pat[k]);
                }
            }
            var columns = []
            // Prepare formula to be calcultated
            for(var k in pat) {
                //console.log("%s: %o, %o, %o",table.id, k, pat[k], cols[pat[k]]);
                try{
                    var exp = 'td:nth-child(' + (cols[pat[k]].pos + 1) + ') ' + cols[pat[k]].type;
                }catch(e){
                    //console.log("%s: %o, %o, %o",table.id, k, pat[k], cols);
                }
                var val = 0;
                if(cols[pat[k]].type == 'input:checkbox') {
                    val = $(row).find(exp).attr('checked') ? 1 : 0;
                }else if(cols[pat[k]].type == 'input:text'){
                    val = parseFloat( $(row).find(exp).val() ) || 0
                }
                var reg = new RegExp('\\b' + pat[k] + '\\b')
                formu = formu.replace(reg, val);
                columns.push(pat[k]);
            }

            var res = eval(formu);
            res = res.toFixed(config.decimals);
            // Pocision the response
            var cell = $(row).find('td:nth-child(' + (cols[col].pos + 1) + ')');
            if(cols[col].type == "") {
                $(cell).html(res);
            }else{
                $(cell).find(type).html(res);
            }
            for(var i=0, l=columns.length ; i< l; i++) {
                if(cols[columns[i]].summary)
                    calculateSummary(columns[i]);
            }
            calculateSummary(col);
        }

        /**
         * Finds a value in the cols Object
         * @param string bus, search parameter
         * @param string prop, Property in the cols Object
         * @return object, Returns the column
         */
        function findColBy(bus, prop) {
            for(var k in cols) {
                if(bus == cols[k][prop]) {
                    return cols[k];
                }
            }
        }

        /**
         * Intializes and adds a number when needed to count the rows added or deleted
         */
        function addFormPos() {
            if(!config.formPos || config.formPos == '') {
                var control = $(table).find('tr:not(.noedit):last').find('input, select, textarea')[0] || false;
                // Row number in the control
                if(control.name) {
                  config["formPos"] = control.name.replace(/^.*\[([0-9]+)\].*$/ig, "$1") || '';
                }
                config.addedRow = true;
                config.formPos++;
            } else {
                config.formPos++;
            }
        }

        /**
         * Function that allows o add new rows
         */
        function addRow() {
            var tr = $(table).find('tr:not(.noedit):first').clone();
            addFormPos();

            if($(tr).find("input, select, textarea").length > 0) {
                $(tr).find("input, textarea, select").each(function(index, elem) {
                    // Change the name of the fields
                    var newName = '';
                    if(config.formPos !== '') {
                        newName = elem.name.replace(/\[[0-9]+\]/i, '[' + config.formPos + ']');
                    }else {
                        newName = elem.name;
                    }
                    if(elem.type == 'checkbox' || elem.type == 'radio') {
                         $(elem).attr({'name': newName, 'checked': false})
                    }else {
                         $(elem).attr({'name': newName, 'value': ''});
                    }
                    $(elem)
                });
                $(tr).find("input:radio, input:checkbox").attr('checked', false);
            }
            if(cols[k] && cols[k].type == "" && cols[k].formula)
                $(tr).find("td:eq(" + cols[k].pos + ")").html('');
            if(config['countRow']) {
                var fila = parseInt($(table).find('tr:not(.noedit):last td:eq('+ config['countRowCol'] +')').html()) + 1;
                $(tr).find('td:eq('+ config['countRowCol'] +')').html(fila);
            }
            $(table).find('tr:not(.noedit):last').after(tr);
            // Register elements that fire events
            setEvents();
            for(var kk in cols){
                if(cols[kk].summary)
                    calculateSummary(cols[kk].name);
            }
        }

        /**
         * Allows to delete a row
         */
        function delRow(elem) {
            if($(table).find('tr:not(.noedit)').length > 1 ) {
                if(config.rails) {
                    var el = $(elem).parents('tr').eq(0).prev("input:hidden[name$='[id]']");
                    if(el.length > 0) {
                        addFormPos();
                        var name = $(el).attr("name");
                        var value = $(el).val();
                        n1 = name.replace(/^(.*)(\[[0-9]+\])(\[id\])$/, "$1["+ config.formPos + "]$3");
                        n2 = name.replace(/^(.*)(\[[0-9]+\])(\[id\])$/, "$1["+ config.formPos +"][_delete]");
                        $(el).remove();
                        $(table).prepend('<span><input type="hidden" name="'+ n1 +'" value="'+ value +'" /><input type="hidden" value="1" name="'+ n2 +'"</span>');
                    }
                }
                $(elem).parents('tr').eq(0).remove();
                if(config['countRow']) {
                    rowNumber();
                }
            }
            for(var k in cols) {
                  if(cols[k].summary)
                      calculateSummary(k);
            }
        }

        /**
         * Number the rows
         */
        function rowNumber() {
            var pos = parseInt(config.countRowCol) + 1;
            $(table).find('tr:not(.noedit) td:nth-child('+ pos +')').each(function(index, elem) {
                var ind = index + 1;
                $(elem).html(ind);
            });
        }

        return {
            cols: cols,
            config: config,
            summaryRow: summaryRow,
            table: table,
            formulaSet: formulaSet,
            calculateFormula: calculateFormula,
            setGrider: setGrider,
            setColumn: setColumn,
            fireCellEvent: fireCellEvent,
            setColType: setColType,
            findColBy: findColBy,
            addRow: addRow,
            addRowWithTab: addRowWithTab,
            delRow: delRow,
            rowNumber: rowNumber
        }
    }

    $.Grider.events = function() {
        return 'nuevo';
    }

})(jQuery);

// Defauls English configurations
Grider = {
    defaults : {
        initCalc: true,
        addRow: true,
        addRowWithTab: true,
        delRow: true,
        decimals: 2,
        addRowText: '<caption><a href="#">Add Row</a></caption>',
        delRowText: '<td><a href="#" class="delete">delete</a></td>',
        countRow: false,
        countRowText: 'Nº',
        countRowCol: 0,
        countRowAdd: false,
        addedRow: false,
        rails: false
    }
}

//Defauls Spanish
/*
Grider = {
    defaults : {
        initCalc: true,
        addRow: true,
        addRowWithTab: true,
        delRow: true,
        decimals: 2,
        addRowText: '<caption><a href="#">Adicionar Fila</a></caption>',
        delRowText: '<td><a href="#" class="delete">borrar</a></td>',
        countRow: false,
        countRowText: 'Nº',
        countRowCol: 0,
        countRowAdd: false,
        addedRow: false
    }
}
*/
