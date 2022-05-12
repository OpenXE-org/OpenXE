/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
  // Define changes to default configuration here. For example:
  // config.language = 'fr';
  //config.uiColor = '#AADC6E';
  config.extraPlugins = 'font';
  config.font_names = 'Arial; Times; Courier;';
  config.enterMode = 2;
  config.forceEnterMode = false;
  config.shiftEnterMode = 2;
  config.FormatSource = false;
  config.toolbarGroups = [
  { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
  { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
  { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
  { name: 'editing', groups: [ 'find', 'selection', 'editing' ] },
  { name: 'forms', groups: [ 'forms' ] },
  { name: 'links', groups: [ 'links' ] },
  { name: 'insert', groups: [ 'insert' ] },
  '/',
  { name: 'styles', groups: [ 'styles' ] },
  { name: 'colors', groups: [ 'colors' ] },
  { name: 'tools', groups: [ 'tools' ] },
  { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
  { name: 'others', groups: [ 'others' ] },
  { name: 'about', groups: [ 'about' ] }
  ];

  config.removeButtons = 'Subscript,Superscript,Textarea,Form,BidiLtr,BidiRtl,Language,Flash,Smiley,PageBreak,Iframe,SelectAll,NewPage,Save,Templates,Checkbox,Radio,TextField,Button,Select,ImageButton,HiddenField,Outdent,Indent,Blockquote,CreateDiv,Print,Preview,ShowBlocks,About';

  config.removePlugins = 'elementspath'; 
};

CKEDITOR.on( 'instanceReady', function( ev ) {
  var blockTags = ['div','h1','h2','h3','h4','h5','h6','p','pre','li','blockquote','ul','ol','table','thead','tbody','tfoot','td','th','br',];
  var rules = {
    indent : false,
    breakBeforeOpen : false,
    breakAfterOpen : false,
    breakBeforeClose : false,
    breakAfterClose :false 
  };

for (var i=0; i<blockTags.length; i++) {
ev.editor.dataProcessor.writer.setRules( blockTags[i], rules );
}
}); 
