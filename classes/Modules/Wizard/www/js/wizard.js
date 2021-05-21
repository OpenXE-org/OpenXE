/**
 * Template function for Wizard objects
 */
var Wizard = function ($) {

    var me = {

        storage: {
            key: null,
            settings: {},
            steps: null,
            title: null,
            activeStep: null,
            minimizedStorageKey: null,
            overlay: null,
            stepGroups: null,
            ajaxRunning: false,
            submitPrevented: null,
            linkPrevented: null,
            minimized: false
        },

        elem: {
            $body: null,
            $container: null,
            $hideTrigger: null,
            $self: null,
            $highlight: null,
            $window: $(window),
            $clickByClickContainer: null
        },

        /**
         * @param {Object} data
         */
        init: function (data) {
            var key = data.key;

            me.storage.key = key;
            me.storage.minimized = data.minimized;
            me.storage.stepGroups = data.step_groups;
            me.storage.settings.title = data.title;
            me.storage.settings.subTitle = data.sub_title;
            me.storage.settings.skipLinkText = data.skip_link_text;

            me.storage.minimizedStorageKey = 'wizard_' + key + '_minimized';

            me.elem.$container = $('#wizard-container');
            me.elem.$hint = $('#wizard-hint');
            me.elem.$hideTrigger = $('#wizard-hide-trigger');
            me.elem.$body = $('body');

            me.generateContainerContents();

            me.nextStep();
        },

        show: function () {
            me.elem.$self.show();
        },

        hide: function () {
            me.elem.$self.slideUp();
        },

        /**
         * @return {boolean}
         */
        isMinimized: function () {
            return me.storage.minimized;
        },

        /**
         * Minimize wizard
         */
        minimize: function () {
            me.storage.minimized = true;
            $('a').unbind("click.wizard");
            $('form').unbind("submit.wizard");
        },

        /**
         * Maximize wizard
         */
        maximize: function () {
            me.storage.minimized = false;
        },

        finishWizard: function() {

            var current, i,
                numberOfStepGroups = Object.keys(me.storage.stepGroups).length,
                numberOfCompletedStepGroups = 0;

            for (var stepGroup in me.storage.stepGroups) {
                if (!me.storage.stepGroups.hasOwnProperty(stepGroup)) {
                    return;
                }

                if(me.storage.stepGroups[stepGroup].completed){
                    numberOfCompletedStepGroups++;
                }
            }

            if(numberOfStepGroups === numberOfCompletedStepGroups){
               me.toggleWizardDetails();
            }
        },

        toggleWizardDetails: function(){
            $('.wizard-details').toggle();
        },

        cancelWizard: function(){
            $.ajax({
                url: 'index.php?module=wizard&action=ajax&cmd=cancel_active_wizard&key=' + me.storage.key,
                method: 'post',
                dataType: 'json',
                success: function (data) {
                    // we don't need to know
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.warn(xhr.status, thrownError);
                }
            })
        },

        /**
         *
         * @param {String} param
         *
         * @returns {String}
         */
        getUrlParameter: function (param) {
            var sPageURL = window.location.search.substring(1),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === param) {
                    return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                }
            }
        },

        /**
         * Register functionality on standard and custom events
         */
        registerEvents: function () {
            me.elem.$self.find('.wizard-step-group h4').on('click touch', function () {
                $(this).parent().toggleClass('closed');
            });

            me.elem.$self.find('.wizard-small-trigger').on('click touch', function () {
                $(this).parent().toggleClass('closed');
            });

            // Handle clicks on step items
            me.elem.$container.on('click touch', '.wizard-step', function () {
                var $step = $(this);
                var link = $step.data('link');
                var stepKey = $step.data('key');
                var $wizard = $step.parents('.wizard');
                var wizardKey = $wizard.data('key');
                var action = $step.data('action');

                action = action.replace(/'/g, '"');
                action = JSON.parse(action);

                me.storage.activeStep = {
                    key: stepKey,
                    node: $step
                };

                // checks if there is a running ajax request,
                if($.active === 1){
                    $(document).ajaxStop(function () {
                        $(this).unbind("ajaxStop");
                        actionHandling();
                    });
                } else {
                    actionHandling();
                }

                function actionHandling(){
                    if (action.type === 'highlight') {
                        me.elem.$highlight = me.findHighlightElement(action);

                        if (me.elem.$highlight && me.elem.$highlight.length > 0) {

                            if (me.elem.$hint.length > 0) {
                                me.elem.$hint.remove();
                            }

                            me.scrollElementIntoView();

                            me.createHint();

                            me.bindRedirectPrevention();
                        } else if (link !== undefined && link !== "undefined" && link.length > 0) {
                            me.visitUrl(link, wizardKey, stepKey);
                        }
                    }

                    if(action.type === 'click_by_click_assistant'){
                        if(link !== undefined && link !== "undefined"){
                            var cleanLink = "";

                            if(link.charAt(0) === "."){ //sent links usually start with, "./index.php..." - not compareable
                                cleanLink = link.replace('.', '');
                            }

                            if(window.location.href.indexOf(cleanLink) > -1){ // if the user is on the correct page
                                me.createClickByClickAssistant(action.data);
                            } else { // otherwise go to the correct page first
                                me.visitUrl(link, wizardKey, stepKey);
                            }

                        } else {
                            me.createClickByClickAssistant(action.data);
                        }
                    }
                }
            });

            me.elem.$body.on('click touch blur change focus', '.wizard-highlight', function (e) {
                if (e.type === $(this).data('data-event')) {
                    me.completeStep();
                    me.removeHint();
                    me.nextStep();
                }
            });

            $('#wizard-container .progress').on('progress-update', function () {
                var self = $(this);

                if (self.data('value') === self.data('max')) {
                    var parentStepgroup =  self.parents('.wizard-step-group');

                    parentStepgroup.removeClass('in-progress');
                    parentStepgroup.addClass(['complete', 'closed']);

                    me.storage.stepGroups[parentStepgroup.data('name')].completed = true;
                }

                me.finishWizard();
            });

            // A form submit has to wait until a Ajax is done
            $('form').on('submit.wizard', function (e) {
                if(me.storage.minimized){
                    return;
                }

                var self = $(this);
                e.preventDefault();

                me.storage.submitPrevented = self;

                if(!me.storage.ajaxRunning){
                    self.unbind('submit.wizard').submit();
                }
            });

            $('.close-wizard').on('click touch',function () {
                me.elem.$hideTrigger.trigger('click');
                me.cancelWizard();
                me.elem.$container.remove();
                me.elem.$hideTrigger.remove();
            })
        },

        bindRedirectPrevention: function(){
            $('a').on('click.wizard', function (e) {
                if(me.storage.minimized){
                    return;
                }

                var self = $(this);
                e.preventDefault();

                me.storage.linkPrevented = self;
            });
        },

        /**
         *
         * @param {Object} data
         */
        createClickByClickAssistant: function(data){
            me.elem.$clickByClickContainer =
                $('<click-by-click-assistant' +
                    '        id="click-by-click-wizard"' +
                    '        v-if="showAssistant"' +
                    '        @close="showAssistant = false"' +
                    '        :pages="pages"' +
                    '        :allowClose="allowClose"' +
                    '        :pagination="pagination">' +
                    '</click-by-click-assistant>');

            me.elem.$clickByClickContainer.appendTo(me.elem.$body);

            if(data.pagination === undefined){
                data.pagination = false;
            }

            new Vue({
                el: '#click-by-click-wizard',
                data: data
            });
        },

        scrollElementIntoView: function(){
            var element = me.elem.$highlight,
                elementTop = element.offset().top,
                elementBottom = elementTop + element.outerHeight(),
                viewportTop = me.elem.$window.scrollTop(),
                viewportBottom = viewportTop + me.elem.$window.height(),
                elementInViewport =  elementBottom > viewportTop && elementTop < viewportBottom;

            if(elementInViewport){
                return;
            }

            var elementHeight = element.height(),
                viewportHeight = me.elem.$window.height(),
                elementOffset = element.offset().top,
                offset;

            if (elementHeight < viewportHeight) {
                offset = elementOffset - ((viewportHeight / 2) - (elementHeight / 2));
            }

            window.setTimeout(function () {
                window.scrollTo({
                    top: offset,
                    behavior: 'smooth'
                });
            }, 100);
        },

        /**
         * @param {string} url
         * @param {string|null} wizardKey
         * @param {string|null} stepKey
         */
        visitUrl: function (url, wizardKey, stepKey) {
            if(url === undefined){
                return;
            }

            if (typeof wizardKey === 'undefined' && typeof stepKey === 'undefined') {
                window.location.href = me.addParameterToUrl(url);
                return;
            }

            window.location.href = me.addParameterToUrl(url);
        },

        /**
         * @param {string} url
         *
         * @return {string}
         */
        addParameterToUrl: function (url) {
            var finalUrl,
                elementParam = '';

            var hashCharPosition = url.lastIndexOf('#');
            if (hashCharPosition === -1) {
                // Keine Sprungmarke in URL > Random-Parameter wird nicht benötigt
                finalUrl = url;
            } else {
                var randomNumber = Math.floor(Math.random() * Math.floor(9999)),
                    urlStart = url.slice(0, hashCharPosition),
                    urlEnd = url.slice(hashCharPosition);

                finalUrl = urlStart + elementParam + '&rand=' + randomNumber + urlEnd;
            }

            return finalUrl + elementParam;
        },

        /**
         * Creates hint box
         */
        createHint: function () {
            if (me.elem.$highlight.length === 0) {
                return;
            }

            var hintHtml =
                '<div id="wizard-hint"><div class="wizard-hint-arrow"></div>' +
                    '<h2>' + me.storage.activeStep.node.data('hint-title') + '</h2>' +
                    '<p>' + me.storage.activeStep.node.data('hint-content') + '</p>' +
                    '<div class="wizard-hint-action">';

                    if(me.storage.activeStep.node.data('hint-cta') !== false){
                        hintHtml += '<button class="button button-secondary">Weiter</button>'
                    }

            hintHtml +=
                    '</div>' +
                '</div>'

            me.elem.$hint = $(hintHtml).appendTo('body');

            me.elem.$hint.find('.wizard-hint-action').on('click touch', function () {
                me.completeStep();
                me.removeHint();
                me.nextStep();
            });

            var resizeTimeout;
            $(window).on('resize scroll', function () {
                if (me.storage.overlay === null) {
                    return;
                }

                if (!!resizeTimeout) {
                    clearTimeout(resizeTimeout);
                }

                resizeTimeout = setTimeout(function () {
                    me.storage.overlay.focus(me.elem.$highlight);
                    me.positionHintAtHighlight();
                }, 10);
            });

            me.positionHintAtHighlight();
        },

        removeHint: function () {
            if(me.elem.$hint !== undefined && me.elem.$hint !== null){
                me.elem.$hint.remove();
            }

            if(me.storage.overlay !== undefined && me.storage.overlay !== null){
                me.storage.overlay.remove();
                me.storage.overlay = null;
            }

            $('a').unbind('click.wizard');
            $('form').unbind('submit.wizard');
        },

        /**
         * Moves hint to highlighted element
         */
        positionHintAtHighlight: function () {
            if (me.storage.overlay !== null) {
                me.storage.overlay.focus(me.elem.$highlight);
            } else {
                me.storage.overlay = new focusOverlay;
                me.storage.overlay.init(me.elem.$highlight);
            }

            var hintOptions = {
                my: 'left-10px top+15px',
                at: 'left bottom',
                of: me.elem.$highlight
            }

            var hintArrowOptions = {
                my: 'left top+5',
                at: 'left bottom',
                of: me.elem.$highlight
            }

            if(me.elem.$highlight.hasClass('hasDatepicker')){
                me.elem.$hint.find('.wizard-hint-arrow').addClass('point-right');
                hintOptions.my = 'left-230 top-50%';
                hintOptions.at = 'left bottom';

                hintArrowOptions.my = 'right+5 top-24';
                hintArrowOptions.at = 'left bottom';
            }

            window.setTimeout(function () {
                me.elem.$hint.position(hintOptions);
                me.elem.$hint.find('.wizard-hint-arrow').position(hintArrowOptions);
            },100);

            me.elem.$highlight.addClass('hint-added');
            me.elem.$highlight.addClass('wizard-highlight');
        },

        /**
         * Completes a step and informs the backend
         */
        completeStep: function () {
            var stepName = me.storage.activeStep.node.data('step-name'),
                parentGroup = me.storage.activeStep.node.data('parent-group'),
                parentSubgroup = me.storage.activeStep.node.data('parent-sub-group');

            if(me.elem.$highlight){
                me.elem.$highlight.removeClass('wizard-highlight');
            }

            me.storage.activeStep.node.addClass('checked');
            me.updateProgressBar();

            if (me.storage.overlay !== null) {
                me.storage.overlay.remove();
            }

            me.updateSubGroupProgress(parentGroup, parentSubgroup, stepName);
        },

        /**
         *
         * @param {String} groupName
         * @param {String} subGroupName
         * @param {String} stepName
         */
        updateSubGroupProgress: function (groupName, subGroupName, stepName) {
            var steps = me.storage.stepGroups[groupName].sub_groups[subGroupName].steps;
            var currentStep = steps[stepName];

            //set step completed in storage
            currentStep.completed = true;

            // check how many siblings the subgroup has and if they are completed
            var stepCounter = 0,
                completeSteps = 0;
            for (var step in steps) {
                if (!steps.hasOwnProperty(step)) {
                    continue;
                }

                if (steps[step].completed) {
                    completeSteps++;
                }

                stepCounter++;
            }

            if (stepCounter === completeSteps) {
                me.storage.stepGroups[groupName].sub_groups[subGroupName].completed = true;

                me.postCompleteStepGroup(groupName, subGroupName);
            }
        },

        /**
         *
         * @param {String} groupName
         * @param {String} subGroupName
         */
        postCompleteStepGroup: function(groupName, subGroupName){
            me.storage.ajaxRunning = true;

            $.ajax({
                url: 'index.php?module=wizard&action=ajax&cmd=complete_step&key=' + me.storage.key,
                method: 'post',
                data: {
                    step: groupName + '-' + subGroupName,
                    link: '.' + window.location.href.substring(window.location.href.indexOf('/index'))
                },
                dataType: 'json',
                success: function (data) {
                    // we don't need to know
                    console.info('ajax done')
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.warn(xhr.status, thrownError);
                }
            }).always(function () {
                me.storage.ajaxRunning = false;

                if(me.storage.submitPrevented){
                    me.storage.submitPrevented.unbind('submit.wizard').submit();
                    me.storage.submitPrevented = null;
                }

                if(me.storage.linkPrevented){
                    window.location.href = me.storage.linkPrevented.attr('href');

                    me.storage.linkPrevented = null;
                }
            });
        },

        /**
         * Finds and triggers the next open step
         * @param {String|null} presetHighlight
         */
        nextStep: function (presetHighlight) {
            if(me.storage.minimized){
                return;
            }

            $('.wizard-step').removeClass('next-step');

            // regular next step
            var incompleteSteps = $('.wizard-step:not(.checked)'),
                nextStep = incompleteSteps[0];

            $(nextStep).addClass('next-step').trigger('click');
            $(nextStep).parents('.wizard-step-group').removeClass("closed");
            $(nextStep).parents('.wizard-step-group').addClass("in-progress");
        },

        /**
         * Looks for a element in DOM
         * @param {String} selector
         * @returns {jQuery|HTMLElement}
         */
        findHighlightElement: function (selector) {

            var selectorString = '',
                element;

            if (selector.node.parent) {
                addSelectors(selector.node.parent);
            }

            if (selector.node.previous_sibling) {
                var initialLength = selectorString.length,
                    processedLength = 0;
                selectorString += initialLength > 0 ? ' ' : '';

                addSelectors(selector.node.previous_sibling);

                processedLength = selectorString.length;

                if (initialLength < processedLength) {
                    selectorString += ' +';
                }
            }

            if (selector.node.self) {
                selectorString += selectorString.length > 0 ? ' ' : '';

                addSelectors(selector.node.self);
            }

            element = $(selectorString).filter(function () { return $(this).css('display') !== 'none'; });

            if (element.length === 0) {
                console.warn('element not found in dom', selectorString);
                return '';
            } else {
                element.data('data-event', selector.complete_event);
            }

            /**
             * Combines
             * @param object
             */
            function addSelectors(object) {
                for (key in object) {
                   if(!object.hasOwnProperty(key)){
                        continue;
                    }

                   var current = object[key];

                    if (key === 'node_name' && current.length > 0) {
                        selectorString += current;

                    } else if (key === 'class' && current.length > 0) {

                        if(current.indexOf(' ') > - 1){
                            current = current.split(' ');

                            for(var i = 0; i < current.length; i++){
                                selectorString += '.' + current[i]
                            }

                        } else {
                            selectorString += '.' + current;
                        }
                    } else if(key === 'contains') {
                        selectorString += ':contains("' + current + '")';
                    } else if(key === 'css_selector'){
                        selectorString += current;
                    } else if (current.length > 0) {
                        selectorString += '[' + key + '="' + current + '"]';
                    }
                }
            }

            if(element.length > 1){
                element = $(element[0]);
            }

            return element;
        },

        /**
         * Updates progress bar on step group
         */
        updateProgressBar: function () {
            var progress = $('.wizard-progress');

            if (progress.length === 0) {
                return;
            }

            var totalSteps = 0;

            for (var j = 0; j < progress.length; j++) {
                var steps = $(progress[j]).parent('ul').find('li'),
                    progressEle = $(progress[j]).find('.progress'),
                    count = 0;

                totalSteps = steps.length;

                for (var i = 0; i < steps.length; i++) {
                    if ($(steps[i]).hasClass('checked')) {
                        count++;
                    }
                }

                progressEle.css('width', count / totalSteps * 100 + '%');
                progressEle.attr('data-value', count);
                progressEle.trigger('progress-update');

            }
        },

        /**
         * Generates the HTML content of a step group
         * @param {string} groupName
         * @param {Object} group
         * @param {Number} groupIndex
         *
         * @returns {string}
         */
        generateStepGroupHtml: function (groupName, group, groupIndex) {
            var stepsTotal = 0,
                stepsFinished = 0,
                self = this,
                groupHtml =
                '<div data-name="' + groupName + '" class="wizard-step-group {{groupActive}} {{groupComplete}} {{groupClosed}}">' +
                '<h4 data-number="{{groupIndex}}">{{groupTitle}}</h4><div class="wizard-group-content"><ul>';

            // subgroup
            var subGroupIndex = 0;

            for (var subGroup in group.sub_groups) {
                if (!group.sub_groups.hasOwnProperty(subGroup)) {
                    continue;
                }

                var subGroupComplete = '';
                if(group.sub_groups[subGroup].completed){
                    subGroupComplete = 'sub-group-complete';

                    stepsFinished += Object.keys(group.sub_groups[subGroup].steps).length;
                }

                groupHtml += '<div class="wizard-sub-group '+ subGroupComplete +'">';

                // create steps
                var stepIndex = 0;
                for (var step in group.sub_groups[subGroup].steps) {
                    if (!group.sub_groups[subGroup].steps.hasOwnProperty(step)) {
                        continue;
                    }

                    groupHtml += self.generateStepHTML(group.sub_groups[subGroup].steps[step], stepIndex, groupName,
                        subGroup, step, group.sub_groups[subGroup].completed);

                    stepsTotal++;
                    stepIndex++;
                }

                groupHtml += '</div>';

                subGroupIndex++;
            }

            groupHtml += '<div class="wizard-progress">' +
                '               <div class="progress" style="width: {{progress}}%" data-value="{{stepsFinished}}" data-max="{{stepsTotal}}"></div>' +
                '         </div></ul></div></div>';

            groupHtml = groupHtml.replace('{{progress}}', stepsFinished / stepsTotal * 100);
            groupHtml = groupHtml.replace('{{stepsFinished}}', stepsFinished);
            groupHtml = groupHtml.replace('{{stepsTotal}}', stepsTotal);
            groupHtml = groupHtml.replace('{{groupIndex}}', groupIndex + 1);
            groupHtml = groupHtml.replace('{{groupTitle}}', group.title);
            groupHtml = groupHtml.replace('{{groupActive}}', group.active ? 'active' : '');
            groupHtml = groupHtml.replace('{{groupComplete}}', group.complete || group.completed ? 'complete closed' : '');
            groupHtml = groupHtml.replace('{{groupClosed}}', group.closed ? 'closed' : '');

            return groupHtml;
        },

        /**
         *
         * @param {Object} item
         * @param {Number} index
         * @param {String} parentGroup
         * @param {String} parentSubGroup
         * @param {String} stepName
         *
         * @returns {*}
         */
        generateStepHTML: function (item, index, parentGroup, parentSubGroup, stepName, parentSubgroupCompleted) {
            var captionHtml = '',
                classes = item.completed === true || parentSubgroupCompleted === true ? 'wizard-step checked' : 'wizard-step';

            if(item.visible !== undefined && item.visible === false){
                classes += ' invisible';
            }

            if (typeof item.title !== 'undefined' && item.title !== null) {
                captionHtml = '<span class="caption">' + item.title + '</span>';
            }

            var itemHtml =
                '<li ' +
                'id="{{id}}" ' +
                'class="{{classes}}" ' +
                'data-key="{{key}}" ' +
                'data-link="{{link}}" ' +
                'data-step-index="{{stepIndex}}" ' +
                'data-action="{{action}}" ' +
                'data-hint-title="{{hintTitle}}" ' +
                'data-hint-cta="{{hintCta}}" ' +
                'data-hint-content="{{hintContent}}"' +
                'data-parent-group="{{parent-group}}"' +
                'data-step-name="{{step-name}}"' +
                'data-parent-sub-group="{{parent-sub-group}}">' +
                '{{captionLine}}' +
                '</li>';

            itemHtml = itemHtml.replace('{{id}}', me.storage.key + '_' + item.position);
            itemHtml = itemHtml.replace('{{classes}}', classes);
            itemHtml = itemHtml.replace('{{link}}', item.link);
            itemHtml = itemHtml.replace('{{key}}', item.position);
            itemHtml = itemHtml.replace('{{stepIndex}}', index);
            itemHtml = itemHtml.replace('{{captionLine}}', captionHtml);
            itemHtml = itemHtml.replace('{{step-name}}', stepName);
            itemHtml = itemHtml.replace('{{parent-sub-group}}', parentSubGroup);
            itemHtml = itemHtml.replace('{{parent-group}}', parentGroup);
            if (item.action) {
                item.action = JSON.stringify(item.action).replace(/"/g, '\'');

                itemHtml = itemHtml.replace('{{action}}', item.action);
                itemHtml = itemHtml.replace('{{hintTitle}}', item.caption);
                itemHtml = itemHtml.replace('{{hintContent}}', item.description);
                itemHtml = itemHtml.replace('{{hintCta}}', item.hint_cta);
            }

            return itemHtml;
        },

        /**
         * Generates the content of the wizard
         */
        generateContainerContents: function () {
            var settings = me.storage.settings,
                subTitle,
                groupsHtml = '',
                content,
                iterationIndex = 0,
                previousGroupComplete = false;

            for (var stepGroup in me.storage.stepGroups) {
                if (!me.storage.stepGroups.hasOwnProperty(stepGroup)) {
                    return;
                }

                if(iterationIndex > 0){
                    me.storage.stepGroups[stepGroup].closed = !previousGroupComplete;
                }

                groupsHtml += me.generateStepGroupHtml(stepGroup, me.storage.stepGroups[stepGroup], iterationIndex);

                previousGroupComplete = me.storage.stepGroups[stepGroup].completed;
                iterationIndex++;
            }

            content =
                '<div class="wizard-small-trigger"></div>'+
                '<div class="wizard-details">' +
                '  <h2>{{titleMinimized}}</h2>' +
                '  <h3>{{subTitle}}</h3>' +
                '  {{groupsHtml}}' +
                '   <span class="close-wizard">Wizard beenden</span>' +
                '</div>';

            content +=
                '<div class="wizard-details wizard-overlay">' +
                '<h2>Wizard erfolgreich beendet!</h2>' +
                '  <p>Du kannst nun zurück zum Learning Dashboard oder hier bleiben</p>' +
                '  <a title="Zurück zum Learning Dashboard" href="index.php?module=learningdashboard&action=list" class="button button-primary">Zurück zum Learning Dashboard</a>' +
                '</div>';

            if (typeof currentStepDescription !== 'undefined' && currentStepDescription !== null) {
                subTitle = '<div class="wizard-description">' + currentStepDescription + '</div>';
            }

            content = content.replace('{{titleMinimized}}', settings.title);
            content = content.replace('{{titleMaximized}}', settings.title);
            content = content.replace('{{groupsHtml}}', groupsHtml);
            content = content.replace('{{subTitle}}', settings.subTitle);

            // Wizard-Element erzeugen und in Wizard-Box packen
            me.elem.$self = $('<div>')
                .addClass('wizard closed')
                .attr('id', 'wizard_' + me.storage.key)
                .data('key', me.storage.key)
                .html(content);
            me.elem.$self.appendTo(me.elem.$container);

            me.registerEvents();
        },

        /**
         * @return {object|null}
         */
        getCurrentStepSettings: function () {
            var currentStep = null;

            // rand-Parameter aus aktueller URL entfernen
            var currentUrl = window.location.href.replace(/&rand=[\d]*/, '');

            me.storage.stepGroups.forEach(function (group) {

                group.steps.forEach(function (step) {
                    // Führenden Punkt aus Schritt-URL entfernen: './index.php?foo' => '/index.php?foo'
                    // Ansonsten ist Vergleich mit aktueller URL nicht möglich
                    var dotPrefixRegex = /\.\/index\.php/;
                    var stepUrl = step.link.replace(dotPrefixRegex, '/index.php');

                    var isCurrentStep = me.stringEndsWith(currentUrl, stepUrl);
                    if (isCurrentStep) {
                        currentStep = step;
                    }
                });
            });

            return currentStep;
        },

        /**
         * @param {string} haystack
         * @param {string} needle
         * @return {boolean}
         */
        stringEndsWith: function (haystack, needle) {
            var position = haystack.length - needle.length;
            if (position < 0) {
                return false;
            }

            var lastIndex = haystack.indexOf(needle, position);

            return lastIndex !== -1 && lastIndex === position;
        }
    };

    return {
        init: me.init,
        show: me.show,
        hide: me.hide,
        completeStep: me.completeStep,
        nextStep: me.nextStep,
        minimize: me.minimize,
        maximize: me.maximize,
        isMinimized: me.isMinimized,
        removeHint: me.removeHint
    };
};


