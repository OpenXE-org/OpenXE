/*global $ */

/**
 * This is a function to load content from an endpoint or add an iframe inside an element.
 *
 * Instructions:
 * 1. The container element needs to have the class "lazy-load-component"
 *    example: <div class="lazy-load-component"></div>
 *
 * 2. a) You can define the url to the iframe you want to load inside your container
 *    example: <div class="lazy-load-component" data-lazy-iframe-src="https://xentral.com/"></div>
 * 2. b) You can define an endpoint where HTML can be fetched
 *    example: <div class="lazy-load-component" data-lazy-inline-src="endpointURL"></div>
 *
 * 3. Trigger the "lazyLoadContent" event on your container to load the content
 *    example: $("#my-nice-container").trigger("lazyLoadContent");
 *
 */

var lazyLoad = function () {
  'use strict';

  var me = {
    elem: {
      $self: null
    },

    settings: {
      lazyIframeSrc: null,
      lazyInlineSrc: null
    },

    init: function (self) {
      me.elem.$self = self;
      me.settings = me.elem.$self.data();

      if (me.settings.lazyIframeSrc) {
        var iframeID = this.createHashFromString(me.settings.lazyIframeSrc);

        this.addInlineCode("<iframe " +
          "id='" + iframeID + "' " +
          "src='" + me.settings.lazyIframeSrc + "'></iframe>",
          iframeID);

      } else if (me.settings.lazyInlineSrc) {
        this.getInlineCode(me.settings.lazyInlineSrc);
      }
    },

    /**
     *
     * @param {string}html
     * @param {string} iframeID
     */
    addInlineCode: function (html, iframeID) {
      var self = this;

      me.elem.$self.html(html);

      if(iframeID){
        $("#" + iframeID).on("load", function () {
          var $self = $(this);

          // timeout is necessary because we don't know when the table inside the iframe is done loading
          window.setTimeout(function () {
            self.resizeIframe($self);
          }, 500);
        })
      }
    },

    /**
     *
     * @param {string} endpoint
     */
    getInlineCode: function (endpoint) {
      var self = this;

      $.ajax({
        url: endpoint,
        type: 'GET',
        success: function (data) {
          self.addInlineCode(data);
        },
        fail: function () {
          console.warn("inline code could not be loaded for endpoint " + endpoint);
        }
      });
    },

    /**
     *
     * @param {Object} iframe
     */
    resizeIframe: function (iframe) {
      iframe[0].height = iframe[0].contentWindow.document.body.scrollHeight + "px";
    },

    /**
     *
     * @param {string} string
     * @returns {number}
     */
    createHashFromString: function (string) {
      var hash = 0, i, chr;
      for (i = 0; i < string.length; i++) {
        chr   = string.charCodeAt(i);
        hash  = ((hash << 5) - hash) + chr;
        hash |= 0; // Convert to 32bit integer
      }
      return hash * -1;
    }
  };

  return me.init($(this));
}

$(function () {
  $(".lazy-load-component").on("lazyLoadContent", lazyLoad);

  $(document).on("click touch", "#accordion .group:not(.lazy-loaded)", function(e) {
    $(this).find(".lazy-load-component").trigger("lazyLoadContent");

    $(this).addClass("lazy-loaded");
  });
});
