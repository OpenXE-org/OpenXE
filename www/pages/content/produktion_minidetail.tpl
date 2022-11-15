<div class="row">
    <div class="col-xs-12 col-md-6 col-md-height">
	    <div class="inside inside-full-height">
            <legend>{|Produktionsfortschritt|}</legend>   
            <div class="inside inside-full-height">
                <table width="100%" border="0">
                    <tr [ARTIKEL_MENGE_VISIBLE]>
                        <td>{|Geplant|}:</td>
                        <td>[MENGE_GEPLANT]</td>
                        <td>{|Offen:|}</td>
                        <td>[MENGE_OFFEN]</td>
                    </tr>
                    <tr [ARTIKEL_MENGE_VISIBLE]>
                        <td>{|Produziert|}:</td>
                        <td>[MENGE_PRODUZIERT]</td>
                        <td>{|Reserviert:|}</td>
                        <td>[MENGE_RESERVIERT]</td>
                    </tr>
                    <tr [ARTIKEL_MENGE_VISIBLE]>
                        <td>{|Erfolgreich|}:</td>
                        <td>[MENGEERFOLGREICH]</td>
                        <td>{|Produzierbar:|}</td>
                        <td>[MENGE_PRODUZIERBAR]</td>
                    </tr>
                    </tr>
                        <td>{|Ausschuss|}:</td>                                    
                        <td>[MENGEAUSSCHUSS]</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>	        		
    <div class="col-xs-12 col-md-6 col-md-height">
	    <div class="inside inside-full-height">
            <legend>{|Protokoll|}</legend>   
            <div class="inside inside-full-height">
                [PROTOKOLL]
            </div>
        </div>
    </div>
</div>	        	



