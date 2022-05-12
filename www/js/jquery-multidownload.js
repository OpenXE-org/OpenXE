;(function($, window, document, undefined) {
  "use strict"

  var download = function (options) {
    var triggerDelay = (options && options.delay) || 100
    var cleaningDelay = (options && options.cleaningDelay) || 1000

    this.each(function (index, item) {
      createIFrame(item, index * triggerDelay, cleaningDelay)
    })
    return this
  }

  var createIFrame = function (item, triggerDelay, cleaningDelay) {
    setTimeout(function () {
      var frame = $('<iframe style="display: none;" class="multi-download-frame"></iframe>')

      frame.attr('src', $(item).attr('href') || $(item).attr('src'))
      $(item).after(frame)

      setTimeout(function () { frame.remove() }, cleaningDelay)
    }, triggerDelay)
  }

  $.fn.multiDownload = function(options) {
      return download.call(this, options)
  }

})(jQuery, window, document);
