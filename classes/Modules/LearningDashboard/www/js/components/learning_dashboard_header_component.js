Vue.component('learning-dashboard-header', {
    props: ['tabs'],
    template:
        '   <div class="learning-dashboard-header grid-container"> ' +
        '        <div class="grid-cell grid-card grid-container grid-3-column grid-padded"> ' +
        '            <div class="grid-cell grid-separator-right grid-padded cell-2-8"> ' +
        '                <h2>{{ $wording.header.headline }}</h2> ' +
        '                <p>{{ $wording.header.subline }}</p> ' +
        '            </div> ' +
        '            <div class="grid-cell grid-separator-right grid-padded cell-4-8 dashboard-header-content">{{ $wording.header.content }}</div> ' +
        '            <div class="grid-cell grid-padded grid-container grid-2-column cell-2-8 dashboard-header-progress"> ' +
        '                <div v-if="tabs.length > 1" class="grid-cell cell-4-8"> ' +
        '                    <div  v-for="(tab, index) in tabs" :key="index">' +
        '                       <div class="grid-container grid-2-column">' +
        '                           <div class="grid-cell cell-6-8 progress-completed-tasks-label">{{ tab.name }}</div> ' +
        '                           <div class="grid-cell cell-2-8 progress-completed-tasks">{{ tab.progress.completed}}</div>' +
        '                       </div>' +
        '                   </div> ' +
        '                </div> ' +
        '                <div class="grid-cell cell-4-8 progress-ring-container">' +
        '                    <div class="progress-ring-percentage">' +
        '                       <span>{{ calculateProgress() }}%</span>{{ $wording.header.progress }}' +
        '                   </div>' +
        '                    <svg class="progress-ring-border" width="115" height="115">' +
        '                       <circle class="progress-ring-border-circle" stroke-width="1" fill="transparent" r="55.5" cx="57.5" cy="57.5"/>' +
        '                    </svg>' +
        '                    <svg class="progress-ring" width="110" height="110">' +
        '                       <circle class="progress-ring-circle" ref="circle" stroke-width="4" fill="transparent" r="47" cx="55" cy="55"/>' +
        '                    </svg>' +
        '                </div> ' +
        '            </div> ' +
        '        </div> ' +
        '    </div>',

    mounted: function () {
        this.progressCircle();
    },
    methods: {
        /**
         * Combines progress of all tabs in percent
         *
         * @returns {number}
         */
        calculateProgress: function () {
            var progress = 0,
                completedTotal = 0,
                total = 0,
                i, current;

            for (i = 0; i < this.tabs.length; i++) {
                current = this.tabs[i];

                completedTotal += current.progress.completed;
                total += current.progress.total;
            }

            progress = Math.round(100 * completedTotal / total);

            return progress;
        },

        progressCircle: function () {
            var circle = this.$refs.circle,
                radius = circle.r.baseVal.value,
                progressReduction = 0.66,
                circumference = radius * 2 * Math.PI,
                progress = this.calculateProgress();

            // reduces progress to not make a full circle, since our style says 2/3 = 100%
            progress = progress * progressReduction;

            if(progress > 0){
                circle.style.strokeDasharray = [circumference, circumference];
                circle.style.strokeDashoffset = circumference - (progress / 100 * circumference);
            } else {
                circle.style.stroke = 'transparent';
            }
        }
    }
});
