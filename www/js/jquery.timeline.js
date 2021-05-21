/*!
 * jQuery UI Widget-factory plugin boilerplate (for 1.8/9+)
 * Author: @addyosmani
 * Further changes: @peolanha
 * Licensed under the MIT license
 */

;(function ($, window, document, undefined) {

	// define your widget under a namespace of your choice
	//  with additional parameters e.g.
	// $.widget( "namespace.widgetname", (optional) - an
	// existing widget prototype to inherit from, an object
	// literal to become the widget's prototype );
	$.widget("grantorino.timeline", {
		//Options to be used as defaults
		options: {
			someValue: null
		},
		_tpl_event: ['',
		  '<li class="tl-item">',
			'  <div class="tl-wrap {{class}}">',
			'    <span class="tl-date">{{time}} - {{username}}</span>',
			'    <div class="tl-content panel padder b-a">',
			'      <span class="arrow left pull-up"></span>',
			'      <div id="TimeLineItem_{{timeline_id}}">',
			'        <div class="timeline-message">{{contentHtml}}</div>',
			'        <textarea id="editTimeline_{{editTimeline_id}}" class="timeline-textarea">{{contentRaw}}</textarea>',
			'        <div class="timeline-buttons">' +
			'          <a class="SaveTimeLineBtn" href="#" onclick="SaveTimelineItem({{timeline_save_id}})"><img src="themes/new/images/haken.png" border="0"></a>&nbsp;',
			'          <a class="EditTimeLineBtn {{timeline_edit_fix}}" href="#" onclick="EditTimelineItem({{timeline_edit_id}})"><img src="themes/new/images/edit.svg" border="0"></a>&nbsp;',
			'          <a class="DeleteTimeLineBtn {{timeline_delete_fix}}" href="#" onclick="DeleteTimelineItem({{timeline_delete_id}})"><img src="themes/new/images/delete.svg" border="0"></a></div>',
			'        </div>',
			'      </div>',
			'  </div>',
			'</li>'
		].join('\n'),
		//Setup widget (eg. element creation, apply theming
		// , bind events etc.)
		_create: function () {
			// _create will automatically run the first time
			// this widget is called. Put the initial widget
			// setup code here, then you can access the element
			// on which the widget was called via this.element.
			// The options defined above can be accessed
			// via this.options this.element.addStuff();
			// 
			// 
			this._buildContainer();
			this._buildTimeline();
		},
		// Destroy an instantiated plugin and clean up
		// modifications the widget has made to the DOM
		destroy: function () {
			// this.element.removeStuff();
			// For UI 1.8, destroy must be invoked from the
			// base widget
			$.Widget.prototype.destroy.call(this);

			// For UI 1.9, define _destroy instead and don't
			// worry about
			// calling the base widget
		},
		add: function (event_data) {
			//_trigger dispatches callbacks the plugin user
			// can subscribe to
			// signature: _trigger( "callbackName" , [eventObject],
			// [uiObject] )
			// eg. this._trigger( "hover", e /*where e.type ==
			// "mouseenter"*/, { hovered: $(e.target)});
			// 

			if ($.isArray(event_data)) {
				var that = this;
				$.each(event_data, function (index, tl_event) {
					that.add(tl_event);
				});
			} else {

				this.element.find("ul.timeline").append(
					this._render_event(event_data)
				);
			}

		},
		methodA: function (event) {
			this._trigger("dataChanged", event, {
				key: "someValue"
			});
		},
		_render_event: function (data) {
			data.contentHtml = data.content.replace(/(?:\r\n|\r|\n)/g, '<br>');

			var event_html = this._tpl_event.replace('{{time}}', this._format_time(data.time));
			event_html = event_html.replace('{{contentRaw}}', data.content);
			event_html = event_html.replace('{{contentHtml}}', data.contentHtml);
			event_html = event_html.replace('{{username}}', data.username);
			event_html = event_html.replace('{{editTimeline_id}}', data.id);
			event_html = event_html.replace('{{timeline_id}}', data.id);
			event_html = event_html.replace('{{timeline_save_id}}', data.id);
			event_html = event_html.replace('{{timeline_edit_id}}', data.id);
			event_html = event_html.replace('{{timeline_delete_id}}', data.id);
			if (data.fix == '1') {
				event_html = event_html.replace('{{timeline_edit_fix}}', 'wiedervorlage_timeline_hidden');
				event_html = event_html.replace('{{timeline_delete_fix}}', 'wiedervorlage_timeline_hidden');
			}
			event_html.replace('{{class}}', data.css);
			return event_html;
		},
		_format_time: function (time) {
			var hours = time.getHours();
			var minutes = time.getMinutes();
			var month = time.getMonth() + 1;
			var day = time.getDate();
			month = month < 10 ? '0' + month : month;
			minutes = minutes < 10 ? '0' + minutes : minutes;
			day = day < 10 ? '0' + day : day;

			return (day + '.' + month + '.' + time.getFullYear() + ' ' + hours + ':' + minutes);
		},

		_buildTimeline: function () {
			var that = this;
			$.each(this.options.data, function (index, tl_event) {
				that.element.find("ul.timeline").append(that._render_event(tl_event));
			});
		},
		_buildContainer: function () {
			this.element.append('<ul class="timeline"></ul>');
		},
		// Respond to any changes the user makes to the
		// option method
		_setOption: function (key, value) {
			switch (key) {
				case "someValue":
					//this.options.someValue = doSomethingWith( value );
					break;
				default:
					//this.options[ key ] = value;
					this._create();
					break;
			}
			// For UI 1.8, _setOption must be manually invoked
			// from the base widget
			$.Widget.prototype._setOption.apply(this, arguments);
			// For UI 1.9 the _super method can be used instead
			// this._super( "_setOption", key, value );
		}
	});
})(jQuery, window, document);