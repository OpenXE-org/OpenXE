Vue.component('click-by-click-assistant', {
    props: ['pages', 'allowclose', 'pagination'],
    data: function(){
        return {
            activePage: 0,
            currentTransition:'',
            dataStorage: []
        }
    },
    template: '<div class="click-by-click-assistant"><div class="wrapper"><div class="container">' +
        '<div v-if="allowclose" class="app-close-button" @click="$emit(\'close\')"></div>' +

        '<transition :name="currentTransition" mode="out-in">' +

            /** DEFAULT TEXT PAGE **/
            '<div class="page" v-for="(page, index) in pages" ' +
                    'v-if="page.type === \'defaultPage\' && activePage === index" ' +
                    ':data-pageIndex="index" ' +
                    ':key="index">' +
                '<app-media v-if="page.headerMedia" :media="page.headerMedia"></app-media>' +
                '<div class="page-content">' +
                    '<div v-if="!page.headerMedia && page.icon" class="header-icon" :class="page.icon"></div>' +
                    '<h2 v-if="page.headline" v-html="page.headline"></h2>'+
                    '<h3 v-if="page.subHeadline" v-html="page.subHeadline"></h3>'+
                    '<p class="page-text" v-if="page.text" v-html="page.text"></p>'+
                    '<div class="flex-container" v-if="page.link">'+
                        '<div v-if="page.link" class="link">'+
                           '<a class="link" :href="page.link.link" >{{ page.link.title }}</a>'+
                       '</div>' +
                    '</div>'+
                    '<button v-if="button.action === \'next\'" ' +
                        'v-for="button in page.ctaButtons" ' +
                        'class="button button-primary cta center" ' +
                        '@click="changePage(\'next\')">{{ button.title }}</button>'+

                    '<button v-if="!button.link && button.action === \'close\'" ' +
                        'v-for="button in page.ctaButtons" ' +
                        'class="button button-primary cta center" ' +
                        '@click="$emit(\'close\')">{{ button.title }}</button>'+

                    '<button v-if="!button.link && button.action === \'completeStep\'" ' +
                        'v-for="button in page.ctaButtons" ' +
                        'class="button button-primary cta center" ' +
                        '@click="$emit(\'completeStep\')">{{ button.title }}</button>'+

                    '<button v-if="button.link && button.action === \'close\'" ' +
                        'v-for="button in page.ctaButtons" ' +
                        'class="button button-primary cta center" ' +
                        '@click="link(button.link)">{{ button.title }}</button>'+

                    '<app-pagination v-if="pagination" :pages="pages" :index="index"></app-pagination>' +
                '</div>'+
            '</div>' +

            /** FORM PAGE **/
            '<div class="page" v-for="(page, index) in pages" ' +
                'v-if="(page.type === \'form\' || page.type === \'survey\') && activePage === index" ' +
                ':data-pageIndex="index" :key="index">' +
                '<app-media v-if="page.headerMedia" :media="page.headerMedia"></app-media>' +
                '<div class="page-content">' +
                    '<div v-if="!page.headerMedia && page.icon" class="header-icon" :class="page.icon"></div>' +
                    '<h2 v-html="page.headline"></h2>'+
                    '<p v-if="page.subHeadline" v-html="page.subHeadline"></p>'+
                    '<app-form :page="page"></app-form>' +
                    '<app-pagination v-if="pagination" :pages="pages" :index="index"></app-pagination>' +
                '</div>' +
            '</div>' +
        '</transition>' +

        '</div></div></div>',
    mounted: function(){
        var self = this;
        self.saveDataRequiredForSubmit();

        self.$on('completeStep', function(){

            if(WizardContainer !== undefined){
                WizardContainer.completeStep();
            }

            self.$emit('close');
        });
    },
    methods: {
        /**
         * @param {string} direction
         */
        changePage: function(direction){
            if(direction !== 'back' && direction !== 'next'){
                return;
            }

            this.activePage = direction === 'next' ? this.activePage + 1 : this.activePage - 1;
            this.currentTransition = direction;
        },
        link: function(link)
        {
            window.location.href = link;
        },

        /**
         * saves all data that was defined on the building JSON file in order to submit it later
         */
        saveDataRequiredForSubmit: function(){
            var current;

            for(var i = 0; i < this.pages.length; i++){
                current = this.pages[i].dataRequiredForSubmit;

                if(current === undefined || current.length === 0){
                    return;
                }
                this.setToStorage(current);
            }
        },

        /**
         * @param {Object} object
         */
        setToStorage: function(object){
            this.dataStorage.push(object);
        },

        /**
         *
         * @returns {Object}
         */
        getStorage: function(){
            return this.dataStorage;
        },

        /**
         * clear storage, but keeps vue listener on this.dataStorage
         */
        clearStorage: function(){
            for (var member in this.dataStorage) {
                delete this.dataStorage[member];
            }
        }
    }
});

