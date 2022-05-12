<click-by-click-assistant
        id="welcome-firststart"
        v-if="showAssistant"
        @close="showAssistant = false"
        :pages="pages"
        :allowClose="allowClose"
        :pagination="pagination">
</click-by-click-assistant>
