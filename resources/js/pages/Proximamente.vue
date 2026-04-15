<template>
    <AppLayout>
        <div class="proximamente">
            <h1>Pròximament!</h1>
            <p>
                Estem treballant en aquesta secció. Torna aviat per veure les
                novetats!
            </p>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/layout/AppLayout.vue';
</script>
<style scoped>
.proximamente {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    text-align: center;
}
.proximamente h1 {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 2.5rem;
    color: #1a8a7d;
    margin-bottom: 0.75rem;
}
.proximamente p {
    font-family: 'Instrument Sans', system-ui, sans-serif;
    font-size: 1.1rem;
    color: #4b5563;
}
</style>
