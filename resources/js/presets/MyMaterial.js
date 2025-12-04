import { definePreset } from '@primeuix/themes';
import Material from '@primeuix/themes/material';

const MyMaterial = definePreset(Material, {
    semantic: {
        colorScheme: {
            light: {
                primary: {

                    50: '#f5f5f5',
                    100: '#e5e5e5',
                    200: '#d4d4d4',
                    300: '#a3a3a3',
                    400: '#737373',
                    500: '#525252',
                    600: '#404040',
                    700: '#262626',
                    800: '#171717',
                    900: '#0a0a0a',
                    950: '#050505'

                },
            },
            dark: {
                primary: {
                    50: '#d6fcff',
                    100: '#adf9ff',
                    200: '#85f5ff',
                    300: '#5df2ff',
                    400: '#35eeff',
                    500: '#00eaff',
                    600: '#00bcd4',
                    700: '#0097a7',
                    800: '#006978',
                    900: '#003b49',
                    950: '#001d24'

                },
            }
        }
    }
});

export default MyMaterial;
