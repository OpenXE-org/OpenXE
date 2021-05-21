<?xml version="1.0" encoding="UTF-8"?><xml>
{*START PREPARING*}
 {assign var=orderData value=$cart->orderData}
 {assign var=transactionNumber1 value=$orderData->token}
 {assign var=transactionNumber2 value=$orderData->transactions->token}
 {assign var=timeStamp value=$orderData->created_at|truncate:20:" "}

{*PREPARE BILLING ADDRESS*}
 {assign var=billingAddressFirstName value=$orderData->billing_address->first_name}
 {assign var=billingAddressLastName value=$orderData->billing_address->last_name}
 {assign var=billingAddressName value="$billingAddressFirstName $billingAddressLastName"}
 {assign var=billingAddress1 value=$orderData->billing_address->address1}
 {assign var=billingAddress2 value=$orderData->billing_address->address2}
 {if $billingAddress2}
  {assign var=billingAddress value="$billingAddress1 $billingAddress2"}
 {else}
  {assign var=billingAddress value="$billingAddress1"}
 {/if}

{*PREPARE SHIPPING ADDRESS*}
 {assign var=shippingAddressFirstName value=$orderData->shipping_address->first_name}
 {assign var=shippingAddressLastName value=$orderData->shipping_address->last_name}
 {assign var=shippingAddressName value="$shippingAddressFirstName $shippingAddressLastName"}
 {assign var=shippingAddress1 value=$orderData->shipping_address->address1}
 {assign var=shippingAddress2 value=$orderData->shipping_address->address2}
 {if $shippingAddress2 != ""}
  {assign var=shippingAddress value="$shippingAddress1 $shippingAddress2"}
 {else}
  {assign var=shippingAddress value="$shippingAddress1"}
 {/if}
{*END PREPARING*}

{*START FILLING THE CART*}
 <auftrag><![CDATA[{$orderData->id}]]></auftrag>
 <onlinebestellnummer><![CDATA[{$orderData->order_number}]]></onlinebestellnummer>
 {if $transactionNumber2 != ""}
  <transaktionsnummer><![CDATA[{$transactionNumber2}]]></transaktionsnummer>
 {else}
  <transaktionsnummer><![CDATA[{$transactionNumber1}]]></transaktionsnummer>
 {/if}
 <gesamtsumme><![CDATA[{$orderData->total_price}]]></gesamtsumme>
 <zahlungsweise><![CDATA[{$orderData->gateway}]]></zahlungsweise>
 <waehrung><![CDATA[{$orderData->currency}]]></waehrung>
 <bestelldatum><![CDATA[{$timeStamp|truncate:10:""}]]></bestelldatum>
 <zeitstempel><![CDATA[{$timeStamp|replace:"T":" "}]]></zeitstempel>
  {foreach key=key item=item from=$orderData->shipping_lines}
   <versandkostenbrutto><![CDATA[{$item->price}]]></versandkostenbrutto>
  {/foreach}
  {foreach key=noteKey item=note from=$orderData->note_attributes}
   {if $note->name == "vat_id"}
    <ustid>$note->value}]]></ustid>
   {/if}
  {/foreach}
 {if $orderData->taxes_included == 1}
  <rabattbrutto><![CDATA[{$orderData->total_discounts}]]></rabattbrutto>
 {else}
  <rabattnetto><![CDATA[{$orderData->total_discounts}]]></rabattnetto>
 {/if}

{*ADDRESS LOGIC*}
 <anrede>herr</anrede>
 {if $orderData->billing_address->company != ""}
  <anrede>firma</anrede>
  <name><![CDATA[{$orderData->billing_address->company}]]></name>
  <ansprechpartner><![CDATA[{$billingAddressName}]]></ansprechpartner>
 {else}
  <name><![CDATA[{$billingAddressName}]]></name>  
 {/if}
 <strasse><![CDATA[{$billingAddress}]]></strasse>
 <plz><![CDATA[{$orderData->billing_address->zip}]]></plz>
 <ort><![CDATA[{$orderData->billing_address->city}]]></ort>
 <land><![CDATA[{$orderData->billing_address->country_code}]]></land>
 <email><![CDATA[{$orderData->contact_email}]]></email>
 <telefon><![CDATA[{$orderData->billing_address->phone}]]></telefon>
 {if $billingAddressName != $shippingAddressName || $billingAddress != $shippingAddress}
  <abweichendelieferadresse>1</abweichendelieferadresse>
  {if $orderData->shipping_address->company != ""}
   <lieferadresse_name><![CDATA[{$orderData->shipping_address->company}]]></lieferadresse_name>
   <lieferadresse_ansprechpartner><![CDATA[{$shippingAddressName}]]></lieferadresse_ansprechpartner>
  {else}
   <lieferadresse_name><![CDATA[{$shippingAddressName}]]></lieferadresse_name>  
  {/if}
  <lieferadresse_strasse><![CDATA[{$shippingAddress}]]></lieferadresse_strasse>
  <lieferadresse_plz><![CDATA[{$orderData->shipping_address->zip}]]></lieferadresse_plz>
  <lieferadresse_ort><![CDATA[{$orderData->shipping_address->city}]]></lieferadresse_ort>
  <lieferadresse_land><![CDATA[{$orderData->shipping_address->country_code}]]></lieferadresse_land>
  <telefon><![CDATA[{$orderData->shipping_address->phone}]]></telefon>
 {/if}

{*ITEM LOGIC*}
 <articlelist>
  {foreach key=lineItemKey item=lineItem from=$orderData->line_items}
   <{$lineItemKey}>
   <articleid><![CDATA[{$lineItem->sku}]]></articleid>
   <fremdnummer><![CDATA[{$lineItem->variant_id}]]></fremdnummer>
   <name><![CDATA[{$lineItem->name}]]></name>
   <quantity><![CDATA[{$lineItem->quantity}]]></quantity>
   <price><![CDATA[{$lineItem->price}]]></price>
   {foreach key=taxKey item=taxItem from=$lineItem->tax_lines}
    <steuersatz><![CDATA[{$taxItem->rate * 100}]]></steuersatz>
   {/foreach}
   </{$lineItemKey}>
  {/foreach}
 </articlelist>

</xml>
