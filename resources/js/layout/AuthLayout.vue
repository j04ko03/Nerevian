<template>
    <div class="auth-wrapper">
        <!-- Video background -->
        <video class="bg-video" autoplay muted loop playsinline preload="auto">
            <source src="/auth-background.mp4" type="video/mp4" />
        </video>

        <!-- Dark overlay -->
        <div class="bg-overlay" />

        <!-- Content -->
        <div class="auth-container">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.auth-wrapper {
    position: relative;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 2rem 1rem;
    overflow: hidden;
}

/* ── Video ──────────────────────────────── */
.bg-video {
    position: fixed;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
    pointer-events: none;
}

/* ── Overlay ────────────────────────────── */
.bg-overlay {
    position: fixed;
    inset: 0;
    background: rgba(12, 8, 4, 0.62);
    z-index: 1;
}

/* ── Content ────────────────────────────── */
.auth-container {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 1300px;
    display: flex;
    flex-direction: column;
    padding: 0 1rem;
    box-sizing: border-box;
}

.logo-container {
    display: flex;
    justify-content: center;
    margin-bottom: 2rem;
}
</style>
