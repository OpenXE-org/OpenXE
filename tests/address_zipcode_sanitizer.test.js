import assert from 'node:assert';
import fs from 'node:fs';
import path from 'node:path';
import {fileURLToPath} from 'node:url';
import vm from 'node:vm';
import test from 'node:test';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const scriptPath = path.resolve(__dirname, '..', 'classes', 'Modules', 'Address', 'www', 'js', 'address_create.js');

const createElement = (id, value = '') => ({
  id,
  value,
  handlers: {},
  classes: new Set(),
});

const createJQueryStub = (elements, documentElement) => {
  function JQueryCollection(resolvedElements) {
    this.elements = resolvedElements;
    this.length = resolvedElements.length;
  }

  JQueryCollection.prototype.on = function (events, handler) {
    const eventList = events.split(/\s+/);
    this.elements.forEach((element) => {
      eventList.forEach((evt) => {
        if (!element.handlers[evt]) {
          element.handlers[evt] = [];
        }
        element.handlers[evt].push(handler);
      });
    });
    return this;
  };

  JQueryCollection.prototype.ready = function (handler) {
    if (typeof handler === 'function') {
      handler();
    }
    return this;
  };

  JQueryCollection.prototype.val = function (newValue) {
    if (newValue === undefined) {
      return this.elements[0]?.value;
    }
    this.elements.forEach((element) => {
      element.value = newValue;
    });
    return this;
  };

  JQueryCollection.prototype.each = function (callback) {
    this.elements.forEach((element, index) => {
      callback.call(element, index, element);
    });
    return this;
  };

  JQueryCollection.prototype.trigger = function (eventName) {
    this.elements.forEach((element) => {
      const handlers = element.handlers[eventName] || [];
      handlers.forEach((handler) => handler.call(element, {type: eventName}));
    });
    return this;
  };

  JQueryCollection.prototype.addClass = function (className) {
    this.elements.forEach((element) => element.classes.add(className));
    return this;
  };

  JQueryCollection.prototype.removeClass = function (className) {
    this.elements.forEach((element) => element.classes.delete(className));
    return this;
  };

  JQueryCollection.prototype.hasClass = function (className) {
    return this.elements.some((element) => element.classes.has(className));
  };

  JQueryCollection.prototype.next = function () {
    return {
      remove: () => undefined,
    };
  };

  JQueryCollection.prototype.insertAfter = function () {
    return this;
  };

  JQueryCollection.prototype.html = function () {
    return this;
  };

  const resolveSelector = (selector) => {
    if (selector === document || selector === 'document') {
      return [documentElement];
    }

    if (typeof selector === 'string') {
      if (selector.startsWith('<')) {
        return [createElement('generated')];
      }
      const ids = selector.split(',').map((part) => part.trim());
      return ids
        .map((id) => (id.startsWith('#') ? id : `#${id}`))
        .map((id) => elements[id])
        .filter(Boolean);
    }

    if (selector && selector.id && elements[`#${selector.id}`]) {
      return [elements[`#${selector.id}`]];
    }

    return [];
  };

  const jQuery = function (selector) {
    return new JQueryCollection(resolveSelector(selector));
  };

  jQuery.ajax = () => ({
    done: (handler) => {
      handler(false);
      return this;
    },
  });

  return jQuery;
};

const setupEnvironment = (plzValue, rechnungPlzValue) => {
  const elements = {
    '#plz': createElement('plz', plzValue),
    '#rechnung_plz': createElement('rechnung_plz', rechnungPlzValue),
    '#name': createElement('name'),
    '#strasse': createElement('strasse'),
    '#ort': createElement('ort'),
  };

  const documentElement = createElement('document');
  const $ = createJQueryStub(elements, documentElement);

  globalThis.$ = $;
  globalThis.jQuery = $;
  globalThis.document = documentElement;

  const scriptContent = fs.readFileSync(scriptPath, 'utf8');
  vm.runInThisContext(scriptContent, {filename: scriptPath});

  return {elements, $};
};

test('sanitizes zipcode fields on init', () => {
  const {elements} = setupEnvironment(' 12345 ', ' 98765 ');

  assert.equal(elements['#plz'].value, '12345');
  assert.equal(elements['#rechnung_plz'].value, '98765');
});

test('sanitizes zipcode fields on blur', () => {
  const {elements, $} = setupEnvironment('12345 ', '98765 ');

  $(elements['#plz']).val(' 54321 ').trigger('blur');
  $(elements['#rechnung_plz']).val(' 56789 ').trigger('blur');

  assert.equal(elements['#plz'].value, '54321');
  assert.equal(elements['#rechnung_plz'].value, '56789');
});
