<click-by-click-assistant
        id="shopware-onboarding"
        v-if="showAssistant"
        @close="showAssistant = false"
        :pages="pages"
        :allowClose="allowClose"
        :pagination="pagination">
</click-by-click-assistant>
