new Vue({
    el: '#shopware-onboarding',
    data: {
        showAssistant: true,
        pagination: true,
        allowClose: false,
        pages: [
            {
                type: 'defaultPage',
                icon: 'consultant',
                title: 'XENTRAL DEMO',
                headline: 'Hat Dir die Demoversion gefallen?',
                subHeadline: 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',
                headerMedia: {
                    type: 'video',
                    link: 'https://www.youtube.com/embed/sVsdXgi858Q'
                },
                ctaButtons: [{
                    title: 'Weiter',
                    action: 'next'
                }]
            },
            {
                type: 'form',
                submitType: 'save',
                icon: 'password-icon',
                headline: 'Passwort ändern',
                subHeadline: 'Bitte gib ein Passwort ein und bestätige es mit einer zweiten Eingabe',
                form: [{
                    id: 0,
                    name: 'set-password-row',
                    inputs: [{
                        type: 'password',
                        name: 'setPassword',
                        label: 'Passwort',
                        validation: true,
                    }]
                },
                    {
                        id: 1,
                        name: 'repeat-password-row',
                        inputs: [{
                            type: 'password',
                            name: 'repeatPassword',
                            label: 'Passwort wiederholen',
                            connectedTo: 'setPassword',
                            validation: true,
                            customErrorMsg: 'Passwörter sind nicht identisch'
                        }]
                    }],
                ctaButtons: [{
                    title: 'Weiter',
                    type: 'submit',
                    action: 'next'
                }]
            },
            {
                type: 'form',
                submitType: 'submit',
                submitUrl: 'index.php?module=welcome&action=shopwareonboarding',
                icon: 'add-person-icon',
                headline: 'Lade Dein Team ein',
                subHeadline: 'Du kannst bis zu 5 weitere Mitglieder hinzufügen',
                form:  [{
                    id: 0,
                    name: 'add-person-row',
                    removable: true,
                    add: {
                        allow: true,
                        maximum: 5,
                        text: "Weitere Mitglieder hinzufügen"
                    },
                    inputs:[{
                        type: 'text',
                        name: 'teamMemberName',
                        label: 'Name',
                        validation: false,
                        customErrorMsg: "too short"
                    }, {
                        type: 'email',
                        name: 'teamMemberEmail',
                        label: 'E-Mail',
                        validation: false,
                    }, {
                        type: 'select',
                        name: 'teamMemberRole',
                        label: 'Rolle',
                        validation: false,
                        options: [
                            {
                                text: 'Vertrieb',
                                value: 'sales'
                            }, {
                                text: 'Einkauf',
                                value: 'purchase'
                            }, {
                                text: 'Verwaltung / Office',
                                value: 'administration'
                            }, {
                                text: 'Customer Service',
                                value: 'customerservice'
                            }, {
                                text: 'Lager / Logistik',
                                value: 'warehouse'
                            }, {
                                text: 'Controlling / Finance',
                                value: 'controlling'
                            }, {
                                text: 'Management',
                                value: 'management'
                            }, {
                                text: 'Produktion',
                                value: 'production'
                            },{
                                text: 'Sonstiges',
                                value: 'misc'
                            }
                        ]
                    }]
                }],
                ctaButtons: [{
                    title: 'Weiter',
                    type: 'submit',
                    action: 'submit'
                }]
            },
            {
                type: 'defaultPage',
                icon: 'add-person-icon',
                headline: 'Die Einladung wird gesendet',
                subHeadline: 'Du musst jetzt nur noch xentral und Shopware verbinden. Gehe zurück zu Shopware und klicke auf verbinden.',

                ctaButtons: [{
                    title: 'Klasse',
                    action: 'close'
                }]
            },
        ]
    }
});
