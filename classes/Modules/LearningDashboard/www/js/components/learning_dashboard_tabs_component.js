Vue.component('learning-dashboard-tabs', {
    props: ['tabs'],
    data: function () {
        return {
            showCompleted: true,
            activeTabIndex: null,
            activeLessonIndex: null
        };
    },
    template:
        '<div>' +
        '<div class="grid-container tab-row" :class="{\'border-bottom\': tabs.length > 1 }">' +
        '    <div class="dashboard-tabs grid-container">' +
        '       <div v-if="tabs.length > 1" class="dashboard-tab noselect" v-for="(tab, tabIndex) in tabs" :key="tabIndex" :class="{\'active\': activeTabIndex === tabIndex }" @click="setActiveTab(tabIndex)">{{ tab.name }}</div>' +
        '    </div>' +
        '    <div class="grid-container">' +
        '       <input type="checkbox" @change="showCompleted = !showCompleted" id="show-completed-input"/>' +
        '       <label class="grid-padded-horizontal noselect" for="show-completed-input">{{ $wording.tabs.hideCompleted}}</label>' +
        '    </div>' +
        '</div>' +
        '<transition name="fade" mode="out-in">' +
        '<div v-for="(tab, tabIndex) in tabs" :key="tabIndex" v-if="tabIndex === activeTabIndex" class="grid-container grid-2-column grid-padded-vertical">' +
        '    <div class="grid-container grid-cell cell-2-8 grid-padded-right dashboard-lessons">' +
        '           <div class="grid-card">' +
        '               <div class="grid-padded-s">' +
        '                       <button class="cta-button" v-if="tab.cta">' +
        '                           <span class="prefix">{{ tab.cta.prefix }}</span>' +
        '                       {{ tab.cta.text }}' +
        '                   </button>' +
        '               </div>' +
        '              <learning-dashboard-lesson' +
        '                v-for="(lesson, lessonIndex) in tab.lessons" ' +
        '                class="dashboard-lesson"' +
        '                :lesson="lesson" ' +
        '                :lessonIndex="lessonIndex"' +
        '                :key="lessonIndex"' +
        '                :class="{\'active\': lessonIndex === activeLessonIndex }"' +
        '                @click.native="setActiveLesson(lessonIndex)">' +
        '              </learning-dashboard-lesson>' +
        '           </div>' +
        '       </div>' +
        '       <div class="grid-container grid-cell tasks cell-6-8 grid-padded-left">' +
        '           <div v-for="(lesson, lessonIndex) in tab.lessons" :key="lessonIndex" class="grid-cell grid-container grid-3-column dashboard-tasks">' +
        '            <learning-dashboard-task' +
        '               v-if="lessonIndex === activeLessonIndex" ' +
        '               v-for="(task, taskIndex) in filterCompletedTasks(lesson.tasks)" ' +
        '               :task="task" ' +
        '               :activeTabIndex="activeTabIndex" ' +
        '               :activeLessonIndex="activeLessonIndex"' +
        '               :showCompleted="showCompleted"' +
        '               :key="taskIndex">' +
        '              </learning-dashboard-task>' +
        '           </div>' +
        '       </div>' +
        '</div>' +
        '</transition>' +
        '</div>',
    mounted: function () {
        this.findActiveTabAndLesson();
    },
    methods: {
        /**
         *
         * @param {Number} index
         */
        setActiveTab: function (index) {
            this.activeTabIndex = index;
        },

        /**
         *
         * @param {Number} index
         */
        setActiveLesson: function (index) {
            this.activeLessonIndex = index;
        },

        /**
         *
         * @param {Object} tasks
         *
         * @returns {Boolean}
         */
        filterCompletedTasks: function (tasks) {
            var self = this;

            return tasks.filter(function (task) {
                if (!self.showCompleted) {
                    if (task.progress.complete === task.progress.total) {
                        return false;
                    }
                }

                return true;
            });
        },

        /**
         * finds active Tab and active Lesson
         */
        findActiveTabAndLesson: function () {
            for (var i = 0; i < this.tabs.length; i++) {
                if (this.tabs[i].active === true) {
                    this.setActiveTab(i);

                    for (var j = 0; j < this.tabs[i].lessons.length; j++) {
                        if (this.tabs[i].lessons[j].active === true) {
                            this.setActiveLesson(j);
                        }
                    }
                }
            }

            if(this.activeLessonIndex === null){
                this.activeLessonIndex = 0;
            }

            if(this.activeTabIndex === null){
                this.activeTabIndex = 0;
            }
        }
    }
});
