Vue.component('progress-bar', {
    props: ['value', 'max', 'percentagePosition'],
    data: function () {
        return {
            complete: false,
            percentageValue: 0
        };
    },
    watch: {
        value: function(){
            this.percentageValue = this.calculatePercentage();
        },
        max: function () {
            this.percentageValue = this.calculatePercentage();
        }
    },
    template:
        '<div class="progress-bar" :class="[complete ? \'complete\': \'\']">' +
        '   <div class="progress" :style="{ width:  percentageValue + \'%\'}"></div>' +
        '   <div class="progress-bar-percentage" :class="percentagePosition"> {{ percentageValue }}%</div>' +
        '</div>',
    mounted: function () {
        this.percentageValue = this.calculatePercentage();
    },
    updated: function () {
        this.percentageValue = this.calculatePercentage();

        if (this.percentageValue !== 100) {
            this.complete = false;
        }
    },
    methods: {
        calculatePercentage: function () {
            if (!Number.isInteger(this.value) || !Number.isInteger(this.max)) {
                return 0;
            }

            var percentage = Math.round((100 * this.value) / this.max);

            if (percentage === 100) {
                this.complete = true;
            }

            return percentage;
        }
    }
});
