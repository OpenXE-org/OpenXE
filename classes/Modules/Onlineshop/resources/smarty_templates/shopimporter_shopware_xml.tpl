<?xml version="1.0" encoding="UTF-8"?><xml>
 {*START PREPARING*}
 {assign var=orderData value=$cart->order}
 {*END PREPARING*}

 {*START FILLING THE CART*}
 <auftrag><![CDATA[{$orderData->id}]]></auftrag>
 <subshop><![CDATA[{$orderData->shopId}]]></subshop>
 <onlinebestellnummer><![CDATA[{$orderData->number}]]></onlinebestellnummer>
 <gesamtsumme><![CDATA[{$orderData->invoiceAmount}]]></gesamtsumme>
 <zahlungsweise><![CDATA[{$orderData->payment->name}]]></zahlungsweise>
 <bestelldatum><![CDATA[{$orderData->orderTime|truncate:10:""}]]></bestelldatum>
 <versandkostenbrutto><![CDATA[{$orderData->invoiceShipping}]]></versandkostenbrutto>
 <email><![CDATA[{$orderData->customer->email}]]></email>
 <lieferung><![CDATA[{$orderData->dispatch->name}]]></lieferung>
 <transaktionsnummer><![CDATA[{$orderData->transactionId}]]></transaktionsnummer>
 <waehrung><![CDATA[{$orderData->currency}]]></waehrung>

 {*ADDRESS LOGIC*}
 <anrede>herr</anrede>
 {if $orderData->billing->salutation == 'mrs'}
  <anrede>frau</anrede>
 {/if}
 {if $orderData->billing->company != ""}
  <anrede>firma</anrede>
  <name><![CDATA[{$orderData->billing->company}]]></name>
  <ansprechpartner><![CDATA[{$orderData->billing->firstName} {$orderData->billing->lastName}]]></ansprechpartner>
 {else}
  <name><![CDATA[{$orderData->billing->firstName} {$orderData->billing->lastName}]]></name>
 {/if}
 <strasse><![CDATA[{$orderData->billing->street}]]></strasse>
 <plz><![CDATA[{$orderData->billing->zipCode}]]></plz>
 <ort><![CDATA[{$orderData->billing->city}]]></ort>
 <ustid><![CDATA[{$orderData->billing->vatId}]]></ustid>
 <land><![CDATA[{$orderData->billing->country->iso}]]></land>
 {if $orderData->shipping->firstName != $orderData->billing->firstName || $orderData->shipping->street != $orderData->billing->street || $orderData->shipping->country->iso != $orderData->billing->country->iso}
  {if $address->attributes->company != ""}
   <lieferadresse_name><![CDATA[{$orderData->shipping->company}]]></lieferadresse_name>
   <lieferadresse_ansprechpartner><![CDATA[{$orderData->shipping->firstName} {$orderData->shipping->lastName}]]></lieferadresse_ansprechpartner>
  {else}
   <lieferadresse_name><![CDATA[{$orderData->shipping->firstName} {$orderData->shipping->lastName}]]></lieferadresse_name>
  {/if}
  <lieferadresse_strasse><![CDATA[{$orderData->shipping->street}]]></lieferadresse_strasse>
  <lieferadresse_plz><![CDATA[{$orderData->shipping->zipCode}]]></lieferadresse_plz>
  <lieferadresse_ort><![CDATA[{$orderData->shipping->city}]]></lieferadresse_ort>
  <lieferadresse_ustid><![CDATA[{$orderData->shipping->vatId}]]></lieferadresse_ustid>
  <lieferadresse_land><![CDATA[{$orderData->shipping->country->iso}]]></lieferadresse_land>
 {/if}

 {*ITEM LOGIC*}
 <articlelist>
  {foreach key=lineItemKey item=detail from=$orderData->details}
   <{$lineItemKey}>
    <articleid><![CDATA[{$detail->articleNumber}]]></articleid>
    <name><![CDATA[{$detail->articleName}]]></name>
    <quantity><![CDATA[{$detail->quantity}]]></quantity>
    <price><![CDATA[{$detail->price}]]></price>
    <steuersatz><![CDATA[{$detail->taxRate}]]></steuersatz>
   </{$lineItemKey}>
  {/foreach}
 </articlelist>

</xml>
