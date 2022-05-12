new Vue({
    el: '#role-survey',
    data: {
        showAssistant: true,
        pagination: false,
        pages: [{
            type: 'survey',
            submitType: 'survey',
            submitUrl: 'index.php?module=welcome&action=survey&cmd=saveSurveyData',
            icon: 'survey-icon',
            headline: 'Wähle Deine Rolle(n) in xentral',
            subHeadline: 'Damit hilfst Du uns, xentral noch besser zu machen',
            dataRequiredForSubmit: {
                surveyName: 'xentral_role'
            },
            form: [{
                id:0,
                name: 'role-survey',
                surveyButtons: [
                    {
                        title: 'Vertrieb',
                        value: 'sales'
                    }, {
                        title: 'Einkauf',
                        value: 'purchase'
                    }, {
                        title: 'Verwaltung / Office',
                        value: 'administration'
                    }, {
                        title: 'Customer Service',
                        value: 'customerservice'
                    }, {
                        title: 'Lager / Logistik',
                        value: 'warehouse'
                    }, {
                        title: 'Controlling / Finance',
                        value: 'controlling'
                    }, {
                        title: 'Management',
                        value: 'management'
                    }, {
                        title: 'Produktion',
                        value: 'production'
                    },{
                        title: 'Sonstiges',
                        value: 'misc'
                    }
                ]
            }],
            ctaButtons: [{
                title: 'Senden',
                action: 'submit'
            }],
            errorMsg: "Bitte wähle mindestens eine Rolle"
        }, {
            type: 'defaultPage',
            icon: 'thanks-icon',
            headline: 'Vielen Dank für Deine Hilfe',
            ctaButtons: [{
                title: 'OK',
                action: 'close'
            }]
        }]
    }
});
