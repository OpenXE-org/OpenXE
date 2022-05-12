<?xml version="1.0" encoding="UTF-8"?><xml>
 {*START PREPARING*}
 {assign var=orderData value=$cart->order}
 {*END PREPARING*}

 {*START FILLING THE CART*}
 <auftrag><![CDATA[{$orderData->id}]]></auftrag>
 <onlinebestellnummer><![CDATA[{$orderData->id}]]></onlinebestellnummer>
 <zahlungsweise><![CDATA[{$orderData->paymentType->module}]]></zahlungsweise>
 <bestelldatum><![CDATA[{$orderData->purchaseDate|truncate:10:""}]]></bestelldatum>
 <email><![CDATA[{$orderData->customer->email}]]></email>
 <lieferung><![CDATA[{$orderData->shippingType->module}]]></lieferung>
 <waehrung><![CDATA[{$orderData->currencyCode}]]></waehrung>

 {foreach key=detailKey item=detail from=$orderData->totals}
  {if $detail->class == "ot_total"}
   <gesamtsumme><![CDATA[{$detail->value}]]></gesamtsumme>
  {/if}
  {if $detail->class == "ot_shipping"}
   <versandkostenbrutto><![CDATA[{$detail->value}]]></versandkostenbrutto>
  {/if}
  {if $detail->class == "ot_discount"}
   <rabattbrutto><![CDATA[{$detail->value}]]></rabattbrutto>
  {/if}
 {/foreach}

 <transaktionsnummer><![CDATA[{$orderData->transactionId}]]></transaktionsnummer>

 {*ADDRESS LOGIC*}
 <anrede>herr</anrede>
 {if $orderData->addresses->billing->gender == 'f'}
  <anrede>frau</anrede>
 {/if}
 <name><![CDATA[{$orderData->addresses->billing->firstname} {$orderData->addresses->billing->lastname}]]></name>
 {if $orderData->addresses->billing->company != ""}
  <anrede>firma</anrede>
  <name><![CDATA[{$orderData->addresses->billing->company}]]></name>
  <ansprechpartner><![CDATA[{$orderData->addresses->billing->firstname} {$orderData->addresses->billing->lastname}]]></ansprechpartner>
 {/if}
 <strasse><![CDATA[{$orderData->addresses->billing->street}]]></strasse>
 <plz><![CDATA[{$orderData->addresses->billing->postcode}]]></plz>
 <ort><![CDATA[{$orderData->addresses->billing->city}]]></ort>
 <ustid><![CDATA[{$orderData->customer->vatId}]]></ustid>
 <land><![CDATA[{$orderData->country->iso2}]]></land>
 {if $orderData->addresses->billing->firstname != $orderData->addresses->delivery->firstname || $orderData->addresses->billing->street != $orderData->addresses->delivery->street || $orderData->addresses->billing->city !=$orderData->addresses->delivery->city}
  <lieferadresse_name><![CDATA[{$orderData->addresses->delivery->firstname} {$orderData->addresses->delivery->lastname}]]></lieferadresse_name>
  {if $orderData->addresses->delivery->company != ""}
   <lieferadresse_name><![CDATA[{$orderData->addresses->delivery->company}]]></lieferadresse_name>
   <lieferadresse_ansprechpartner><![CDATA[{$orderData->addresses->delivery->firstname} {$orderData->addresses->delivery->lastname}]]></lieferadresse_ansprechpartner>
  {/if}
  <lieferadresse_strasse><![CDATA[{$orderData->addresses->delivery->street}]]></lieferadresse_strasse>
  <lieferadresse_plz><![CDATA[{$orderData->addresses->delivery->postcode}]]></lieferadresse_plz>
  <lieferadresse_ort><![CDATA[{$orderData->addresses->delivery->city}]]></lieferadresse_ort>
  <lieferadresse_ustid><![CDATA[{$orderData->addresses->delivery->vatId}]]></lieferadresse_ustid>
  <lieferadresse_land><![CDATA[{$orderData->deliveryCountry->iso2}]]></lieferadresse_land>
 {/if}

 {*ITEM LOGIC*}
 <articlelist>
 {foreach key=lineItemKey item=detail from=$orderData->items}
  <{$lineItemKey}>
   <articleid><![CDATA[{$detail->model}]]></articleid>
   <name><![CDATA[{$detail->name}]]></name>
   <quantity><![CDATA[{$detail->quantity}]]></quantity>
   <price><![CDATA[{$detail->price}]]></price>
   <steuersatz><![CDATA[{$detail->tax}]]></steuersatz>
  </{$lineItemKey}>
 {/foreach}
 </articlelist>

</xml>
