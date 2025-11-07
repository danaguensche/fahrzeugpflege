import { createVuetify } from "vuetify";
import { VFileUpload } from 'vuetify/labs/VFileUpload'
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import colors from 'vuetify/util/colors'
import { de } from 'vuetify/locale'

const vuetify = createVuetify({
    components,
    directives,
    VFileUpload,
    colors,
    locale: {
        locale: 'de',
        messages: { de }
    },
});

export default vuetify;