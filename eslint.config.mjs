import globals from "globals";
import pluginJs from "@eslint/js";


export default [
  {
    languageOptions: { 
      // declare well known system globals 
      globals: { 
        ...globals.browser, 
        ...globals.jquery,
        'd3': 'readonly',
      },
    },
  },
  pluginJs.configs.recommended,
];