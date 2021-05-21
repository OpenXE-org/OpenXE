<?xml version="1.0" encoding="UTF-8"?><xml>
{*START PREPARING*}
 {assign var=orderData value=$cart->order}

{*PREPARE ADDRESS*}
 {assign var=billingAddressId value=$orderData->attributes->billingAddressId}
{*END PREPARING*}

{*START FILLING THE CART*}
 <auftrag><![CDATA[{$orderData->id}]]></auftrag>
 <onlinebestellnummer><![CDATA[{$orderData->attributes->orderNumber}]]></onlinebestellnummer>
 <gesamtsumme><![CDATA[{$orderData->attributes->amountTotal}]]></gesamtsumme>
 <zahlungsweise><![CDATA[{$orderData->paymentMethod->data->attributes->name}]]></zahlungsweise>
 <bestelldatum><![CDATA[{$orderData->attributes->orderDate|truncate:10:""}]]></bestelldatum>
 <versandkostenbrutto><![CDATA[{$orderData->attributes->shippingTotal}]]></versandkostenbrutto>
 {foreach key=customerKey item=customer from=$orderData->customer->data}
  <email><![CDATA[{$customer->attributes->email}]]></email> 
 {/foreach}
 <lieferung></lieferung> 
 {foreach key=shippingKey item=shipping from=$orderData->shippingMethod->data}
  <lieferung><![CDATA[{$shipping->attributes->name}]]></lieferung> 
 {/foreach}
 <transaktionsnummer></transaktionsnummer>
 {foreach key=addressKey item=address from=$orderData->transactions->data}
   <transaktionsnummer><![CDATA[{$orderData->transactionId}]]></transaktionsnummer>
 {/foreach}

{*ADDRESS LOGIC*}
 <anrede>herr</anrede>
 {foreach key=salutationKey item=salutation from=$orderData->addresses->included}
  {if $salutation->type == "salutation"}
   {if $salutation->attributes->salutationKey != 'mr'}
    <anrede>frau</anrede>
   {/if}
  {/if}
 {/foreach}

 {foreach key=addressKey item=address from=$orderData->addresses->data}
  {if $address->id == $billingAddressId}
    {if $address->attributes->company != ""}
    <anrede>firma</anrede>
    <name><![CDATA[{$address->attributes->company}]]></name>
    <ansprechpartner><![CDATA[{$address->attributes->firstName} {$address->attributes->lastName}]]></ansprechpartner>
   {else}
    <name><![CDATA[{$address->attributes->firstName} {$address->attributes->lastName}]]></name>  
   {/if}
   <strasse><![CDATA[{$address->attributes->street}]]></strasse>
   <plz><![CDATA[{$address->attributes->zipcode}]]></plz>
   <ort><![CDATA[{$address->attributes->city}]]></ort>
   <ustid><![CDATA[{$address->attributes->vatId}]]></ustid> 
   {foreach key=countryKey item=country from=$orderData->addresses->included}
    {if $country->id == $address->attributes->countryId}
     <land><![CDATA[{$country->attributes->iso}]]></land>
    {/if}
   {/foreach}
  {else}
    {if $address->attributes->company != ""}
    <lieferadresse_name><![CDATA[{$address->attributes->company}]]></lieferadresse_name>
    <lieferadresse_ansprechpartner><![CDATA[{$address->attributes->firstName} {$address->attributes->lastName}]]></lieferadresse_ansprechpartner>
   {else}
    <lieferadresse_name><![CDATA[{$address->attributes->firstName} {$address->attributes->lastName}]]></lieferadresse_name>  
   {/if}
   <lieferadresse_strasse><![CDATA[{$address->attributes->street}]]></lieferadresse_strasse>
   <lieferadresse_plz><![CDATA[{$address->attributes->zipcode}]]></lieferadresse_plz>
   <lieferadresse_ort><![CDATA[{$address->attributes->city}]]></lieferadresse_ort>
   <lieferadresse_ustid><![CDATA[{$address->attributes->vatId}]]></lieferadresse_ustid> 
   {foreach key=countryKey item=country from=$orderData->addresses->included}
    {if $country->id == $address->attributes->countryId}
     <lieferadresse_land><![CDATA[{$country->attributes->iso}]]></lieferadresse_land>
    {/if}
   {/foreach}
  {/if}
 {/foreach}

{*ITEM LOGIC*}
 <articlelist>
  {foreach key=lineItemKey item=lineItem from=$orderData->lineItems->data}
   <{$lineItemKey}>
   <articleid><![CDATA[{$lineItem->attributes->payload->productNumber}]]></articleid>
   <name><![CDATA[{$lineItem->attributes->label}]]></name>
   <quantity><![CDATA[{$lineItem->attributes->quantity}]]></quantity>
   <price><![CDATA[{$lineItem->attributes->price->unitPrice}]]></price>
   {foreach key=taxKey item=tax from=$lineItem->attributes->price->calculatedTaxes}
    <steuersatz><![CDATA[{$tax->taxRate}]]></steuersatz>
   {/foreach}
   </{$lineItemKey}>
  {/foreach}
 </articlelist>

</xml>
