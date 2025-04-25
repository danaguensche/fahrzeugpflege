import { createVuetify } from "vuetify";
import { VFileUpload } from 'vuetify/labs/VFileUpload'
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import colors from 'vuetify/util/colors'

const vuetify = createVuetify({
  components,
  directives,
  VFileUpload,
  colors,
});

export default vuetify;
