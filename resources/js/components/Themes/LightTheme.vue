

<template>
    <n-config-provider :theme="theme" :theme-overrides="themeOverrides" :locale="locale" :date-locale="dateLocale">
        <slot></slot>
    </n-config-provider>
</template>

<script>
import { NConfigProvider, createTheme, deDE, dateDeDE } from 'naive-ui'

const theme = createTheme({
    common: {
        primaryColor: '#fc0328',
        primaryColorHover: 'firebrick',
        primaryColorPressed: '#c00',
        primaryColorSuppl: '#ff4d4f',
        infoColor: '#1890ff',
        successColor: '#52c41a',
        warningColor: '#faad14',
        errorColor: '#ff4d4f',
        textColorBase: '#000',
        textColor1: '#000',
        textColor2: '#000',
        textColor3: '#000',
        textColorDisabled: '#000',
        placeholderColor: '#000',
        borderColor: '#d9d9d9',
        dividerColor: '#d9d9d9',
        backgroundColor: '#fff',
        boxShadow1: '0 1px 2px rgba(0, 0, 0, 0.12)',
        boxShadow2: '0 2px 4px rgba(0, 0, 0, 0.12)',
        boxShadow3: '0 4px 8px rgba(0, 0, 0, 0.12)',
    },
    Button: {
        textColor: '#fff',
        textColorHover: '#fff',
        textColorPressed: '#fff',
        textColorFocus: '#fff',
        textColorDisabled: '#fff',
        color: '#fc0328',
        colorHover: 'firebrick',
        colorPressed: '#c00',
        colorFocus: '#c00',
        colorDisabled: '#c00',
        border: '1px solid #fc0328',
        borderHover: '1px solid firebrick',
        borderPressed: '1px solid #c00',
        borderFocus: '1px solid #c00',
        borderDisabled: '1px solid #c00',
        rippleColor: '#fff',
    }
})

const themeOverrides = {
    common: {
        primaryColor: '#fc0328',
        primaryColorHover: 'firebrick',
        primaryColorPressed: '#c00',
    },
    Button: {
        textColor: '#fff',
        textColorHover: 'darkslategray',
        textColorPressed: '#fff',
    }
}

export default {
    name: 'LightTheme',
    components: {
        NConfigProvider
    },
    data() {
        return {
            theme,
            themeOverrides,
            locale: deDE,
            dateLocale: dateDeDE
        }
    }
}
</script>

<style scoped>
/* Fügen Sie hier Ihre benutzerdefinierten Stile hinzu */
</style>