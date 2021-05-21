Vue.component('learning-dashboard-task', {
    props: ['task', 'activeTabIndex', 'activeLessonIndex'],
    data: function () {
        return {
            complete: false,
            percentageValue: 0,
            showDetails: false
        };
    },
    template:
        '   <div class="grid-cell grid-card grid-padded task grid-margin-m">' +
        '                <div v-if="task.progress.complete === task.progress.total" class="task-completion-status complete">' +
        '                   <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '                       <path d="M1 5.2L4.2 8.2L10.6 1" stroke="white" stroke-width="1.5"/>' +
        '                   </svg>' +
        '                </div>' +
        '               <div v-else-if="task.progress.complete > 0" class="task-completion-status in-progress">' +
        '                   <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '                       <path d="M4.39733 7.27575L6.07047 6.02091V3.46477C6.07047 3.20775 6.27822 3 6.53523 3C6.79225 3 7 3.20775 7 3.46477V6.25331C7 6.3997 6.93121 6.53775 6.81409 6.62512L4.95508 8.0194C4.87143 8.08214 4.77381 8.11235 4.67669 8.11235C4.53494 8.11235 4.39551 8.04867 4.3044 7.92598C4.15007 7.72099 4.1919 7.42959 4.39733 7.27575Z" fill="white"/>' +
        '                       <path d="M6 0C9.3086 0 12 2.6914 12 6C12 9.3086 9.3086 12 6 12C2.6914 12 0 9.3086 0 6C0 2.6914 2.6914 0 6 0ZM6 11.0705C8.79551 11.0705 11.0705 8.79551 11.0705 6C11.0705 3.20449 8.79551 0.929508 6 0.929508C3.20402 0.929508 0.929508 3.20449 0.929508 6C0.929508 8.79551 3.20449 11.0705 6 11.0705Z" fill="white"/>' +
        '                   </svg>'+
        '               </div>'+
        '                <div>' +
        '                <div class="task-icon" :class="\'app-category-icon-\'+task.category"></div>' +
        '                  <div class="grid-padded-vertical">' +
        '                    <h3>{{ task.title }}</h3>' +
        '                    <p v-html="task.description">{{ task.description}}</p>' +
        '                   </div>' +
        '                </div>' +
        '                    <div v-if="task.missing_permissions.length === 0 && task.missing_modules.length === 0" class="grid-container grid-3-column">' +
        '                        <progress-bar ' +
        '                           class="grid-cell cell-3-8 grid-padded-horizontal" ' +
        '                           :value="task.progress.complete" ' +
        '                           :max="task.progress.total" ' +
        '                           percentagePosition="top">' +
        '                        </progress-bar>' +
        '                        <a v-if="task.progress.complete === task.progress.total" class="grid-cell cell-3-8 button button-tertiary" :href="task.link">{{$wording.task.cta.completed}}</a>' +
        '                        <a v-else class="grid-cell cell-3-8 button button-primary" role="button" :href="task.link">{{$wording.task.cta.incomplete}}</a>' +
        '                    </div>' +

        '                    <div v-else class="grid-container grid-3-column">' +
        '                       <div class="grid-container grid-column grid-cell cell-5-8">' +
        '                       </div>' +
        '                       <button  @click="showDetails = true" class="grid-cell cell-3-8 button button-tertiary">{{$wording.task.cta.incomplete}}</button>' +
        '                    </div>' +
        '                   <div v-if="showDetails" class="task-details">' +
        '                       <div class="task-details-header grid-padded-s">' +
        '                           <svg @click="showDetails = false" width="17" height="18" viewBox="0 0 17 18" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '                               <path d="M12.52 8.99854H1" stroke="#94979E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '                               <path d="M10.1204 6.49854L12.5204 8.99854L10.1204 11.4985" stroke="#94979E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '                               <path d="M15.3999 1.49854V16.4985" stroke="#374051" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '                           </svg>' +
        '                       </div>' +
        '                       <div class="task-details-content grid-padded">' +
        '                           <p>{{$wording.task.details.info}}</p>' +
        '                           <h4 v-if="task.missing_modules.length > 0">{{$wording.task.details.missing.modules.headline}}</h4>' +
        '                           <ul v-if="task.missing_modules.length > 0">' +
        '                               <li v-for="(missingModule, missingModulesIndex) in task.missing_modules" :key="missingModulesIndex"><a :href="\'index.php?module=appstore&action=list&cmd=detail&app=\' + missingModule" target="_blank">{{ missingModule }}</a></li>' +
        '                           </ul>' +
        '                           <h4 v-if="task.missing_permissions.length > 0">{{$wording.task.details.missing.permissions.headline}}</h4>' +
        '                           <ul v-if="task.missing_permissions.length > 0">' +
        '                               <li v-for="(missingPermission, missingPermissionIndex) in task.missing_permissions" :key="missingPermissionIndex">{{ missingPermission }}</li>' +
        '                           </ul>' +
        '                       </div>' +
        '                   </div>'+
        '            </div>'
});
