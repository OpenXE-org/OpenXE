Vue.component('learning-dashboard-lesson', {
    props: ['lesson', 'lessonIndex'],
    template:
        '<div class="grid-container grid-2-column grid-padded-vertical">' +
        '   <div class="grid-cell grid-container cell-2-8 dashboard-icon">' +
        '      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
        '           <path d="M20.25 11.25V14.25C20.25 15.0784 19.5784 15.75 18.75 15.75H5.25C4.42157 15.75 3.75 15.0784 3.75 14.25V11.25" stroke="#374051" stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '           <path fill-rule="evenodd" clip-rule="evenodd" d="M21.7484 23.25H2.2524C1.73242 23.2502 1.24945 22.981 0.976044 22.5387C0.702636 22.0964 0.677798 21.5441 0.910404 21.079L2.7464 17.408C3.25463 16.3919 4.29327 15.75 5.4294 15.75H18.5714C19.7075 15.75 20.7462 16.3919 21.2544 17.408L23.0904 21.079C23.323 21.5441 23.2982 22.0964 23.0248 22.5387C22.7514 22.981 22.2684 23.2502 21.7484 23.25Z" stroke="#374051" stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '           <path d="M10.5 20.25H13.5" stroke="#374051" stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '           <path fill-rule="evenodd" clip-rule="evenodd" d="M21.148 0.75H2.852C2.50038 0.749942 2.19579 0.993864 2.119 1.337L0.75 6C0.75 7.24264 1.75736 8.25 3 8.25C4.24264 8.25 5.25 7.24264 5.25 6C5.25 7.24264 6.25736 8.25 7.5 8.25C8.74264 8.25 9.75 7.24264 9.75 6C9.75 7.24264 10.7574 8.25 12 8.25C13.2426 8.25 14.25 7.24264 14.25 6C14.25 7.24264 15.2574 8.25 16.5 8.25C17.7426 8.25 18.75 7.24264 18.75 6C18.75 7.24264 19.7574 8.25 21 8.25C22.2426 8.25 23.25 7.24264 23.25 6L21.88 1.337C21.804 0.993808 21.4995 0.749658 21.148 0.75Z" stroke="#374051" stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>' +
        '       </svg>' +
        '     </div>' +
        '     <div class="grid-cell cell-6-8">' +
        '       <h4>{{ lesson.name }}</h4>' +
        '       <progress-bar class="grid-cell cell-3-8" ' +
        '         :value="lesson.progress.completed" ' +
        '          :max="lesson.progress.total">' +
        '       </progress-bar>' +
        '       </div>' +
        '</div>'
});
