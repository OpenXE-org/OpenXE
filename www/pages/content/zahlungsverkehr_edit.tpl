<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <!-- Example for multiple tabs
    <ul hidden">
        <li><a href="#tabs-1">First Tab</a></li>
        <li><a href="#tabs-2">Second Tab</a></li>
    </ul>
    -->
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->zahlungsverkehr|}</legend><i>Info like this.</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Returnorder_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="returnorder_id" id="returnorder_id" value="[RETURNORDER_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_status|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_status" id="payment_status" value="[PAYMENT_STATUS]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_account_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_account_id" id="payment_account_id" value="[PAYMENT_ACCOUNT_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Address_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="address_id" id="address_id" value="[ADDRESS_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Amount|}:
                                        </td>
                                        <td>
                                            <input type="text" name="amount" id="amount" value="[AMOUNT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Currency|}:
                                        </td>
                                        <td>
                                            <input type="text" name="currency" id="currency" value="[CURRENCY]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_reason|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_reason" id="payment_reason" value="[PAYMENT_REASON]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_json|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_json" id="payment_json" value="[PAYMENT_JSON]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Liability_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="liability_id" id="liability_id" value="[LIABILITY_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_transaction_group_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_transaction_group_id" id="payment_transaction_group_id" value="[PAYMENT_TRANSACTION_GROUP_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_info|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_info" id="payment_info" value="[PAYMENT_INFO]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Created_at|}:
                                        </td>
                                        <td>
                                            <input type="text" name="created_at" id="created_at" value="[CREATED_AT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Doc_typ|}:
                                        </td>
                                        <td>
                                            <input type="text" name="doc_typ" id="doc_typ" value="[DOC_TYP]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Doc_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="doc_id" id="doc_id" value="[DOC_ID]" size="20">
                                        </td>
                                    </tr>
                                    
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <!-- Example for 2nd row            
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Another legend|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Returnorder_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="returnorder_id" id="returnorder_id" value="[RETURNORDER_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_status|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_status" id="payment_status" value="[PAYMENT_STATUS]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_account_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_account_id" id="payment_account_id" value="[PAYMENT_ACCOUNT_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Address_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="address_id" id="address_id" value="[ADDRESS_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Amount|}:
                                        </td>
                                        <td>
                                            <input type="text" name="amount" id="amount" value="[AMOUNT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Currency|}:
                                        </td>
                                        <td>
                                            <input type="text" name="currency" id="currency" value="[CURRENCY]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_reason|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_reason" id="payment_reason" value="[PAYMENT_REASON]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_json|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_json" id="payment_json" value="[PAYMENT_JSON]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Liability_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="liability_id" id="liability_id" value="[LIABILITY_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_transaction_group_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_transaction_group_id" id="payment_transaction_group_id" value="[PAYMENT_TRANSACTION_GROUP_ID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Payment_info|}:
                                        </td>
                                        <td>
                                            <input type="text" name="payment_info" id="payment_info" value="[PAYMENT_INFO]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Created_at|}:
                                        </td>
                                        <td>
                                            <input type="text" name="created_at" id="created_at" value="[CREATED_AT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Doc_typ|}:
                                        </td>
                                        <td>
                                            <input type="text" name="doc_typ" id="doc_typ" value="[DOC_TYP]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Doc_id|}:
                                        </td>
                                        <td>
                                            <input type="text" name="doc_id" id="doc_id" value="[DOC_ID]" size="20">
                                        </td>
                                    </tr>
                                    
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div> -->
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
    <!-- Example for 2nd tab
    <div id="tabs-2">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-12 col-md-12 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|...|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    ...
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
    -->
</div>