var WizardContainer = (function ($) {

    var me = {

        elem: {
            $container: null,
            $hint: null,
            $hideTrigger: null
        },

        storage: {
            initialized: false,
            fetchedWizardData: null,
            minimized: false,
            wizards: {} // Loaded wizard instances
        },

        init: function () {
            if (!me.isEnabled()) {
                console.info('Wizard ist deaktiviert.');
                return;
            }
            if (me.isInitialized()) {
                console.warn('Wizard ist bereits initialisiert!');
                return;
            }

            me.storage.initialized = true;

            me.fetchWizards().then(me.createContainerPromise).then(me.createWizards);
        },

        show: function () {
            me.elem.$hideTrigger.removeClass('is-hidden');
            me.elem.$container.removeClass('is-hidden');
            me.elem.$container.css({
                opacity: '1',
                display: 'block'
            });

            me.setWizardMinimizedState(false);

            me.storage.minimized = false;
        },

        hide: function () {
            me.elem.$hideTrigger.addClass('is-hidden');
            me.elem.$container.addClass('is-hidden');
            me.elem.$container.css({
                opacity: '0',
                display: 'none'
            });

            $('a').unbind('click.wizard');
            $('form').unbind('submit.wizard');

            me.setWizardMinimizedState(true);

            me.storage.minimized = true;

            for(var wizard in me.storage.wizards){
                me.storage.wizards[wizard].removeHint();
            }
        },

        completeStep: function(){
            for(var wizard in me.storage.wizards){
                me.storage.wizards[wizard].completeStep();
                me.storage.wizards[wizard].nextStep();
            }
        },

        /**
         *
         * @param {Boolean} isMinimized
         */
        setWizardMinimizedState: function(isMinimized) {

            $.ajax({
                url: 'index.php?module=wizard&action=ajax&cmd=set_minimized&value=' + isMinimized,
                method: 'post',
                success: function(data){

                },
                error: function (jqXhr) {
                    console.warn('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        },

        enable: function () {
            localStorage.setItem('wizard_enabled', 'true');
            me.init();
        },

        isMinimized: function(){
            return me.storage.minimized;
        },

        disable: function () {
            me.hide();
            me.storage.initialized = false;
            me.elem.$container.remove();
            me.elem.$container = null;
            localStorage.setItem('wizard_enabled', 'false');
        },

        /**
         * @return {boolean}
         */
        isEnabled: function () {
            var store = localStorage.getItem('wizard_enabled');

            return typeof store !== 'undefined' && store !== 'false';
        },

        /**
         * @return {boolean}
         */
        isInitialized: function () {
            return me.storage.initialized;
        },

        /**
         * Creates wizard container only if there is at least one wizard
         *
         * @return {Promise}
         */
        createContainerPromise: function () {
            return $.Deferred(function (dfd) {
                if (me.storage.fetchedWizardData === null) {
                    console.info('No active wizards found.');
                    dfd.fail();
                    return;
                }

                me.storage.minimized = me.storage.fetchedWizardData.minimized;

                me.createContainerElement();
                dfd.resolve();
            }).promise();
        },

        /**
         * Creates wizard container and events
         */
        createContainerElement: function () {
            me.elem.$container = $('<div id="wizard-container"></div>').appendTo('body');
            me.elem.$container.css({
                opacity: '0',
                display: 'none'
            });

            // Make container draggable
            me.elem.$container.draggable({
                start: function () {
                    $(this).css({transition: 'none'});
                },
                stop: function () {
                    $(this).css({height: 'auto'}); // otherwise jQuery would set a fixed height
                }
            });

            // Hide/Show wizard container
            me.elem.$hideTrigger = $(
                '<div id="wizard-hide-trigger">' +
                '<div class="wizard-trigger-overlay"></div>' +
                '<div class="wizard-close-x"></div>' +
                '</div>').appendTo('body');

            if(me.storage.minimized){
                me.elem.$hideTrigger.addClass('is-hidden');
            }

            me.elem.$hideTrigger.on('click touch', function (e) {
                e.preventDefault();
                if (me.elem.$hideTrigger.hasClass('is-hidden')) {
                    me.show();
                    me.storage.minimized = false;
                } else {
                    me.hide();
                    me.storage.minimized = true;
                }
            });
        },

        /**
         * Fetch active wizards
         *
         * @return {Deferred} jQuery Promise Object
         */
        fetchWizards: function () {
            var activeWizardEle = $('#active-wizard'),
                key;

            if (activeWizardEle.length > 0) {
                key = JSON.parse(activeWizardEle.text());
            }

            if (key === undefined || typeof key.key !== 'string') {
                return $.Deferred(function (dfd) {
                    dfd.reject();
                }).promise();
            } else {
                return $.ajax({
                    url: 'index.php?module=wizard&action=ajax&cmd=get_by_key&key=' + key.key,
                    method: 'get',
                    success: function (data) {
                        me.storage.fetchedWizardData = data;
                    }
                });
            }
        },

        /**
         *
         */
        createWizards: function () {
            me.createWizard(me.storage.fetchedWizardData);
            if(!me.storage.minimized){
                me.show();
            }
        },

        /**
         * @param {Object} data
         */
        createWizard: function (data) {
            var wizardInstance = new Wizard($);
            var wizardKey = data.key;

            wizardInstance.init(data);
            me.storage.wizards[wizardKey] = wizardInstance;
        },

        /**
         * @param {string} key
         *
         * @return {boolean}
         */
        hasWizard: function (key) {
            return me.storage.wizards.hasOwnProperty(key);
        },

        /**
         * @param {string} key
         *
         * @return {Wizard}
         */
        getWizard: function (key) {
            return me.storage.wizards[key];
        },

        /**
         * @param {String} wizardKey
         */
        skipWizard: function (wizardKey) {
            if (!me.hasWizard(wizardKey)) {
                return;
            }

            $.ajax({
                url: 'index.php?module=wizard&action=ajax&cmd=deactivate_wizard',
                method: 'post',
                data: {
                    wizard: wizardKey
                },
                dataType: 'json',
                success: function () {
                    me.getWizard(wizardKey).hide();
                },
                error: function (jqXhr) {
                    alert('Fehler: ' + jqXhr.responseJSON.error);
                }
            });
        }

    };

    return {
        init: me.init,
        show: me.show,
        hide: me.hide,
        enable: me.enable,
        disable: me.disable,
        completeStep: me.completeStep,
        isMinimized: me.isMinimized
    };

})(jQuery);

$(document).ready(function () {
    WizardContainer.init();
});


var focusOverlay = function () {

    var me = {

        storage: {
            target: {},
            window: {},
            targetOffset: null,
            settings: null,
            platesPositions: ['top', 'right', 'bottom', 'left']
        },

        elem: {
            $container: null,
            $target: null,
            $self: null,
            $window: null,
            $plates: []
        },

        init: function (element) {
            if (element === undefined || element.length === 0) {
                console.warn('Please pass an element to focus');
                return;
            }

            me.elem.$container = $('<div id="focus-overlay"></div>').appendTo('body');
            me.addEventHandler();

            me.focus(element);
        },

        addEventHandler: function () {
            var resizeTimeout;
            $(window).on('resize scroll', function () {
                if (!!resizeTimeout) {
                    clearTimeout(resizeTimeout);
                }
                resizeTimeout = setTimeout(function () {
                    me.storeWindow();
                    me.focus();
                }, 10);
            });
        },

        storeElement: function (element) {
            me.elem.$target = element;
            me.storage.target.offset = element.offset();
            me.storage.target.width = element.outerWidth();
            me.storage.target.height = element.outerHeight();
        },

        storeWindow: function () {
            me.elem.$window = $(window);
            me.storage.window.width = me.elem.$window.outerWidth();
            me.storage.window.height = me.elem.$window.outerHeight();
            me.storage.window.scrollTop = me.elem.$window.scrollTop();
        },

        focus: function (element) {
            element = element || me.elem.$target;
            me.storeElement(element);
            me.storeWindow();

            me.addOverlay();
        },

        remove: function () {
            me.elem.$container.remove();
        },

        addOverlay: function () {
            var current,
                overlay,
                finishedOptions = [],
                overlayTemplate = $('<div class="focus-overlay-plate"></div>');

            for (var i = 0; i < me.storage.platesPositions.length; i++) {
                current = me.storage.platesPositions[i];

                if (me.elem.$plates.length < 4) {
                    overlay = overlayTemplate.clone();
                } else {
                    overlay = me.elem.$plates[i];
                }

                var options = {
                        'width': 0,
                        'height': 0,
                        'top': 0,
                        'right': 0,
                        'bottom': 0,
                        'left': 0
                    };

                if(finishedOptions[0] !== undefined){
                    var topEleHeigt = finishedOptions[0].height;
                }

                if(finishedOptions[1] !== undefined){
                    var rightEleWidth = finishedOptions[1].width;
                }

                if(finishedOptions[2] !== undefined){
                    var bottomEleHeight = finishedOptions[2].height;
                }

                var elementViewportOffset = me.storage.target.offset.top - me.storage.window.scrollTop;

                if (current === 'top') {
                    options.width = '100%';
                    options.height = elementViewportOffset - 10;
                }

                if (current === 'right') {
                    options.width = me.storage.window.width - me.storage.target.offset.left - me.storage.target.width -
                        10;
                    options.height = me.storage.window.height - topEleHeigt;
                    options.top = topEleHeigt;
                    options.left = me.storage.target.offset.left + me.storage.target.width + 10;
                }

                if (current === 'bottom') {
                    options.width = me.storage.window.width - rightEleWidth;
                    options.height = me.storage.window.height - elementViewportOffset -
                        me.storage.target.height - 10;
                    options.top = elementViewportOffset + me.storage.target.height + 10;

                    if(me.elem.$target.hasClass('hasDatepicker')){
                        options.top += 150;
                        options.height -= 150;
                    }
                }

                if (current === 'left') {
                    options.width = me.storage.target.offset.left - 10;
                    options.height = me.storage.window.height - topEleHeigt -
                        bottomEleHeight;
                    options.top = topEleHeigt;
                }

                finishedOptions.push(options)

                if (me.elem.$plates.length < 4) {
                    overlay.addClass(current);
                    overlay.appendTo(me.elem.$container);
                    me.elem.$plates.push(overlay);
                }
            }

            // options are not pushed in for loop, in order to add them all at once. (animation issues)
            me.elem.$plates[0].css(finishedOptions[0]);
            me.elem.$plates[1].css(finishedOptions[1]);
            me.elem.$plates[2].css(finishedOptions[2]);
            me.elem.$plates[3].css(finishedOptions[3]);

        }
    };

    return {
        init: me.init,
        focus: me.focus,
        remove: me.remove
    };
};