Vue.component('app-form',{
   props: ['page'],
    data: function () {
        return {
            rowId: 0,
            surveyChoice: [],
            showSurveyError: false,
            formValid: true,
            formWasValidated: false,
            loading: false,
            errorMsg: 'Bitte überprüfe die Eingabefelder'
        }
    },
    template:
    '<form @submit.prevent="processForm" novalidate>' +
        '<div class="flex-container" v-for="(row, rowIndex) in page.form" :key="row.id">' +
            '<app-input-row v-if="row.inputs !== undefined && row.inputs.length > 0" ' +
                    'ref="row" :row="row" ' +
                    ':hasSiblings="page.form.length > 1" ' +
                    '@deleteme="removeInputRow(rowIndex)"></app-input-row>' +

            '<span v-else-if="row.surveyButtons !== undefined && row.surveyButtons.length > 0" ' +
                    'class="survey-button-container" ' +
                    'v-for="button in row.surveyButtons">'+
                '<input type="checkbox" :id="button.value" name="data" :value="button.value" v-model="surveyChoice"/>'+
                '<label :for="button.value" class="button button-secondary" > {{ button.title }} </label>' +
            '</span>' +
        '</div>' +
        '<div class="flex-container" v-if="page.link">'+
            '<div v-if="page.link" class="add-row">'+
                '<a class="link" :href="page.link.link" >{{ page.link.title }}</a>'+
            '</div>' +
        '</div>'+
        '<transition name="fade">' +
            '<div v-if="page.errorMsg && showSurveyError && surveyChoice.length === 0" ' +
                    'class="errorMsg">{{ page.errorMsg }}</div>'+
            '<div v-if="formWasValidated && !formValid" class="errorMsg"> {{ errorMsg }}</div>'+
        '</transition>' +
        '<button v-for="button in page.ctaButtons" ' +
            ':type="button.action" class="button button-primary cta center">{{ button.title }}' +
        '<app-spinner v-if="loading"></app-spinner></button>' +
        '</form>',
    methods:{
        /**
         * @param {Object} row
         */
        addInputRow: function(row){
            this.rowId++
            row.id = this.rowId;

            for(var k = 0; k < row.inputs.length; k++){
                row.inputs[k].name += this.rowId;
            }

            this.page.form.push(row);

            for(var i = 0; i < this.page.form.length -1; i++){
                this.page.form[i].add.allow = false;
            }

            if(row.add.maximum === this.page.form.length){
                this.allowAddOnLastRow(false);
            }
        },

        /**
         * @param {number} index
         */
        removeInputRow: function(index){
            if(this.page.form.length <= 1){
                return;
            }

            this.page.form.splice(index, 1);

            this.allowAddOnLastRow(true);
        },

        /**
         * @param {string} decision
         */
        allowAddOnLastRow: function(decision){
            var lastIndex =  this.page.form.length - 1;
            this.page.form[lastIndex].add.allow = decision;
        },

        /**
         * @param {Object} e
         */
        processForm: function(e){
            this.validateForm();

            if(!this.formValid){
                return;
            }

            if(!this.page.submitType){
                throw new Error("Please define submitType in your JSON");
            }

            if(this.page.submitType === "save"){
                this.$parent.setToStorage(this.filterDataFromSubmitEvent(e));
                this.$parent.changePage("next");

                return;
            }

            this.submitForm(e);
        },

        validateForm: function(){
            if(this.page.submitType === 'survey'){
                this.formValid = this.surveyChoice.length !== 0;
                this.formWasValidated = true;

                if(!this.formValid){
                    this.showSurveyError = true;
                }
            } else {
                this.formValid = this.requiredRowsValid();
            }
        },

        /**
         * Checks if the required rows are valid
         * @returns {boolean}
         */
        requiredRowsValid: function(){
            if(this.$refs === undefined){
                console.error("Please define ref on child component");
                return false;
            }

            if(this.$refs.row === undefined) {
                return true;
            }

            var current;

            for(var i = 0; i < this.$refs.row.length; i++){
                current = this.$refs.row[i];

                // case 1: if row has not been validated (no user input), form is valid in regard of this row
                if(!current.rowWasValidated){
                    this.formWasValidated = false;
                    return true;
                }

                // case 2: if row is invalid, form is not valid
                // rowValid only includes required inputs (filtered out on row component)
                if(!current.rowValid){
                    this.formWasValidated = true;
                    return false;
                }
            }

            return true; // if case1 or case2 didn't match, form valid
        },

        /**
         * @param {Event} e
         */
        submitForm: function(e){
            var request = new XMLHttpRequest(),
                self = this,
                data,
                responseJson;

            data = this.prepareSubmitData(e);

            request.open("POST", this.page.submitUrl + '', true);

            request.addEventListener('load', function(event) {
                if (request.status >= 200 && request.status < 300) {
                    console.log("POST " + request.statusText + " status: " + request.status);
                    responseJson = JSON.parse(request.responseText);
                    if(responseJson.page !== undefined) {
                        self.$parent.pages.push(responseJson.page);
                    }
                    self.$parent.clearStorage();
                    if(responseJson.dataRequiredForSubmit !== undefined){
                        self.$parent.setToStorage(responseJson.dataRequiredForSubmit);
                    }

                    self.$parent.changePage("next");

                    self.loading = false;
                } else {
                    console.warn(request.statusText, request.responseText);

                    self.loading = false;
                    self.formValid = false;
                    self.formWasValidated = true;

                    responseJson = JSON.parse(request.responseText);

                    if(responseJson.error !== undefined) {
                        self.errorMsg = responseJson.error;
                    }
                    else {
                        self.errorMsg = 'Ooops, da ist etwas schief gelaufen. Bitte versuche es erneut.';
                    }

                    if(responseJson.dataRequiredForSubmit !== undefined){
                        self.$parent.setToStorage(responseJson.dataRequiredForSubmit);
                    }
                }
            });
            self.loading = true;
            request.send(data);
        },

        /**
         * Combines all available data of all not-submitted pages
         *
         * @param {Object} e
         *
         * @returns {FormData}
         */
        prepareSubmitData: function(e){
            var submitData = new FormData(),
                filteredEventData,
                storageData;

            filteredEventData = this.filterDataFromSubmitEvent(e);
            storageData = JSON.parse(JSON.stringify(this.$parent.getStorage()));
            if(storageData !== undefined && storageData.length > 0){
                for(var i = 0; i < storageData.length; i++){

                    for(var key in storageData[i]){
                        submitData.append(key, storageData[i][key]);
                    }
                }
            }
            if(filteredEventData !== undefined){
                for(var filteredEventDataKey in filteredEventData){
                    submitData.append(filteredEventDataKey, filteredEventData[filteredEventDataKey]);
                }
            }
            return submitData;
        },

        /**
         * Serializes all data from a form submit event
         *
         * @param e
         *
         * @returns {Object}
         */
        filterDataFromSubmitEvent: function(e){
            var data = {},
                checkedInSurvey = [],
                current;

            for(var i = 0; i < e.target.length; i++){
                current = e.target[i];

                if(current.tagName === "button" || current.tagName === "BUTTON"){
                    continue;
                }

                if(!current.name){
                    throw new Error("Please define names for all inputs");
                }

                if(this.page.type === "survey" && (current.tagName === "input" || current.tagName === "INPUT")){
                    if(current.checked){
                        checkedInSurvey.push(current.value);
                    }

                    data[current.name] = checkedInSurvey;
                } else {
                    if(current.type === 'checkbox') {
                        if(current.checked){
                            data[current.name] = current.value;
                        }
                    }
                    else {
                        data[current.name] = current.value;
                    }
                }
            }

            return data;
        }
    }
});

