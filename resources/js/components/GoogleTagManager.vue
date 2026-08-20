<script setup lang="ts">
import { onMounted } from 'vue';

const GTM_ID = 'GTM-MWXBRNTW';
const SCRIPT_ID = 'gtm-script';
const NOSCRIPT_ID = 'gtm-noscript';

onMounted(() => {
    if (document.getElementById(SCRIPT_ID) !== null) {
        return;
    }

    const windowWithDataLayer = window as unknown as {
        dataLayer?: unknown[];
    };

    windowWithDataLayer.dataLayer ??= [];
    windowWithDataLayer.dataLayer.push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js',
    });

    const firstScript = document.getElementsByTagName('script')[0];
    const script = document.createElement('script');
    script.id = SCRIPT_ID;
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtm.js?id=${GTM_ID}`;

    if (firstScript?.parentNode) {
        firstScript.parentNode.insertBefore(script, firstScript);
    } else {
        document.head.appendChild(script);
    }

    if (document.getElementById(NOSCRIPT_ID) !== null) {
        return;
    }

    const noscript = document.createElement('noscript');
    noscript.id = NOSCRIPT_ID;

    const iframe = document.createElement('iframe');
    iframe.src = `https://www.googletagmanager.com/ns.html?id=${GTM_ID}`;
    iframe.height = '0';
    iframe.width = '0';
    iframe.style.display = 'none';
    iframe.style.visibility = 'hidden';

    noscript.appendChild(iframe);
    document.body.insertBefore(noscript, document.body.firstChild);
});
</script>

<template>
    <span style="display: none" aria-hidden="true"></span>
</template>
