
tinyMCE.init({
  theme: "modern",
  menubar: false,
  statusbar : false,
  toolbar_items_size: 'small',
  mode : "exact",
  editor_deselector : "mceNoEditor",
  width : "100%",
  entity_encoding : "raw",
  element_format : "html",
  force_br_newlines : true,
  force_p_newlines : false,
 
paste_data_images: true,

  force_br_newlines : true,
  force_p_newlines : false,
 paste_preprocess: function(plugin, args) {
var re = /<br>/gi;
args.content = args.content.replace(re, "<div>"); 
        args.content = args.content;
    },

  elements : "links_v,beschreibung_v,uebersicht_v,name_vi,infoauftragserfassung,rabattinformation,sonstiges,internebemerkung,beschreibung_html,mandatsreferenzhinweis",
  plugins: [ "link fullscreen code image textcolor paste" ],

   toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontsizeselect | cut copy paste | searchreplace | bullist numlist | outdent indent | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor | restoredraft  | print fullscreen",
        toolbar2: "",
        toolbar3: ""

});

tinyMCE.init({
  theme: "modern",
  menubar: false,
  statusbar : false,
  toolbar_items_size: 'small',
  mode : "exact",
  editor_deselector : "mceNoEditor",
  width : "100%",
  entity_encoding : "raw",
  element_format : "html",
  force_br_newlines : true,
  force_p_newlines : false,
  forced_root_block: false,
paste_data_images: true,

  elements : "beschreibung_de,beschreibung_en,uebersicht_de,uebersicht_en,links_de,links_en,startseite_de,startseite_en",
  plugins: [ "link fullscreen code image textcolor paste" ],

   toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontsizeselect | cut copy paste | searchreplace | bullist numlist | outdent indent | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor | restoredraft  | print fullscreen",
        toolbar2: "",
        toolbar3: ""

});

tinyMCE.init({
  theme: "modern",
  menubar: false,
  toolbar_items_size: 'small',
  mode : "exact",
  width : "100%",
  entity_encoding : "raw",
  element_format : "html",
  elements : "readonlybox,readonlybox2",
  plugins : "",
	menubar: false,
  statusbar: false,
  toolbar: false,
  readonly: 1,
  theme_modern_buttons1 : "",
  theme_modern_buttons1_add : "",
  theme_modern_buttons2 : "",
  theme_modern_buttons3 : ""
});


tinyMCE.init({
  theme: "modern",
  menubar: false,
  toolbar_items_size: 'small',
  statusbar: false,
  mode : "exact",
  entity_encoding : "raw",
  element_format : "html",
  elements : "freitext,angebot_header,angebot_footer,auftrag_header,auftrag_footer,rechnung_header,rechnung_footer,lieferschein_header,lieferschein_footer,gutschrift_header,gutschrift_footer,bestellung_header,bestellung_footer,arbeitsnachweis_header,arbeitsnachweis_footer,provisionsgutschrift_header,provisionsgutschrift_footer",
  plugins : "",
inline_styles: false,
formats : {
        underline : {inline : 'u', exact : true}
        },
  toolbar1 : "bold,italic,underline"
});


tinyMCE.init({
  theme: "modern",
  menubar: false,
  toolbar_items_size: 'small',
 
  mode : "exact",
  entity_encoding : "raw",
  element_format : "html",
  elements : "emailtext,quickantwort",
  plugins : "textcolor",
  theme_modern_buttons1 : "bold,italic,forecolor,formatselect,link,unlink,bullist,numlist",
  theme_modern_buttons1_add : "removeformat,code,fullscreen",
  theme_modern_buttons2 : "",
  theme_modern_buttons3 : ""
});


tinyMCE.init({
  theme: "modern",
  mode : "exact",
  menubar: false,
  toolbar_items_size: 'small',
 
  entity_encoding : "raw",
  element_format : "html",
  elements : "uebersichtstext",
  plugins : "textcolor",
  theme_modern_buttons1 : "bold,italic,forecolor,formatselect,link,unlink,bullist,numlist",
  theme_modern_buttons1_add : "removeformat,code,fullscreen",
  theme_modern_buttons2 : "",
  theme_modern_buttons3 : ""
});

tinyMCE.init({
  theme: "modern",
  mode : "exact",
  menubar: false,
  toolbar_items_size: 'small',
 
  entity_encoding : "raw",
  element_format : "html",
  elements : "html",
  plugins : "textcolor",
  theme_modern_buttons1 : "bold,italic,forecolor,formatselect,link,unlink,bullist,numlist",
  theme_modern_buttons1_add : "removeformat,code,fullscreen,image,table",
  theme_modern_buttons2 : "hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
  theme_modern_buttons3 : ""
});

tinyMCE.init({
  theme: "modern",
  mode : "exact",
  menubar: false,
  toolbar_items_size: 'small',
  entity_encoding : "raw",
  element_format : "html",
  elements : "ticket_nachricht",
  visual : false,
  readonly : 1,
  theme_modern_buttons1 : "",
  theme_modern_buttons2 : "",
  theme_modern_buttons3 : ""
});

tinyMCE.init({
  theme: "modern",
  mode : "exact",
  menubar: false,
  toolbar_items_size: 'small',
 
  entity_encoding : "raw",
  element_format : "html",
  elements : "le_uebersicht",
  plugins : "textcolor",
  theme_modern_buttons1 : "bold,italic,forecolor,formatselect,link,unlink,bullist,numlist",
  theme_modern_buttons1_add : "removeformat,code,fullscreen",
  theme_modern_buttons2 : "",
  theme_modern_buttons3 : ""
});

tinyMCE.init({
  theme: "modern",
  mode : "exact",
  menubar: false,
  toolbar_items_size: 'small',
 
  entity_encoding : "raw",
  element_format : "html",
  elements : "le_beschreibung",
  plugins : "textcolor",
  theme_modern_buttons1 : "bold,italic,forecolor,formatselect,link,unlink,bullist,numlist",
  theme_modern_buttons1_add : "removeformat,code,fullscreen",
  theme_modern_buttons2 : "",
  theme_modern_buttons3 : ""
});

tinyMCE.init({
  theme: "modern",
  mode : "exact",
  menubar: false,
  toolbar_items_size: 'small',
 
  entity_encoding : "raw",
  element_format : "html",
  elements : "le_links",
  plugins : "textcolor",
  theme_modern_buttons1 : "bold,italic,forecolor,formatselect,link,unlink,bullist,numlist",
  theme_modern_buttons1_add : "removeformat,code,fullscreen",
  theme_modern_buttons2 : "",
  theme_modern_buttons3 : ""
});

tinyMCE.init({
  theme: "modern",
  mode : "exact",
  menubar: false,
  toolbar_items_size: 'small',
 
  entity_encoding : "raw",
  element_format : "html",
  elements : "content",
  width : "100%",
  paste_data_images: true,
  plugins: [
                "link fullscreen code image table textcolor"
        ],

   toolbar1: "bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontsizeselect | cut copy paste | searchreplace | bullist numlist | outdent indent | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor | restoredraft | table | print fullscreen",
        toolbar2: "",
        toolbar3: ""

});