Vue.component('app-input-row',{
    props: ['row', 'hasSiblings'],
    data: function(){
      return {
          rowValid: true,
          rowWasValidated: false
      }
    },
    template:
        '<div class="app-row-container">' +
            '<div class="app-input-row" :class="{\'reduced-width\': row.inputs.length === 1  }">' +
                '<div class="app-row-valid" :class="{\'icon-ok\': rowValid && rowWasValidated}"></div>' +
                '<app-input ' +
                'v-for="(input, inputIndex) in row.inputs" ' +
                ':type="input.type" ' +
                ':validation="input.validation" ' +
                ':customErrorMsg="input.customErrorMsg" ' +
                ':name="input.name" ' +
                ':label="input.label"' +
                ':value="input.value"' +
                ':connectedTo="input.connectedTo"' +
                ':options="input.options"' +
                'ref="input"'+
                ':key="inputIndex"></app-input>' +
                '<div v-if="row.removable && hasSiblings" @click="$emit(\'deleteme\')" class="remove-row"></div>' +
            '</div>' +
            '<div v-if="row.add && row.add.allow" @click="addRow" class="add-row">{{ row.add.text }}</div>' +
            '<div v-if="row.link" class="add-row"><a class="link" :href="row.link.link" >{{ row.link.title }}</a></div>' +
        '</div>',
    methods:{
        addRow: function(){
            var newRow = JSON.parse(JSON.stringify(this.row)); // removes vue observable and makes it possible to change

            this.$parent.addInputRow(newRow);
        },

        validateRow: function(){
            this.rowValid = this.requiredInputsValid();

            this.rowWasValidated = true;

            if(this.rowValid){
                this.$parent.validateForm();
            }
        },

        /**
         * Checks if the required Inputs are valid
         *
         * @returns {boolean}
         */
        requiredInputsValid: function(){
            if(this.$refs === undefined){
                throw new Error("Please define ref on child component");
            }

            var valid = true,
                current;

            for(var i = 0; i < this.$refs.input.length; i++){
                current = this.$refs.input[i];

                if(!current.validation){
                    valid = true;
                    continue;
                }

                valid = current.validation && current.valid && current.wasValidated;

                // row is invalid after first invalid input
                if(!valid){
                    return false;
                }
            }

            return valid;
        }
    }
});

Vue.component('app-input', {
    props: ['type', 'validation', 'name', 'label', 'customErrorMsg', 'options', 'value', 'connectedTo'],
    data: function () {
        return {
            inputValue: this.value ? this.value : '',
            inputType: this.type,
            inputErrorMsg: undefined,
            valid: true,
            wasValidated: false
        }
    },
    template:
        '<div v-if="type === \'select\'" class="app-input select" :class="{\'input-error\': !valid }">' +
            '<select :id="name" :name="name" v-model="inputValue" :class="{\'hasSelected\': inputValue.length > 0  }" ' +
                    '@change="validateInput" >' +
            '<option v-for="(option, index) in options" :value="option.value" :key="index">{{ option.text }}</option>' +
            '</select>' +
            '<label :for="name">{{ label }} <span v-if="validation"> (Pflichtfeld)</span></label>' +
        '</div>'+

        '<div v-else class="app-input" :class="{\'input-error\': !valid}">' +
            //'<input style="display: none" type="password" />' +
            '<input :type="inputType" :id="name" :name="name" v-model="inputValue" ' +
                    ':class="{\'hasValue\': inputValue.length > 0  }" ' +
                    '@blur="validateInput" autocomplete="off" required />' +
            '<div v-if="type === \'password\'" class="reveal" @click="togglePasswordVisibility"></div>' +
            '<label :for="name">{{ label }} <span v-if="validation"> (Pflichtfeld)</span></label>' +
            '<transition name="fade">' +
                '<div v-if="!valid && inputErrorMsg" class="input-error"> {{ inputErrorMsg }}</div>' +
            '</transition>'+
        '</div>',
    mounted: function(){
        var self = this;

        // listens to compare request "broadcast" from other component
        self.$root.$on('compareConnected', function(data){
          if(self.name !== data.connectedTo) {
              return;
          }

          // "broadcasts" to every component listening
          self.$root.$emit('comparisonResult', {
              requestingInput: data.requestingInput.name,
              valid: self.inputValue === data.requestingInput.inputValue && self.valid
          })
        });

        self.$root.$on('comparisonResult', function(result){
            if(self.name === result.requestingInput){
                self.valid = result.valid;
            }
        });
    },
    methods: {
        validateInput: function(){
            if((this.inputValue.length === 0 && !this.wasValidated) || !this.validation){
                // input is valid if it has a value and wasn't validated before (inputs do not get validated on page render)
                // or if it's not necessary to validate
                this.valid = true;
                return;
            }

            switch(this.type){
                case "email":
                    var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    this.valid = regex.test(this.inputValue);
                    this.inputErrorMsg = this.customErrorMsg || "Adresse nicht gültig";
                    break;

                case "text":
                    this.valid = this.inputValue.length >= 2;
                    this.inputErrorMsg = this.customErrorMsg || "Mindestens zwei Zeichen";
                    break;

                case "password":

                    // "broadcasting" event to listening components
                    // In this case we compare if passwords match in connected fields -> "connectedTo" option in JSON
                    if(this.connectedTo !== undefined){
                        this.$root.$emit('compareConnected', {
                            connectedTo: this.connectedTo,
                            requestingInput: {
                                name: this.name,
                                inputValue: this.inputValue
                            }
                        });
                        this.inputErrorMsg = this.customErrorMsg || "Bitte wiederholen Sie das Passwort";

                    } else {
                        this.valid = this.inputValue.length >= 4;
                        this.inputErrorMsg = this.customErrorMsg || "Mindestens vier Zeichen";
                    }

                    break;

                case "select":
                    // it's "selected/changed" (event) so it always has a valid value
                    break;

                default:
                    break;
            }

            this.wasValidated = true;

            this.$parent.validateRow();
        },

        togglePasswordVisibility: function(){
            this.inputType = this.inputType === 'password' ? 'text' : 'password';
        }
    }
});

Vue.component('app-pagination', {
    props: ["pages", "index"],
    template: '' +
        '<div class="app-pagination">' +
            '<div v-for="(dot, dotIndex) in pages" :class="{\'active\': index === dotIndex}"></div>' +
        '</div>'
});

Vue.component('app-spinner',{
    template: '<div class="spinner spinner-circle"></div>'
});

Vue.component('app-media',{
    props: ["media"],
    template:
        '<div>' +
            '<iframe v-if="media.type === \'video\'"' +
                'class="media-youtube" ' +
                ':src="media.link + \'?rel=0\'" ' +
                'frameborder="0" ' +
                'allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" ' +
                'allowfullscreen>' +
            '</iframe>' +

            '<img ' +
                'v-if="media.type === \'image\'"' +
                'class="media-image"' +
                ':src="media.link">' +
            '<img/>' +
        '</div>'
});
